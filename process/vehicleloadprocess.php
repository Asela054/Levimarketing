
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
$area =$_POST['area'];
$officer =$_POST['officer'];
$driver =$_POST['driver'];
$helper =$_POST['helper1'];
$helper2 =$_POST['helper2'];
$tableData =$_POST['tableData'];

if($recordOption==1){
// data insert
    $insertvehicleload = "INSERT INTO `tbl_vehicle_load`(`date`, `lorryid`, `driverid`, `officerid`, `repid`, `helperid`, `helperid2`, `approvestatus`, `unloadstatus`, `veiwallcustomerstatus`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_area_idtbl_area`)
    VALUES ('$date','$vehicleno','$driver','$officer','$driver','$helper','$helper2','0','0','0','1','$updatedatetime','$userID','$area')";

    if($conn->query($insertvehicleload)==true){
        $last_id = mysqli_insert_id($conn);

        foreach($tableData as $rowtabedata){
                $product=$rowtabedata['col_2'];
                $qty=$rowtabedata['col_3'];

                $sqlloaddetails ="INSERT INTO `tbl_vehicle_load_detail`( `qty`, `status`, `updatedatetime`, `tbl_vehicle_load_idtbl_vehicle_load`, `tbl_user_idtbl_user`, `tbl_product_idtbl_product`)
                VALUES ('$qty','1','$updatedatetime','$last_id','$userID','$product')";

                $sqlstock = "UPDATE tbl_stock  SET qty = qty - '$qty'  WHERE tbl_product_idtbl_product ='$product'";

                if($conn->query($sqlloaddetails)==true && $conn->query($sqlstock)==true){
                        $actionObj = new stdClass();
                        $actionObj->icon = 'fas fa-check-circle';
                        $actionObj->title = '';
                        $actionObj->message = 'Add Successfully';
                        $actionObj->url = '';
                        $actionObj->target = '_blank';
                        $actionObj->type = 'success';

                        echo $actionJSON = json_encode($actionObj, JSON_FORCE_OBJECT);
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
elseif($recordOption==2){
// data update
        $updatevehicleload ="UPDATE tbl_vehicle_load SET `date` ='$date',`lorryid` = '$vehicleno',`driverid` = '$driver',`officerid` ='$officer',`repid`= '$driver',`helperid` = '$helper',`helperid2` ='$helper2' ,`approvestatus` = '0',`updatedatetime` ='$updatedatetime',`tbl_user_idtbl_user` ='$userID',`tbl_area_idtbl_area` = '$area'  WHERE idtbl_vehicle_load  = '$recordID'";
        if( $conn->query($updatevehicleload)==true){

                $sqlgetqty="SELECT `qty`,`tbl_product_idtbl_product` FROM `tbl_vehicle_load_detail` WHERE tbl_vehicle_load_idtbl_vehicle_load ='$recordID'";
                $result=$conn->query($sqlgetqty);

                while ($row2 = $result->fetch_assoc()) {
                        $qty=$row2['qty'];
                        $productid=$row2['tbl_product_idtbl_product'];

                        $sqlupdatestock2 = "UPDATE tbl_stock  SET qty = qty - '$qty'  WHERE tbl_product_idtbl_product ='$productid'";
                        $conn->query($sqlupdatestock2);
                }

                $sqldelete = "DELETE FROM `tbl_vehicle_load_detail` WHERE `tbl_vehicle_load_idtbl_vehicle_load`='$recordID'";
                $conn->query($sqldelete);

                foreach($tableData as $rowtabedata){
                        $product=$rowtabedata['col_2'];
                        $qty=$rowtabedata['col_3'];
        
                        $sqlloaddetails ="INSERT INTO `tbl_vehicle_load_detail`( `qty`, `status`, `updatedatetime`, `tbl_vehicle_load_idtbl_vehicle_load`, `tbl_user_idtbl_user`, `tbl_product_idtbl_product`)
                        VALUES ('$qty','1','$updatedatetime','$recordID','$userID','$product')";
                        $conn->query($sqlloaddetails);
        
                        $sqlstock = "UPDATE tbl_stock  SET qty = qty - '$qty'  WHERE tbl_product_idtbl_product ='$product'";
                        $conn->query($sqlstock);
                }
                $actionObj = new stdClass();
                $actionObj->icon = 'fas fa-check-circle';
                $actionObj->title = '';
                $actionObj->message = 'Add Successfully';
                $actionObj->url = '';
                $actionObj->target = '_blank';
                $actionObj->type = 'success';

                echo $actionJSON = json_encode($actionObj, JSON_FORCE_OBJECT);
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
