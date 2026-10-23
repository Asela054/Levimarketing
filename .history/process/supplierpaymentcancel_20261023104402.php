<?php
session_start();
if(!isset($_SESSION['userid'])){header ("Location:../index.php"); exit;}
require_once('../connection/db.php');

$userID=$_SESSION['userid'];
$paymentID=isset($_POST['paymentid']) ? intval($_POST['paymentid']) : 0;
$now=date('Y-m-d H:i:s');

function payresponse($icon, $message, $type){
    $actionObj=new stdClass();
    $actionObj->icon=$icon;
    $actionObj->title='';
    $actionObj->message=$message;
    $actionObj->url='';
    $actionObj->target='_blank';
    $actionObj->type=$type;
    echo json_encode($actionObj);
    exit;
}

if(!$paymentID){ payresponse('fas fa-exclamation-triangle','Invalid payment','danger'); }

$conn->begin_transaction();
try{
    // Lock the payment row so it cannot be cancelled twice
    $resulthead=$conn->query("SELECT `status` FROM `tbl_supplier_payment` WHERE `idtbl_supplier_payment`='$paymentID' FOR UPDATE");
    if(!$resulthead || $resulthead->num_rows==0){ throw new Exception('Payment not found'); }
    $head=$resulthead->fetch_assoc();
    if($head['status']!=1){ throw new Exception('Payment is already cancelled'); }

    $resultdetail=$conn->query("SELECT `tbl_grn_idtbl_grn` AS grnid, `amount` FROM `tbl_supplier_payment_detail`
                                WHERE `tbl_supplier_payment_idtbl_supplier_payment`='$paymentID' AND `status`=1");
    while($row=$resultdetail->fetch_assoc()){
        $grnid=intval($row['grnid']);
        $amount=floatval($row['amount']);
        // Take this payment's amount back off the GRN; it has a balance again, so it is not complete
        $updategrn="UPDATE `tbl_grn` SET `paidamount`=GREATEST(`paidamount`-$amount,0), `paymentcomplete`=0 WHERE `idtbl_grn`='$grnid'";
        if(!$conn->query($updategrn)){ throw new Exception('GRN update error'); }
    }

    if(!$conn->query("UPDATE `tbl_supplier_payment_detail` SET `status`=3, `updatedatetime`='$now' WHERE `tbl_supplier_payment_idtbl_supplier_payment`='$paymentID'")){
        throw new Exception('Payment detail update error');
    }
    if(!$conn->query("UPDATE `tbl_supplier_payment` SET `status`=3, `updatedatetime`='$now', `updateuser`='$userID' WHERE `idtbl_supplier_payment`='$paymentID'")){
        throw new Exception('Payment update error');
    }

    $conn->commit();
    payresponse('fas fa-check-circle','Payment SP-'.$paymentID.' cancelled and GRN balances restored','success');
}
catch(Exception $e){
    $conn->rollback();
    payresponse('fas fa-exclamation-triangle',$e->getMessage(),'danger');
}