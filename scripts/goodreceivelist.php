<?php
session_start();
/*
 * Server-side processing for the GRN LIST (grn.php DataTable, #dataTableGrn).
 * One row per GRN header. Restructured to follow the same
 * ssp.customized.class.php column-array pattern used by the GRN
 * detail-wise report (grnreport.php), instead of building SQL by hand.
 *
 * Filtered to the logged-in user's location via $_SESSION['location_id'],
 * same convention as grn.php / the detail report.
 */

if (!isset($_SESSION['userid'])) {
    http_response_code(403);
    exit;
}

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// Base table for this report (GRN header drives the list)
$table = 'tbl_grn';

// Table's primary key
$primaryKey = 'idtbl_grn';

// Array of database columns which should be read and sent back to DataTables.
// dt keys match what grn.php's #dataTableGrn "columns" render functions expect
// (full['idtbl_grn'], full['porder_id'], full['suppliername'], etc).
$columns = array(
    array( 'db' => '`g`.`idtbl_grn`',       'dt' => 'idtbl_grn',     'field' => 'idtbl_grn' ),
    array( 'db' => '`g`.`date`',            'dt' => 'date',          'field' => 'date' ),
    array( 'db' => '`g`.`porder_id`',       'dt' => 'porder_id',     'field' => 'porder_id' ),
    array( 'db' => '`s`.`suppliername`',    'dt' => 'suppliername',  'field' => 'suppliername' ),
    array( 'db' => '`l`.`location`',    'dt' => 'location',      'field' => 'location' ),
    array( 'db' => '`g`.`invoicenum`',      'dt' => 'invoicenum',    'field' => 'invoicenum' ),
    array( 'db' => '`g`.`dispatchnum`',     'dt' => 'dispatchnum',   'field' => 'dispatchnum' ),
    array( 'db' => '`g`.`subtotal`',        'dt' => 'subtotal',      'field' => 'subtotal' ),
    array( 'db' => '`g`.`vattype`',         'dt' => 'vattype',       'field' => 'vattype' ),
    array( 'db' => '`g`.`vatpercentage`',   'dt' => 'vatpercentage', 'field' => 'vatpercentage' ),
    array( 'db' => '`g`.`vatamount`',       'dt' => 'vatamount',     'field' => 'vatamount' ),
    array( 'db' => '`g`.`total`',           'dt' => 'total',         'field' => 'total' ),
    array( 'db' => '`g`.`confirm_status`',  'dt' => 'confirm_status','field' => 'confirm_status' )
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

// Location comes from session, same convention as grn.php / the detail report.
$locationID = (int) $_SESSION['location_id'];

// g   = tbl_grn        (the GRN header - drives this list)
// po  = tbl_porder     (only used to fall back to the PO's supplier)
// s   = tbl_supplier   (resolved from the GRN's own supplier first, falling
//                        back to the linked PO's supplier - same logic as
//                        the detail report, needed because "Without PO" GRNs
//                        store the supplier directly on tbl_grn)
// l   = tbl_location
$joinQuery = "FROM `tbl_grn` AS `g`
    LEFT JOIN `tbl_porder` AS `po` ON (`po`.`idtbl_porder` = `g`.`porder_id` AND `g`.`porder_id` > 0)
    LEFT JOIN `tbl_supplier` AS `s` ON (`s`.`idtbl_supplier` = COALESCE(`g`.`tbl_supplier_idtbl_supplier`, `po`.`tbl_supplier_idtbl_supplier`))
    LEFT JOIN `tbl_location` AS `l` ON (`l`.`idtbl_location` = `g`.`tbl_location_idtbl_location`)";

// status = 1 so voided/cancelled GRNs don't leak into the list; location
// filter matches the session location, same as before.
$extraWhere = "`g`.`status` = 1 AND `g`.`tbl_location_idtbl_location` = $locationID";

if (!empty($_POST['search_grn'])) {
    $grnID = (int) $_POST['search_grn'];
    $extraWhere .= " AND `g`.`idtbl_grn` = $grnID";
}

echo json_encode(
    SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);