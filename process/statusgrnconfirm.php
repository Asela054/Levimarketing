<?php 
session_start();
if(!isset($_SESSION['userid'])){header ("Location:../index.php"); exit;}
require_once('../connection/db.php');

$userID=$_SESSION['userid'];
$updatedatetime=date('Y-m-d h:i:s');
$today=date('Y-m-d');

$record=intval($_GET['record']);

$sqlGrn = "SELECT `tbl_location_idtbl_location`, `porder_id`
           FROM `tbl_grn`
           WHERE `idtbl_grn` = '$record'";
$resultGrn = $conn->query($sqlGrn);
$rowGrn = $resultGrn->fetch_assoc();

if (!$rowGrn) {
    header("Location:../grn.php?action=5");
    exit;
}

$locationID = $rowGrn['tbl_location_idtbl_location'];
$linkedPorderID = intval($rowGrn['porder_id']);

$sqldetail="SELECT * FROM `tbl_grndetail` WHERE `tbl_grn_idtbl_grn` = '$record' AND `status`=1";
$resultdetail =$conn-> query($sqldetail); 

$sqlupdategrn="UPDATE `tbl_grn` SET `confirm_status`='1',`updatedatetime`='$updatedatetime' WHERE `idtbl_grn`='$record'";

if($conn->query($sqlupdategrn)==true){

    if($resultdetail->num_rows > 0) {
        while ($row = $resultdetail->fetch_assoc()){
            $qty = $row['qty'];
            $productID = $row['tbl_product_idtbl_product'];

            // Check existing stock row for this product + location
            $sqlstockcheck = "SELECT `idtbl_stock`, `qty`, `update`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_product_idtbl_product`, `tbl_location_idtbl_location`
                FROM `tbl_stock`
                WHERE `tbl_product_idtbl_product` = '$productID'
                AND `tbl_location_idtbl_location` = '$locationID' AND `status` = 1";
            $resultstockcheck = $conn->query($sqlstockcheck);

            if($resultstockcheck->num_rows > 0){
                $updatestock = "UPDATE `tbl_stock`
                    SET `qty` = (`qty` + '$qty'), `updatedatetime` = '$updatedatetime'
                    WHERE `tbl_product_idtbl_product` = '$productID'
                    AND `tbl_location_idtbl_location` = '$locationID'";
                $conn->query($updatestock);
            }
            else{
                $insertstock = "INSERT INTO `tbl_stock`
                    (`qty`, `update`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_product_idtbl_product`, `tbl_location_idtbl_location`)
                    VALUES
                    ('$qty', '$today', '1', '$updatedatetime', '$userID', '$productID', '$locationID')";
                $conn->query($insertstock);
            }
        }
    }

    // Mark the originating Purchase Order as GRN-issued, only if this GRN was created with a PO
    if ($linkedPorderID > 0) {
        $sqlupdateporder = "UPDATE `tbl_porder` SET `grnissuestatus`='1' WHERE `idtbl_porder`='$linkedPorderID'";
        $conn->query($sqlupdateporder);
    }

    header("Location:../grn.php?action=6");
}
else{
    header("Location:../grn.php?action=5");
}
?>