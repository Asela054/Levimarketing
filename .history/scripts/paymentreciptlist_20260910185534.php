<?php

session_start();

/*
 * Server-side processing for the Payment Receipt report.
 * One row per payment-detail line (uc), joined to the invoice(s) that
 * payment covers via the bridge table, and to the customer through the
 * invoice. This report is deliberately receipt-level (unlike the Invoice
 * Payment report, which is payment-header level), so joining `uc` directly
 * - rather than via EXISTS - is correct here.
 *
 * FIXES applied vs the previous version of this script:
 * 1. $extraWhere was only ever set INSIDE the date-filter if/elseif branches -
 *    on first page load (no date filter posted yet) it was undefined. It now
 *    always has a base value up front, and the date branches override it.
 * 2. Invoice ID now resolves to the invoice's tax/manual invoice number
 *    (COALESCE(taxinvoice_no, manuelinvno, idtbl_invoice)) instead of the raw
 *    foreign key idtbl_invoice, matching the other invoice-related reports.
 * 3. Added Customer + Payment Method filters, using the `ue`/`uc` joins that
 *    were already present but only used for display, not filtering.
 */

$table = 'tbl_invoice_payment';
$primaryKey = 'idtbl_invoice_payment';

// Invoice number: prefer taxinvoice_no, fall back to manuelinvno, then
// idtbl_invoice as a last resort. Aliased explicitly since it's a computed
// expression - ssp.customized.class.php reads rows back by the raw fetched
// column name, and a bare expression with no AS would come back keyed by
// the whole expression text rather than the intended 'invoiceno'.
$invoiceNoExpr = "COALESCE(NULLIF(`ub`.`taxinvoice_no`, ''), `ub`.`manuelinvno`, `ub`.`idtbl_invoice`)";

$columns = array(
	array( 'db' => '`u`.`idtbl_invoice_payment`', 'dt' => 'idtbl_invoice_payment', 'field' => 'idtbl_invoice_payment' ),
    array( 'db' => '`ud`.`manuelinvno`', 'dt' => 'id', 'field' => 'manuelinvno' ),
    array( 'db' => '`ud`.`taxinvoice_no`', 'dt' => 'taxinvoice_no', 'field' => 'taxinvoice_no' ),
	array( 'db' => $invoiceNoExpr,                'dt' => 'invoiceno',            'field' => 'invoiceno', 'as' => 'invoiceno' ),
	array( 'db' => '`u`.`date`',                  'dt' => 'date',                 'field' => 'date' ),
	array( 'db' => '`ue`.`name`',                 'dt' => 'name',                 'field' => 'name' ),
	array( 'db' => '`uc`.`method`',               'dt' => 'method',               'field' => 'method' ),
	array( 'db' => '`uc`.`bank`',                 'dt' => 'bank',                 'field' => 'bank' ),
	array( 'db' => '`uc`.`receiptno`',            'dt' => 'receiptno',            'field' => 'receiptno' ),
	array( 'db' => '`uc`.`chequeno`',             'dt' => 'chequeno',             'field' => 'chequeno' ),
	array( 'db' => '`uc`.`chequedate`',           'dt' => 'chequedate',           'field' => 'chequedate' ),
	array( 'db' => '`uc`.`amount`',               'dt' => 'amount',               'field' => 'amount' )
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

// u  = tbl_invoice_payment                (payment header)
// ud = tbl_invoice_payment_has_tbl_invoice (bridge - which invoice(s) this payment covers)
// uc = tbl_invoice_payment_detail          (the receipt/payment-detail line - method, bank, receipt/cheque info, amount)
// ub = tbl_invoice                         (invoice header - for the invoice number + customer link)
// ue = tbl_customer
$joinQuery = "FROM `tbl_invoice_payment` AS `u`
    LEFT JOIN `tbl_invoice_payment_has_tbl_invoice` AS `ud` ON (`ud`.`tbl_invoice_payment_idtbl_invoice_payment` = `u`.`idtbl_invoice_payment`)
    LEFT JOIN `tbl_invoice_payment_detail` AS `uc` ON (`uc`.`tbl_invoice_payment_idtbl_invoice_payment` = `u`.`idtbl_invoice_payment`)
    LEFT JOIN `tbl_invoice` AS `ub` ON (`ub`.`idtbl_invoice` = `ud`.`tbl_invoice_idtbl_invoice`)
    LEFT JOIN `tbl_customer` AS `ue` ON (`ue`.`idtbl_customer` = `ub`.`customerid`)";

// Base clause - always defined, regardless of which (if any) date filter is posted.
$extraWhere = "`u`.`status` IN (0,1)";

if (!empty($_POST['search_date'])) {
    $date = $_POST['search_date'];
    $extraWhere = "`u`.`status` IN (0,1) AND `u`.`date` = '$date'";
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

    $extraWhere = "`u`.`status` IN (0,1) AND `u`.`date` BETWEEN '$startDate' AND '$endDate'";
} elseif (!empty($_POST['search_month'])) {
    $month = $_POST['search_month'];
    $month_arr = explode('-', $month);
    $extraWhere = "`u`.`status` IN (0,1) AND YEAR(`u`.`date`) = '$month_arr[0]' AND MONTH(`u`.`date`) = '$month_arr[1]'";
} elseif (!empty($_POST['search_from_date']) && !empty($_POST['search_to_date'])) {
    $from_date = $_POST['search_from_date'];
    $to_date = $_POST['search_to_date'];
    $extraWhere = "`u`.`status` IN (0,1) AND `u`.`date` BETWEEN '$from_date' AND '$to_date'";
}

// Customer filter
if (!empty($_POST['search_customer'])) {
    $search_customer = intval($_POST['search_customer']);
    $extraWhere .= " AND `ue`.`idtbl_customer` = " . $search_customer;
}

// Payment Method filter (1 = Cash, 2 = Cheque, 3 = Card, 4 = Online Transfer)
if (isset($_POST['filterpaymentmethod']) && $_POST['filterpaymentmethod'] !== '') {
    $filterpaymentmethod = intval($_POST['filterpaymentmethod']);
    $extraWhere .= " AND `uc`.`method` = " . $filterpaymentmethod;
}

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);