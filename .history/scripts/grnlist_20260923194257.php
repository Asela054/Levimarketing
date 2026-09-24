<?php
session_start();
/*
 * Server-side processing for the GRN DETAIL-WISE report (grnreport.php DataTable).
 * Unlike the GRN header list, this returns one row per GRN line item, joined
 * against the GRN header, supplier, product, and location tables. Follows the
 * same ssp.customized.class.php pattern used elsewhere.
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

// Location comes from session, same convention as grn.php.
// Cast to int since it feeds directly into the WHERE clause below.
$locationID = (int) $_SESSION['location_id'];

// gd  = tbl_grndetail  (the line items - drives the report)
// g   = tbl_grn        (the GRN header - date, invoice/dispatch no, status)
// po  = tbl_porder     (only used to fall back to the PO's supplier)
// s   = tbl_supplier   (resolved from the GRN's own supplier first, falling
//                        back to the linked PO's supplier - needed because
//                        "Without PO" GRNs store the supplier directly on tbl_grn)
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

// GRN number search - accepts "GRN-12", "grn12" or "12".
// Everything except digits is stripped, then matched exactly on the GRN id.
if (isset($_POST['search_grn']) && trim($_POST['search_grn']) !== '') {
    $grnID = (int) preg_replace('/\D/', '', $_POST['search_grn']);
    if ($grnID > 0) {
        $extraWhere .= " AND `g`.`idtbl_grn` = $grnID";
    } else {
        // Text with no number in it (e.g. "abc") should return nothing
        $extraWhere .= " AND 1 = 0";
    }
}

// Optional extra filters - supplier and product. Add the matching <select>
// inputs on the report page and they'll be picked up automatically.
if (!empty($_POST['search_supplier'])) {
    $supplierID = (int) $_POST['search_supplier'];
    $extraWhere .= " AND COALESCE(`g`.`tbl_supplier_idtbl_supplier`, `po`.`tbl_supplier_idtbl_supplier`) = $supplierID";
}

if (!empty($_POST['search_product'])) {
    $productID = (int) $_POST['search_product'];
    $extraWhere .= " AND `gd`.`tbl_product_idtbl_product` = $productID";
}

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);