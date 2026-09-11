<?php
session_start();

$locationID = isset($_SESSION['location_id']) 
    ? (int) $_SESSION['location_id'] 
    : 0;
/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'tbl_invoice_payment';

// Table's primary key
$primaryKey = 'idtbl_invoice_payment';

// Array of database columns which should be read and sent back to DataTables.
$columns = array(
	array( 'db' => '`u`.`idtbl_invoice_payment`', 'dt' => 'idtbl_invoice_payment', 'field' => 'idtbl_invoice_payment' ),
    array( 'db' => '`i`.`invtype`',              'dt' => 'invtype',              'field' => 'invtype' ),
    array( 'db' => '`i`.`manuelinvno`',          'dt' => 'manuelinvno',          'field' => 'manuelinvno' ),
    array( 'db' => '`i`.`taxinvoice_no`',        'dt' => 'taxinvoice_no',        'field' => 'taxinvoice_no' ),
	array( 'db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date' ),
	array( 'db' => '`u`.`payment`', 'dt' => 'payment', 'field' => 'payment' ),
	array( 'db' => '`u`.`balance`', 'dt' => 'balance', 'field' => 'balance' ),
	array( 'db' => '`u`.`status`',   'dt' => 'status', 'field' => 'status' )
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

require('ssp.customized.class.php' );

// --- Prefix-aware search -------------------------------------------------
// "PR-2280"  -> should match idtbl_invoice_payment (Receipt No) ONLY
// "INV-2280" -> should match manuelinvno (manual Invoice No) ONLY
// Anything else (no recognised prefix) falls back to DataTables' normal
// search-all-columns behaviour.
//
// Without this, a plain global LIKE across every column would match
// "2280" against idtbl_invoice_payment (giving PR-2280) even when the
// user typed INV-2280 looking for the invoice with manuelinvno = 2280.
$prefixWhere = '';

if (isset($_POST['search']['value']) && trim($_POST['search']['value']) !== '') {
    $searchValue = trim($_POST['search']['value']);

    if (preg_match('/^PR-?\s*(\d+)/i', $searchValue, $m)) {
        // Receipt number search
        $num = (int) $m[1];
        $prefixWhere = "`u`.`idtbl_invoice_payment` LIKE '%".$num."%'";
        $_POST['search']['value'] = ''; // stop SSP's own OR-across-all-columns search
    } elseif (preg_match('/^INV-?\s*(\d+)/i', $searchValue, $m)) {
        // Manual invoice number search
        $num = (int) $m[1];
        $prefixWhere = "`i`.`manuelinvno` LIKE '%".$num."%'";
        $_POST['search']['value'] = ''; // stop SSP's own OR-across-all-columns search
    }
    // else: no recognised prefix, leave $_POST['search']['value'] as-is
    // so the default multi-column search still works for other input.
}
// --- end prefix-aware search ---------------------------------------------

$joinQuery = "
    FROM `tbl_invoice_payment` AS `u`

    INNER JOIN `tbl_invoice_payment_has_tbl_invoice` AS `uphi`
        ON `uphi`.`tbl_invoice_payment_idtbl_invoice_payment`
           = `u`.`idtbl_invoice_payment`

    INNER JOIN `tbl_invoice` AS `i`
        ON `i`.`idtbl_invoice`
           = `uphi`.`tbl_invoice_idtbl_invoice`
";

$extraWhere = "
    `u`.`status` IN (1, 2)
    AND `i`.`tbl_location_idtbl_location` = ".$locationID.
    ( $prefixWhere !== '' ? " AND ".$prefixWhere : "" );

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);