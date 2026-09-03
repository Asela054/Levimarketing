<?php
session_start();
/*
 * Server-side processing for the Cheque Collection report (rptchequecollection.php DataTable).
 * One row per cheque payment-detail line, joined through the payment/invoice
 * bridge table to the invoice it was applied to. Follows the same
 * ssp.customized.class.php pattern used in grnlist.php / grnreportdetail.php.
 *
 * IMPORTANT - about row duplication:
 * `tbl_invoice_payment_has_tbl_invoice` is a many-to-many bridge - a single
 * payment (and therefore a single cheque) can be split across more than one
 * invoice. This report is written "full detail wise" - one row per
 * cheque-per-invoice - so a cheque that paid 2 invoices will legitimately
 * appear as 2 rows here. `pd.amount` is always the FULL cheque value;
 * `iph.payamount` is the portion of that cheque applied to THAT invoice, so
 * the two columns are intentionally different. If you'd rather collapse
 * multi-invoice cheques into a single row, GROUP_CONCAT the invoice numbers
 * instead of joining tbl_invoice directly - ask and I'll adjust it.
 *
 * FILTERS - trimmed down to just Customer + Date (per request). The
 * bank / cheque-no / addaccountstatus filters have been removed from this
 * script; those columns are still returned (still displayed in the report),
 * they're just no longer filterable from the UI.
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// Base table for this report (cheque payment-detail rows drive the report)
$table = 'tbl_invoice_payment_detail';

// Table's primary key
$primaryKey = 'idtbl_invoice_payment_detail';

// Array of database columns which should be read and sent back to DataTables.
// NOTE ON 'as': ssp.customized.class.php reads each fetched row back out by
// the column's raw SQL name (it doesn't auto-alias joined columns), so any
// column here whose 'dt' differs from its actual DB column name - or whose
// DB column name collides with another selected column (e.g. `p`.`date`
// and `i`.`date` both being plain `date`) - MUST carry an explicit 'as' so
// the SELECT aliases it in SQL and the class can find it under 'dt'.
// Columns where 'dt' already equals the raw column name don't need one.
$columns = array(
	array( 'db' => '`pd`.`idtbl_invoice_payment_detail`', 'dt' => 'idtbl_invoice_payment_detail', 'field' => 'idtbl_invoice_payment_detail' ),
	array( 'db' => '`p`.`date`',              'dt' => 'paymentdate',    'field' => 'paymentdate',   'as' => 'paymentdate' ),
	array( 'db' => '`i`.`idtbl_invoice`',     'dt' => 'idtbl_invoice',  'field' => 'idtbl_invoice' ),
	array( 'db' => '`i`.`invtype`',           'dt' => 'invtype',        'field' => 'invtype' ),
	array( 'db' => '`i`.`manuelinvno`',       'dt' => 'manuelinvno',    'field' => 'manuelinvno' ),
	array( 'db' => '`i`.`taxinvoice_no`',     'dt' => 'taxinvoice_no',  'field' => 'taxinvoice_no' ),
	array( 'db' => '`i`.`date`',              'dt' => 'invoicedate',    'field' => 'invoicedate',   'as' => 'invoicedate' ),
	array( 'db' => '`c`.`idtbl_customer`',    'dt' => 'idtbl_customer', 'field' => 'idtbl_customer' ),
	array( 'db' => '`c`.`name`',              'dt' => 'name',           'field' => 'name' ),
	array( 'db' => '`l`.`location`',          'dt' => 'location',       'field' => 'location' ),
	array( 'db' => '`pd`.`bank`',             'dt' => 'bank',           'field' => 'bank' ),
	array( 'db' => '`pd`.`chequeno`',         'dt' => 'chequeno',       'field' => 'chequeno' ),
	array( 'db' => '`pd`.`chequedate`',       'dt' => 'chequedate',     'field' => 'chequedate' ),
	array( 'db' => '`pd`.`amount`',           'dt' => 'chequeamount',   'field' => 'chequeamount',  'as' => 'chequeamount' ),
	array( 'db' => '`iph`.`payamount`',       'dt' => 'invoiceamount',  'field' => 'invoiceamount', 'as' => 'invoiceamount' ),
	array( 'db' => '`pd`.`addaccountstatus`', 'dt' => 'addaccountstatus','field' => 'addaccountstatus' ),
	array( 'db' => '`pd`.`status`',           'dt' => 'paymentdetailstatus', 'field' => 'paymentdetailstatus', 'as' => 'paymentdetailstatus' )
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

// Location comes from session, same convention as grn.php / grnreportdetail.php.
$locationID = (int) $_SESSION['location_id'];

// pd  = tbl_invoice_payment_detail  (the cheque line - drives the report, method = 2)
// p   = tbl_invoice_payment         (the payment header - payment date)
// iph = tbl_invoice_payment_has_tbl_invoice (bridge - which invoice(s) this payment covers,
//                                              and how much of it was applied to each)
// i   = tbl_invoice                 (invoice header - filtered to the session location)
// c   = tbl_customer                (idtbl_customer / name, per tbl_customer schema)
// l   = tbl_location
$joinQuery = "FROM `tbl_invoice_payment_detail` AS `pd`
    INNER JOIN `tbl_invoice_payment` AS `p` ON (`p`.`idtbl_invoice_payment` = `pd`.`tbl_invoice_payment_idtbl_invoice_payment`)
    INNER JOIN `tbl_invoice_payment_has_tbl_invoice` AS `iph` ON (`iph`.`tbl_invoice_payment_idtbl_invoice_payment` = `p`.`idtbl_invoice_payment`)
    INNER JOIN `tbl_invoice` AS `i` ON (`i`.`idtbl_invoice` = `iph`.`tbl_invoice_idtbl_invoice`)
    LEFT JOIN `tbl_customer` AS `c` ON (`c`.`idtbl_customer` = `i`.`customerid`)
    LEFT JOIN `tbl_location` AS `l` ON (`l`.`idtbl_location` = `i`.`tbl_location_idtbl_location`)";

// method = 2 -> cheque payments only. status = 1 on both the payment-detail
// row and the invoice so cancelled/voided records don't leak in. Location
// filter matches the session location (via the invoice, since that's where
// tbl_location_idtbl_location actually lives).
$extraWhere = "`pd`.`method` = 2 AND `pd`.`status` = 1 AND `i`.`status` = 1 AND `i`.`tbl_location_idtbl_location` = $locationID";

// Date filters - applied to the CHEQUE date by default, since that's what a
// "cheque collection" report is normally organised around. Swap `chequedate`
// for `p`.`date` below if you'd rather filter by payment-entry date instead.
if (!empty($_POST['search_date'])) {
    $date = $_POST['search_date'];
    $extraWhere .= " AND `pd`.`chequedate` = '$date'";
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

    $extraWhere .= " AND `pd`.`chequedate` BETWEEN '$startDate' AND '$endDate'";
} elseif (!empty($_POST['search_month'])) {
    $month = $_POST['search_month'];
    $month_arr = explode('-', $month);
    $extraWhere .= " AND YEAR(`pd`.`chequedate`) = '$month_arr[0]' AND MONTH(`pd`.`chequedate`) = '$month_arr[1]'";
} elseif (!empty($_POST['search_from_date']) && !empty($_POST['search_to_date'])) {
    $from_date = $_POST['search_from_date'];
    $to_date = $_POST['search_to_date'];

    $extraWhere .= " AND `pd`.`chequedate` BETWEEN '$from_date' AND '$to_date'";
}

// Customer filter - only remaining non-date filter, per request.
if (!empty($_POST['search_customer'])) {
    $customerID = (int) $_POST['search_customer'];
    $extraWhere .= " AND `c`.`idtbl_customer` = $customerID";
}

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);