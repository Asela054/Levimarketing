<?php

session_start();

/*
 * Server-side processing for the Invoice Payment report.
 *
 * FIXES applied vs the previous version of this script:
 * 1. $extraWhere was only ever set INSIDE the date-filter if/elseif branches -
 *    if none of search_date / search_week / search_month / search_from_date+
 *    search_to_date were posted (e.g. first page load before any filter is
 *    chosen), $extraWhere was undefined and passed to SSP::simple() as such.
 *    It's now given a base value up front and the branches override it.
 * 2. The old direct `LEFT JOIN tbl_invoice_payment_detail AS uc` was declared
 *    but never actually used in $columns or $extraWhere - dead join, removed.
 *    A Payment Method filter now uses an EXISTS subquery instead of a JOIN,
 *    since joining payment-detail rows directly would multiply each payment
 *    row once per cash/card/cheque line on that payment (in addition to the
 *    existing per-invoice duplication from the tbl_invoice_payment_has_tbl_invoice
 *    bridge below), which would silently inflate the Total/Payamount/Payment/
 *    Balance figures shown per row.
 * 3. Invoice ID now resolves to the invoice's tax/manual invoice number
 *    (COALESCE(taxinvoice_no, manuelinvno, idtbl_invoice)) instead of the raw
 *    foreign key idtbl_invoice, matching the other invoice-related reports.
 * 4. Added Customer (name + filter) via tbl_invoice -> tbl_customer.
 * 5. NEW: When a Payment Method filter is applied, the PAYMENT column no
 *    longer shows the full payment-header amount (`u`.`payment`), which can
 *    include multiple methods split across one payment (e.g. half Cash,
 *    half Card). It now shows only the amount actually paid via the
 *    selected method, sourced from a LEFT JOINed derived table that sums
 *    `tbl_invoice_payment_detail.amount` per payment, per method. This
 *    mirrors the same fix applied to the Daily Cash Collection report.
 *    TOTAL / DISCOUNT / PAYAMOUNT (from the invoice-payment bridge) and
 *    BALANCE (payment-level remaining balance) are unaffected - they are
 *    not payment-method-specific figures.
 *
 * NOTE ON ROW DUPLICATION: `tbl_invoice_payment_has_tbl_invoice` is a
 * many-to-many bridge (a payment can be split across more than one invoice),
 * so a payment applied to 2 invoices will legitimately appear as 2 rows here,
 * one per invoice - same convention as the cheque collection report.
 */

$table = 'tbl_invoice_payment';
$primaryKey = 'idtbl_invoice_payment';

// ================================
// Read payment method filter FIRST
// (needed before building $columns / $joinQuery)
// ================================
$filterpaymentmethod = null;
if (isset($_POST['filterpaymentmethod']) && $_POST['filterpaymentmethod'] !== '') {
    $filterpaymentmethod = intval($_POST['filterpaymentmethod']);
}

// Invoice number: prefer taxinvoice_no, fall back to manuelinvno, then
// idtbl_invoice as a last resort. Aliased explicitly since it's a computed
// expression - ssp.customized.class.php reads rows back by the raw fetched
// column name, and a bare expression with no AS would come back keyed by
// the whole expression text rather than the intended 'invoiceno'.
$invoiceNoExpr = "COALESCE(NULLIF(`i`.`taxinvoice_no`, ''), `i`.`manuelinvno`, `i`.`idtbl_invoice`)";

// PAYMENT column source: full payment-header amount normally, or just the
// amount paid via the selected method when that filter is active.
if ($filterpaymentmethod !== null) {
    $paymentDb = '`pm`.`method_total`';
} else {
    $paymentDb = '`u`.`payment`';
}

// Array of database columns which should be read and sent back to DataTables.
// Any column whose 'dt' differs from its raw DB column name - or whose DB
// column name could collide with another selected column - carries an
// explicit 'as' so ssp.customized.class.php can find it correctly.
$columns = array(
	array( 'db' => '`u`.`idtbl_invoice_payment`', 'dt' => 'idtbl_invoice_payment', 'field' => 'idtbl_invoice_payment' ),
	array( 'db' => $invoiceNoExpr,                'dt' => 'invoiceno',            'field' => 'invoiceno',   'as' => 'invoiceno' ),
	array( 'db' => '`c`.`name`',                  'dt' => 'customername',         'field' => 'customername','as' => 'customername' ),
	array( 'db' => '`u`.`date`',                  'dt' => 'date',                 'field' => 'date' ),
	array( 'db' => '`ud`.`total`',                'dt' => 'total',                'field' => 'total' ),
	array( 'db' => '`ud`.`discount`',             'dt' => 'discount',             'field' => 'discount' ),
	array( 'db' => '`ud`.`payamount`',            'dt' => 'payamount',            'field' => 'payamount' ),
	array( 'db' => $paymentDb,                    'dt' => 'payment',              'field' => 'payment',     'as' => 'payment' ),
	array( 'db' => '`u`.`balance`',               'dt' => 'balance',              'field' => 'balance' )
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

// u  = tbl_invoice_payment                      (payment header)
// ud = tbl_invoice_payment_has_tbl_invoice       (bridge - which invoice(s), and how much of the payment applied to each)
// i  = tbl_invoice                               (invoice header - for the invoice number + customer link)
// c  = tbl_customer
// pm = derived table - SUM(amount) per payment, for the selected payment method only (only joined when that filter is active)
$joinQuery = "FROM `tbl_invoice_payment` AS `u`
    LEFT JOIN `tbl_invoice_payment_has_tbl_invoice` AS `ud` ON (`ud`.`tbl_invoice_payment_idtbl_invoice_payment` = `u`.`idtbl_invoice_payment`)
    LEFT JOIN `tbl_invoice` AS `i` ON (`i`.`idtbl_invoice` = `ud`.`tbl_invoice_idtbl_invoice`)
    LEFT JOIN `tbl_customer` AS `c` ON (`c`.`idtbl_customer` = `i`.`customerid`)";

if ($filterpaymentmethod !== null) {
    $joinQuery .= "
    LEFT JOIN (
        SELECT
            `ipd`.`tbl_invoice_payment_idtbl_invoice_payment` AS `payment_id`,
            SUM(`ipd`.`amount`) AS `method_total`
        FROM `tbl_invoice_payment_detail` AS `ipd`
        WHERE `ipd`.`method` = " . $filterpaymentmethod . "
        GROUP BY `ipd`.`tbl_invoice_payment_idtbl_invoice_payment`
    ) AS `pm`
    ON `pm`.`payment_id` = `u`.`idtbl_invoice_payment`
    ";
}

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
    $extraWhere .= " AND `c`.`idtbl_customer` = " . $search_customer;
}

// Payment Method filter (1 = Cash, 2 = Cheque, 3 = Card, 4 = Online Transfer)
// Still needed to restrict WHICH rows appear at all - the LEFT JOIN to `pm`
// above alone wouldn't exclude payments that don't have that method.
if ($filterpaymentmethod !== null) {
    $extraWhere .= " AND EXISTS (
        SELECT 1 FROM `tbl_invoice_payment_detail` AS `ipd2`
        WHERE `ipd2`.`tbl_invoice_payment_idtbl_invoice_payment` = `u`.`idtbl_invoice_payment`
          AND `ipd2`.`method` = " . $filterpaymentmethod . "
    )";
}

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);