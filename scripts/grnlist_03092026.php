<?php
session_start();
/*
 * Server-side processing for the GRN header list (grn.php DataTable).
 * Follows the same ssp.customized.class.php pattern used elsewhere in this project.
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'tbl_grn';

// Table's primary key
$primaryKey = 'idtbl_grn';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier.
$columns = array(
	array( 'db' => '`g`.`idtbl_grn`',     'dt' => 'idtbl_grn',     'field' => 'idtbl_grn' ),
	array( 'db' => '`g`.`date`',          'dt' => 'date',          'field' => 'date' ),
	array( 'db' => '`g`.`porder_id`',     'dt' => 'porder_id',     'field' => 'porder_id' ),
	array( 'db' => '`s`.`suppliername`',  'dt' => 'suppliername',  'field' => 'suppliername' ),
	array( 'db' => '`l`.`location`',      'dt' => 'location',      'field' => 'location' ),
	array( 'db' => '`g`.`invoicenum`',    'dt' => 'invoicenum',    'field' => 'invoicenum' ),
	array( 'db' => '`g`.`dispatchnum`',   'dt' => 'dispatchnum',   'field' => 'dispatchnum' ),
	array( 'db' => '`g`.`total`',         'dt' => 'total',         'field' => 'total' ),
	array( 'db' => '`g`.`confirm_status`','dt' => 'confirm_status','field' => 'confirm_status' )
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

// porder_id = 0 means "no PO linked" - the `AND g.porder_id > 0` guard stops
// those rows from incorrectly matching idtbl_porder = 0.
// suppliername resolves from the GRN's own supplier column first, falling back
// to the linked PO's supplier when there is one.
$locationID=$_SESSION['location_id'];
$joinQuery = "FROM `tbl_grn` AS `g`
    LEFT JOIN `tbl_porder` AS `po` ON (`po`.`idtbl_porder` = `g`.`porder_id` AND `g`.`porder_id` > 0)
    LEFT JOIN `tbl_supplier` AS `s` ON (`s`.`idtbl_supplier` = COALESCE(`g`.`tbl_supplier_idtbl_supplier`, `po`.`tbl_supplier_idtbl_supplier`))
    LEFT JOIN `tbl_location` AS `l` ON (`l`.`idtbl_location` = `g`.`tbl_location_idtbl_location`)";

$extraWhere = "`g`.`status` = 1 AND `g`.`tbl_location_idtbl_location` = $locationID";

if (!empty($_POST['search_date'])) {
    $date = $_POST['search_date'];
    $extraWhere .= " AND `g`.`date` = '$date'";
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

    $extraWhere .= " AND `g`.`date` BETWEEN '$startDate' AND '$endDate'";
} elseif (!empty($_POST['search_month'])) {
    $month = $_POST['search_month'];
    $month_arr = explode('-', $month);
    $extraWhere .= " AND YEAR(`g`.`date`) = '$month_arr[0]' AND MONTH(`g`.`date`) = '$month_arr[1]'";
} elseif (!empty($_POST['search_from_date']) && !empty($_POST['search_to_date'])) {
    $from_date = $_POST['search_from_date'];
    $to_date = $_POST['search_to_date'];

    $extraWhere .= " AND `g`.`date` BETWEEN '$from_date' AND '$to_date'";
}

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);