<?php
session_start();
if(!isset($_SESSION['userid'])){header ("Location:../index.php"); exit;}
require_once('../connection/db.php');

$userID=$_SESSION['userid'];

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

$supplierID=isset($_POST['paysupplier']) ? intval($_POST['paysupplier']) : 0;
$paydate=isset($_POST['paydate']) ? $conn->real_escape_string($_POST['paydate']) : '';
$paymethod=isset($_POST['paymethod']) ? intval($_POST['paymethod']) : 0; // 1 = Cash, 2 = Cheque, 3 = Bank Transfer
$chequeno=isset($_POST['chequeno']) ? $conn->real_escape_string(trim($_POST['chequeno'])) : '';
$chequedate=isset($_POST['chequedate']) ? trim($_POST['chequedate']) : '';
$bankref=isset($_POST['bankref']) ? $conn->real_escape_string(trim($_POST['bankref'])) : '';
$remark=isset($_POST['payremark']) ? $conn->real_escape_string(trim($_POST['payremark'])) : '';
$tableData=isset($_POST['tableData']) ? $_POST['tableData'] : array();

$now=date('Y-m-d H:i:s');

if(!$supplierID){ payresponse('fas fa-exclamation-triangle','Supplier is required','danger'); }
if($paydate===''){ payresponse('fas fa-exclamation-triangle','Payment date is required','danger'); }
if(!in_array($paymethod, array(1,2,3), true)){ payresponse('fas fa-exclamation-triangle','Payment method is required','danger'); }
if(!is_array($tableData) || count($tableData)==0){ payresponse('fas fa-exclamation-triangle','Select at least one GRN','danger'); }
if($paymethod==2 && ($chequeno==='' || $chequedate==='')){ payresponse('fas fa-exclamation-triangle','Cheque number and cheque date are required','danger'); }
if($paymethod==3 && $bankref===''){ payresponse('fas fa-exclamation-triangle','Bank reference is required','danger'); }

if($paymethod!=2){ $chequeno=''; }
if($paymethod!=3){ $bankref=''; }
$chequedateSql=($paymethod==2) ? "'".$conn->real_escape_string($chequedate)."'" : "NULL";

$conn->begin_transaction();
try{
    $lines=array();
    $seen=array();
    $total=0;

    // Lock and validate every GRN before writing anything
    foreach($tableData as $rowtabledata){
        $grnid=intval($rowtabledata['grnid']);
        $amount=round(floatval($rowtabledata['amount']),2);

        if(isset($seen[$grnid])){ throw new Exception('GRN-'.$grnid.' is selected twice'); }
        $seen[$grnid]=1;

        $sqlgrn="SELECT `total`, `paidamount` FROM `tbl_grn`
                 WHERE `idtbl_grn`='$grnid' AND `tbl_supplier_idtbl_supplier`='$supplierID' AND `status`=1 AND `confirm_status`=1 FOR UPDATE";
        $resultgrn=$conn->query($sqlgrn);
        if(!$resultgrn || $resultgrn->num_rows==0){
            throw new Exception('GRN-'.$grnid.' was not found for this supplier or is not approved');
        }
        $grn=$resultgrn->fetch_assoc();

        $grntotal=round($grn['total'],2);
        $balance=round($grntotal-$grn['paidamount'],2);
        if($amount<=0 || $amount>$balance){
            throw new Exception('Invalid amount for GRN-'.$grnid.' (balance '.number_format($balance,2).')');
        }

        $newpaid=round($grn['paidamount']+$amount,2);
        $lines[]=array(
            'grnid'=>$grnid,
            'amount'=>$amount,
            'newpaid'=>$newpaid,
            'complete'=>($newpaid>=$grntotal) ? 1 : 0   // 1 = fully paid, 0 = partial
        );
        $total+=$amount;
    }
    $total=round($total,2);

    $insertpayment="INSERT INTO `tbl_supplier_payment`(`paymentno`, `date`, `paymethod`, `chequeno`, `chequedate`, `bankref`, `totalpayment`, `remark`, `status`, `insertdatetime`, `updatedatetime`, `updateuser`, `tbl_user_idtbl_user`, `tbl_supplier_idtbl_supplier`)
        VALUES ('','$paydate','$paymethod','$chequeno',$chequedateSql,'$bankref','$total','$remark','1','$now','$now','$userID','$userID','$supplierID')";
    if(!$conn->query($insertpayment)){ throw new Exception('Payment record error'); }
    $paymentID=$conn->insert_id;

    $conn->query("UPDATE `tbl_supplier_payment` SET `paymentno`='SP-$paymentID' WHERE `idtbl_supplier_payment`='$paymentID'");

    foreach($lines as $line){
        $grnid=$line['grnid'];
        $amount=$line['amount'];
        $newpaid=$line['newpaid'];
        $complete=$line['complete'];

        $insertdetail="INSERT INTO `tbl_supplier_payment_detail`(`amount`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_supplier_payment_idtbl_supplier_payment`, `tbl_grn_idtbl_grn`)
            VALUES ('$amount','1','$now','$userID','$paymentID','$grnid')";
        if(!$conn->query($insertdetail)){ throw new Exception('Payment detail error'); }

        $updategrn="UPDATE `tbl_grn` SET `paidamount`='$newpaid', `paymentcomplete`='$complete' WHERE `idtbl_grn`='$grnid'";
        if(!$conn->query($updategrn)){ throw new Exception('GRN update error'); }
    }

    $conn->commit();
    payresponse('fas fa-check-circle','Supplier Payment SP-'.$paymentID.' Saved Successfully','success');
}
catch(Exception $e){
    $conn->rollback();
    payresponse('fas fa-exclamation-triangle',$e->getMessage(),'danger');
}