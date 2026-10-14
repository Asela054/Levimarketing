<?php
// same includes/session lines as your other process files
header('Content-Type: application/json');

$detailID = (int)$_POST['chequeid'];
$reason   = $conn->real_escape_string(trim($_POST['reason']));
$userID   = $_SESSION['userid'];
$today    = date('Y-m-d');
$now      = date('Y-m-d H:i:s');

$res = $conn->query("SELECT `method`,`amount`,`chequedate`,`chequereturnstatus`,`tbl_invoice_payment_idtbl_invoice_payment` AS paymentid
                     FROM `tbl_invoice_payment_detail`
                     WHERE `idtbl_invoice_payment_detail`='$detailID' AND `status`=1");
$row = $res->fetch_assoc();

if (!$row) { echo json_encode(['status'=>0,'msg'=>'Payment record not found.']); exit; }
if ($row['method'] != 3) { echo json_encode(['status'=>0,'msg'=>'Only cheque payments can be returned.']); exit; }
if ($row['chequereturnstatus'] == 1) { echo json_encode(['status'=>0,'msg'=>'This cheque is already returned.']); exit; }
if ($today <= $row['chequedate']) {
    echo json_encode(['status'=>0,'msg'=>'Cheque can only be returned after the cheque date ('.$row['chequedate'].').']); exit;
}

$paymentID = (int)$row['paymentid'];
$amount    = (float)$row['amount'];

$conn->begin_transaction();

// 1) Mark the cheque as returned on the payment detail row
$ok1 = $conn->query("UPDATE `tbl_invoice_payment_detail`
                     SET `chequereturnstatus`=1, `chequereturndate`='$today', `chequereturnreason`='$reason',
                         `chequereturnuser`='$userID', `updatedatetime`='$now'
                     WHERE `idtbl_invoice_payment_detail`='$detailID'");

// 2) Unlock the related invoice(s) for payment again
$ok2 = $conn->query("UPDATE `tbl_invoice` i
                     INNER JOIN `tbl_invoice_payment_has_tbl_invoice` p ON p.`tbl_invoice_idtbl_invoice`=i.`idtbl_invoice`
                     SET i.`paymentcomplete`=0
                     WHERE p.`tbl_invoice_payment_idtbl_invoice_payment`='$paymentID'");

// 3) Write the return history record
$ok3 = $conn->query("INSERT INTO `tbl_invoice_cheque_return`
                     (`returndate`,`reason`,`amount`,`status`,`updatedatetime`,`tbl_user_idtbl_user`,
                      `tbl_invoice_payment_idtbl_invoice_payment`,`tbl_invoice_payment_detail_idtbl_invoice_payment_detail`)
                     VALUES ('$today','$reason','$amount',1,'$now','$userID','$paymentID','$detailID')");

if ($ok1 && $ok2 && $ok3) {
    $conn->commit();
    echo json_encode(['status'=>1,'msg'=>'Cheque returned. Invoice is unlocked for payment again.']);
} else {
    $conn->rollback();
    echo json_encode(['status'=>0,'msg'=>'Failed to return the cheque.']);
}