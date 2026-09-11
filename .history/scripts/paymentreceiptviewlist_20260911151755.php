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
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
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

// require( 'ssp.class.php' );
require('ssp.customized.class.php' );

// --- Manual search handling (bypasses SSP's built-in global search) ------
// We build the WHERE clause ourselves instead of relying on
// SSP::simple()'s automatic multi-column search, because that automatic
// search depends on column metadata the client sends per DataTables
// column, and this table's client-side column count (6 display columns)
// doesn't line up with the server-side $columns array (8 db columns) —
// which was silently causing some columns (like taxinvoice_no) to be
// skipped in the search.
//
// "PR-2280"  -> match idtbl_invoice_payment (Receipt No) ONLY
// "INV-2280" -> match manuelinvno (manual Invoice No) ONLY
// anything else -> match manuelinvno OR taxinvoice_no OR date OR payment
//                  OR balance (covers plain tax invoice numbers like
//                  "26AUG_LV1_02954", dates, and amounts)
$prefixWhere = '';

if (isset($_POST['search']['value']) && trim($_POST['search']['value']) !== '') {
    $searchValue = trim($_POST['search']['value']);

    if (preg_match('/^PR-?\s*(\d+)/i', $searchValue, $m)) {
        // Receipt number search
        $num = (int) $m[1];
        $prefixWhere = "`u`.`idtbl_invoice_payment` LIKE '%".$num."%'";
    } elseif (preg_match('/^INV-?\s*(\d+)/i', $searchValue, $m)) {
        // Manual invoice number search
        $num = (int) $m[1];
        $prefixWhere = "`i`.`manuelinvno` LIKE '%".$num."%'";
    } else {
        // No recognised prefix: search across the columns users actually
        // look things up by, built manually so taxinvoice_no is guaranteed
        // to be included. Escape via a local mysqli connection since this
        // script doesn't otherwise open its own $conn (SSP manages its
        // own connection internally using $sql_details).
        $escConn = new mysqli($db_host, $db_username, $db_password, $db_name);
        $escaped = $escConn->real_escape_string($searchValue);
        $escConn->close();

        $prefixWhere = "(
            `i`.`manuelinvno`   LIKE '%".$escaped."%'
            OR `i`.`taxinvoice_no` LIKE '%".$escaped."%'
            OR `u`.`date`          LIKE '%".$escaped."%'
            OR `u`.`payment`       LIKE '%".$escaped."%'
            OR `u`.`balance`       LIKE '%".$escaped."%'
        )";
    }

    // Always clear DataTables' own search value once we've built our own
    // WHERE clause, so SSP::simple() doesn't also try (and potentially
    // mis-fire) its built-in global search on top of ours.
    $_POST['search']['value'] = '';
}
// --- end manual search handling -------------------------------------------

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