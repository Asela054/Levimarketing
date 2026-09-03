<?php 
session_start();
if(!isset($_SESSION['userid'])){header ("");}
require_once('../connection/db.php');

$userID=$_SESSION['userid'];
$updatedatetime=date('Y-m-d h:i:s');

$record=$_GET['recordID'];
$type=$_GET['type'];


if($type==1){$value=1;}
else if($type==2){$value=0;}

$sql="UPDATE tbl_vehicle_load  SET `veiwallcustomerstatus`= '$value',`updatedatetime`='$updatedatetime' WHERE `idtbl_vehicle_load`='$record'";
if($conn->query($sql)==true){header("Location:../vehicalload.php?action=$type");
}
else{header("Location:../vehicalload.php?action=5");}
?>