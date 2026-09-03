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
$table = 'tbl_stock';

// Table's primary key
$primaryKey = 'idtbl_stock';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => '`u`.`idtbl_stock`', 'dt' => 'idtbl_stock', 'field' => 'idtbl_stock' ),
	array( 'db' => '`u`.`qty`', 'dt' => 'qty', 'field' => 'qty' ),
    array( 'db' => '`u`.`update`',   'dt' => 'update', 'field' => 'update' ),
    array( 'db' => '`u`.`status`',   'dt' => 'status', 'field' => 'status' ),
    array( 'db' => '`ud`.`product_name`',   'dt' => 'product_name', 'field' => 'product_name' ),
    array( 'db' => '`uc`.`location`',   'dt' => 'location', 'field' => 'location' )

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

$joinQuery = "FROM `tbl_stock` AS `u` LEFT JOIN `tbl_product` AS `ud` ON (`ud`.`idtbl_product` = `u`.`tbl_product_idtbl_product`) LEFT JOIN `tbl_location` AS `uc` ON (`uc`.`idtbl_location` = `u`.`tbl_location_idtbl_location`)";
 
if(!empty($_POST['search_date'])){ 
    $date = $_POST['search_date'];
    $extraWhere = "`u`.`status` IN (0,1) AND `u`.update = '$date'";
}elseif(!empty($_POST['search_week'])){

        $week = $_POST['search_week'];
		$weeksep=explode('-W', $week);
		$year=$weeksep[0];
		$week1=$weeksep[1];
		$dto = new DateTime();
		$dto->setISODate($year, $week1);
		$startDate = $dto->format('Y-m-d');
		$dto->modify('+6 days');
		$endDate = $dto->format('Y-m-d');

		$extraWhere = "`u`.`status` IN (0,1) AND `u`.update BETWEEN '$startDate' AND '$endDate'";
}
elseif(!empty($_POST['search_month'])){
	$month = $_POST['search_month'];
	$month_arr = explode('-',$month);
	$extraWhere = "`u`.`status` IN (0,1) AND YEAR(`u`.update) = '$month_arr[0]' AND Month(`u`.update) = '$month_arr[1]'";

}
elseif(!empty($_POST['search_from_date'] && $_POST['search_to_date'])){

    $from_date = $_POST['search_from_date'];
    $to_date = $_POST['search_to_date'];

    $extraWhere = "`u`.`status` IN (0,1) AND `u`.update BETWEEN '$from_date' AND '$to_date'";
}
echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery,$extraWhere) 
);