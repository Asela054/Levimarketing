<?php

session_start();
if (!isset($_SESSION['userid'])) {
    http_response_code(401);
    exit;
}

$locationID=$_SESSION['location_id'];

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'tbl_invoice';

// Table's primary key
$primaryKey = 'idtbl_invoice';

// Array of database columns which should be read and sent back to DataTables.
// idtbl_invoice is included (but not shown as a visible column) so the JS
// side can build the print link without the server rendering HTML.
$columns = array(
    array( 'db' => '`i`.`idtbl_invoice`',         'dt' => 'idtbl_invoice',     'field' => 'idtbl_invoice' ),
    array( 'db' => '`i`.`taxinvoice_no`',         'dt' => 'taxinvoice_no',     'field' => 'taxinvoice_no' ),
    array( 'db' => '`i`.`date`',                  'dt' => 'date',              'field' => 'date' ),
    array( 'db' => '`c`.`name`',                  'dt' => 'name',      'field' => 'name' ),
    array( 'db' => '`i`.`nettotal`',              'dt' => 'nettotal',          'field' => 'nettotal' ),
    array( 'db' => '`i`.`vatamount`',             'dt' => 'vatamount',         'field' => 'vatamount' ),
    array( 'db' => '`i`.`nettotal_with_vat`',     'dt' => 'nettotal_with_vat', 'field' => 'nettotal_with_vat' )
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

$joinQuery = "FROM `tbl_invoice` AS `i` INNER JOIN `tbl_customer` AS `c` ON (`c`.`idtbl_customer` = `i`.`customerid`)";

$extraWhere = "`i`.`status` = 1 AND `i`.`invtype` = 1 AND `i`.`tbl_location_idtbl_location` = " . $locationID;

header('Content-Type: application/json');
echo json_encode(
    SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);