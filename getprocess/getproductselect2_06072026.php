<?php
require_once('../connection/db.php');

$searchTerm = $_POST['searchTerm'] ?? '';
$searchTerm = trim($searchTerm);

if (empty($searchTerm)) {
    $sql = "SELECT `idtbl_product`, `product_code`, `product_name` FROM `tbl_product` WHERE `status` = 1 LIMIT 10";
} else {
    $safeTerm = $conn->real_escape_string($searchTerm);
    $sql = "SELECT `idtbl_product`, `product_code`, `product_name` FROM `tbl_product` WHERE `status` = 1 AND (`product_name` LIKE '%$safeTerm%' OR `product_code` LIKE '%$safeTerm%') LIMIT 20";
}

$result = $conn->query($sql);
$items = array();
while ($row = $result->fetch_assoc()) {
    $label = trim($row['product_code']) !== '' ? '[' . $row['product_code'] . '] ' . $row['product_name'] : $row['product_name'];
    $items[] = array('id' => $row['idtbl_product'], 'text' => $label);
}

echo json_encode($items);
