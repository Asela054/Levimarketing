<?php

session_start();
/*
 * Server-side processing for the Invoice Report (rptinvoiceviewlist.php DataTable).
 *
 * FILTERS - Customer, Payment Method, and Invoice Type only. There are no
 * date filters - use the DataTables search box / column sorting for
 * anything else. If you ever want date filtering back, add the usual
 * search_date / search_week / search_month / search_from_date+search_to_date
 * block against `u`.`date`, same as before.
 *
 * INVOICE TYPE FILTER - `u`.`invtype`: 0 = Non-Tax Invoice, 1 = Tax Invoice.
 */

$type =  $_SESSION['privatetype'];
$locationId = $_SESSION['location_id'];

$table = 'tbl_invoice';
$primaryKey = 'idtbl_invoice';

// Invoice number: prefer taxinvoice_no, fall back to manuelinvno, then idtbl_invoice
// as a last resort. Aliased explicitly to `id` since it's a computed expression -
// ssp.customized.class.php reads rows back by raw fetched column name, and a bare
// expression with no AS would come back keyed by the whole expression text, not 'id'.
$invoiceNoExpr = "COALESCE(NULLIF(`u`.`taxinvoice_no`, ''), `u`.`manuelinvno`, `u`.`idtbl_invoice`)";

if($type==1){
	$columns = array(
		array( 'db' => $invoiceNoExpr, 'dt' => 'id', 'field' => 'id', 'as' => 'id' ),
		array( 'db' => '`ud`.`name`',   'dt' => 'name', 'field' => 'name' ),
		array( 'db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date' ),
		array( 'db' => '`u`.`saletype`', 'dt' => 'saletype', 'field' => 'saletype' ),
		array( 'db' => '`u`.`total`', 'dt' => 'total', 'field' => 'total' )
	);
}else{
	$columns = array(
		array( 'db' => $invoiceNoExpr, 'dt' => 'id', 'field' => 'id', 'as' => 'id' ),
		array( 'db' => '`ud`.`name`',   'dt' => 'name', 'field' => 'name' ),
		array( 'db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date' ),
		array( 'db' => '`u`.`saletype`', 'dt' => 'saletype', 'field' => 'saletype' ),
		array( 'db' => '`u`.`total`', 'dt' => 'total', 'field' => 'total' )
	);
}

require('config.php');
$sql_details = array(
	'user' => $db_username,
	'pass' => $db_password,
	'db'   => $db_name,
	'host' => $db_host
);

require('ssp.customized.class.php' );

$joinQuery = "FROM `tbl_invoice` AS `u` LEFT JOIN `tbl_customer` AS `ud` ON (`ud`.`idtbl_customer` = `u`.`customerid`)";

// Base clause. For $type==1 (manual invoice numbers only) keep restricting to
// rows that actually have a manuelinvno, same as before.
if($type==1){
	$extraWhere = "`u`.`status` IN (0,1) AND `u`.`manuelinvno` IS NOT NULL";
}else{
	$extraWhere = "`u`.`status` IN (0,1)";
}

// Customer filter
if(!empty($_POST['search_customer'])){
	$search_customer = intval($_POST['search_customer']);
	$extraWhere .= " AND `u`.`customerid` = " . $search_customer;
}

// Payment Method filter (1 = Cash, 2 = Card, 3 = Cheque, 4 = Online Transfer) — EXISTS pattern
// since an invoice can have more than one payment line and a join would duplicate rows.
if(isset($_POST['filterpaymentmethod']) && $_POST['filterpaymentmethod'] !== ''){
	$filterpaymentmethod = intval($_POST['filterpaymentmethod']);
	$extraWhere .= " AND EXISTS (
		SELECT 1
		FROM `tbl_invoice_payment_has_tbl_invoice` AS `iphi`
		INNER JOIN `tbl_invoice_payment_detail` AS `ipd`
			ON `ipd`.`tbl_invoice_payment_idtbl_invoice_payment` = `iphi`.`tbl_invoice_payment_idtbl_invoice_payment`
		WHERE `iphi`.`tbl_invoice_idtbl_invoice` = `u`.`idtbl_invoice`
		  AND `ipd`.`method` = " . $filterpaymentmethod . "
	)";
}

// Invoice Type filter (0 = Non-Tax Invoice, 1 = Tax Invoice)
if(isset($_POST['filterinvtype']) && $_POST['filterinvtype'] !== ''){
	$filterinvtype = intval($_POST['filterinvtype']);
	$extraWhere .= " AND `u`.`invtype` = " . $filterinvtype;
}

// Location filter - restrict every query to the logged-in user's location
if (!empty($locationId)) {
	$extraWhere .= " AND `u`.`tbl_location_idtbl_location` = " . intval($locationId);
}

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
