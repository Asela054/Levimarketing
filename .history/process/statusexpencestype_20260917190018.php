<?php 
session_start();
if(!isset($_SESSION['userid'])){header ("");}
require_once('../connection/db.php');

$userID=$_SESSION['userid'];
$updatedatetime=date('Y-m-d h:i:s');

$record=$_GET['record'];
$type=$_GET['type'];

if($type==1){$value=1;}
else if($type==2){$value=2;}
else if($type==3){$value=3;}

$sql="UPDATE `tbl_expences_type` SET `status`='$value',`updatedateime`='$updatedatetime' WHERE `idtbl_expences_type`='$record'";
if($conn->query($sql)==true){header("Location:../expencestype.php?action=$type");}
else{header("Location:../expencestype.php?action=5");}
?>