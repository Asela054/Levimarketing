<?php
session_start();
if (!isset($_SESSION['userid'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}
require_once('../connection/db.php');

$locationID = intval($_SESSION['location_id'] ?? 0);
$searchTerm = trim($_POST['searchTerm'] ?? '');
$likeTerm   = '%' . $searchTerm . '%';

// Current stock on hand: the single active stock row for this product
// at this location. `qty` already holds the balance, so we just read it.
$stockSubquery =
    "(SELECT s.`qty`
      FROM `tbl_stock` s
      WHERE s.`tbl_product_idtbl_product` = p.`idtbl_product`
        AND s.`tbl_location_idtbl_location` = ?
        AND s.`status` = 1
      ORDER BY s.`idtbl_stock` DESC
      LIMIT 1)";

if ($searchTerm === '') {
    $sql = "SELECT p.`idtbl_product`, p.`product_code`, p.`product_name`, p.`unitprice`, p.`saleprice`, p.`wholesaleprice`,
                   COALESCE($stockSubquery, 0) AS availableqty
            FROM `tbl_product` p
            WHERE p.`status` = 1
            ORDER BY p.`product_name` ASC
            LIMIT 10";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $locationID);
} else {
    $sql = "SELECT p.`idtbl_product`, p.`product_code`, p.`product_name`, p.`unitprice`, p.`saleprice`, p.`wholesaleprice`,
                   COALESCE($stockSubquery, 0) AS availableqty
            FROM `tbl_product` p
            WHERE p.`status` = 1
              AND (p.`product_name` LIKE ? OR p.`product_code` LIKE ?)
            ORDER BY p.`product_name` ASC
            LIMIT 20";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iss', $locationID, $likeTerm, $likeTerm);
}

if (!$stmt) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();
$items = array();

while ($row = $result->fetch_assoc()) {
    $label = trim($row['product_code']) !== ''
        ? '[' . $row['product_code'] . '] ' . $row['product_name']
        : $row['product_name'];

    $items[] = array(
        'id'             => $row['idtbl_product'],
        'text'           => $label,
        // sent along with the select2 result so the invoice page can
        // auto-fill the unit price and check stock without a second request
        'unitprice'      => floatval($row['unitprice']),
        'saleprice'      => floatval($row['saleprice']),
        'wholesaleprice' => floatval($row['wholesaleprice']),
        'availableqty'   => floatval($row['availableqty']),
    );
}

$stmt->close();

header('Content-Type: application/json');
echo json_encode($items);