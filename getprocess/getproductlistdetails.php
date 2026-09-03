<?php 

require_once('../connection/db.php');

$record=$_POST['recordID'];
$type = $_POST['saletypes'];
$loadID = $_POST['loadID'];

$sql="SELECT `tbl_product`.*, `tbl_vehicle_load_detail`.`qty` FROM `tbl_product` LEFT JOIN `tbl_vehicle_load_detail` ON `tbl_vehicle_load_detail`.`tbl_product_idtbl_product`=`tbl_product`.`idtbl_product` WHERE `tbl_product`.`idtbl_product`='$record' AND `tbl_vehicle_load_detail`.`tbl_vehicle_load_idtbl_vehicle_load`='$loadID'";
$result=$conn->query($sql);
$row=$result->fetch_assoc();

$obj=new stdClass();
$obj->id=$row['idtbl_product'];
$obj->product=$row['product_name'];
$obj->procode=$row['product_code'];
$obj->unitprice=$row['unitprice'];
if($row['qty']>0){
     $obj->availableqty=$row['qty'];
}
else{
     $obj->availableqty=0;
}
if($type == 1){
     $obj->saleprice=$row['saleprice'];
}elseif($type == 2){
     $obj->saleprice=$row['wholesaleprice'];
}else{
     $obj->saleprice= 0;
}
echo json_encode($obj); 
?>