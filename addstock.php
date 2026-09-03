<?php
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');//die('bc');
$userID=$_SESSION['userid'];

$updatedatetime=date('Y-m-d h:i:s');
$today=date('Y-m-d');

$sqlproduct = "SELECT `idtbl_product` FROM `tbl_product`";
$result=$conn->query($sqlproduct);

while($row=$result->fetch_assoc()) {
    $productID = $row['idtbl_product'];

    $insertstock="INSERT INTO `tbl_stock`(`qty`, `update`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_product_idtbl_product`) VALUES ('0','$today','1','$updatedatetime','$userID','$productID')";
    $conn->query($insertstock);
    $c++;

}

print_r($c);

?>