<?php
session_start();
if (!isset($_SESSION['userid'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}
require_once('../connection/db.php');

$searchTerm = trim($_POST['searchTerm'] ?? '');

if ($searchTerm === '') {
    $sql = "SELECT `idtbl_customer`, `name`, `nic`, `phone`
            FROM `tbl_customer`
            WHERE `status` = 1
            ORDER BY `name` ASC
            LIMIT 10";
} else {
    $safeTerm = $conn->real_escape_string($searchTerm);
    $sql = "SELECT `idtbl_customer`, `name`, `nic`, `phone`
            FROM `tbl_customer`
            WHERE `status` = 1
              AND (`name` LIKE '%$safeTerm%' OR `phone` LIKE '%$safeTerm%' OR `nic` LIKE '%$safeTerm%')
            ORDER BY `name` ASC
            LIMIT 20";
}

$result = $conn->query($sql);
$items = array();

while ($row = $result->fetch_assoc()) {
    $label = $row['name'] . (trim($row['phone']) !== '' ? ' - ' . $row['phone'] : '');
    $items[] = array(
        'id'   => $row['idtbl_customer'],
        'text' => $label,
    );
}

header('Content-Type: application/json');
echo json_encode($items);