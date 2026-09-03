<?php
require_once('../connection/db.php');

$record=$_POST['recordID'];

$sql="SELECT * FROM `tbl_customer` WHERE `idtbl_customer`='$record'";
$result=$conn->query($sql);
$row=$result->fetch_assoc();

$obj=new stdClass();
$obj->id=$row['idtbl_customer'];
$obj->name=$row['name'];
$obj->type=$row['type'];
$obj->nic=$row['nic'];
$obj->phone=$row['phone'];
$obj->address=$row['address'];
$obj->vat_num=$row['vat_num'];
$obj->svat=$row['s_vat'];
$obj->email=$row['email'];
$obj->credit=$row['creditlimit'];
$obj->credittype=$row['credittype'];
$obj->creditperiod=$row['creditperiod'];
$obj->area=$row['tbl_area_idtbl_area'];

echo json_encode($obj);
?>