<?php
session_start();
if (!isset($_SESSION['userid'])) {
    http_response_code(403);
    exit;
}
require_once('../connection/db.php');

// NOTE: adjust `l.location` / `u.username` below to match your actual
// tbl_location / tbl_user column names if they differ.
$sql = "SELECT r.idtbl_invoice_return, r.date, r.total, r.remarks, r.approvestatus,
               r.tbl_location_idtbl_location, l.location AS location_name,
               u.username AS approved_by
        FROM tbl_invoice_return r
        LEFT JOIN tbl_location l ON l.idtbl_location = r.tbl_location_idtbl_location
        LEFT JOIN tbl_user u ON u.idtbl_user = r.tbl_user_idtbl_user
        WHERE r.status = 1
        ORDER BY r.idtbl_invoice_return DESC";

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);