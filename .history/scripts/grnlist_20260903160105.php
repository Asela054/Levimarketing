<?php
session_start();
/*
 * Server-side processing for the GRN DETAIL-WISE report (grnreport.php DataTable).
 * Unlike grnlist.php (one row per GRN header), this returns one row per GRN
 * line item, joined against the GRN header, supplier, product, and location
 * tables. Follows the same ssp.customized.class.php pattern used elsewhere.
 *
 * Filtered to the logged-in user's location via $_SESSION['location_id'],
 * same as the create-GRN page.
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// Base table for this report (detail rows drive the report)
$table = 'tbl_grndetail';

// Table's primary key
$primaryKey = 'idtbl_grndetail';

// Array of database columns which should be read and sent back to DataTables.
$columns = array(
	array( 'db' => '`gd`.`idtbl_grndetail`', 'dt' => 'idtbl_grndetail', 'field' => 'idtbl_grndetail' ),
	array( 'db' => '`g`.`idtbl_grn`',         'dt' => 'idtbl_grn',       'field' => 'idtbl_grn' ),
	array( 'db' => '`g`.`date`',              'dt' => 'date',            'field' => 'date' ),
	array( 'db' => '`g`.`porder_id`',         'dt' => 'porder_id',       'field' => 'porder_id' ),
	array( 'db' => '`s`.`suppliername`',      'dt' => 'suppliername',    'field' => 'suppliername' ),
	array( 'db' => '`l`.`location`',          'dt' => 'location',        'field' => 'location' ),
	array( 'db' => '`g`.`invoicenum`',        'dt' => 'invoicenum',      'field' => 'invoicenum' ),
	array( 'db' => '`g`.`dispatchnum`',       'dt' => 'dispatchnum',     'field' => 'dispatchnum' ),
	array( 'db' => '`p`.`product_code`',      'dt' => 'product_code',    'field' => 'product_code' ),
	array( 'db' => '`p`.`product_name`',      'dt' => 'product_name',    'field' => 'product_name' ),
	array( 'db' => '`gd`.`type`',             'dt' => 'type',            'field' => 'type' ),
	array( 'db' => '`gd`.`qty`',              'dt' => 'qty',             'field' => 'qty' ),
	array( 'db' => '`gd`.`unitprice`',        'dt' => 'unitprice',       'field' => 'unitprice' ),
	array( 'db' => '`gd`.`total`',            'dt' => 'total',           'field' => 'total' ),
	array( 'db' => '`g`.`confirm_status`',    'dt' => 'confirm_status',  'field' => 'confirm_status' )
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

require('ssp.customized.class.php');

// Location comes from session, same convention as grn.php / grnlist.php.
// Cast to int since it feeds directly into the WHERE clause below.
$locationID = (int) $_SESSION['location_id'];

// gd  = tbl_grndetail  (the line items - drives the report)
// g   = tbl_grn        (the GRN header - date, invoice/dispatch no, status)
// po  = tbl_porder     (only used to fall back to the PO's supplier)
// s   = tbl_supplier   (resolved from the GRN's own supplier first, falling
//                        back to the linked PO's supplier - same logic as
//                        grnlist.php, needed because "Without PO" GRNs store
//                        the supplier directly on tbl_grn)
// l   = tbl_location
// p   = tbl_product    (product on each line item)
$joinQuery = "FROM `tbl_grndetail` AS `gd`
    INNER JOIN `tbl_grn` AS `g` ON (`g`.`idtbl_grn` = `gd`.`tbl_grn_idtbl_grn`)
    LEFT JOIN `tbl_porder` AS `po` ON (`po`.`idtbl_porder` = `g`.`porder_id` AND `g`.`porder_id` > 0)
    LEFT JOIN `tbl_supplier` AS `s` ON (`s`.`idtbl_supplier` = COALESCE(`g`.`tbl_supplier_idtbl_supplier`, `po`.`tbl_supplier_idtbl_supplier`))
    LEFT JOIN `tbl_location` AS `l` ON (`l`.`idtbl_location` = `g`.`tbl_location_idtbl_location`)
    LEFT JOIN `tbl_product` AS `p` ON (`p`.`idtbl_product` = `gd`.`tbl_product_idtbl_product`)";

// status = 1 on both header and detail so voided/cancelled GRNs and lines
// don't leak into the report; location filter matches the session location.
$extraWhere = "`g`.`status` = 1 AND `gd`.`status` = 1 AND `g`.`tbl_location_idtbl_location` = $locationID";

if (!empty($_POST['search_date'])) {
    $date = $_POST['search_date'];
    $extraWhere .= " AND `g`.`date` = '$date'";
} elseif (!empty($_POST['search_week'])) {
    $week = $_POST['search_week'];
    $weeksep = explode('-W', $week);
    $year = $weeksep[0];
    $week1 = $weeksep[1];
    $dto = new DateTime();
    $dto->setISODate($year, $week1);
    $startDate = $dto->format('Y-m-d');
    $dto->modify('+6 days');
    $endDate = $dto->format('Y-m-d');

    $extraWhere .= " AND `g`.`date` BETWEEN '$startDate' AND '$endDate'";
} elseif (!empty($_POST['search_month'])) {
    $month = $_POST['search_month'];
    $month_arr = explode('-', $month);
    $extraWhere .= " AND YEAR(`g`.`date`) = '$month_arr[0]' AND MONTH(`g`.`date`) = '$month_arr[1]'";
} elseif (!empty($_POST['search_from_date']) && !empty($_POST['search_to_date'])) {
    $from_date = $_POST['search_from_date'];
    $to_date = $_POST['search_to_date'];

    $extraWhere .= " AND `g`.`date` BETWEEN '$from_date' AND '$to_date'";
}

// Optional extra filters for a "full detail" report - supplier and product,
// on top of the date-range filters above. Add the matching <select> inputs
// on the report page and they'll be picked up automatically.
if (!empty($_POST['search_supplier'])) {
    $supplierID = (int) $_POST['search_supplier'];
    $extraWhere .= " AND COALESCE(`g`.`tbl_supplier_idtbl_supplier`, `po`.`tbl_supplier_idtbl_supplier`) = $supplierID";
}

if (!empty($_POST['search_product'])) {
    $productID = (int) $_POST['search_product'];
    $extraWhere .= " AND `gd`.`tbl_product_idtbl_product` = $productID";
}

if (!empty($_POST['search_grn'])) {
    $grnID = (int) $_POST['search_grn'];
    $extraWhere .= " AND `g`.`idtbl_grn` = $grnID";
}

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);