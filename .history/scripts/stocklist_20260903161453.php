<?php

session_start();
$location

/*
 * Server-side processing for the Stock Info report.
 *
 * FIXES applied vs the previous version of this script:
 * 1. `` `u`.update `` referenced the `update` column WITHOUT backticks around
 *    the column name itself (only the table alias `u` was quoted). UPDATE is
 *    a MySQL reserved word, so this was a live SQL-syntax risk depending on
 *    MySQL mode/version - now consistently written as `` `u`.`update` ``.
 * 2. $extraWhere was only ever set INSIDE the date-filter if/elseif branches -
 *    on first page load (no date filter posted yet) it was undefined. It now
 *    always has a base value up front, and the date branches override it.
 * 3. Added a Product filter, since that's the natural thing to browse a
 *    stock report by. (Location stays a displayed column via the `uc` join,
 *    but is not filterable, per request.)
 */

$table = 'tbl_stock';
$primaryKey = 'idtbl_stock';

$columns = array(
	array( 'db' => '`u`.`idtbl_stock`',   'dt' => 'idtbl_stock',   'field' => 'idtbl_stock' ),
	array( 'db' => '`u`.`qty`',           'dt' => 'qty',           'field' => 'qty' ),
	array( 'db' => '`u`.`update`',        'dt' => 'update',        'field' => 'update' ),
	array( 'db' => '`u`.`status`',        'dt' => 'status',        'field' => 'status' ),
	array( 'db' => '`ud`.`product_name`', 'dt' => 'product_name',  'field' => 'product_name' ),
	array( 'db' => '`uc`.`location`',     'dt' => 'location',      'field' => 'location' )
);

// SQL server connection information
require('config.php');
$sql_details = array(
	'user' => $db_username,
	'pass' => $db_password,
	'db'   => $db_name,
	'host' => $db_host
);

require('ssp.customized.class.php');

// u  = tbl_stock
// ud = tbl_product   (product name)
// uc = tbl_location  (location name)
$joinQuery = "FROM `tbl_stock` AS `u`
    LEFT JOIN `tbl_product` AS `ud` ON (`ud`.`idtbl_product` = `u`.`tbl_product_idtbl_product`)
    LEFT JOIN `tbl_location` AS `uc` ON (`uc`.`idtbl_location` = `u`.`tbl_location_idtbl_location`)";

// Base clause - always defined, regardless of which (if any) date filter is posted.
$extraWhere = "`u`.`status` IN (0,1)";

if (!empty($_POST['search_date'])) {
    $date = $_POST['search_date'];
    $extraWhere = "`u`.`status` IN (0,1) AND `u`.`update` = '$date'";
} elseif (!empty($_POST['search_week'])) {
    $week = $_POST['search_week'];
    $weeksep = explode('-W', $week);
    $year = $weeksep[0];
    $week1 = $weeksep[1];
    $dto = new DateTime();
    $dto->setISODate($year, $week1);
    $startDate = $dto->format('Y-m-d');
    $dto->modify('+6 days');
    $endDate = $dto->format('Y-m-d');

    $extraWhere = "`u`.`status` IN (0,1) AND `u`.`update` BETWEEN '$startDate' AND '$endDate'";
} elseif (!empty($_POST['search_month'])) {
    $month = $_POST['search_month'];
    $month_arr = explode('-', $month);
    $extraWhere = "`u`.`status` IN (0,1) AND YEAR(`u`.`update`) = '$month_arr[0]' AND MONTH(`u`.`update`) = '$month_arr[1]'";
} elseif (!empty($_POST['search_from_date']) && !empty($_POST['search_to_date'])) {
    $from_date = $_POST['search_from_date'];
    $to_date = $_POST['search_to_date'];
    $extraWhere = "`u`.`status` IN (0,1) AND `u`.`update` BETWEEN '$from_date' AND '$to_date'";
}

// Product filter
if (!empty($_POST['search_product'])) {
    $search_product = intval($_POST['search_product']);
    $extraWhere .= " AND `u`.`tbl_product_idtbl_product` = " . $search_product;
}

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);