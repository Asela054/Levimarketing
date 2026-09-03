<?php
session_start();

// Uses the same credentials as scripts/config.php — adjust path if this file
// lives somewhere other than getprocess/ relative to scripts/.
require "../scripts/config.php";
$conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// -------- Access check (mirror the pattern used elsewhere in the app) --------
// $viewcheck = checkprivilege($_SESSION['menuprivilegearray'], <MENU_ID>, 5);
// if (!$viewcheck) { die("Access denied"); }

$whereClauses = [];

if (!empty($_GET['action_type'])) {
    $actionType = mysqli_real_escape_string($conn, $_GET['action_type']);
    if (in_array($actionType, ['EDIT', 'DELETE'], true)) {
        $whereClauses[] = "action_type = '{$actionType}'";
    }
}

if (!empty($_GET['location'])) {
    $locationId = intval($_GET['location']);
    $whereClauses[] = "tbl_location_idtbl_location = {$locationId}";
}

if (!empty($_GET['date_from'])) {
    $dateFrom = mysqli_real_escape_string($conn, $_GET['date_from']);
    $whereClauses[] = "action_datetime >= '{$dateFrom} 00:00:00'";
}

if (!empty($_GET['date_to'])) {
    $dateTo = mysqli_real_escape_string($conn, $_GET['date_to']);
    $whereClauses[] = "action_datetime <= '{$dateTo} 23:59:59'";
}

if (!empty($_GET['keyword'])) {
    $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
    $whereClauses[] = "(product_name_snapshot LIKE '%{$keyword}%' OR username_snapshot LIKE '%{$keyword}%')";
}

$whereSql = count($whereClauses) ? ('WHERE ' . implode(' AND ', $whereClauses)) : '';

$sql = "SELECT idtbl_stock_activity_log, action_type, product_name_snapshot, location_name_snapshot,
               old_qty, new_qty, qty_change, reason, username_snapshot, ip_address, action_datetime
        FROM tbl_stock_activity_log
        {$whereSql}
        ORDER BY action_datetime DESC";

$result = mysqli_query($conn, $sql);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=stock_activity_log_' . date('Ymd_His') . '.csv');

$output = fopen('php://output', 'w');

// Header row
fputcsv($output, [
    'ID', 'Action', 'Product', 'Location', 'Old Qty', 'New Qty',
    'Qty Change', 'Reason', 'User', 'IP Address', 'Date/Time'
]);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['idtbl_stock_activity_log'],
            $row['action_type'],
            $row['product_name_snapshot'],
            $row['location_name_snapshot'],
            $row['old_qty'],
            $row['new_qty'],
            $row['qty_change'],
            $row['reason'],
            $row['username_snapshot'],
            $row['ip_address'],
            $row['action_datetime'],
        ]);
    }
}

fclose($output);
exit;