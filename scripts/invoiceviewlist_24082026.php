<?php

session_start();

$type =  $_SESSION['privatetype'];
$locationID=$_SESSION['location_id'];

$table = 'tbl_invoice';
$primaryKey = 'idtbl_invoice';

$columns = array(
    array( 'db' => '`u`.`manuelinvno`', 'dt' => 'id', 'field' => 'manuelinvno' ),
    array( 'db' => '`u`.`idtbl_invoice`', 'dt' => 'idtbl_invoice', 'field' => 'idtbl_invoice' ),
    array( 'db' => '`u`.`taxinvoice_no`', 'dt' => 'taxinvoice_no', 'field' => 'taxinvoice_no' ),
    array( 'db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date' ),
    array( 'db' => '`u`.`total`', 'dt' => 'total', 'field' => 'total' ),
    array( 'db' => '`u`.`discounttotal`', 'dt' => 'discounttotal', 'field' => 'discounttotal' ),
    array( 'db' => '`u`.`nettotal`', 'dt' => 'nettotal', 'field' => 'nettotal' ),
    array( 'db' => '`u`.`paymentcomplete`', 'dt' => 'paymentcomplete', 'field' => 'paymentcomplete' ),
    array( 'db' => '`u`.`invtype`', 'dt' => 'invtype', 'field' => 'invtype' ),
    array( 'db' => '`u`.`saletype`', 'dt' => 'saletype', 'field' => 'saletype' ),
    array( 'db' => '`ua`.`name`',   'dt' => 'name', 'field' => 'name' ),
    array( 'db' => '`ub`.`name`', 'dt' => 'approveuser', 'field' => 'approveuser', 'as' => 'approveuser' ),
    array( 'db' => '`u`.`pricechangestatus`',   'dt' => 'pricechangestatus', 'field' => 'pricechangestatus' ),
    array( 'db' => '`u`.`tbl_location_idtbl_location`',   'dt' => 'tbl_location_idtbl_location', 'field' => 'tbl_location_idtbl_location' ),
    array( 'db' => '`u`.`status`',   'dt' => 'status', 'field' => 'status' )
);

require('config.php');
$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

require('ssp.customized.class.php' );

$joinQuery = "FROM `tbl_invoice` AS `u` LEFT JOIN `tbl_customer` AS `ua` ON (`ua`.`idtbl_customer` = `u`.`customerid`) LEFT JOIN `tbl_user` AS `ub` ON (`ub`.`idtbl_user` = `u`.`changeapproveuser`)";

$extraWhere = "`u`.`status` IN (1, 2) AND `u`.`tbl_location_idtbl_location` = " . $locationID;

// NEW: Sale Type filter (1 = Retail, 2 = Whole Sale) — direct column on tbl_invoice
if(isset($_POST['filtersaletype']) && $_POST['filtersaletype'] !== ''){
    $filtersaletype = intval($_POST['filtersaletype']);
    $extraWhere .= " AND `u`.`saletype` = " . $filtersaletype;
}

// NEW: Payment Method filter (1 = Cash, 2 = Card) — lives on tbl_invoice_payment_detail,
// reached via tbl_invoice_payment_has_tbl_invoice. An invoice can have more than one
// payment line, so we check existence rather than joining (which would duplicate rows).
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