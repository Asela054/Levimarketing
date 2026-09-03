<?php

/*
 * DataTables server-side processing script for Quotations.
 * Follows the same SSP::simple pattern used across the project (e.g. supplier,
 * GRN, invoice report scripts).
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'tbl_quotation';

// Table's primary key
$primaryKey = 'idtbl_quotation';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier.
$columns = array(
	array( 'db' => '`q`.`idtbl_quotation`',      'dt' => 'idtbl_quotation',    'field' => 'idtbl_quotation' ),
	array( 'db' => '`q`.`quotation_no`',         'dt' => 'quotation_no',       'field' => 'quotation_no' ),
	array( 'db' => '`q`.`date`',                 'dt' => 'date',               'field' => 'date' ),
	array( 'db' => '`c`.`name`',                 'dt' => 'name',               'field' => 'name' ),
	array( 'db' => '`q`.`nettotal`',             'dt' => 'nettotal',           'field' => 'nettotal' ),
	array( 'db' => '`q`.`vatamount`',            'dt' => 'vatamount',          'field' => 'vatamount' ),
	array( 'db' => '`q`.`nettotal_with_vat`',    'dt' => 'nettotal_with_vat',  'field' => 'nettotal_with_vat' ),
	array( 'db' => '`q`.`status`',               'dt' => 'status',             'field' => 'status' )
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

$joinQuery = "FROM `tbl_quotation` AS `q`
              LEFT JOIN `tbl_customer` AS `c` ON `c`.`idtbl_customer` = `q`.`tbl_customer_idtbl_customer`
              WHERE `q`.`status` = 1";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery)
);