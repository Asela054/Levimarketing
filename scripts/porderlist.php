<?php
session_start();
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
$table = 'tbl_porder';

// Table's primary key
$primaryKey = 'idtbl_porder';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
//
// Removed `idtbl_dispatch` (and the tbl_dispatch join that fed it) — it isn't
// referenced anywhere in the front-end DataTable columns/render callbacks, but
// tbl_dispatch has no index on `porder_id`, so that LEFT JOIN was forcing an
// unindexed scan of tbl_dispatch for every single tbl_porder row on every
// page load. That was the main reason this table loaded much slower than GRN's.
// Also dropped `status` and `grnissuestatus`, which are likewise fetched but
// never used client-side.
$columns = array(
	array( 'db' => '`u`.`idtbl_porder`', 'dt' => 'idtbl_porder', 'field' => 'idtbl_porder' ),
	array( 'db' => '`u`.`orderdate`', 'dt' => 'orderdate', 'field' => 'orderdate' ),
	array( 'db' => '`u`.`nettotal`', 'dt' => 'nettotal', 'field' => 'nettotal' ),
	array( 'db' => '`uc`.`name`', 'dt' => 'name', 'field' => 'name' ),
	array( 'db' => '`s`.`suppliername`', 'dt' => 'suppliername', 'field' => 'suppliername' ),
	array( 'db' => '`u`.`confirmstatus`', 'dt' => 'confirmstatus', 'field' => 'confirmstatus' ),
	array( 'db' => '`u`.`tbl_location_idtbl_location`',   'dt' => 'tbl_location_idtbl_location', 'field' => 'tbl_location_idtbl_location' ),
	array( 'db' => '`l`.`location`',   'dt' => 'location', 'field' => 'location' )
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

$locationID=$_SESSION['location_id'];

$joinQuery = "FROM `tbl_porder` AS `u` LEFT JOIN `tbl_user` AS `uc` ON (`uc`.`idtbl_user` = `u`.`tbl_user_idtbl_user`) LEFT JOIN `tbl_location` AS `l` ON (`l`.`idtbl_location` = `u`.`tbl_location_idtbl_location`) LEFT JOIN `tbl_supplier` AS `s` ON (`s`.`idtbl_supplier` = `u`.`tbl_supplier_idtbl_supplier`)";

$extraWhere = "`u`.`confirmstatus` IN (1,0,2) AND `u`.`status`=1 AND `u`.`potype`=0 AND `u`.`tbl_location_idtbl_location`='$locationID'";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);