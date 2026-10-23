<?php
session_start();
/*
 * Server-side processing for the SUPPLIER PAYMENT REPORT (supplierpaymentreport.php).
 * One row per supplier payment. Same ssp.customized.class.php column-array pattern
 * as goodreceivelist.php. Filters (all optional, sent by the report page):
 *   fromdate / todate (Y-m-d), supplier (id), paymethod (1/2/3), paystatus (pending/approved/cancelled)
 * The JSON also carries a "summary" block (approved / pending / cancelled totals for the
 * same filters) so the page can show totals above the table.
 */

if (!isset($_SESSION['userid'])) {
    http_response_code(403);
    exit;
}

$table = 'tbl_supplier_payment';
$primaryKey = 'idtbl_supplier_payment';

$columns = array(
    array( 'db' => '`p`.`idtbl_supplier_payment`', 'dt' => 'idtbl_supplier_payment', 'field' => 'idtbl_supplier_payment' ),
    array( 'db' => '`p`.`date`',                   'dt' => 'date',                   'field' => 'date' ),
    array( 'db' => '`s`.`suppliername`',           'dt' => 'suppliername',           'field' => 'suppliername' ),
    array( 'db' => '`p`.`paymethod`',              'dt' => 'paymethod',              'field' => 'paymethod' ),
    array( 'db' => '`p`.`chequeno`',               'dt' => 'chequeno',               'field' => 'chequeno' ),
    array( 'db' => '`p`.`chequedate`',             'dt' => 'chequedate',             'field' => 'chequedate' ),
    array( 'db' => '`p`.`bankref`',                'dt' => 'bankref',                'field' => 'bankref' ),
    array( 'db' => '`p`.`totalpayment`',           'dt' => 'totalpayment',           'field' => 'totalpayment' ),
    array( 'db' => '`p`.`status`',                 'dt' => 'status',                 'field' => 'status' ),
    array( 'db' => '`p`.`confirm_status`',         'dt' => 'confirm_status',         'field' => 'confirm_status' ),
    array( 'db' => '`p`.`approvedatetime`',        'dt' => 'approvedatetime',        'field' => 'approvedatetime' )
);

// SQL server connection information
require('config.php');
$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

require('ssp.customized.class.php');

$joinQuery = "FROM `tbl_supplier_payment` AS `p`
    JOIN `tbl_supplier` AS `s` ON (`s`.`idtbl_supplier` = `p`.`tbl_supplier_idtbl_supplier`)";

// ---- filters (everything is validated / cast before it touches SQL) ----
$where = array('1=1');

$fromdate = isset($_POST['fromdate']) ? $_POST['fromdate'] : '';
$todate = isset($_POST['todate']) ? $_POST['todate'] : '';
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fromdate)) { $where[] = "`p`.`date` >= '$fromdate'"; }
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $todate)) { $where[] = "`p`.`date` <= '$todate'"; }

$supplierID = isset($_POST['supplier']) ? (int) $_POST['supplier'] : 0;
if ($supplierID > 0) { $where[] = "`p`.`tbl_supplier_idtbl_supplier` = $supplierID"; }

$paymethod = isset($_POST['paymethod']) ? (int) $_POST['paymethod'] : 0;   // 1 Cash, 2 Cheque, 3 Bank Transfer
if (in_array($paymethod, array(1, 2, 3), true)) { $where[] = "`p`.`paymethod` = $paymethod"; }

$paystatus = isset($_POST['paystatus']) ? $_POST['paystatus'] : '';
if ($paystatus === 'pending') {
    $where[] = "`p`.`status` = 1 AND `p`.`confirm_status` = 0";
} elseif ($paystatus === 'approved') {
    $where[] = "`p`.`status` = 1 AND `p`.`confirm_status` = 1";
} elseif ($paystatus === 'cancelled') {
    $where[] = "`p`.`status` = 3";
}

$extraWhere = implode(' AND ', $where);

$result = SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere );

// ---- totals for the same filters (the DataTables search box is not part of these) ----
$summary = array('approved' => 0, 'pending' => 0, 'cancelled' => 0);
$mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);
if (!$mysqli->connect_errno) {
    $sumSql = "SELECT
            IFNULL(SUM(CASE WHEN `p`.`status` = 1 AND `p`.`confirm_status` = 1 THEN `p`.`totalpayment` END), 0) AS approved,
            IFNULL(SUM(CASE WHEN `p`.`status` = 1 AND `p`.`confirm_status` = 0 THEN `p`.`totalpayment` END), 0) AS pending,
            IFNULL(SUM(CASE WHEN `p`.`status` = 3 THEN `p`.`totalpayment` END), 0) AS cancelled
        FROM `tbl_supplier_payment` AS `p`
        WHERE $extraWhere";
    $resultSum = $mysqli->query($sumSql);
    if ($resultSum) { $summary = $resultSum->fetch_assoc(); }
    $mysqli->close();
}
$result['summary'] = $summary;

echo json_encode($result);