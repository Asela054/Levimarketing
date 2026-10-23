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
    // Lock the payment so it cannot be approved twice
    $resulthead=$conn->query("SELECT `status`, `confirm_status` FROM `tbl_supplier_payment` WHERE `idtbl_supplier_payment`='$paymentID' FOR UPDATE");
    if(!$resulthead || $resulthead->num_rows==0){ throw new Exception('Payment not found'); }
    $head=$resulthead->fetch_assoc();
    if($head['status']!=1){ throw new Exception('Payment is cancelled'); }
    if($head['confirm_status']==1){ throw new Exception('Payment is already approved'); }

    $resultdetail=$conn->query("SELECT `tbl_grn_idtbl_grn` AS grnid, `amount` FROM `tbl_supplier_payment_detail`
                                WHERE `tbl_supplier_payment_idtbl_supplier_payment`='$paymentID' AND `status`=1");
    if(!$resultdetail || $resultdetail->num_rows==0){ throw new Exception('Payment has no GRN lines'); }

    while($row=$resultdetail->fetch_assoc()){
        $grnid=intval($row['grnid']);
        $amount=round(floatval($row['amount']),2);

        $resultgrn=$conn->query("SELECT `total`, `paidamount` FROM `tbl_grn` WHERE `idtbl_grn`='$grnid' FOR UPDATE");
        if(!$resultgrn || $resultgrn->num_rows==0){ throw new Exception('GRN-'.$grnid.' not found'); }
        $grn=$resultgrn->fetch_assoc();

        $grntotal=round($grn['total'],2);
        $balance=round($grntotal-$grn['paidamount'],2);
        if($amount>$balance){
            throw new Exception('GRN-'.$grnid.' balance is now '.number_format($balance,2).', less than this payment ('.number_format($amount,2).')');
        }

        $newpaid=round($grn['paidamount']+$amount,2);
        $complete=($newpaid>=$grntotal) ? 1 : 0;   // 1 = fully paid, 0 = partial
        if(!$conn->query("UPDATE `tbl_grn` SET `paidamount`='$newpaid', `paymentcomplete`='$complete' WHERE `idtbl_grn`='$grnid'")){
            throw new Exception('GRN update error');
        }
    }

    $updatehead="UPDATE `tbl_supplier_payment` SET `confirm_status`=1, `approveuser`='$userID', `approvedatetime`='$now', `updatedatetime`='$now', `updateuser`='$userID' WHERE `idtbl_supplier_payment`='$paymentID'";
    if(!$conn->query($updatehead)){ throw new Exception('Payment update error'); }

    $conn->commit();
    payresponse('fas fa-check-circle','Payment SP-'.$paymentID.' approved','success');
}
catch(Exception $e){
    $conn->rollback();
    payresponse('fas fa-exclamation-triangle',$e->getMessage(),'danger');
}