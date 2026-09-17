<?php
session_start();
/*
 * Server-side processing for the Expenses Cheque Payment report
 * (rptexpensecheque.php DataTable).
 *
 * One row per cheque expense payment. Unlike the customer cheque collection
 * report there is no bridge table here - tbl_expensepayment already holds one
 * row per payment, so no row duplication is possible.
 *
 * FILTERS: Expenses Type + Cheque Status only. There are no date filters -
 * use the DataTables search box / column sorting for anything else. If you
 * ever want date filtering back, add the usual
 * search_date / search_week / search_month / search_from_date+search_to_date
 * block against `u`.`cheque_date`.
 *
 * NOTE ON LOCATION: tbl_expensepayment has no location column, so this report
 * is NOT filtered by $_SESSION['location_id'].
 *
 * NOTE ON THE TYPE JOIN: the FK column is named
 * `tbl_expensetype_idtbl_expensetype` but the live master table is
 * `tbl_expences_type` - the join below reflects that.
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// Base table for this report
$table = 'tbl_expensepayment';

// Table's primary key
$primaryKey = 'idtbl_expensepayment';

// Array of database columns which should be read and sent back to DataTables.
// Same 'as' rule as rptchequecollection.php: any column whose 'dt' differs
// from the raw DB column name needs an explicit 'as' so ssp.customized.class.php
// can find it in the fetched row.
$columns = array(
	array( 'db' => '`u`.`idtbl_expensepayment`', 'dt' => 'idtbl_expensepayment', 'field' => 'idtbl_expensepayment' ),
	array( 'db' => '`u`.`refno`',                'dt' => 'refno',                'field' => 'refno' ),
	array( 'db' => '`u`.`paymentdate`',          'dt' => 'paymentdate',          'field' => 'paymentdate' ),
	array( 'db' => '`u`.`amount`',               'dt' => 'amount',               'field' => 'amount' ),
	array( 'db' => '`u`.`cheque_no`',            'dt' => 'cheque_no',            'field' => 'cheque_no' ),
	array( 'db' => '`u`.`cheque_bank_name`',     'dt' => 'cheque_bank_name',     'field' => 'cheque_bank_name' ),
	array( 'db' => '`u`.`cheque_branch`',        'dt' => 'cheque_branch',        'field' => 'cheque_branch' ),
	array( 'db' => '`u`.`cheque_date`',          'dt' => 'cheque_date',          'field' => 'cheque_date' ),
	array( 'db' => '`u`.`cheque_status`',        'dt' => 'cheque_status',        'field' => 'cheque_status' ),
	array( 'db' => '`u`.`remarks`',              'dt' => 'remarks',              'field' => 'remarks' ),
	array( 'db' => '`u`.`status`',               'dt' => 'paymentstatus',        'field' => 'paymentstatus', 'as' => 'paymentstatus' ),
	array( 'db' => '`et`.`idtbl_expences_type`', 'dt' => 'idtbl_expences_type',  'field' => 'idtbl_expences_type' ),
	array( 'db' => '`et`.`expencestype`',        'dt' => 'expencestype',         'field' => 'expencestype' )
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

// u  = tbl_expensepayment  (the cheque payment row - drives the report, paymentmethod = 3)
// et = tbl_expences_type   (expenses type master)
$joinQuery = "FROM `tbl_expensepayment` AS `u`
    LEFT JOIN `tbl_expences_type` AS `et` ON (`et`.`idtbl_expences_type` = `u`.`tbl_expensetype_idtbl_expensetype`)";

// paymentmethod = 3 -> cheque payments only.
// status = 1 so deactivated / removed payment rows don't leak in.
$extraWhere = "`u`.`paymentmethod` = 3 AND `u`.`status` = 1";

// Expenses type filter
if (!empty($_POST['search_expencestype'])) {
    $expencestypeID = (int) $_POST['search_expencestype'];
    $extraWhere .= " AND `u`.`tbl_expensetype_idtbl_expensetype` = $expencestypeID";
}

// Cheque status filter (1 = Pending, 2 = Realized, 3 = Returned)
if (!empty($_POST['search_cheque_status'])) {
    $chequeStatus = (int) $_POST['search_cheque_status'];
    $extraWhere .= " AND `u`.`cheque_status` = $chequeStatus";
}

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);