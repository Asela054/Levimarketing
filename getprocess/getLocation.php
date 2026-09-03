<?php 
require_once('../connection/db.php');

$record=$_POST['recordID'];

$sql="SELECT * FROM `tbl_location` WHERE `idtbl_location`='$record'";
$result=$conn->query($sql);
$row=$result->fetch_assoc();

$obj=new stdClass();
$obj->id=$row['idtbl_location'];
$obj->location=$row['location'];
$obj->location_code=$row['code'];

echo json_encode($obj);
?>