<?php

/*
 * Server-side processing script for Customer Credit Analysis Report
 */

// DB table to use
$table = 'tbl_invoice';

// Table's primary key
$primaryKey = 'idtbl_invoice';

// Array of database columns which should be read and sent back to DataTables
$columns = array(
    array( 'db' => '`c`.`name`', 'dt' => 'customer_name', 'field' => 'name' ),
    array( 'db' => '`i`.`date`', 'dt' => 'invoice_date', 'field' => 'date' ),
    // CHANGED: send raw columns instead of a server-side COALESCE,
    // so the invoice number can be formatted client-side the same way invoiceviewlist.php does
    array( 'db' => '`i`.`idtbl_invoice`', 'dt' => 'idtbl_invoice', 'field' => 'idtbl_invoice' ),
    array( 'db' => '`i`.`manuelinvno`', 'dt' => 'manuelinvno', 'field' => 'manuelinvno' ),
    array( 'db' => '`i`.`taxinvoice_no`', 'dt' => 'taxinvoice_no', 'field' => 'taxinvoice_no' ),
    array( 'db' => '`i`.`invtype`', 'dt' => 'invtype', 'field' => 'invtype' ),
    array( 'db' => '`i`.`total`', 'dt' => 'invoice_amount', 'field' => 'total' ),
    array( 'db' => 'COALESCE(`pm`.`total_paid`, 0) AS `amount_paid`', 'dt' => 'amount_paid', 'field' => 'amount_paid' ),
    array( 'db' => '(`i`.`total` - COALESCE(`pm`.`total_paid`, 0)) AS `outstanding`', 'dt' => 'outstanding', 'field' => 'outstanding' ),
    array( 'db' => 'DATE_ADD(`i`.`date`, INTERVAL COALESCE(`c`.`creditperiod`, 30) DAY) AS `due_date`', 'dt' => 'due_date', 'field' => 'due_date' )
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

$joinQuery = "FROM `tbl_invoice` AS `i` 
              LEFT JOIN `tbl_customer` AS `c` ON (`c`.`idtbl_customer` = `i`.`customerid`)
              LEFT JOIN (
                SELECT `ip`.`tbl_invoice_idtbl_invoice`, SUM(`p`.`payment`) AS `total_paid`
                FROM `tbl_invoice_payment_has_tbl_invoice` AS `ip`
                LEFT JOIN `tbl_invoice_payment` AS `p` ON (`p`.`idtbl_invoice_payment` = `ip`.`tbl_invoice_payment_idtbl_invoice_payment`)
                WHERE `p`.`status` = 1
                GROUP BY `ip`.`tbl_invoice_idtbl_invoice`
              ) AS `pm` ON (`pm`.`tbl_invoice_idtbl_invoice` = `i`.`idtbl_invoice`)";

$extraWhere = "`i`.`status` IN (0,1) AND `i`.`paymentmethod` = 2";

// Filter by customer if provided
if(!empty($_POST['search_customer_id'])) {
    $customer_id = intval($_POST['search_customer_id']);
    $extraWhere .= " AND `c`.`idtbl_customer` = $customer_id";
}

// Filter by date range
if(!empty($_POST['search_from_date']) && !empty($_POST['search_to_date'])) {
    $from_date = $_POST['search_from_date'];
    $to_date = $_POST['search_to_date'];
    $extraWhere .= " AND `i`.`date` BETWEEN '$from_date' AND '$to_date'";
}

echo json_encode(
    SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
?>