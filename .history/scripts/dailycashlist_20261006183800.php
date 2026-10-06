<?php

session_start();

$type       = $_SESSION['privatetype'];
$locationID = intval($_SESSION['location_id']);

/*
 * DataTables server-side processing
 */

// DB table
$table = 'tbl_invoice';

// Primary key
$primaryKey = 'idtbl_invoice';


// ================================
// Payment method filter (optional)
// ================================
$filterpaymentmethod = null;
if (isset($_POST['filterpaymentmethod']) && $_POST['filterpaymentmethod'] != '') {
    $filterpaymentmethod = intval($_POST['filterpaymentmethod']);
}

// ================================
// Total is ALWAYS the per-method amount, so an invoice paid by
// cash + card shows as two rows, each with its own amount.
// ================================
$totalDb = '`pm`.`method_total` AS `total`';


// ================================
// Columns
// ================================
if ($type == 1) {

    $columns = array(
        array(
            'db'    => '`u`.`manuelinvno`',
            'dt'    => 'id',
            'field' => 'manuelinvno'
        ),
        array(
            'db'    => '`ud`.`name`',
            'dt'    => 'name',
            'field' => 'name'
        ),
        array(
            'db'    => '`u`.`date`',
            'dt'    => 'date',
            'field' => 'date'
        ),
        array(
            'db'    => '`u`.`saletype`',
            'dt'    => 'saletype',
            'field' => 'saletype'
        ),
        array(
            'db'    => $totalDb,
            'dt'    => 'total',
            'field' => 'total'
        ),
        array(
            'db'    => '`u`.`manuelinvno`',
            'dt'    => 'manuelinvno',
            'field' => 'manuelinvno'
        ),
        array(
            'db'    => '`pm`.`method`',
            'dt'    => 'method',
            'field' => 'method'
        )
    );

} else {

    $columns = array(
        array(
            'db'    => '`u`.`idtbl_invoice`',
            'dt'    => 'id',
            'field' => 'idtbl_invoice'
        ),
        array(
            'db'    => '`ud`.`name`',
            'dt'    => 'name',
            'field' => 'name'
        ),
        array(
            'db'    => '`u`.`date`',
            'dt'    => 'date',
            'field' => 'date'
        ),
        array(
            'db'    => '`u`.`saletype`',
            'dt'    => 'saletype',
            'field' => 'saletype'
        ),
        array(
            'db'    => $totalDb,
            'dt'    => 'total',
            'field' => 'total'
        ),
        array(
            'db'    => '`u`.`manuelinvno`',
            'dt'    => 'manuelinvno',
            'field' => 'manuelinvno'
        ),
        array(
            'db'    => '`pm`.`method`',
            'dt'    => 'method',
            'field' => 'method'
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


// ================================
// Join
// One row per invoice + payment method
// ================================
$joinQuery = "
FROM `tbl_invoice` AS `u`
LEFT JOIN `tbl_customer` AS `ud`
    ON (`ud`.`idtbl_customer` = `u`.`customerid`)
INNER JOIN (
    SELECT
        `iphi`.`tbl_invoice_idtbl_invoice` AS `invoice_id`,
        `ipd`.`method`                     AS `method`,
        SUM(`ipd`.`amount`)                AS `method_total`
    FROM `tbl_invoice_payment_has_tbl_invoice` AS `iphi`
    INNER JOIN `tbl_invoice_payment_detail` AS `ipd`
        ON `ipd`.`tbl_invoice_payment_idtbl_invoice_payment`
         = `iphi`.`tbl_invoice_payment_idtbl_invoice_payment`
    WHERE `ipd`.`status` = 1
";

// Restrict to one method only when the filter is selected
if ($filterpaymentmethod !== null) {
    $joinQuery .= " AND `ipd`.`method` = " . $filterpaymentmethod . " ";
}

$joinQuery .= "
    GROUP BY `iphi`.`tbl_invoice_idtbl_invoice`, `ipd`.`method`
) AS `pm`
    ON `pm`.`invoice_id` = `u`.`idtbl_invoice`
";


// ================================
// Date filter (validated)
// ================================
$date = isset($_POST['selectdate']) ? $_POST['selectdate'] : '';
$dt   = DateTime::createFromFormat('Y-m-d', $date);
if (!$dt || $dt->format('Y-m-d') !== $date) {
    $date = date('Y-m-d');
}


// ================================
// Base condition
// ================================
if ($type == 2) {

    $extraWhere = "
        `u`.`status` IN (1,2)
        AND `u`.`date` = '$date'
        AND `u`.`tbl_location_idtbl_location` = $locationID
    ";

} else {

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
if (isset($_POST['filtersaletype']) && $_POST['filtersaletype'] != '') {

    $filtersaletype = intval($_POST['filtersaletype']);

    $extraWhere .= "
        AND `u`.`saletype` = " . $filtersaletype . "
    ";
}

// (The old AND EXISTS (...) payment-method block is no longer needed:
//  the INNER JOIN above already limits invoices to the selected method.)


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

?>