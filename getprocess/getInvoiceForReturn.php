<?php
session_start();
if (!isset($_SESSION['userid'])) {
    http_response_code(403);
    exit;
}
require_once('../connection/db.php');

header('Content-Type: application/json');

// Preferred: dropdown posts the invoice's primary key directly.
$invoiceID = isset($_POST['invoiceID']) ? (int) $_POST['invoiceID'] : 0;

// Fallback: still support lookup by typed tax/manual invoice number.
$search = trim($_POST['invoiceNumber'] ?? '');

if ($invoiceID > 0) {
    $stmt = $conn->prepare("SELECT idtbl_invoice, invtype, taxinvoice_no, manuelinvno, date, total,
                                    nettotal, customerid, tbl_location_idtbl_location, status
                             FROM tbl_invoice
                             WHERE idtbl_invoice = ?
                               AND status = 1
                             LIMIT 1");
    $stmt->bind_param("i", $invoiceID);
} elseif ($search !== '') {
    $stmt = $conn->prepare("SELECT idtbl_invoice, invtype, taxinvoice_no, manuelinvno, date, total,
                                    nettotal, customerid, tbl_location_idtbl_location, status
                             FROM tbl_invoice
                             WHERE (taxinvoice_no = ? OR manuelinvno = ?)
                               AND status = 1
                             LIMIT 1");
    $stmt->bind_param("ss", $search, $search);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'No invoice specified.']);
    exit;
}

$stmt->execute();
$invoice = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$invoice) {
    http_response_code(404);
    echo json_encode(['error' => 'Invoice not found, or it is not in an active state.']);
    exit;
}

$invoiceID = (int) $invoice['idtbl_invoice'];

// Pull the invoice's sold lines, along with product name and how much of
// each product has already been returned (pending or approved, i.e. any
// non-cancelled return record) so we know what's still eligible.
$lineStmt = $conn->prepare("
    SELECT d.idtbl_invoice_detail, d.tbl_product_idtbl_product, d.qty, d.unitprice,
           p.product_name,
           COALESCE((
               SELECT SUM(rd.qty)
               FROM tbl_invoice_return_detail rd
               INNER JOIN tbl_invoice_return r ON r.idtbl_invoice_return = rd.tbl_invoice_return_idtbl_invoice_return
               WHERE r.tbl_invoice_idtbl_invoice = d.tbl_invoice_idtbl_invoice
                 AND rd.tbl_product_idtbl_product = d.tbl_product_idtbl_product
                 AND r.status = 1
                 AND rd.status = 1
           ), 0) AS already_returned
    FROM tbl_invoice_detail d
    LEFT JOIN tbl_product p ON p.idtbl_product = d.tbl_product_idtbl_product
    WHERE d.tbl_invoice_idtbl_invoice = ?
      AND d.status = 1
");
$lineStmt->bind_param("i", $invoiceID);
$lineStmt->execute();
$result = $lineStmt->get_result();

$lines = [];
while ($row = $result->fetch_assoc()) {
    $row['available_qty'] = (float) $row['qty'] - (float) $row['already_returned'];
    $lines[] = $row;
}
$lineStmt->close();

echo json_encode([
    'invoice' => $invoice,
    'lines'   => $lines,
]);