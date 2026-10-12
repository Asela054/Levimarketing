<?php
session_start();
/*
 * Server-side processing for the Daily Sales & Payment Reconciliation report.
 * One row per invoice line (tbl_invoice_detail), filtered by DATE RANGE.
 *
 * POST: search_from = "YYYY-MM-DD", search_to = "YYYY-MM-DD"
 *
 * Column definitions:
 *  - Quantity           = tbl_invoice_detail.qty
 *  - Rate per unit      = tbl_product.saleprice
 *  - Total sales value  = tbl_invoice_detail.saleprice (line total)
 *  - Payment received   = SUM(tbl_invoice_payment_detail.amount) of the invoice's payments,
 *                         shared across the invoice lines in proportion to line value
 *  - Payment method     = tbl_invoice_payment_detail.method
 *                         (1 Cash, 2 Card, 3 Cheque, 4 Online Transfer)
 *  - Outstanding        = (invoice payable - payment received), never below 0,
 *                         shared across the invoice lines in the same proportion
 *
 * Invoice payable = nettotal_with_vat, else nettotal, else total (first non-zero).
 */

$type       = $_SESSION['privatetype'];
$locationId = $_SESSION['location_id'];

$table      = 'tbl_invoice_detail';
$primaryKey = 'idtbl_invoice_detail';

// Total sales value = line total saved in invoice detail
$lineTotalExpr = "`d`.`saleprice`";

// This line's share of the whole invoice (sum of its active lines)
$shareExpr = "IF(`ls`.`lines_sum` > 0, (`d`.`saleprice` / `ls`.`lines_sum`), 0)";

// What the invoice must be paid in total
$payableExpr = "COALESCE(NULLIF(`u`.`nettotal_with_vat`, 0), NULLIF(`u`.`nettotal`, 0), `u`.`total`)";

$paidExpr    = "ROUND(COALESCE(`pay`.`paid`, 0) * $shareExpr, 2)";
$methodExpr  = "COALESCE(`mth`.`methods`, '')";
$balanceExpr = "ROUND(GREATEST($payableExpr - COALESCE(`pay`.`paid`, 0), 0) * $shareExpr, 2)";

$columns = array(
	array( 'db' => '`u`.`date`',           'dt' => 'date',      'field' => 'date' ),
	array( 'db' => '`ud`.`name`',          'dt' => 'name',      'field' => 'name' ),
	array( 'db' => '`p`.`product_name`',   'dt' => 'product',   'field' => 'product_name' ),
	array( 'db' => '`d`.`qty`',            'dt' => 'qty',       'field' => 'qty' ),
	array( 'db' => '`p`.`saleprice`',      'dt' => 'rate',      'field' => 'saleprice' ),
	array( 'db' => $lineTotalExpr,         'dt' => 'linetotal', 'field' => 'linetotal', 'as' => 'linetotal' ),
	array( 'db' => $paidExpr,              'dt' => 'paid',      'field' => 'paid',      'as' => 'paid' ),
	array( 'db' => $methodExpr,            'dt' => 'method',    'field' => 'methods',   'as' => 'methods' ),
	array( 'db' => $balanceExpr,           'dt' => 'balance',   'field' => 'balance',   'as' => 'balance' )
);

require('config.php');
$sql_details = array(
	'user' => $db_username,
	'pass' => $db_password,
	'db'   => $db_name,
	'host' => $db_host
);

require('ssp.customized.class.php');

/*
 * Derived tables (kept separate so amounts are never multiplied):
 *  ls  = sum of active line totals per invoice (used to share payments across lines)
 *  pd  = total amount per payment, from tbl_invoice_payment_detail
 *  pt  = how many invoices each payment is linked to (and the weight total), used only to
 *        avoid counting a multi-invoice payment more than once
 *  pay = total paid per invoice = sum of its payments' detail amounts
 *  mth = payment method(s) used per invoice
 */
$joinQuery = "FROM `tbl_invoice_detail` AS `d`
	INNER JOIN `tbl_invoice` AS `u` ON (`u`.`idtbl_invoice` = `d`.`tbl_invoice_idtbl_invoice`)
	LEFT JOIN `tbl_customer` AS `ud` ON (`ud`.`idtbl_customer` = `u`.`customerid`)
	LEFT JOIN `tbl_product` AS `p` ON (`p`.`idtbl_product` = `d`.`tbl_product_idtbl_product`)
	LEFT JOIN (
		SELECT `tbl_invoice_idtbl_invoice` AS `inv`, SUM(`saleprice`) AS `lines_sum`
		FROM `tbl_invoice_detail`
		WHERE `status` = 1
		GROUP BY `tbl_invoice_idtbl_invoice`
	) AS `ls` ON (`ls`.`inv` = `u`.`idtbl_invoice`)
	LEFT JOIN (
		SELECT `lk`.`tbl_invoice_idtbl_invoice` AS `inv`,
			SUM(COALESCE(`pd`.`amt`, 0) * IF(`pt`.`wsum` > 0, `lk`.`payamount` / `pt`.`wsum`, 1 / `pt`.`cnt`)) AS `paid`
		FROM `tbl_invoice_payment_has_tbl_invoice` AS `lk`
		INNER JOIN `tbl_invoice_payment` AS `ip`
			ON (`ip`.`idtbl_invoice_payment` = `lk`.`tbl_invoice_payment_idtbl_invoice_payment`)
		INNER JOIN (
			SELECT `tbl_invoice_payment_idtbl_invoice_payment` AS `pid`,
				COUNT(*) AS `cnt`, SUM(`payamount`) AS `wsum`
			FROM `tbl_invoice_payment_has_tbl_invoice`
			GROUP BY `tbl_invoice_payment_idtbl_invoice_payment`
		) AS `pt` ON (`pt`.`pid` = `ip`.`idtbl_invoice_payment`)
		LEFT JOIN (
			SELECT `tbl_invoice_payment_idtbl_invoice_payment` AS `pid`, SUM(`amount`) AS `amt`
			FROM `tbl_invoice_payment_detail`
			WHERE `status` IN (0,1)
			GROUP BY `tbl_invoice_payment_idtbl_invoice_payment`
		) AS `pd` ON (`pd`.`pid` = `ip`.`idtbl_invoice_payment`)
		WHERE `ip`.`status` IN (0,1)
		GROUP BY `lk`.`tbl_invoice_idtbl_invoice`
	) AS `pay` ON (`pay`.`inv` = `u`.`idtbl_invoice`)
	LEFT JOIN (
		SELECT `lk2`.`tbl_invoice_idtbl_invoice` AS `inv`,
			GROUP_CONCAT(DISTINCT CASE `ipd`.`method`
				WHEN 1 THEN 'Cash'
				WHEN 2 THEN 'Card'
				WHEN 3 THEN 'Cheque'
				WHEN 4 THEN 'Online Transfer'
				ELSE 'Other' END
				ORDER BY `ipd`.`method` SEPARATOR ', ') AS `methods`
		FROM `tbl_invoice_payment_has_tbl_invoice` AS `lk2`
		INNER JOIN `tbl_invoice_payment` AS `ip2`
			ON (`ip2`.`idtbl_invoice_payment` = `lk2`.`tbl_invoice_payment_idtbl_invoice_payment`)
		INNER JOIN `tbl_invoice_payment_detail` AS `ipd`
			ON (`ipd`.`tbl_invoice_payment_idtbl_invoice_payment` = `ip2`.`idtbl_invoice_payment`)
		WHERE `ip2`.`status` IN (0,1) AND `ipd`.`status` IN (0,1)
		GROUP BY `lk2`.`tbl_invoice_idtbl_invoice`
	) AS `mth` ON (`mth`.`inv` = `u`.`idtbl_invoice`)";

$extraWhere = "`d`.`status` = 1 AND `u`.`status` IN (0,1)";

if ($type == 1) {
	$extraWhere .= " AND `u`.`manuelinvno` IS NOT NULL";
}

// Date range filter (validated YYYY-MM-DD, defaults to today)
$datePattern = '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/';

$from = (!empty($_POST['search_from']) && preg_match($datePattern, $_POST['search_from'])) ? $_POST['search_from'] : date('Y-m-d');
$to   = (!empty($_POST['search_to'])   && preg_match($datePattern, $_POST['search_to']))   ? $_POST['search_to']   : $from;

if ($to < $from) { $to = $from; }

$extraWhere .= " AND `u`.`date` >= '" . $from . "' AND `u`.`date` <= '" . $to . "'";

// Location filter
if (!empty($locationId)) {
	$extraWhere .= " AND `u`.`tbl_location_idtbl_location` = " . intval($locationId);
}

echo json_encode(
	SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);