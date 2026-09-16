<?php

session_start();

$type = $_SESSION['privatetype'];
$locationID = $_SESSION['location_id'];

/*
 * DataTables server-side processing
 */

// DB table
$table = 'tbl_invoice';

// Primary key
$primaryKey = 'idtbl_invoice';


// ================================
// Read payment method filter FIRST
// (needed before building $columns)
// ================================
$filterpaymentmethod = null;
if (isset($_POST['filterpaymentmethod']) && $_POST['filterpaymentmethod'] != '') {
    $filterpaymentmethod = intval($_POST['filterpaymentmethod']);
}

// Build the "total" column source depending on whether a payment method filter is active
if ($filterpaymentmethod !== null) {
    // Show only the amount paid via the selected method for this invoice
    $totalDb = "(
        SELECT SUM(`ipd2`.`amount`)
        FROM `tbl_invoice_payment_has_tbl_invoice` AS `iphi2`
        INNER JOIN `tbl_invoice_payment_detail` AS `ipd2`
            ON `ipd2`.`tbl_invoice_payment_idtbl_invoice_payment`
             = `iphi2`.`tbl_invoice_payment_idtbl_invoice_payment`
        WHERE `iphi2`.`tbl_invoice_idtbl_invoice` = `u`.`idtbl_invoice`
        AND `ipd2`.`method` = " . $filterpaymentmethod . "
    )";
} else {
    // No payment method filter -> normal invoice total (summed view)
    $totalDb = "`u`.`total`";
}


// Columns
if($type==1){

    $columns = array(
        array( 
            'db' => '`u`.`manuelinvno`', 
            'dt' => 'id', 
            'field' => 'manuelinvno' 
        ),
        array( 
            'db' => '`ud`.`name`',   
            'dt' => 'name', 
            'field' => 'name' 
        ),
        array( 
            'db' => '`u`.`date`', 
            'dt' => 'date', 
            'field' => 'date' 
        ),
        array( 
            'db' => '`u`.`saletype`', 
            'dt' => 'saletype', 
            'field' => 'saletype'
        ),
        array( 
            'db' => $totalDb, 
            'dt' => 'total', 
            'field' => 'total' 
        ),
        array( 
            'db' => '`u`.`manuelinvno`', 
            'dt' => 'manuelinvno', 
            'field' => 'manuelinvno'
        )
    );

}else{

    $columns = array(
        array( 
            'db' => '`u`.`idtbl_invoice`', 
            'dt' => 'id', 
            'field' => 'idtbl_invoice' 
        ),
        array( 
            'db' => '`ud`.`name`',   
            'dt' => 'name', 
            'field' => 'name' 
        ),
        array( 
            'db' => '`u`.`date`', 
            'dt' => 'date', 
            'field' => 'date' 
        ),
        array( 
            'db' => '`u`.`saletype`', 
            'dt' => 'saletype', 
            'field' => 'saletype'
        ),
        array( 
            'db' => $totalDb, 
            'dt' => 'total', 
            'field' => 'total' 
        ),
        array( 
            'db' => '`u`.`manuelinvno`', 
            'dt' => 'manuelinvno', 
            'field' => 'manuelinvno'
        )
    );
}



// Database connection
require('config.php');

$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);


require('ssp.customized.class.php');


// Join
$joinQuery = "
FROM `tbl_invoice` AS `u`
LEFT JOIN `tbl_customer` AS `ud`
ON (`ud`.`idtbl_customer` = `u`.`customerid`)
";


// Date filter
$date = $_POST['selectdate'];


// Base condition
if($type==2){

    $extraWhere = "
        `u`.`status` IN (1,2) 
        AND `u`.`date` = '$date'
        AND `u`.`tbl_location_idtbl_location` = $locationID
    ";

}else{

    $extraWhere = "
        `u`.`status` IN (1,2) 
        AND `u`.`date` = '$date'
        AND `u`.`manuelinvno` != 0
        AND `u`.`tbl_location_idtbl_location` = $locationID
    ";
}



// ================================
// SALE TYPE FILTER
// ================================
if(isset($_POST['filtersaletype']) && $_POST['filtersaletype'] != ''){

    $filtersaletype = intval($_POST['filtersaletype']);

    $extraWhere .= "
        AND `u`.`saletype` = ".$filtersaletype."
    ";
}



// ================================
// PAYMENT METHOD FILTER
// (still needed to restrict WHICH invoices appear)
// ================================
if($filterpaymentmethod !== null){

    $extraWhere .= "
    AND EXISTS (

        SELECT 1

        FROM `tbl_invoice_payment_has_tbl_invoice` AS `iphi`

        INNER JOIN `tbl_invoice_payment_detail` AS `ipd`
        ON `ipd`.`tbl_invoice_payment_idtbl_invoice_payment` 
        = `iphi`.`tbl_invoice_payment_idtbl_invoice_payment`

        WHERE `iphi`.`tbl_invoice_idtbl_invoice`
        = `u`.`idtbl_invoice`

        AND `ipd`.`method` = ".$filterpaymentmethod."

    )
    ";
}




echo json_encode(
    SSP::simple(
        $_POST,
        $sql_details,
        $table,
        $primaryKey,
        $columns,
        $joinQuery,
        $extraWhere
    )
);