<?php
session_start();
/*
 * Bin Card - one row per product.
 * POST: search_from, search_to (YYYY-MM-DD)
 *
 *  GRN qty/value   = tbl_grndetail (qty, total) in date range
 *  Issue qty/value = tbl_invoice_detail (qty + freeqty, saleprice) in date range
 *  Current stock   = SUM(tbl_stock.qty)
 *  Remain value    = current stock * tbl_product.unitprice (cost)
 */

$type       = $_SESSION['privatetype'];
$locationId = $_SESSION['location_id'];

$table      = 'tbl_product';
$primaryKey = 'idtbl_product';

// ---- dates ----
$datePattern = '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/';
$from = (!empty($_POST['search_from']) && preg_match($datePattern, $_POST['search_from'])) ? $_POST['search_from'] : date('Y-m-01');
$to   = (!empty($_POST['search_to'])   && preg_match($datePattern, $_POST['search_to']))   ? $_POST['search_to']   : date('Y-m-d');
if ($to < $from) { $to = $from; }

$locGrn = $locInv = $locStk = '';
if (!empty($locationId)) {
	$loc    = intval($locationId);
	$locGrn = " AND `g`.`tbl_location_idtbl_location` = $loc";
	$locInv = " AND `u`.`tbl_location_idtbl_location` = $loc";
	$locStk = " AND `tbl_location_idtbl_location` = $loc";
}
$manualOnly = ($type == 1) ? " AND `u`.`manuelinvno` IS NOT NULL" : "";

// ---- expressions ----
$grnQtyExpr   = "COALESCE(`gr`.`qty`, 0)";
$grnValExpr   = "COALESCE(`gr`.`total`, 0)";
$issQtyExpr   = "COALESCE(`iv`.`qty`, 0)";
$issValExpr   = "COALESCE(`iv`.`total`, 0)";
$stockExpr    = "COALESCE(`st`.`qty`, 0)";
$remainExpr   = "ROUND(COALESCE(`st`.`qty`, 0) * `p`.`unitprice`, 2)";

$columns = array(
	array( 'db' => '`p`.`product_name`', 'dt' => 'product',     'field' => 'product_name' ),
	array( 'db' => '`p`.`saleprice`',    'dt' => 'saleprice',   'field' => 'saleprice' ),
	array( 'db' => '`p`.`unitprice`',    'dt' => 'costprice',   'field' => 'unitprice' ),
	array( 'db' => $grnQtyExpr,          'dt' => 'grnqty',      'field' => 'grnqty',      'as' => 'grnqty' ),
	array( 'db' => $grnValExpr,          'dt' => 'grnvalue',    'field' => 'grnvalue',    'as' => 'grnvalue' ),
	array( 'db' => $issQtyExpr,          'dt' => 'issueqty',    'field' => 'issueqty',    'as' => 'issueqty' ),
	array( 'db' => $issValExpr,          'dt' => 'issuevalue',  'field' => 'issuevalue',  'as' => 'issuevalue' ),
	array( 'db' => $stockExpr,           'dt' => 'stockqty',    'field' => 'stockqty',    'as' => 'stockqty' ),
	array( 'db' => $remainExpr,          'dt' => 'remainvalue', 'field' => 'remainvalue', 'as' => 'remainvalue' ),
	array( 'db' => '`p`.`idtbl_product`','dt' => 'id',          'field' => 'idtbl_product' )
);

require('config.php');
$sql_details = array(
	'user' => $db_username,
	'pass' => $db_password,
	'db'   => $db_name,
	'host' => $db_host
);

require('ssp.customized.class.php');

// Derived tables keep one row per product so nothing is multiplied.
// NOTE: if your GRNs only affect stock after confirmation, keep confirm_status = 1; otherwise remove it.
$joinQuery = "FROM `tbl_product` AS `p`
	LEFT JOIN (
		SELECT `gd`.`tbl_product_idtbl_product` AS `pid`, SUM(`gd`.`qty`) AS `qty`, SUM(`gd`.`total`) AS `total`
		FROM `tbl_grndetail` AS `gd`
		INNER JOIN `tbl_grn` AS `g` ON (`g`.`idtbl_grn` = `gd`.`tbl_grn_idtbl_grn`)
		WHERE `gd`.`status` = 1 AND `g`.`status` = 1 AND `g`.`confirm_status` = 1
		  AND `g`.`date` >= '$from' AND `g`.`date` <= '$to' $locGrn
		GROUP BY `gd`.`tbl_product_idtbl_product`
	) AS `gr` ON (`gr`.`pid` = `p`.`idtbl_product`)
	LEFT JOIN (
		SELECT `d`.`tbl_product_idtbl_product` AS `pid`, SUM(`d`.`qty` + `d`.`freeqty`) AS `qty`, SUM(`d`.`saleprice`) AS `total`
		FROM `tbl_invoice_detail` AS `d`
		INNER JOIN `tbl_invoice` AS `u` ON (`u`.`idtbl_invoice` = `d`.`tbl_invoice_idtbl_invoice`)
		WHERE `d`.`status` = 1 AND `u`.`status` IN (0,1)
		  AND `u`.`date` >= '$from' AND `u`.`date` <= '$to' $locInv $manualOnly
		GROUP BY `d`.`tbl_product_idtbl_product`
	) AS `iv` ON (`iv`.`pid` = `p`.`idtbl_product`)
	LEFT JOIN (
		SELECT `tbl_product_idtbl_product` AS `pid`, SUM(`qty`) AS `qty`
		FROM `tbl_stock`
		WHERE `status` = 1 $locStk
		GROUP BY `tbl_product_idtbl_product`
	) AS `st` ON (`st`.`pid` = `p`.`idtbl_product`)";

// Only products that had movement in the range or still have stock
$extraWhere = "`p`.`status` = 1 AND (COALESCE(`gr`.`qty`,0) <> 0 OR COALESCE(`iv`.`qty`,0) <> 0 OR COALESCE(`st`.`qty`,0) <> 0)";

echo json_encode(
	SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);