<?php
require_once('../connection/db.php');

$productID=$_POST['productID'];

$sqlproduct="SELECT `saleprice`,`unitprice` FROM `tbl_product` WHERE `idtbl_product`='$productID'";
$resultproduct=$conn->query($sqlproduct);
$rowproduct=$resultproduct->fetch_assoc();

if($resultproduct-> num_rows > 0) {
    $obj=new stdClass();
    $obj->saleprice=$rowproduct['saleprice'];
    $obj->unitprice=$rowproduct['unitprice'];
}
else{
    $obj=new stdClass();
    $obj->saleprice='0';
    $obj->unitprice='0';
}
echo json_encode($obj);
?>