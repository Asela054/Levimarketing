<?php 
require_once('../connection/db.php');

$searchTerm = $_POST['searchTerm'] ?? '';
$searchType = $_POST['searchType'] ?? '';
$searchLoad = $_POST['searchLoad'] ?? '';

if($searchType==1){
    $sqlloadinfo="SELECT `veiwallcustomerstatus` FROM `tbl_vehicle_load` WHERE `idtbl_vehicle_load`='$searchLoad'";
    $resultloadinfo = $conn->query($sqlloadinfo);
    $rowloadinfo = $resultloadinfo->fetch_assoc();

    $allcustomerallow=$rowloadinfo['veiwallcustomerstatus'];

    if (empty($searchTerm)) {
        if($allcustomerallow==1){
            $sql = "SELECT `idtbl_customer`, `name` FROM `tbl_customer` WHERE `status` = 1 LIMIT 5";
        }
        else{
            $sql = "SELECT `idtbl_customer`, `name` FROM `tbl_customer` LEFT JOIN `tbl_vehicle_load` ON `tbl_vehicle_load`.`tbl_area_idtbl_area`=`tbl_customer`.`tbl_area_idtbl_area` WHERE `tbl_customer`.`status` = 1 AND `tbl_vehicle_load`.`idtbl_vehicle_load`='$searchLoad' LIMIT 5";
        }
    } else {
        if($allcustomerallow==1){
            $sql = "SELECT `idtbl_customer`, `name` FROM `tbl_customer` WHERE `status` = 1 AND `name` LIKE '$searchTerm%'";
        }
        else{
            $sql = "SELECT `idtbl_customer`, `name` FROM `tbl_customer` LEFT JOIN `tbl_vehicle_load` ON `tbl_vehicle_load`.`tbl_area_idtbl_area`=`tbl_customer`.`tbl_area_idtbl_area` WHERE `tbl_customer`.`status` = 1 AND `tbl_vehicle_load`.`idtbl_vehicle_load`='$searchLoad' AND `tbl_customer`.`name` LIKE '$searchTerm%'";
        }
    }
    $result = $conn->query($sql);
    $customers = array();
    while ($row = $result->fetch_assoc()) {
        $customers[] = array("id" => $row['idtbl_customer'], "text" => $row['name']);
    }
}
else{
    if (empty($searchTerm)) {
        $sql = "SELECT `idtbl_customer`, `name` FROM `tbl_customer` WHERE `status` = 1 LIMIT 5";
    } else {
        $sql = "SELECT `idtbl_customer`, `name` FROM `tbl_customer` WHERE `status` = 1 AND `name` LIKE '$searchTerm%'";
    }
    $result = $conn->query($sql);
    $customers = array();
    while ($row = $result->fetch_assoc()) {
        $customers[] = array("id" => $row['idtbl_customer'], "text" => $row['name']);
    }
}

echo json_encode($customers);
?>