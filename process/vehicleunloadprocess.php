
<?php 
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');

$userID=$_SESSION['userid'];
$updatedatetime=date('Y-m-d h:i:s');

$recordID=$_POST['recordID'];

// unloading quary
$vehicleunload ="UPDATE tbl_vehicle_load SET  unloadstatus = '1', `updatedatetime` ='$updatedatetime' WHERE idtbl_vehicle_load  = '$recordID'";

$sqlgetqty="SELECT `qty`,`tbl_product_idtbl_product` FROM `tbl_vehicle_load_detail` WHERE tbl_vehicle_load_idtbl_vehicle_load ='$recordID'";
        $result=$conn->query($sqlgetqty);

        while ($row2 = $result->fetch_assoc()) {
                $qty=$row2['qty'];
                $productid=$row2['tbl_product_idtbl_product'];

                $sqlstockupdate = "UPDATE tbl_stock  SET qty = qty + '$qty'  WHERE tbl_product_idtbl_product ='$productid'";
                $conn->query($sqlstockupdate);
        }        
        
if( $conn->query($vehicleunload)==true){     
        $actionObj = new stdClass();
        $actionObj->icon = 'fas fa-check-circle';
        $actionObj->title = '';
        $actionObj->message = 'Add Successfully';
        $actionObj->url = '';
        $actionObj->target = '_blank';
        $actionObj->type = 'success';

        echo $actionJSON = json_encode($actionObj, JSON_FORCE_OBJECT);    
}
else{
        $actionObj = new stdClass();
        $actionObj->icon = 'fas fa-exclamation-triangle';
        $actionObj->title = '';
        $actionObj->message = 'Record Error';
        $actionObj->url = '';
        $actionObj->target = '_blank';
        $actionObj->type = 'danger';

        echo $actionJSON = json_encode($actionObj, JSON_FORCE_OBJECT);
}
?>
