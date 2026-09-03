<?php
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');//die('bc');
$userID=$_SESSION['userid'];

$recordOption=$_POST['recordOption'];
if(!empty($_POST['recordID'])){$recordID=$_POST['recordID'];}
$product_name = addslashes($_POST['productName']);
$productcode = $_POST['productcode'];
$unitprice = $_POST['unitprice'];
$saleprice = $_POST['saleprice'];
$wholesaleprice = $_POST['wholesaleprice'];
$category = $_POST['category'];

$supplier = $_POST['supplier'];
$subcategory = $_POST['subcategory'];
$groupcategory = $_POST['groupcategory'];
$rol = $_POST['rol'];
$barcode = $_POST['barcode'];
$retail = $_POST['retail'];
$peices = $_POST['peices'];
$starpoints = $_POST['starpoints'];
$dollarrate = $_POST['dollarrate'];

$salediscount = $_POST['salediscount'];
$retaildiscount = $_POST['retaildiscount'];
$priceradio = $_POST['priceradio'];
$maxdiscount = $_POST['maxdiscount'];

if(!empty($_POST['additionaldiscount'])){
    $additionaldiscount=$_POST['additionaldiscount'];
}else{
    $additionaldiscount=0;
}

$updatedatetime=date('Y-m-d h:i:s');
$today=date('Y-m-d');

if($recordOption==1){
    $query = "INSERT INTO `tbl_product`(`product_code`, `product_name`, `size`, `unitprice`, `saleprice`, `wholesaleprice`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_product_category_idtbl_product_category`, `barcode`, `rol`, `pices_per_box`, `retail`, `starpoints`, `tbl_group_category_idtbl_group_category`, `tbl_sub_product_category_idtbl_sub_product_category`, `tbl_supplier_idtbl_supplier`, `retaildiscount`, `salediscount`, `price_acceptable`, `additional_discount`, `dollarrate`, `maxdiscount`) Values ('$productcode','$product_name','','$unitprice','$saleprice','$wholesaleprice','1','$updatedatetime','$userID','$category','$barcode','$rol','$peices','$retail','$starpoints','$groupcategory','$subcategory','$supplier','$retaildiscount','$salediscount', '$priceradio', '$additionaldiscount', '$dollarrate', '$maxdiscount')";
    if($conn->query($query)==true){
        header("Location:../product.php?action=4");
    }
    else{header("Location:../product.php?action=5");}
}
else{
    $query = "UPDATE `tbl_product` SET `product_code`='$productcode',`product_name`='$product_name',`unitprice`='$unitprice',`saleprice`='$saleprice',`wholesaleprice`='$wholesaleprice',`updatedatetime`='$updatedatetime',`tbl_user_idtbl_user`='$userID', `tbl_product_category_idtbl_product_category`='$category',`barcode`='$barcode',`rol`='$rol',`pices_per_box`='$peices',`retail`='$retail',`starpoints`='$starpoints',`tbl_sub_product_category_idtbl_sub_product_category`='$subcategory',`tbl_group_category_idtbl_group_category`='$groupcategory',`tbl_supplier_idtbl_supplier`='$supplier',`retaildiscount`='$retaildiscount',`salediscount`='$salediscount', `price_acceptable` = '$priceradio', `additional_discount` = '$additionaldiscount', `dollarrate` = '$dollarrate', `maxdiscount`='$maxdiscount'  WHERE `idtbl_product`='$recordID'";
    if($conn->query($query)==true){
        header("Location:../product.php?action=6");
    }
    else{header("Location:../product.php?action=5");}
}
?>