<?php 
require_once('../connection/db.php');

$record=$_POST['recordID'];

$sql="SELECT * FROM `tbl_location` WHERE `idtbl_location`='$record'";
$result=$conn->query($sql);
$row=$result->fetch_assoc();

$obj=new stdClass();
$obj->id=$row['idtbl_location'];
$obj->location=$row['location'];
$obj->location_code =$row['code'];
$obj->location_name=$row['companyname'];
$obj->location_address=$row['address'];
$obj->location_contact1=$row['contact1'];
$obj->location_contact2=$row['contact2'];
$obj->location_contact3=$row['contact3'];
$obj->location_email=$row['email'];

echo json_encode($obj);
?>