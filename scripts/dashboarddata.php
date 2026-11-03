<?php
session_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

set_exception_handler(function ($e) {
    echo json_encode(array('error' => $e->getMessage()));
    exit;
});
register_shutdown_function(function () {
    $e = error_get_last();
    if ($e && in_array($e['type'], array(E_ERROR, E_PARSE, E_COMPILE_ERROR, E_CORE_ERROR))) {
        echo json_encode(array('error' => $e['message']));
    }
});
require_once('../connection/db.php');
header('Content-Type: application/json');

/* Which session value holds the user's location? 0 = no location filter */
$loc = isset($_SESSION['location']) ? (int)$_SESSION['location'] : 0;   // <-- CHANGE key name if different

/* Count only confirmed GRNs as purchases? set false if your GRNs never get confirmed */
define('GRN_NEEDS_CONFIRM', true);

/* ---------- input ---------- */
function validDate($d) {
    return is_string($d) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) && strtotime($d) !== false;
}
$today      = date('Y-m-d');
$monthStart = date('Y-m-01');
$monthEnd   = date('Y-m-t');

$from = isset($_GET['from']) && validDate($_GET['from']) ? $_GET['from'] : $monthStart;
$to   = isset($_GET['to'])   && validDate($_GET['to'])   ? $_GET['to']   : $monthEnd;
if ($from > $to) { $t = $from; $from = $to; $to = $t; }

/* ---------- helpers ---------- */
function rows($conn, $sql) {
    $out = array();
    $res = $conn->query($sql);
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $out[$r['d']] = (float)$r['amt'];
        }
    }
    return $out;
}

/*
 * Returns one array per measure, each keyed by date => amount, for $from..$to
 */
function fetchRange($conn, $from, $to, $loc) {
    $locInvoice = $loc > 0 ? " AND i.tbl_location_idtbl_location = $loc" : "";
    $locGrn     = $loc > 0 ? " AND g.tbl_location_idtbl_location = $loc" : "";
    $locGrnRet  = $loc > 0 ? " AND r.tbl_location_idtbl_location = $loc" : "";
    $locInvRet  = $loc > 0 ? " AND ir.tbl_location_idtbl_location = $loc" : "";

    /* price comes from tbl_product, NOT tbl_invoice_detail:
       saletype 1 = retail  -> tbl_product.saleprice
       saletype 2 = wholesale -> tbl_product.wholesaleprice            */
    $price = "CASE i.saletype WHEN 1 THEN p.saleprice WHEN 2 THEN p.wholesaleprice ELSE 0 END";

    $data = array();

    /* SALES (qty x product price) */
    $data['sales'] = rows($conn, "
        SELECT i.`date` AS d, SUM(d.qty * ($price)) AS amt
        FROM tbl_invoice i
        JOIN tbl_invoice_detail d ON d.tbl_invoice_idtbl_invoice = i.idtbl_invoice AND d.status = 1
        JOIN tbl_product p ON p.idtbl_product = d.tbl_product_idtbl_product
        WHERE i.status = 1 AND i.`date` BETWEEN '$from' AND '$to' $locInvoice
        GROUP BY i.`date`");

    /* COST of goods sold (qty x product unit price) - used for Profit tab */
    $data['cost'] = rows($conn, "
        SELECT i.`date` AS d, SUM(d.qty * p.unitprice) AS amt
        FROM tbl_invoice i
        JOIN tbl_invoice_detail d ON d.tbl_invoice_idtbl_invoice = i.idtbl_invoice AND d.status = 1
        JOIN tbl_product p ON p.idtbl_product = d.tbl_product_idtbl_product
        WHERE i.status = 1 AND i.`date` BETWEEN '$from' AND '$to' $locInvoice
        GROUP BY i.`date`");

    /* CUSTOMER RETURNS = approved invoice returns */
    $data['returns'] = rows($conn, "
        SELECT ir.`date` AS d, SUM(ir.total) AS amt
        FROM tbl_invoice_return ir
        WHERE ir.status = 1 AND ir.approvestatus = 1
          AND ir.`date` BETWEEN '$from' AND '$to' $locInvRet
        GROUP BY ir.`date`");

    /* PURCHASES = GRN values */
    $grnConfirm = GRN_NEEDS_CONFIRM ? " AND g.confirm_status = 1" : "";
    $data['purchases'] = rows($conn, "
        SELECT g.`date` AS d, SUM(g.total) AS amt
        FROM tbl_grn g
        WHERE g.status = 1 $grnConfirm
          AND g.`date` BETWEEN '$from' AND '$to' $locGrn
        GROUP BY g.`date`");

    /* SUPPLIER RETURNS = approved GRN returns */
    $data['purchase_returns'] = rows($conn, "
        SELECT r.`date` AS d, SUM(r.total) AS amt
        FROM tbl_grn_return r
        WHERE r.status = 1 AND r.confirm_status = 1
          AND r.`date` BETWEEN '$from' AND '$to' $locGrnRet
        GROUP BY r.`date`");

    /* COLLECTION = money received from customers (invoice payment tables).
       Location is applied through the invoices the payment was made against. */
    $locCollect = $loc > 0
        ? " AND EXISTS (SELECT 1 FROM tbl_invoice_payment_has_tbl_invoice h
                        JOIN tbl_invoice i ON i.idtbl_invoice = h.tbl_invoice_idtbl_invoice
                        WHERE h.tbl_invoice_payment_idtbl_invoice_payment = pay.idtbl_invoice_payment
                          AND i.tbl_location_idtbl_location = $loc)"
        : "";
    $data['collection'] = rows($conn, "
        SELECT pay.`date` AS d, SUM(pd.amount) AS amt
        FROM tbl_invoice_payment pay
        JOIN tbl_invoice_payment_detail pd
             ON pd.tbl_invoice_payment_idtbl_invoice_payment = pay.idtbl_invoice_payment AND pd.status = 1
        WHERE pay.status = 1 AND pd.chequereturnstatus = 0
          AND pay.`date` BETWEEN '$from' AND '$to' $locCollect
        GROUP BY pay.`date`");

    /* PAYMENT = approved payments made to suppliers (location via the GRNs paid) */
    $locSupPay = $loc > 0
        ? " AND EXISTS (SELECT 1 FROM tbl_supplier_payment_detail sd
                        JOIN tbl_grn g ON g.idtbl_grn = sd.tbl_grn_idtbl_grn
                        WHERE sd.tbl_supplier_payment_idtbl_supplier_payment = sp.idtbl_supplier_payment
                          AND g.tbl_location_idtbl_location = $loc)"
        : "";
    $data['payment'] = rows($conn, "
        SELECT sp.`date` AS d, SUM(sp.totalpayment) AS amt
        FROM tbl_supplier_payment sp
        WHERE sp.status = 1 AND sp.confirm_status = 1
          AND sp.`date` BETWEEN '$from' AND '$to' $locSupPay
        GROUP BY sp.`date`");

    /* EXPENSES (no location column on this table) */
    $data['expenses'] = rows($conn, "
        SELECT e.paymentdate AS d, SUM(e.amount) AS amt
        FROM tbl_expensepayment e
        WHERE e.status = 1 AND e.paymentdate BETWEEN '$from' AND '$to'
        GROUP BY e.paymentdate");

    return $data;
}

/* ---------- cards ---------- */
$month = fetchRange($conn, $monthStart, $monthEnd, $loc);
$cards = array('month' => array(), 'today' => array());
$map = array(
    'sales'      => 'sales',
    'purchase'   => 'purchases',
    'collection' => 'collection',
    'payment'    => 'payment'
);
foreach ($map as $card => $key) {
    $cards['month'][$card] = round(array_sum($month[$key]), 2);
    $cards['today'][$card] = round(isset($month[$key][$today]) ? $month[$key][$today] : 0, 2);
}

/* ---------- chart ---------- */
$range = ($from === $monthStart && $to === $monthEnd) ? $month : fetchRange($conn, $from, $to, $loc);

$labels = array();
$series = array(
    'sales' => array(), 'returns' => array(), 'purchases' => array(), 'purchase_returns' => array(),
    'collection' => array(), 'payment' => array(), 'expenses' => array(),
    'cost' => array(), 'gross' => array(), 'net' => array()
);
$cur = new DateTime($from);
$end = new DateTime($to);
while ($cur <= $end) {
    $d = $cur->format('Y-m-d');
    $labels[] = $d;
    foreach (array('sales','returns','purchases','purchase_returns','collection','payment','expenses','cost') as $k) {
        $series[$k][] = round(isset($range[$k][$d]) ? $range[$k][$d] : 0, 2);
    }
    $i = count($labels) - 1;
    $series['gross'][] = round($series['sales'][$i] - $series['cost'][$i], 2);
    $series['net'][]   = round($series['gross'][$i] - $series['expenses'][$i], 2);
    $cur->modify('+1 day');
}

echo json_encode(array(
    'cards'  => $cards,
    'labels' => $labels,
    'series' => $series
));