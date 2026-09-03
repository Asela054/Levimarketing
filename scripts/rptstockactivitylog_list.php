<?php

/*
 * DataTables server-side processing for the Stock Activity Log report.
 * Follows the same SSP::simple pattern used by tbl_stock's list script
 * (config.php for credentials, ssp.customized.class.php for the heavy lifting).
 */

// -------- Access check (mirror the pattern used elsewhere in the app) --------
// session_start();
// Example, adjust menu id once you add the report to tbl_menu_list:
// $viewcheck = checkprivilege($_SESSION['menuprivilegearray'], <MENU_ID>, 5); // access_status
// if (!$viewcheck) { echo json_encode(["error" => "Access denied"]); exit; }

// DB table to use
$table = 'tbl_stock_activity_log';

// Table's primary key
$primaryKey = 'idtbl_stock_activity_log';

// Array of database columns which should be read and sent back to DataTables.
$columns = array(
    array( 'db' => '`u`.`idtbl_stock_activity_log`', 'dt' => 'idtbl_stock_activity_log', 'field' => 'idtbl_stock_activity_log' ),
    array( 'db' => '`u`.`action_type`',               'dt' => 'action_type',               'field' => 'action_type' ),
    array( 'db' => '`u`.`product_name_snapshot`',     'dt' => 'product_name_snapshot',     'field' => 'product_name_snapshot' ),
    array( 'db' => '`u`.`location_name_snapshot`',    'dt' => 'location_name_snapshot',    'field' => 'location_name_snapshot' ),
    array( 'db' => '`u`.`old_qty`',                   'dt' => 'old_qty',                   'field' => 'old_qty' ),
    array( 'db' => '`u`.`new_qty`',                   'dt' => 'new_qty',                   'field' => 'new_qty' ),
    array( 'db' => '`u`.`qty_change`',                'dt' => 'qty_change',                'field' => 'qty_change' ),
    array( 'db' => '`u`.`reason`',                    'dt' => 'reason',                    'field' => 'reason' ),
    array( 'db' => '`u`.`username_snapshot`',         'dt' => 'username_snapshot',         'field' => 'username_snapshot' ),
    array( 'db' => '`u`.`ip_address`',                'dt' => 'ip_address',                'field' => 'ip_address' ),
    array( 'db' => '`u`.`action_datetime`',           'dt' => 'action_datetime',           'field' => 'action_datetime' ),
);

// SQL server connection information
require('config.php');
$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require('ssp.customized.class.php');

// All the fields we need already live on tbl_stock_activity_log itself
// (product/location/user are stored as snapshots), so no joins are required.
$joinQuery = "FROM `tbl_stock_activity_log` AS `u`";

$whereParts = array();

if (!empty($_POST['action_type']) && in_array($_POST['action_type'], array('EDIT', 'DELETE'), true)) {
    $actionType = $_POST['action_type'];
    $whereParts[] = "`u`.`action_type` = '$actionType'";
}

if (!empty($_POST['location'])) {
    $locationId = intval($_POST['location']);
    $whereParts[] = "`u`.`tbl_location_idtbl_location` = $locationId";
}

if (!empty($_POST['date_from'])) {
    $dateFrom = $_POST['date_from'];
    $whereParts[] = "`u`.`action_datetime` >= '$dateFrom 00:00:00'";
}

if (!empty($_POST['date_to'])) {
    $dateTo = $_POST['date_to'];
    $whereParts[] = "`u`.`action_datetime` <= '$dateTo 23:59:59'";
}

if (!empty($_POST['keyword'])) {
    $keyword = $_POST['keyword'];
    $whereParts[] = "(`u`.`product_name_snapshot` LIKE '%$keyword%' OR `u`.`username_snapshot` LIKE '%$keyword%')";
}

$extraWhere = count($whereParts) ? implode(' AND ', $whereParts) : '';

echo json_encode(
    SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);