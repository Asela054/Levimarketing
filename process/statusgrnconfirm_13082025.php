<?php 
session_start();
if(!isset($_SESSION['userid'])){header ("");}
require_once('../connection/db.php');

$userID=$_SESSION['userid'];
$updatedatetime=date('Y-m-d h:i:s');

$record=$_GET['record'];
$today=date('Y-m-d');

$sql="SELECT * FROM `tbl_grndetail` WHERE `tbl_grn_idtbl_grn` = '$record'";
$result =$conn-> query($sql); 

$sql="UPDATE `tbl_grn` SET `confirm_status`='1',`updatedatetime`='$updatedatetime' WHERE `idtbl_grn`='$record'";
if($conn->query($sql)==true){
    if($result->num_rows > 0) {while ($row = $result-> fetch_assoc()){
        $qty = $row['qty'];
        $productID = $row['tbl_product_idtbl_product'];
        
        $sqlstockcheck="SELECT `qty` FROM `tbl_stock` WHERE `tbl_product_idtbl_product`='$productID'"; 
        $resultstockcheck=$conn->query($sqlstockcheck);
        $rowstockcheck = $resultstockcheck-> fetch_assoc();

        if($resultstockcheck->num_rows > 0){
            $updatestock="UPDATE `tbl_stock` SET `qty`=(`qty`+'$qty') WHERE `tbl_product_idtbl_product`='$productID'";
            $conn->query($updatestock);
        }
        else{
            $insertstock="INSERT INTO `tbl_stock`(`qty`, `update`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_product_idtbl_product`) VALUES ('$qty','$today','1','$updatedatetime','$userID','$productID')";
            $conn->query($insertstock);
        }
    }}
    header("Location:../grn.php?action=6");
}
else{header("Location:../grn.php?action=5");}
?>