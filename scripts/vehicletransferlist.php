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
$table = 'tbl_vehicle_transfer';

// Table's primary key
$primaryKey = 'idtbl_vehicle_transfer';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => '`u`.`idtbl_vehicle_transfer`', 'dt' => 'idtbl_vehicle_transfer', 'field' => 'idtbl_vehicle_transfer' ),
	array( 'db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date' ),
	array( 'db' => '`u`.`status`',  'dt' => 'status', 'field' => 'status' ),
	array( 'db' => '`u`.`approvestatus`',  'dt' => 'approvestatus', 'field' => 'approvestatus' ),
	array( 'db' => '`ua`.`vehicleno` AS `currentvehicleno`',  'dt' => 'currentvehicleno', 'field' => 'currentvehicleno' ),
	array( 'db' => '`ub`.`vehicleno` AS `transfervehicleno`',  'dt' => 'transfervehicleno', 'field' => 'transfervehicleno' ),
	array( 'db' => '`uc`.`area`', 'dt' => 'area', 'field' => 'area' ),
	array( 'db' => '`ud`.`name` AS `driver`', 'dt' => 'driver', 'field' => 'driver' ),
	array( 'db' => '`ue`.`name` AS `officer`', 'dt' => 'officer', 'field' => 'officer' ),
	array( 'db' => '`uf`.`name` AS `helper1`', 'dt' => 'helper1', 'field' => 'helper1' ),
	array( 'db' => '`ug`.`name` AS `helper2`', 'dt' => 'helper2', 'field' => 'helper2' )
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

$joinQuery = "FROM `tbl_vehicle_transfer` AS `u` LEFT JOIN `tbl_vehicle` AS `ua` ON (`ua`.`idtbl_vehicle` = `u`.`current_lorryid`) LEFT JOIN `tbl_vehicle` AS `ub` ON (`ub`.`idtbl_vehicle` = `u`.`transfer_lorryid`) LEFT JOIN `tbl_area` AS `uc` ON (`uc`.`idtbl_area` = `u`.`tbl_area_idtbl_area`) LEFT JOIN `tbl_employee` AS `ud` ON (`ud`.`idtbl_employee` = `u`.`driverid`) LEFT JOIN `tbl_employee` AS `ue` ON (`ud`.`idtbl_employee` = `u`.`officerid`) LEFT JOIN `tbl_employee` AS `uf` ON (`ud`.`idtbl_employee` = `u`.`helperid`) LEFT JOIN `tbl_employee` AS `ug` ON (`ud`.`idtbl_employee` = `u`.`helperid2`)";

$extraWhere = "`u`.`status` IN (1,2)";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);