<?php 
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');
$userID=(int)$_SESSION['userid'];

$tblData=isset($_POST['tblData']) ? $_POST['tblData'] : array();
$tblPayData=isset($_POST['tblPayData']) ? $_POST['tblPayData'] : array();
$totAmount=(float)$_POST['totAmount'];
$payAmount=(float)$_POST['payAmount'];
$balAmount=(float)$_POST['balAmount'];

$today=date('Y-m-d');
$updatedatetime=date('Y-m-d H:i:s');

function sendResult($paymentID, $icon, $message, $type){
    $actionObj=new stdClass();
    $actionObj->icon=$icon;
    $actionObj->title='';
    $actionObj->message=$message;
    $actionObj->url='';
    $actionObj->target='_blank';
    $actionObj->type=$type;

    $obj=new stdClass();
    $obj->paymentinvoice=$paymentID;
    $obj->action=json_encode($actionObj);

    echo json_encode($obj);
    exit;
}

$conn->begin_transaction();
$allOk = true;

$insertpayment="INSERT INTO `tbl_invoice_payment`(`date`, `payment`, `balance`, `status`, `updatedatetime`, `tbl_user_idtbl_user`) VALUES ('$today','$payAmount','$balAmount','1','$updatedatetime','$userID')";

if($conn->query($insertpayment)!==true){
    $conn->rollback();
    error_log('Payment insert failed: '.$conn->error);
    sendResult('0', 'fas fa-exclamation-triangle', 'Record Error', 'danger');
}

$paymentID=$conn->insert_id;

// ---- Invoice allocation ------------------------------------------------
foreach($tblData as $rowtabledata){

    $invoiceID    = (int)$rowtabledata['col_1'];
    $invamount    = (float)$rowtabledata['col_5'];
    $invpayamount = (float)$rowtabledata['col_10'];

    if($invpayamount > 0){

        $previousPaid = 0;

        // Returned cheque receipts have their has-table rows deleted, so they are not counted here
        $previousPaymentQuery = "
            SELECT COALESCE(SUM(payamount), 0) AS previous_paid
            FROM tbl_invoice_payment_has_tbl_invoice
            WHERE tbl_invoice_idtbl_invoice = '$invoiceID'
        ";

        $previousPaymentResult = $conn->query($previousPaymentQuery);

        if($previousPaymentResult && $previousPaymentResult->num_rows > 0){
            $previousPaymentRow = $previousPaymentResult->fetch_assoc();
            $previousPaid = (float)$previousPaymentRow['previous_paid'];
        }

        $totalPaid = $previousPaid + $invpayamount;

        if($totalPaid >= $invamount){
            $paymentcompletestatus = 1;
            $fullstatus = 1;
            $halfstatus = 0;
        }else{
            $paymentcompletestatus = 0;
            $fullstatus = 0;
            $halfstatus = 1;
        }

        $updateinvoice = "
            UPDATE tbl_invoice
            SET
                paymentcomplete = '$paymentcompletestatus',
                updatedatetime = '$updatedatetime',
                tbl_user_idtbl_user = '$userID'
            WHERE idtbl_invoice = '$invoiceID'
        ";

        if(!$conn->query($updateinvoice)){
            error_log('Invoice update failed: '.$conn->error);
            $allOk = false;
        }

        $updateinvoicehas = "
            INSERT INTO tbl_invoice_payment_has_tbl_invoice
            (
                tbl_invoice_payment_idtbl_invoice_payment,
                tbl_invoice_idtbl_invoice,
                total,
                discount,
                payamount,
                fullstatus,
                halfstatus
            )
            VALUES
            (
                '$paymentID',
                '$invoiceID',
                '$invpayamount',
                '0',
                '$invpayamount',
                '$fullstatus',
                '$halfstatus'
            )
        ";

        if(!$conn->query($updateinvoicehas)){
            error_log('Payment has-invoice insert failed: '.$conn->error);
            $allOk = false;
        }
    }
}

// ---- Payment method details --------------------------------------------
foreach($tblPayData as $rowtablepaydata){
    // Method id saved in tbl_invoice_payment_detail.method: 1 Cash, 2 Card, 3 Cheque, 4 Online Transfer
    $typeID=(int)$rowtablepaydata['col_9'];

    $cashamount=(float)$rowtablepaydata['col_2'];
    $bankamount=(float)$rowtablepaydata['col_3'];
    $paidamount=($typeID==1) ? $cashamount : $bankamount;

    $chequeno=$conn->real_escape_string(trim($rowtablepaydata['col_4']));
    $receiptno=$conn->real_escape_string(trim($rowtablepaydata['col_5']));
    $bankname=$conn->real_escape_string(trim($rowtablepaydata['col_7']));

    // Cash / card / online rows send '-' for the date; the column is NOT NULL, so store a valid date
    $chequedateRaw=trim($rowtablepaydata['col_6']);
    $dt=DateTime::createFromFormat('Y-m-d', $chequedateRaw);
    $chequedate=($dt && $dt->format('Y-m-d')===$chequedateRaw) ? $chequedateRaw : $today;

    // Card (2) and Online Transfer (4) send their reference in the Ref / Che No column
    $cardlast4=($typeID==2 || $typeID==4) ? substr($chequeno, 0, 4) : '';
    $cardlast4=$conn->real_escape_string($cardlast4);

    $insertpaydetail="INSERT INTO `tbl_invoice_payment_detail`(`method`, `amount`, `bank`, `receiptno`, `chequeno`, `chequedate`, `chequereturnstatus`, `cardlast4`, `addaccountstatus`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_invoice_payment_idtbl_invoice_payment`) VALUES ('$typeID','$paidamount','$bankname','$receiptno','$chequeno','$chequedate','0','$cardlast4','1','1','$updatedatetime','$userID','$paymentID')";

    if(!$conn->query($insertpaydetail)){
        error_log('Payment detail insert failed: '.$conn->error);
        $allOk = false;
    }
}

// ---- Commit or roll back everything together ---------------------------
if($allOk){
    $conn->commit();
    sendResult($paymentID, 'fas fa-check-circle', 'Add Successfully', 'success');
}else{
    $conn->rollback();
    sendResult('0', 'fas fa-exclamation-triangle', 'Record Error', 'danger');
}
?>