<?php

session_start();

$type =  $_SESSION['privatetype'];
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
$table = 'tbl_invoice';

// Table's primary key
$primaryKey = 'idtbl_invoice';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes

if($type==1){
	$columns = array(
	
		array( 'db' => '`u`.`manuelinvno`', 'dt' => 'id', 'field' => 'manuelinvno' ),
		array( 'db' => '`ud`.`name`',   'dt' => 'name', 'field' => 'name' ),
		array( 'db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date' ),
		array( 'db' => '`u`.`saletype`', 'dt' => 'saletype', 'field' => 'saletype' ),
		array( 'db' => '`u`.`total`', 'dt' => 'total', 'field' => 'total' )
	);
}else{
	$columns = array(
	
		array( 'db' => '`u`.`idtbl_invoice`', 'dt' => 'id', 'field' => 'idtbl_invoice' ),
		array( 'db' => '`ud`.`name`',   'dt' => 'name', 'field' => 'name' ),
		array( 'db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date' ),
		array( 'db' => '`u`.`saletype`', 'dt' => 'saletype', 'field' => 'saletype' ),
		array( 'db' => '`u`.`total`', 'dt' => 'total', 'field' => 'total' )
	);
}


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

$joinQuery = "FROM `tbl_invoice` AS `u` LEFT JOIN `tbl_customer` AS `ud` ON (`ud`.`idtbl_customer` = `u`.`customerid`)";
 

if($type==1){

    // Default condition (always applied)
    $extraWhere = "`u`.`status` IN (0,1) 
                   AND `u`.`paymentmethod` = 2 
                   AND `u`.`manuelinvno` IS NOT NULL";

    if(!empty($_POST['search_date'])){ 
        $date = $_POST['search_date'];
        $extraWhere .= " AND `u`.date = '$date'";
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

        $extraWhere .= " AND `u`.date BETWEEN '$startDate' AND '$endDate'";
    }
    elseif(!empty($_POST['search_month'])){
        $month = $_POST['search_month'];
        $month_arr = explode('-',$month);
        $extraWhere .= " AND YEAR(`u`.date) = '$month_arr[0]' 
                         AND Month(`u`.date) = '$month_arr[1]'";
    }
    elseif(!empty($_POST['search_from_date'] && $_POST['search_to_date'])){
        $from_date = $_POST['search_from_date'];
        $to_date = $_POST['search_to_date'];
        $extraWhere .= " AND `u`.date BETWEEN '$from_date' AND '$to_date'";
    }

}else{

    // Default condition (always applied)
    $extraWhere = "`u`.`status` IN (0,1) 
                   AND `u`.`paymentmethod` = 2";

    if(!empty($_POST['search_date'])){ 
        $date = $_POST['search_date'];
        $extraWhere .= " AND `u`.date = '$date'";
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

        $extraWhere .= " AND `u`.date BETWEEN '$startDate' AND '$endDate'";
    }
    elseif(!empty($_POST['search_month'])){
        $month = $_POST['search_month'];
        $month_arr = explode('-',$month);
        $extraWhere .= " AND YEAR(`u`.date) = '$month_arr[0]' 
                         AND Month(`u`.date) = '$month_arr[1]'";
    }
    elseif(!empty($_POST['search_from_date'] && $_POST['search_to_date'])){
        $from_date = $_POST['search_from_date'];
        $to_date = $_POST['search_to_date'];
        $extraWhere .= " AND `u`.date BETWEEN '$from_date' AND '$to_date'";
    }
}


echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery,$extraWhere)
);