<?php

session_start();

$type =  $_SESSION['privatetype'];

$table = 'tbl_invoice';
$primaryKey = 'idtbl_invoice';

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

require('config.php');
$sql_details = array(
	'user' => $db_username,
	'pass' => $db_password,
	'db'   => $db_name,
	'host' => $db_host
);

require('ssp.customized.class.php' );

$joinQuery = "FROM `tbl_invoice` AS `u` LEFT JOIN `tbl_customer` AS `ud` ON (`ud`.`idtbl_customer` = `u`.`customerid`)";

// NEW: default base clause, so $extraWhere is always defined even if no date/week/month/range param is sent
if($type==1){
	$extraWhere = "`u`.`status` IN (0,1) AND `u`.`manuelinvno` IS NOT NULL";
}else{
	$extraWhere = "`u`.`status` IN (0,1)";
}

if($type==1){

	if(!empty($_POST['search_date'])){ 
		$date = $_POST['search_date'];
		$extraWhere = "`u`.`status` IN (0,1) AND `u`.date = '$date' AND `u`.`manuelinvno`  IS NOT NULL";
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
			$extraWhere = "`u`.`status` IN (0,1) AND `u`.date BETWEEN '$startDate' AND '$endDate' AND `u`.`manuelinvno`  IS NOT NULL";
	}
	elseif(!empty($_POST['search_month'])){
		$month = $_POST['search_month'];
		$month_arr = explode('-',$month);
		$extraWhere = "`u`.`status` IN (0,1) AND YEAR(`u`.date) = '$month_arr[0]' AND Month(`u`.date) = '$month_arr[1]' AND `u`.`manuelinvno`  IS NOT NULL";
	}
	elseif(!empty($_POST['search_from_date'] && $_POST['search_to_date'])){
		$from_date = $_POST['search_from_date'];
		$to_date = $_POST['search_to_date'];
		$extraWhere = "`u`.`status` IN (0,1) AND `u`.date BETWEEN '$from_date' AND '$to_date' AND `u`.`manuelinvno`  IS NOT NULL";
	}

}else{

	if(!empty($_POST['search_date'])){ 
		$date = $_POST['search_date'];
		$extraWhere = "`u`.`status` IN (0,1) AND `u`.date = '$date'";
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
			$extraWhere = "`u`.`status` IN (0,1) AND `u`.date BETWEEN '$startDate' AND '$endDate'";
	}
	elseif(!empty($_POST['search_month'])){
		$month = $_POST['search_month'];
		$month_arr = explode('-',$month);
		$extraWhere = "`u`.`status` IN (0,1) AND YEAR(`u`.date) = '$month_arr[0]' AND Month(`u`.date) = '$month_arr[1]'";
	}
	elseif(!empty($_POST['search_from_date'] && $_POST['search_to_date'])){
		$from_date = $_POST['search_from_date'];
		$to_date = $_POST['search_to_date'];
		$extraWhere = "`u`.`status` IN (0,1) AND `u`.date BETWEEN '$from_date' AND '$to_date'";
	}
}

// Sale Type filter (1 = Retail, 2 = Whole Sale) — direct column on tbl_invoice
if(isset($_POST['filtersaletype']) && $_POST['filtersaletype'] !== ''){
	$filtersaletype = intval($_POST['filtersaletype']);
	$extraWhere .= " AND `u`.`saletype` = " . $filtersaletype;
}

// Payment Method filter (1 = Cash, 2 = Card) — same EXISTS pattern used on the invoice view page,
// since an invoice can have more than one payment line and a join would duplicate rows.
if(isset($_POST['filterpaymentmethod']) && $_POST['filterpaymentmethod'] !== ''){
	$filterpaymentmethod = intval($_POST['filterpaymentmethod']);
	$extraWhere .= " AND EXISTS (
		SELECT 1
		FROM `tbl_invoice_payment_has_tbl_invoice` AS `iphi`
		INNER JOIN `tbl_invoice_payment_detail` AS `ipd`
			ON `ipd`.`tbl_invoice_payment_idtbl_invoice_payment` = `iphi`.`tbl_invoice_payment_idtbl_invoice_payment`
		WHERE `iphi`.`tbl_invoice_idtbl_invoice` = `u`.`idtbl_invoice`
		  AND `ipd`.`method` = " . $filterpaymentmethod . "
	)";
}

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);