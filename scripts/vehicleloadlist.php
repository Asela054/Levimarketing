<?php

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
$table = 'tbl_vehicle_load';

// Table's primary key
$primaryKey = 'idtbl_vehicle_load';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => '`u`.`idtbl_vehicle_load`', 'dt' => 'idtbl_vehicle_load', 'field' => 'idtbl_vehicle_load' ),
	array( 'db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date' ),
    array( 'db' => '`uc`.`vehicleno`', 'dt' => 'vehicleno', 'field' => 'vehicleno' ),
	array( 'db' => '`ua`.`area`', 'dt' => 'area', 'field' => 'area' ),
	array( 'db' => '`ub`.`name`', 'dt' => 'name', 'field' => 'name' ),
	// array( 'db' => '`ue`.`product_name`', 'dt' => 'product_name', 'field' => 'product_name' ),
	// array( 'db' => '`ud`.`qty`',   'dt' => 'qty', 'field' => 'qty' ),
	array( 'db' => '`u`.`approvestatus`',  'dt' => 'approvestatus', 'field' => 'approvestatus' ),
	array( 'db' => '`u`.`status`',  'dt' => 'status', 'field' => 'status' ),
	array( 'db' => '`u`.`type`',  'dt' => 'type', 'field' => 'type' ),
	array( 'db' => '`u`.`unloadstatus`',   'dt' => 'unloadstatus', 'field' => 'unloadstatus' ),
	array( 'db' => '`u`.`veiwallcustomerstatus`',   'dt' => 'veiwallcustomerstatus', 'field' => 'veiwallcustomerstatus' ),
	array( 'db' => '`u`.`transferstatus`','dt' => 'transferstatus', 'field' => 'transferstatus' ),
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

$joinQuery = "FROM `tbl_vehicle_load` AS `u` LEFT JOIN `tbl_area` AS `ua` ON (`ua`.`idtbl_area` = `u`.`tbl_area_idtbl_area`) LEFT JOIN `tbl_employee` AS `ub` ON (`ub`.`idtbl_employee` = `u`.`driverid`) LEFT JOIN `tbl_vehicle` AS `uc` ON (`uc`.`idtbl_vehicle` = `u`.`lorryid`)";

$extraWhere = "`u`.`status` IN (1,2)";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);