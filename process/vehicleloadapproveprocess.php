<?php 
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');

$userID=$_SESSION['userid'];
$updatedatetime=date('Y-m-d h:i:s');

$recordID=$_POST['recordID'];

$sqlupdatevehicletransfer = "UPDATE `tbl_vehicle_load` SET `approvestatus`='1' WHERE `idtbl_vehicle_load`='$recordID' AND `status`='1'";
if($conn->query($sqlupdatevehicletransfer)==true){
        
        
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