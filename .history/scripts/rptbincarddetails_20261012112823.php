<?php
session_start();
header('Content-Type: application/json');

$type       = $_SESSION['privatetype'];
$locationId = $_SESSION['location_id'];

// Same setting as rptbincardlist.php: which action types count as manual adjustments.
// Leave empty to include every row in tbl_stock_activity_log.
$adjActionTypes = array();

require('config.php');
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);
$conn->set_charset('utf8');

$pid = intval($_POST['product_id'] ?? 0);

$datePattern = '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/';
$from = (!empty($_POST['search_from']) && preg_match($datePattern, $_POST['search_from'])) ? $_POST['search_from'] : date('Y-m-01');
$to   = (!empty($_POST['search_to'])   && preg_match($datePattern, $_POST['search_to']))   ? $_POST['search_to']   : date('Y-m-d');
if ($to < $from) { $to = $from; }

$loc = !empty($locationId) ? intval($locationId) : 0;

$out = array('grn' => array(), 'inv' => array(), 'adj' => array());

// GRNs
$sql = "SELECT g.idtbl_grn AS grnno, g.date, g.invoicenum, g.dispatchnum,
               gd.qty, gd.unitprice, gd.total
        FROM tbl_grndetail gd
        INNER JOIN tbl_grn g ON g.idtbl_grn = gd.tbl_grn_idtbl_grn
        WHERE gd.status = 1 AND g.status = 1 AND g.confirm_status = 1
          AND gd.tbl_product_idtbl_product = ?
          AND g.date BETWEEN ? AND ?" . ($loc ? " AND g.tbl_location_idtbl_location = $loc" : "") . "
        ORDER BY g.date, g.idtbl_grn";
$st = $conn->prepare($sql);
$st->bind_param('iss', $pid, $from, $to);
$st->execute();
$res = $st->get_result();
while ($r = $res->fetch_assoc()) { $out['grn'][] = $r; }
$st->close();

// Invoices
$sql = "SELECT u.idtbl_invoice AS invid, u.taxinvoice_no, u.manuelinvno, u.date,
               c.name AS customer, d.qty, d.freeqty, d.saleprice AS total
        FROM tbl_invoice_detail d
        INNER JOIN tbl_invoice u ON u.idtbl_invoice = d.tbl_invoice_idtbl_invoice
        LEFT JOIN tbl_customer c ON c.idtbl_customer = u.customerid
        WHERE d.status = 1 AND u.status IN (0,1)
          AND d.tbl_product_idtbl_product = ?
          AND u.date BETWEEN ? AND ?" . ($loc ? " AND u.tbl_location_idtbl_location = $loc" : "")
          . ($type == 1 ? " AND u.manuelinvno IS NOT NULL" : "") . "
        ORDER BY u.date, u.idtbl_invoice";
$st = $conn->prepare($sql);
$st->bind_param('iss', $pid, $from, $to);
$st->execute();
$res = $st->get_result();
while ($r = $res->fetch_assoc()) { $out['inv'][] = $r; }
$st->close();

// Stock adjustments
$adjTypeSql = '';
if (!empty($adjActionTypes)) {
	$q = array();
	foreach ($adjActionTypes as $t) { $q[] = "'" . $conn->real_escape_string($t) . "'"; }
	$adjTypeSql = " AND action_type IN (" . implode(',', $q) . ")";
}

$fromDT = $from . ' 00:00:00';
$toDT   = $to . ' 23:59:59';

$sql = "SELECT action_datetime, action_type, old_qty, new_qty, qty_change, reason, username_snapshot
        FROM tbl_stock_activity_log
        WHERE tbl_product_idtbl_product = ?
          AND action_datetime BETWEEN ? AND ?" . ($loc ? " AND tbl_location_idtbl_location = $loc" : "") . $adjTypeSql . "
        ORDER BY action_datetime, idtbl_stock_activity_log";
$st = $conn->prepare($sql);
$st->bind_param('iss', $pid, $fromDT, $toDT);
$st->execute();
$res = $st->get_result();
while ($r = $res->fetch_assoc()) { $out['adj'][] = $r; }
$st->close();

echo json_encode($out);