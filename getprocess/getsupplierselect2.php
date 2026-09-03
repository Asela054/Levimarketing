<?php
require_once('../connection/db.php');

$searchTerm = $_POST['searchTerm'] ?? '';
$searchTerm = trim($searchTerm);

if (empty($searchTerm)) {
    $sql = "SELECT `idtbl_supplier`, `suppliername` FROM `tbl_supplier` WHERE `status` = 1 LIMIT 10";
} else {
    $safeTerm = $conn->real_escape_string($searchTerm);
    $sql = "SELECT `idtbl_supplier`, `suppliername` FROM `tbl_supplier` WHERE `status` = 1 AND `suppliername` LIKE '%$safeTerm%' LIMIT 20";
}

$result = $conn->query($sql);
$items = array();
while ($row = $result->fetch_assoc()) {
    $items[] = array('id' => $row['idtbl_supplier'], 'text' => $row['suppliername']);
}

echo json_encode($items);
