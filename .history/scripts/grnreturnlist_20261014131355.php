<?php
session_start();

// DB table to use
$table = 'tbl_grn_return';

// Table's primary key
$primaryKey = 'idtbl_grn_return';

$columns = array(
	array( 'db' => '`u`.`idtbl_grn_return`', 'dt' => 'idtbl_grn_return', 'field' => 'idtbl_grn_return' ),
	array( 'db' => '`u`.`returnnum`', 'dt' => 'returnnum', 'field' => 'returnnum' ),
	array( 'db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date' ),
	array( 'db' => '`u`.`tbl_grn_idtbl_grn`', 'dt' => 'tbl_grn_idtbl_grn', 'field' => 'tbl_grn_idtbl_grn' ),
	array( 'db' => '`u`.`reason`', 'dt' => 'reason', 'field' => 'reason' ),
	array( 'db' => '`u`.`total`', 'dt' => 'total', 'field' => 'total' ),
	array( 'db' => '`u`.`confirm_status`', 'dt' => 'confirm_status', 'field' => 'confirm_status' ),
	array( 'db' => '`u`.`status`', 'dt' => 'status', 'field' => 'status' ),
	array( 'db' => '`ua`.`suppliername`', 'dt' => 'suppliername', 'field' => 'suppliername' )
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

$locationID=intval($_SESSION['location_id']);

$joinQuery = "FROM `tbl_grn_return` AS `u` LEFT JOIN `tbl_supplier` AS `ua` ON (`ua`.`idtbl_supplier` = `u`.`tbl_supplier_idtbl_supplier`)";

$extraWhere = "`u`.`status` = 1 AND `u`.`tbl_location_idtbl_location` = '$locationID'";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);