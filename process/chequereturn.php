<?php
session_start();
require_once('../connection/db.php');
header('Content-Type: application/json');

$detailID   = (int)$_POST['chequeid'];
$returnDate = trim($_POST['returndate']);
$reasonRaw  = trim($_POST['reason']);
$reason     = $conn->real_escape_string($reasonRaw);
$userID     = (int)$_SESSION['userid'];
$today      = date('Y-m-d');
$now        = date('Y-m-d H:i:s');

// Return date must be a valid Y-m-d date
$d = DateTime::createFromFormat('Y-m-d', $returnDate);
if (!$d || $d->format('Y-m-d') !== $returnDate) {
    echo json_encode(['status'=>0,'msg'=>'Please select a valid return date.']); exit;
}
if ($reasonRaw === '') {
    echo json_encode(['status'=>0,'msg'=>'Please enter the return reason.']); exit;
}

$res = $conn->query("SELECT `method`,`amount`,`chequedate`,`chequereturnstatus`,`tbl_invoice_payment_idtbl_invoice_payment` AS paymentid
                     FROM `tbl_invoice_payment_detail`
                     WHERE `idtbl_invoice_payment_detail`='$detailID' AND `status`=1");
$row = $res->fetch_assoc();

if (!$row) { echo json_encode(['status'=>0,'msg'=>'Payment record not found.']); exit; }
if ($row['method'] != 3) { echo json_encode(['status'=>0,'msg'=>'Only cheque payments can be returned.']); exit; }
if ($row['chequereturnstatus'] == 1) { echo json_encode(['status'=>0,'msg'=>'This cheque is already returned.']); exit; }
if ($returnDate <= $row['chequedate']) {
    echo json_encode(['status'=>0,'msg'=>'Return date must be after the cheque date ('.$row['chequedate'].').']); exit;
}
if ($returnDate > $today) {
    echo json_encode(['status'=>0,'msg'=>'Return date cannot be in the future.']); exit;
}

$paymentID = (int)$row['paymentid'];
$amount    = (float)$row['amount'];

$conn->begin_transaction();

// 1) Mark the cheque as returned on the cheque's detail row (kept for history)
$ok1 = $conn->query("UPDATE `tbl_invoice_payment_detail`
                     SET `chequereturnstatus`=1, `chequereturndate`='$returnDate', `chequereturnreason`='$reason',
                         `chequereturnuser`='$userID', `updatedatetime`='$now'
                     WHERE `idtbl_invoice_payment_detail`='$detailID'");

// 2) Unlock the related invoice(s) for payment again.
//    Must run BEFORE the has-table delete (step 6), because it finds the invoices through that table.
$ok2 = $conn->query("UPDATE `tbl_invoice` i
                     INNER JOIN `tbl_invoice_payment_has_tbl_invoice` p ON p.`tbl_invoice_idtbl_invoice`=i.`idtbl_invoice`
                     SET i.`paymentcomplete`=0
                     WHERE p.`tbl_invoice_payment_idtbl_invoice_payment`='$paymentID'");

// 3) Write the return history record
$ok3 = $conn->query("INSERT INTO `tbl_invoice_cheque_return`
                     (`returndate`,`reason`,`amount`,`status`,`updatedatetime`,`tbl_user_idtbl_user`,
                      `tbl_invoice_payment_idtbl_invoice_payment`,`tbl_invoice_payment_detail_idtbl_invoice_payment_detail`)
                     VALUES ('$returnDate','$reason','$amount',1,'$now','$userID','$paymentID','$detailID')");

// 4) Cancel the receipt: status = 3
$ok4 = $conn->query("UPDATE `tbl_invoice_payment`
                     SET `status`=3, `updatedatetime`='$now'
                     WHERE `idtbl_invoice_payment`='$paymentID'");

// 5) Cancel all payment detail rows of that receipt: status = 3
$ok5 = $conn->query("UPDATE `tbl_invoice_payment_detail`
                     SET `status`=3, `updatedatetime`='$now'
                     WHERE `tbl_invoice_payment_idtbl_invoice_payment`='$paymentID'");

// 6) Remove the receipt <-> invoice link so the invoice balance is no longer reduced
$ok6 = $conn->query("DELETE FROM `tbl_invoice_payment_has_tbl_invoice`
                     WHERE `tbl_invoice_payment_idtbl_invoice_payment`='$paymentID'");

if ($ok1 && $ok2 && $ok3 && $ok4 && $ok5 && $ok6) {
    $conn->commit();
    echo json_encode(['status'=>1,'msg'=>'Cheque returned. Invoice is unlocked for payment again.']);
} else {
    $conn->rollback();
    echo json_encode(['status'=>0,'msg'=>'Failed to return the cheque.']);
}