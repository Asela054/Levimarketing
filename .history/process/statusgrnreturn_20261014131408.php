<?php 
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');

$userID=$_SESSION['userid'];
$locationID=intval($_SESSION['location_id']);
$updatedatetime=date('Y-m-d H:i:s');

$recordID=intval($_GET['record']);
$type=intval($_GET['type']);

// runs a query and throws if it fails so the transaction can roll back
function run($conn, $sql){
    $result=$conn->query($sql);
    if($result===false){ throw new Exception('Database error: '.$conn->error); }
    return $result;
}

$conn->begin_transaction();

try{
    // lock the return; it must still be pending and belong to this location
    $resultreturn=run($conn, "SELECT * FROM `tbl_grn_return` WHERE `idtbl_grn_return`='$recordID' AND `status`=1 AND `confirm_status`=0 AND `tbl_location_idtbl_location`='$locationID' FOR UPDATE");
    if($resultreturn->num_rows==0){ throw new Exception('Return not found, or it is already approved'); }

    if($type==1){
        // APPROVE: deduct stock of the GRN location, product by product
        $resultlines=run($conn, "SELECT `tbl_product_idtbl_product` AS `productid`, SUM(`qty`) AS `qty` 
            FROM `tbl_grn_return_detail` 
            WHERE `tbl_grn_return_idtbl_grn_return`='$recordID' AND `status`=1 
            GROUP BY `tbl_product_idtbl_product`");

        while($line=$resultlines->fetch_assoc()){
            $productID=intval($line['productid']);
            $needqty=floatval($line['qty']);

            $resultstock=run($conn, "SELECT `idtbl_stock`, `qty` FROM `tbl_stock` WHERE `tbl_product_idtbl_product`='$productID' AND `tbl_location_idtbl_location`='$locationID' FOR UPDATE");
            $stock=$resultstock->fetch_assoc();
            $available=$stock ? floatval($stock['qty']) : 0;

            if($available+0.0001<$needqty){
                $resultname=run($conn, "SELECT `product_name` FROM `tbl_product` WHERE `idtbl_product`='$productID'");
                $name=$resultname->fetch_assoc();
                throw new Exception('Not enough stock for '.$name['product_name'].' (available '.$available.', return '.$needqty.'). Return not approved.');
            }

            run($conn, "UPDATE `tbl_stock` SET `qty`=`qty`-$needqty, `updatedatetime`='$updatedatetime', `tbl_user_idtbl_user`='$userID' WHERE `idtbl_stock`='".$stock['idtbl_stock']."'");
        }

        run($conn, "UPDATE `tbl_grn_return` SET `confirm_status`=1, `approvedatetime`='$updatedatetime', `approveuser`='$userID', `updatedatetime`='$updatedatetime' WHERE `idtbl_grn_return`='$recordID'");

        $conn->commit();
        header("Location:../grnreturn.php?action=6");
    }
    else if($type==3){
        // REMOVE: only pending returns, nothing was deducted yet
        run($conn, "UPDATE `tbl_grn_return` SET `status`=3, `updatedatetime`='$updatedatetime' WHERE `idtbl_grn_return`='$recordID'");
        run($conn, "UPDATE `tbl_grn_return_detail` SET `status`=3, `updatedatetime`='$updatedatetime' WHERE `tbl_grn_return_idtbl_grn_return`='$recordID'");

        $conn->commit();
        header("Location:../grnreturn.php?action=6");
    }
    else{
        throw new Exception('Invalid request');
    }
}
catch(Exception $e){
    $conn->rollback();
    $_SESSION['grnreturn_msg']=$e->getMessage();
    header("Location:../grnreturn.php?action=5");
}
?>