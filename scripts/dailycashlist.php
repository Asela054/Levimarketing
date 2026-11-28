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
// Paid amount per method from the payment tables; invoices that have no payment
// rows (e.g. tax invoices with payment_created = 0) fall back to the invoice amount.
$totalDb  = 'COALESCE(`pm`.`method_total`, `u`.`total` - `u`.`discounttotal`) AS `total`';

// Payment method: from the payment tables, else the method saved on the invoice
$methodDb = 'COALESCE(`pm`.`method`, `u`.`paymentmethod`) AS `method`';


// ================================
// Invoice numbers
// Before the cutoff date -> old numbering (manuelinvno / taxinvoice_no)
// On/after the cutoff    -> new numbering (nontaxinvnum_new / taxinvnum_new)
// ================================
$cutoffDate = '2026-11-16';

$nonTaxDb = "CASE WHEN `u`.`date` < '$cutoffDate'
                  THEN `u`.`manuelinvno`
                  ELSE `u`.`nontaxinvnum_new` END AS `nontaxno`";

$taxDb    = "CASE WHEN `u`.`date` < '$cutoffDate'
                  THEN `u`.`taxinvoice_no`
                  ELSE `u`.`taxinvnum_new` END AS `taxno`";


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
            'db'    => $methodDb,
            'dt'    => 'method',
            'field' => 'method'
        ),
        array(
            'db'    => $nonTaxDb,
            'dt'    => 'nontaxno',
            'field' => 'nontaxno'
        ),
        array(
            'db'    => $taxDb,
            'dt'    => 'taxno',
            'field' => 'taxno'
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
            'db'    => $methodDb,
            'dt'    => 'method',
            'field' => 'method'
        ),
        array(
            'db'    => $nonTaxDb,
            'dt'    => 'nontaxno',
            'field' => 'nontaxno'
        ),
        array(
            'db'    => $taxDb,
            'dt'    => 'taxno',
            'field' => 'taxno'
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
LEFT JOIN (
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
// Daily cash collection = fully paid invoices (paymentcomplete = 1),
// both normal (invtype 0) and tax (invtype 1) invoices.
$extraWhere = "
    `u`.`status` IN (1,2)
    AND `u`.`paymentcomplete` = 1
    AND `u`.`date` = '$date'
";

// Location filter.
// Normal invoices (invtype 0) are limited to the logged-in location.
// Tax invoices (invtype 1) are saved under a different location id (e.g. 3),
// so set $taxAllLocations = false if they should ALSO be limited to the logged-in location.
$taxAllLocations = true;

if ($taxAllLocations) {
    $extraWhere .= "
    AND (
          IFNULL(`u`.`invtype`, 0) = 1
       OR `u`.`tbl_location_idtbl_location` = $locationID
    )
    ";
} else {
    $extraWhere .= "
    AND `u`.`tbl_location_idtbl_location` = $locationID
    ";
}


// ================================
// PAYMENT METHOD FILTER
// Matches the payment rows, or the invoice's own method when it has no payment rows
// ================================
if ($filterpaymentmethod !== null) {

    $extraWhere .= "
        AND (
              `pm`.`method` IS NOT NULL
           OR (
                NOT EXISTS (
                    SELECT 1
                    FROM `tbl_invoice_payment_has_tbl_invoice` AS `x1`
                    INNER JOIN `tbl_invoice_payment_detail` AS `x2`
                        ON `x2`.`tbl_invoice_payment_idtbl_invoice_payment`
                         = `x1`.`tbl_invoice_payment_idtbl_invoice_payment`
                    WHERE `x1`.`tbl_invoice_idtbl_invoice` = `u`.`idtbl_invoice`
                      AND `x2`.`status` = 1
                )
                AND `u`.`paymentmethod` = " . $filterpaymentmethod . "
           )
        )
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