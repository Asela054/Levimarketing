
<?php 
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');

$userID=$_SESSION['userid'];
$updatedatetime=date('Y-m-d h:i:s');

$recordID=$_POST['recordID'];
$recordOption=$_POST['recordOption'];

$date =$_POST['date'];
$vehicleno =$_POST['vehicleno'];
$transfervehicleNo =$_POST['transfervehicleNo'];
$area =$_POST['area'];
$officer =$_POST['officer'];
$driver =$_POST['driver'];
$helper =$_POST['helper1'];
$helper2 =$_POST['helper2'];
$tableData =$_POST['tableData'];

if($recordOption==1){
// data insert
    $insertvehicletransfer = "INSERT INTO `tbl_vehicle_transfer`(`date`, `current_lorryid`, `transfer_lorryid`, `driverid`, `officerid`, `repid`, `helperid`, `helperid2`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_area_idtbl_area`)
    VALUES ('$date','$vehicleno','$transfervehicleNo','$driver','$officer','$driver','$helper','$helper2','1','$updatedatetime','$userID','$area')";

    if($conn->query($insertvehicletransfer)==true){
        $last_id = mysqli_insert_id($conn);

        foreach($tableData as $rowtabedata){
                $productid = $rowtabedata['productid'];
                $qty = $rowtabedata['qty'];
                $transferqty = $rowtabedata['transferqty'];

                $sqltransferetails ="INSERT INTO `tbl_vehicle_transfer_detail`(`availableqty`, `transferqty`, `status`, `updatedatetime`, `tbl_vehicle_transfer_idtbl_vehicle_transfer`, `tbl_user_idtbl_user`, `tbl_product_idtbl_product`)
                VALUES ('$qty','$transferqty','1','$updatedatetime','$last_id','$userID','$productid')";
                $conn->query($sqltransferetails);

                $sqlvehicleloaddetails = "UPDATE `tbl_vehicle_load_detail` SET `qty` = (`qty` - '$qty')  WHERE `tbl_product_idtbl_product` ='$productid' AND `tbl_vehicle_load_idtbl_vehicle_load`='$recordID'";
                $conn->query($sqlvehicleloaddetails);
        } 

        $sqlvehileload="UPDATE ` tbl_vehicle_load` SET `transferstatus` = '1' WHERE `idtbl_vehicle_load`='$recordID'";
        if($conn->query($sqlvehileload)==true){
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
    }else{
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
?>
