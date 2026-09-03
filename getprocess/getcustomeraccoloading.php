<?php 
require_once('../connection/db.php');

$searchTerm = $_POST['searchTerm'] ?? '';
$searchLoad = $_POST['searchLoad'] ?? '';
$searchDate = $_POST['searchDate'] ?? '';

$sqlloadinfo="SELECT `veiwallcustomerstatus` FROM `tbl_vehicle_load` WHERE `idtbl_vehicle_load`='$searchLoad'";
$resultloadinfo = $conn->query($sqlloadinfo);
$rowloadinfo = $resultloadinfo->fetch_assoc();

$allcustomerallow=$rowloadinfo['veiwallcustomerstatus'];

if (empty($searchTerm)) {
    if($allcustomerallow==1){
        $sql = "SELECT `idtbl_customer`, `name` FROM `tbl_customer` WHERE `tbl_customer`.`status` = 1 AND `tbl_customer`.`idtbl_customer` NOT IN (SELECT `tbl_customer_idtbl_customer` FROM `tbl_vehicle_load_has_tbl_invoice` WHERE `tbl_vehicle_load_has_tbl_invoice`.`tbl_vehicle_load_idtbl_vehicle_load`='$searchLoad') LIMIT 5";
    }
    else{
        $sql = "SELECT `idtbl_customer`, `name` FROM `tbl_customer` LEFT JOIN `tbl_vehicle_load` ON `tbl_vehicle_load`.`tbl_area_idtbl_area`=`tbl_customer`.`tbl_area_idtbl_area` WHERE `tbl_customer`.`status` = 1 AND `tbl_vehicle_load`.`idtbl_vehicle_load`='$searchLoad' AND `tbl_customer`.`idtbl_customer` NOT IN (SELECT `tbl_customer_idtbl_customer` FROM `tbl_vehicle_load_has_tbl_invoice` WHERE `tbl_vehicle_load_has_tbl_invoice`.`tbl_vehicle_load_idtbl_vehicle_load`='$searchLoad') LIMIT 5";
    }
} else {
    if($allcustomerallow==1){
        $sql = "SELECT `idtbl_customer`, `name` FROM `tbl_customer` WHERE `tbl_customer`.`status` = 1 AND `tbl_customer`.`name` LIKE '$searchTerm%' AND `tbl_customer`.`idtbl_customer` NOT IN (SELECT `tbl_customer_idtbl_customer` FROM `tbl_vehicle_load_has_tbl_invoice` WHERE `tbl_vehicle_load_has_tbl_invoice`.`tbl_vehicle_load_idtbl_vehicle_load`='$searchLoad')";
    }
    else{
        $sql = "SELECT `idtbl_customer`, `name` FROM `tbl_customer` LEFT JOIN `tbl_vehicle_load` ON `tbl_vehicle_load`.`tbl_area_idtbl_area`=`tbl_customer`.`tbl_area_idtbl_area` WHERE `tbl_customer`.`status` = 1 AND `tbl_vehicle_load`.`idtbl_vehicle_load`='$searchLoad' AND `tbl_customer`.`name` LIKE '$searchTerm%' AND `tbl_customer`.`idtbl_customer` NOT IN (SELECT `tbl_customer_idtbl_customer` FROM `tbl_vehicle_load_has_tbl_invoice` WHERE `tbl_vehicle_load_has_tbl_invoice`.`tbl_vehicle_load_idtbl_vehicle_load`='$searchLoad')";
    }
}
$result = $conn->query($sql);
$customers = array();
while ($row = $result->fetch_assoc()) {
    $customers[] = array("id" => $row['idtbl_customer'], "text" => $row['name']);
}

echo json_encode($customers);
?>