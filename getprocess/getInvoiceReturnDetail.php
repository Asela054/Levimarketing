<?php
session_start();
if (!isset($_SESSION['userid'])) {
    http_response_code(403);
    exit;
}
require_once('../connection/db.php');

$returnID = (int) $_POST['recordID'];

$sql = "SELECT d.idtbl_invoice_return_detail, d.qty, d.unitprice, d.nettotal, d.comment,
               p.idtbl_product, p.product_name
        FROM tbl_invoice_return_detail d
        LEFT JOIN tbl_product p ON p.idtbl_product = d.tbl_product_idtbl_product
        WHERE d.tbl_invoice_return_idtbl_invoice_return = ?
          AND d.status = 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $returnID);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($r = $result->fetch_assoc()) {
    $rows[] = $r;
}
$stmt->close();

header('Content-Type: application/json');
echo json_encode($rows);