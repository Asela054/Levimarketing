<?php
session_start();

$locationID = isset($_SESSION['location_id']) 
    ? (int) $_SESSION['location_id'] 
    : 0;
/*
 * DataTables server-side processing script (see http://datatables.net/usage/server-side).
 * @license MIT - http://datatables.net/license_mit
 */

// DB table to use
$table = 'tbl_invoice_payment';

// Table's primary key
$primaryKey = 'idtbl_invoice_payment';

// Columns sent back to DataTables.
$columns = array(
	array( 'db' => '`u`.`idtbl_invoice_payment`', 'dt' => 'idtbl_invoice_payment', 'field' => 'idtbl_invoice_payment' ),
    array( 'db' => '`i`.`invtype`',              'dt' => 'invtype',              'field' => 'invtype' ),
    array( 'db' => '`i`.`manuelinvno`',          'dt' => 'manuelinvno',          'field' => 'manuelinvno' ),
    array( 'db' => '`i`.`taxinvoice_no`',        'dt' => 'taxinvoice_no',        'field' => 'taxinvoice_no' ),
    array( 'db' => '`i`.`nontaxinvnum_new`',     'dt' => 'nontaxinvnum_new',     'field' => 'nontaxinvnum_new' ),
    array( 'db' => '`i`.`taxinvnum_new`',        'dt' => 'taxinvnum_new',        'field' => 'taxinvnum_new' ),
	array( 'db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date' ),
	array( 'db' => '`u`.`payment`', 'dt' => 'payment', 'field' => 'payment' ),
	array( 'db' => '`u`.`balance`', 'dt' => 'balance', 'field' => 'balance' ),
	array( 'db' => '`u`.`status`',   'dt' => 'status', 'field' => 'status' ),
	// --- cheque return columns (from tbl_invoice_payment_detail, alias pd) ---
	array( 'db' => '`pd`.`idtbl_invoice_payment_detail`', 'dt' => 'chequeid',           'field' => 'idtbl_invoice_payment_detail' ),
	array( 'db' => '`pd`.`chequedate`',                   'dt' => 'chequedate',         'field' => 'chequedate' ),
	array( 'db' => '`pd`.`chequereturnstatus`',           'dt' => 'chequereturnstatus', 'field' => 'chequereturnstatus' )
);

// SQL server connection information
require('config.php');
$sql_details = array(
	'user' => $db_username,
	'pass' => $db_password,
	'db'   => $db_name,
	'host' => $db_host
);

require('ssp.customized.class.php' );

// --- Manual search handling (bypasses SSP's built-in global search) ------
// "PR-2280"  -> match idtbl_invoice_payment (Receipt No) ONLY
// "INV-2280" -> match the normal invoice number: new number (nontaxinvnum_new)
//               OR old manual number (manuelinvno)
// anything else -> match manuelinvno, nontaxinvnum_new, taxinvoice_no,
//                  taxinvnum_new, date, payment, balance
$prefixWhere = '';

if (isset($_POST['search']['value']) && trim($_POST['search']['value']) !== '') {
    $searchValue = trim($_POST['search']['value']);

    if (preg_match('/^PR-?\s*(\d+)/i', $searchValue, $m)) {
        // Receipt number search
        $num = (int) $m[1];
        $prefixWhere = "`u`.`idtbl_invoice_payment` LIKE '%".$num."%'";
    } elseif (preg_match('/^INV-?\s*(\d+)/i', $searchValue, $m)) {
        // Normal invoice number search (new number or old manual number)
        $num = (int) $m[1];
        $prefixWhere = "(`i`.`nontaxinvnum_new` LIKE '%".$num."%' OR `i`.`manuelinvno` LIKE '%".$num."%')";
    } else {
        $escConn = new mysqli($db_host, $db_username, $db_password, $db_name);
        $escaped = $escConn->real_escape_string($searchValue);
        $escConn->close();

        $prefixWhere = "(
            `i`.`manuelinvno`        LIKE '%".$escaped."%'
            OR `i`.`nontaxinvnum_new` LIKE '%".$escaped."%'
            OR `i`.`taxinvoice_no`   LIKE '%".$escaped."%'
            OR `i`.`taxinvnum_new`   LIKE '%".$escaped."%'
            OR `u`.`date`            LIKE '%".$escaped."%'
            OR `u`.`payment`         LIKE '%".$escaped."%'
            OR `u`.`balance`         LIKE '%".$escaped."%'
        )";
    }

    // Clear DataTables' own search so SSP::simple() does not apply it on top of ours.
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

    LEFT JOIN `tbl_invoice_payment_detail` AS `pd`
        ON `pd`.`tbl_invoice_payment_idtbl_invoice_payment`
           = `u`.`idtbl_invoice_payment`
       AND `pd`.`method` = 3
       AND `pd`.`status` = 1
";

$extraWhere = "
    `u`.`status` IN (1, 2)
    AND `i`.`tbl_location_idtbl_location` = ".$locationID.
    ( $prefixWhere !== '' ? " AND ".$prefixWhere : "" );

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);