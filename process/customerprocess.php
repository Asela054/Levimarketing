<?php
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');//die('bc');
$userID=$_SESSION['userid'];

$recordOption=$_POST['recordOption'];
if(!empty($_POST['recordID'])){$recordID=$_POST['recordID'];}
$cusName=$_POST['cusName'];
$cusType=$_POST['cusType'];
$cusMobile=$_POST['cusMobile'];
$cusNic=$_POST['cusNic'];
$address=$_POST['address'];
$cusVatNum=$_POST['cusVatNum'];
$cusSVat=$_POST['cusSVat'];
$cusEmail=$_POST['cusEmail'];
$area=$_POST['area'];

$cusCreditlimit=$_POST['cusCreditlimit'];
$cuscredittype=$_POST['cuscredittype'];
$cuscreditdays=$_POST['cuscreditdays'];

$updatedatetime=date('Y-m-d h:i:s');

if($recordOption==1){
    $query = "INSERT INTO `tbl_customer`(`type`, `name`, `nic`, `phone`, `email`, `address`, `vat_num`, `s_vat`, `creditlimit`, `credittype`, `creditperiod`, `emergencydate`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_area_idtbl_area`) VALUES ('$cusType', '$cusName', '$cusNic', '$cusMobile', '$cusEmail', '$address', '$cusVatNum', '$cusSVat', '$cusCreditlimit','$cuscredittype','$cuscreditdays','','1','$updatedatetime', '$userID', '$area')";
    if($conn->query($query)==true){
        $customerID=$conn->insert_id;
        header("Location:../customer.php?action=4");
    }
    else{
        header("Location:../customer.php?action=5");
    }
}
else{
    $update="UPDATE `tbl_customer` SET `type`='$cusType',`name`='$cusName',`nic`='$cusNic',`phone`='$cusMobile',`email`='$cusEmail',`address`='$address',`vat_num`='$cusVatNum',`s_vat`='$cusSVat',`creditlimit`='$cusCreditlimit',`credittype`='$cuscredittype',`creditperiod`='$cuscreditdays',`updatedatetime`='$updatedatetime',`tbl_user_idtbl_user`='$userID',`tbl_area_idtbl_area`='$area' WHERE `idtbl_customer`='$recordID'";
    if($conn->query($update)==true){
        header("Location:../customer.php?action=6");
    }
    else{
        header("Location:../customer.php?action=5");
    }
}

?>