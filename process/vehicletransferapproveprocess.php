<?php 
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');

$userID=$_SESSION['userid'];
$updatedatetime=date('Y-m-d h:i:s');

$recordID=$_POST['recordID'];

$sqlupdatevehicletransfer = "UPDATE `tbl_vehicle_transfer` SET `approvestatus`='1' WHERE `idtbl_vehicle_transfer`='$recordID' AND `status`='1'";
if($conn->query($sqlupdatevehicletransfer)==true){
        $sqlselectvehicletransfer = "SELECT * FROM `tbl_vehicle_transfer` WHERE `idtbl_vehicle_transfer` = '$recordID' AND `status` = '1'";
        $result = $conn->query($sqlselectvehicletransfer);
        $row = $result->fetch_assoc();

        $date=$row['date'];
        $lorryID=$row['transfer_lorryid'];
        $driver=$row['driverid'];
        $officer=$row['officerid'];
        $rep=$row['repid'];
        $helper=$row['helperid'];
        $helper2=$row['helperid2'];
        $area=$row['tbl_area_idtbl_area'];

        $insertvehicleload = "INSERT INTO `tbl_vehicle_load`(`date`, `type`, `lorryid`, `driverid`, `officerid`, `repid`, `helperid`, `helperid2`, `approvestatus`, `unloadstatus`, `veiwallcustomerstatus`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_area_idtbl_area`)
        VALUES ('$date','1','$lorryID','$driver','$officer','$rep','$helper','$helper2','0','0','0','1','$updatedatetime','$userID','$area')";

        if($conn->query($insertvehicleload)==true){
                $newVehicleLoadID = $conn->insert_id;
                $sqlgetqty = "SELECT `transferqty`, `tbl_product_idtbl_product` FROM `tbl_vehicle_transfer_detail` WHERE `tbl_vehicle_transfer_idtbl_vehicle_transfer` = '$recordID'";
                $result2 = $conn->query($sqlgetqty);

                while ($row2 = $result2->fetch_assoc()) {
                        $qty = $row2['transferqty'];
                        $productid = $row2['tbl_product_idtbl_product'];

                        $sqlinsertvehicleloaddetails = "INSERT INTO `tbl_vehicle_load_detail`(`qty`, `status`, `updatedatetime`, `tbl_vehicle_load_idtbl_vehicle_load`, `tbl_user_idtbl_user`, `tbl_product_idtbl_product`) 
                        VALUES ('$qty', '1', '$updatedatetime', '$newVehicleLoadID', '$userID', '$productid')";
                        $conn->query($sqlinsertvehicleloaddetails);
                }

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
