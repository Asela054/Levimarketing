<?php
require_once('../connection/db.php');

$record=$_POST['recordID'];

$sql="SELECT * FROM `tbl_product_free` WHERE `idtbl_product_free`='$record'";
$result=$conn->query($sql);
$row=$result->fetch_assoc();

$obj=new stdClass();
$obj->id=$row['idtbl_product_free'];
$obj->qtycount=$row['qtycount'];
$obj->freecount=$row['freecount'];
$obj->issueproductid=$row['issueproductid'];
$obj->freeproductid=$row['freeproductid'];

echo json_encode($obj);
?>