<?php
/*
 RECONSTRUCTED FILE - your original was not provided.
 This follows the standard DataTables server-side protocol (draw/start/length/search)
 and matches the header query you specified:

 SELECT idtbl_grn, date, total, subtotal, vattype, vatpercentage, vatamount,
        invoicenum, dispatchnum, porder_id, status, confirm_status, updatedatetime,
        tbl_user_idtbl_user, tbl_location_idtbl_location, tbl_supplier_idtbl_supplier
 FROM tbl_grn WHERE 1

 One row per GRN header only - tbl_grndetail is never touched here.
 Compare column indexes / filters against your real file before deploying.
*/
session_start();
if(!isset($_SESSION['userid'])){ http_response_code(403); exit; }
require_once('../connection/db.php');

$locationid = $conn->real_escape_string($_SESSION['location_id']);

$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 25;
$searchValue = isset($_POST['search']['value']) ? $conn->real_escape_string($_POST['search']['value']) : '';

// column index -> sortable column name (matches the #dataTableGrn "columns" array in grn.php)
$columns = [
    0 => 'g.idtbl_grn',
    1 => 'g.date',
    2 => 'g.idtbl_grn',
    3 => 'g.porder_id',
    4 => 's.suppliername',
    5 => 'l.locationname',
    6 => 'g.invoicenum',
    7 => 'g.dispatchnum',
    8 => 'g.subtotal',
    9 => 'g.vattype',
    10 => 'g.total',
    11 => 'g.confirm_status',
];

$orderColIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir = (isset($_POST['order'][0]['dir']) && strtolower($_POST['order'][0]['dir']) === 'asc') ? 'ASC' : 'DESC';
$orderCol = isset($columns[$orderColIndex]) ? $columns[$orderColIndex] : 'g.idtbl_grn';

$baseFrom = "FROM `tbl_grn` g
    LEFT JOIN `tbl_supplier` s ON s.`idtbl_supplier` = g.`tbl_supplier_idtbl_supplier`
    LEFT JOIN `tbl_location` l ON l.`idtbl_location` = g.`tbl_location_idtbl_location`
    WHERE g.`status` = 1 AND g.`tbl_location_idtbl_location` = '$locationid'";

$whereSearch = '';
if ($searchValue !== '') {
    $whereSearch = " AND (
        g.`idtbl_grn` LIKE '%$searchValue%'
        OR g.`invoicenum` LIKE '%$searchValue%'
        OR g.`dispatchnum` LIKE '%$searchValue%'
        OR s.`suppliername` LIKE '%$searchValue%'
        OR l.`locationname` LIKE '%$searchValue%'
    )";
}

// Total records (before filtering)
$sqlTotal = "SELECT COUNT(*) AS cnt $baseFrom";
$resultTotal = $conn->query($sqlTotal);
$rowTotal = $resultTotal ? $resultTotal->fetch_assoc() : ['cnt' => 0];
$totalRecords = intval($rowTotal['cnt']);

// Filtered count
$sqlFiltered = "SELECT COUNT(*) AS cnt $baseFrom $whereSearch";
$resultFiltered = $conn->query($sqlFiltered);
$rowFiltered = $resultFiltered ? $resultFiltered->fetch_assoc() : ['cnt' => 0];
$totalFiltered = intval($rowFiltered['cnt']);

// One row per GRN header - no join to tbl_grndetail
$sqlData = "SELECT
        g.`idtbl_grn`, g.`date`, g.`total`, g.`subtotal`, g.`vattype`, g.`vatpercentage`,
        g.`vatamount`, g.`invoicenum`, g.`dispatchnum`, g.`porder_id`, g.`status`,
        g.`confirm_status`, g.`updatedatetime`, g.`tbl_user_idtbl_user`,
        g.`tbl_location_idtbl_location`, g.`tbl_supplier_idtbl_supplier`,
        s.`suppliername`, l.`locationname` AS location
    $baseFrom $whereSearch
    ORDER BY $orderCol $orderDir
    LIMIT $start, $length";

$resultData = $conn->query($sqlData);

$data = [];
if ($resultData && $resultData->num_rows > 0) {
    while ($row = $resultData->fetch_assoc()) {
        $data[] = [
            "idtbl_grn"     => $row['idtbl_grn'],
            "date"          => $row['date'],
            "porder_id"     => $row['porder_id'],
            "suppliername"  => $row['suppliername'],
            "location"      => $row['location'],
            "invoicenum"    => $row['invoicenum'],
            "dispatchnum"   => $row['dispatchnum'],
            "subtotal"      => $row['subtotal'],
            "vattype"       => $row['vattype'],
            "vatpercentage" => $row['vatpercentage'],
            "vatamount"     => $row['vatamount'],
            "total"         => $row['total'],
            "confirm_status"=> $row['confirm_status'],
        ];
    }
}

$response = [
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalFiltered,
    "data" => $data,
];

header('Content-Type: application/json');
echo json_encode($response);