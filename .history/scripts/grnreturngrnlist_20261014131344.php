<?php
session_start();

// DB table to use
$table = 'tbl_grn';

// Table's primary key
$primaryKey = 'idtbl_grn';

$columns = array(
	array( 'db' => '`u`.`idtbl_grn`', 'dt' => 'idtbl_grn', 'field' => 'idtbl_grn' ),
	array( 'db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date' ),
	array( 'db' => '`u`.`invoicenum`', 'dt' => 'invoicenum', 'field' => 'invoicenum' ),
	array( 'db' => '`u`.`total`', 'dt' => 'total', 'field' => 'total' ),
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

$joinQuery = "FROM `tbl_grn` AS `u` LEFT JOIN `tbl_supplier` AS `ua` ON (`ua`.`idtbl_supplier` = `u`.`tbl_supplier_idtbl_supplier`)";

// approved GRNs of this location that still have something left to return
$extraWhere = "`u`.`status` = 1 AND `u`.`confirm_status` = 1 AND `u`.`tbl_location_idtbl_location` = '$locationID'
	AND EXISTS (
		SELECT 1 FROM `tbl_grndetail` AS `d`
		WHERE `d`.`tbl_grn_idtbl_grn` = `u`.`idtbl_grn` AND `d`.`status` = 1
		AND `d`.`qty` > COALESCE((
			SELECT SUM(`rd`.`qty`) FROM `tbl_grn_return_detail` AS `rd`
			JOIN `tbl_grn_return` AS `r` ON `r`.`idtbl_grn_return` = `rd`.`tbl_grn_return_idtbl_grn_return`
			WHERE `rd`.`tbl_grndetail_idtbl_grndetail` = `d`.`idtbl_grndetail` AND `rd`.`status` = 1 AND `r`.`status` = 1
		), 0)
	)";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);