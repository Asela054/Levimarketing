<?php
session_start();
/*
 * Server-side processing for the Full Detail Sale Report.
 * One row per invoice line (tbl_invoice_detail), filtered by MONTH only.
 *
 * POST: search_month = "YYYY-MM" (from <input type="month">)
 *
 * Prices come from tbl_product (NOT from tbl_invoice_detail):
 *   tbl_invoice.saletype = 1 (retail)    -> tbl_product.saleprice
 *   tbl_invoice.saletype = 2 (wholesale) -> tbl_product.wholesaleprice
 */

$type       = $_SESSION['privatetype'];
$locationId = $_SESSION['location_id'];

$table      = 'tbl_invoice_detail';
$primaryKey = 'idtbl_invoice_detail';

$invoiceNoExpr = "COALESCE(NULLIF(`u`.`taxinvoice_no`, ''), `u`.`manuelinvno`, `u`.`idtbl_invoice`)";

// Selling price from the product table, chosen by the invoice sale type
$salePriceExpr = "(CASE `u`.`saletype` WHEN 2 THEN `p`.`wholesaleprice` ELSE `p`.`saleprice` END)";

// Line total = qty x selling price (free qty is not charged)
$lineTotalExpr = "(`d`.`qty` * $salePriceExpr)";

$columns = array(
	array( 'db' => $invoiceNoExpr,            'dt' => 'id',        'field' => 'id', 'as' => 'id' ),
	array( 'db' => '`u`.`date`',              'dt' => 'date',      'field' => 'date' ),
	array( 'db' => '`ud`.`name`',             'dt' => 'name',      'field' => 'name' ),
	array( 'db' => '`u`.`saletype`',          'dt' => 'saletype',  'field' => 'saletype' ),
	array( 'db' => '`p`.`product_name`',      'dt' => 'product',   'field' => 'product_name' ),
	array( 'db' => '`d`.`qty`',               'dt' => 'qty',       'field' => 'qty' ),
	array( 'db' => '`d`.`freeqty`',           'dt' => 'freeqty',   'field' => 'freeqty' ),
	array( 'db' => '`p`.`unitprice`',         'dt' => 'unitprice', 'field' => 'unitprice' ),
	array( 'db' => $salePriceExpr,            'dt' => 'saleprice', 'field' => 'saleprice', 'as' => 'saleprice' ),
	array( 'db' => '`d`.`discountamount`',    'dt' => 'discount',  'field' => 'discountamount' ),
	array( 'db' => $lineTotalExpr,            'dt' => 'linetotal', 'field' => 'linetotal', 'as' => 'linetotal' )
);

require('config.php');
$sql_details = array(
	'user' => $db_username,
	'pass' => $db_password,
	'db'   => $db_name,
	'host' => $db_host
);

require('ssp.customized.class.php');

$joinQuery = "FROM `tbl_invoice_detail` AS `d`
	INNER JOIN `tbl_invoice` AS `u` ON (`u`.`idtbl_invoice` = `d`.`tbl_invoice_idtbl_invoice`)
	LEFT JOIN `tbl_customer` AS `ud` ON (`ud`.`idtbl_customer` = `u`.`customerid`)
	LEFT JOIN `tbl_product` AS `p` ON (`p`.`idtbl_product` = `d`.`tbl_product_idtbl_product`)";

$extraWhere = "`d`.`status` = 1 AND `u`.`status` IN (0,1)";

if ($type == 1) {
	$extraWhere .= " AND `u`.`manuelinvno` IS NOT NULL";
}

// Month filter (index-friendly range instead of DATE_FORMAT)
if (!empty($_POST['search_month']) && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $_POST['search_month'])) {
	$monthStart = $_POST['search_month'] . '-01';
	$monthEnd   = date('Y-m-d', strtotime($monthStart . ' +1 month'));
	$extraWhere .= " AND `u`.`date` >= '" . $monthStart . "' AND `u`.`date` < '" . $monthEnd . "'";
}

// Location filter
if (!empty($locationId)) {
	$extraWhere .= " AND `u`.`tbl_location_idtbl_location` = " . intval($locationId);
}

echo json_encode(
	SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);