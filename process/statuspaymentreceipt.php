<?php 
session_start();
if(!isset($_SESSION['userid'])){header ("Location:../index.php");exit;}
require_once('../connection/db.php');

$userID=$_SESSION['userid'];
$updatedatetime=date('Y-m-d h:i:s');

$record=$_GET['record'];
$type=$_GET['type'];

if($type==1){$value=1;}
else if($type==2){$value=2;}
else if($type==3){$value=3;}
else{$value=3;}

$sql="UPDATE `tbl_invoice_payment` SET `status`='$value', `updatedatetime`='$updatedatetime', `tbl_user_idtbl_user`='$userID' WHERE `idtbl_invoice_payment`='$record'";
if($conn->query($sql)==true){

    $updateinvoice="UPDATE `tbl_invoice` INNER JOIN `tbl_invoice_payment_has_tbl_invoice` ON `tbl_invoice`.`idtbl_invoice` = `tbl_invoice_payment_has_tbl_invoice`.`tbl_invoice_idtbl_invoice` SET `tbl_invoice`.`paymentcomplete`='0' WHERE `tbl_invoice_payment_has_tbl_invoice`.`tbl_invoice_payment_idtbl_invoice_payment`='$record'";
    $conn->query($updateinvoice);

    header("Location:../paymentreceipt.php?action=$type");
}
else{header("Location:../paymentreceipt.php?action=5");}
?>