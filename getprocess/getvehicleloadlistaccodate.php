<?php 
require_once('../connection/db.php');

$searchdate=$_POST['searchdate'];

$sql="SELECT `tbl_vehicle_load`.`idtbl_vehicle_load`, `tbl_vehicle`.`vehicleno` FROM `tbl_vehicle_load` LEFT JOIN `tbl_vehicle` ON `tbl_vehicle`.`idtbl_vehicle`=`tbl_vehicle_load`.`lorryid` WHERE `tbl_vehicle_load`.`status`=1 AND `tbl_vehicle_load`.`date`='$searchdate' AND `approvestatus`=1";
$result=$conn->query($sql);

$arraylist=array();
while($row=$result->fetch_assoc()){
    $obj=new stdClass();
    $obj->id=$row['idtbl_vehicle_load'];
    $obj->vehicleno=$row['vehicleno'];

    array_push($arraylist, $obj);
}

echo json_encode($arraylist); 
?>