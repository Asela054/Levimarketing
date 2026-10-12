<?php
session_start();
/*
 * Server-side processing for the Daily Sales & Payment Reconciliation report.
 * One row per invoice line (tbl_invoice_detail), filtered by DATE RANGE.
 *
 * POST: search_from = "YYYY-MM-DD", search_to = "YYYY-MM-DD"
 *
 * Column definitions:
 *  - Quantity             = tbl_invoice_detail.qty
 *  - Rate per unit        = tbl_product.saleprice (product table)
 *  - Total sales value    = tbl_invoice_detail.saleprice (line total saved on the invoice)
 *  - Payment received     = SUM(payamount) in tbl_invoice_payment_has_tbl_invoice for the invoice
 *  - Payment method       = tbl_invoice_payment_detail.method
 *                           (1 Cash, 2 Card, 3 Cheque, 4 Online Transfer)
 *  - Outstanding/Balance  = invoice total (nettotal_with_vat) - payment received
 *
 * NOTE: payments are stored per invoice, not per line. To avoid counting the
 * same payment once for every product line, Payment Received, Payment Method
 * and Outstanding are shown ONLY on the first line of each invoice
 * (the other lines show 0 / blank). Column totals therefore stay correct.
 */

$type       = $_SESSION['privatetype'];
$locationId = $_SESSION['location_id'];

$table      = 'tbl_invoice_detail';
$primaryKey = 'idtbl_invoice_detail';

// Total sales value = line total saved in invoice detail
$lineTotalExpr = "`d`.`saleprice`";

// First line of the invoice? (payment info is shown on that line only)
$isFirst = "`d`.`idtbl_invoice_detail` = `fl`.`first_id`";

$paidExpr    = "IF($isFirst, COALESCE(`pay`.`paid`, 0), 0)";
$methodExpr  = "IF($isFirst, COALESCE(`mth`.`methods`, ''), '')";
$balanceExpr = "IF($isFirst, (`u`.`nettotal_with_vat` - COALESCE(`pay`.`paid`, 0)), 0)";

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
 * Derived tables (kept separate so payment amounts are never multiplied):
 *  fl  = first invoice line id per invoice
 *  pay = total paid per invoice
 *  mth = payment method(s) used per invoice
 *
 * Payment method codes:
 *  1 = Cash, 2 = Card, 3 = Cheque, 4 = Online Transfer
 */
$joinQuery = "FROM `tbl_invoice_detail` AS `d`
	INNER JOIN `tbl_invoice` AS `u` ON (`u`.`idtbl_invoice` = `d`.`tbl_invoice_idtbl_invoice`)
	LEFT JOIN `tbl_customer` AS `ud` ON (`ud`.`idtbl_customer` = `u`.`customerid`)
	LEFT JOIN `tbl_product` AS `p` ON (`p`.`idtbl_product` = `d`.`tbl_product_idtbl_product`)
	LEFT JOIN (
		SELECT `tbl_invoice_idtbl_invoice` AS `inv`, MIN(`idtbl_invoice_detail`) AS `first_id`
		FROM `tbl_invoice_detail`
		WHERE `status` = 1
		GROUP BY `tbl_invoice_idtbl_invoice`
	) AS `fl` ON (`fl`.`inv` = `u`.`idtbl_invoice`)
	LEFT JOIN (
		SELECT `pi`.`tbl_invoice_idtbl_invoice` AS `inv`, SUM(`pi`.`payamount`) AS `paid`
		FROM `tbl_invoice_payment_has_tbl_invoice` AS `pi`
		INNER JOIN `tbl_invoice_payment` AS `ip`
			ON (`ip`.`idtbl_invoice_payment` = `pi`.`tbl_invoice_payment_idtbl_invoice_payment`)
		WHERE `ip`.`status` = 1
		GROUP BY `pi`.`tbl_invoice_idtbl_invoice`
	) AS `pay` ON (`pay`.`inv` = `u`.`idtbl_invoice`)
	LEFT JOIN (
		SELECT `pi2`.`tbl_invoice_idtbl_invoice` AS `inv`,
			GROUP_CONCAT(DISTINCT CASE `ipd`.`method`
				WHEN 1 THEN 'Cash'
				WHEN 2 THEN 'Card'
				WHEN 3 THEN 'Cheque'
				WHEN 4 THEN 'Online Transfer'
				ELSE 'Other' END
				ORDER BY `ipd`.`method` SEPARATOR ', ') AS `methods`
		FROM `tbl_invoice_payment_has_tbl_invoice` AS `pi2`
		INNER JOIN `tbl_invoice_payment` AS `ip2`
			ON (`ip2`.`idtbl_invoice_payment` = `pi2`.`tbl_invoice_payment_idtbl_invoice_payment`)
		INNER JOIN `tbl_invoice_payment_detail` AS `ipd`
			ON (`ipd`.`tbl_invoice_payment_idtbl_invoice_payment` = `ip2`.`idtbl_invoice_payment`)
		WHERE `ip2`.`status` = 1 AND `ipd`.`status` = 1
		GROUP BY `pi2`.`tbl_invoice_idtbl_invoice`
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