<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['userid'])) { http_response_code(403); exit; }

require('config.php');
$mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);

$dayendID = isset($_POST['dayendid']) ? (int) $_POST['dayendid'] : 0;

$sql = "SELECT d.`idtbl_stock`, p.`productname`, l.`locationname`, d.`qty`, d.`update`, d.`status`
        FROM `tbl_stock_dayend` d
        LEFT JOIN `tbl_product` p ON p.`idtbl_product` = d.`tbl_product_idtbl_product`
        LEFT JOIN `tbl_location` l ON l.`idtbl_location` = d.`tbl_location_idtbl_location`
        WHERE d.`tbl_dayend_idtbl_dayend` = ?
        ORDER BY p.`productname` ASC";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $dayendID);
$stmt->execute();
$res = $stmt->get_result();

$data = array();
while ($row = $res->fetch_assoc()) { $data[] = $row; }

echo json_encode(array('data' => $data));