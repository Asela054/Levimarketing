<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['userid'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Session expired. Please login again.']);
    exit;
}

require_once('../connection/db.php');

$userID     = intval($_SESSION['userid']);
$locationID = intval($_SESSION['location_id'] ?? 0);

$customerID    = intval($_POST['customerid'] ?? 0);
$quotationDate = $_POST['quotationdate'] ?? date('Y-m-d');
$discountTotal = floatval($_POST['discounttotal'] ?? 0);
$vatPercent    = floatval($_POST['vatpercent'] ?? 0);
$validityDays  = intval($_POST['validitydays'] ?? 0);
$remarks       = trim($_POST['remarks'] ?? '');

// 1 = inclusive (prices already include VAT), 2 = exclusive (VAT added on top)
$vatType = intval($_POST['vattype'] ?? 2);
if (!in_array($vatType, [1, 2], true)) {
    $vatType = 2;
}

// Line items arrive as parallel arrays: productid[], description[], qty[], unitprice[]
$productIds   = $_POST['productid']   ?? [];
$descriptions = $_POST['description'] ?? [];
$qtys         = $_POST['qty']         ?? [];
$unitPrices   = $_POST['unitprice']   ?? [];

if ($customerID <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Please select a customer.']);
    exit;
}

if (empty($descriptions)) {
    echo json_encode(['status' => 'error', 'message' => 'Please add at least one line.']);
    exit;
}

// ---- Validate line items and compute totals server-side ----
$lines = [];
$total = 0.0;

foreach ($descriptions as $i => $desc) {
    $desc      = trim($desc);
    $pid       = intval($productIds[$i] ?? 0);
    $qty       = floatval($qtys[$i] ?? 0);
    $unitPrice = floatval($unitPrices[$i] ?? 0);

    if ($desc === '' || $qty <= 0) {
        continue; // skip incomplete rows
    }

    $lineAmount = $qty * $unitPrice;
    $total += $lineAmount;

    $lines[] = [
        'productid'   => $pid > 0 ? $pid : null,
        'description' => $desc,
        'qty'         => $qty,
        'unitprice'   => $unitPrice,
        'amount'      => $lineAmount,
    ];
}

if (empty($lines)) {
    echo json_encode(['status' => 'error', 'message' => 'No valid lines to save.']);
    exit;
}

if ($discountTotal < 0) {
    $discountTotal = 0;
}
if ($discountTotal > $total) {
    $discountTotal = $total;
}

// ---- Whole-bill VAT calculation (mirrors vatinvoiceprocess.php) ----
if ($vatType === 1) {
    // Inclusive - extract VAT for display, don't add it again
    $totalWithVat    = $total - $discountTotal;
    $netTotal        = $vatPercent > 0 ? ($totalWithVat / (1 + ($vatPercent / 100))) : $totalWithVat;
    $vatAmount       = $totalWithVat - $netTotal;
    $netTotalWithVat = $totalWithVat;
} else {
    // Exclusive - add VAT on top of the discounted net total
    $netTotal        = $total - $discountTotal;
    $vatAmount       = $netTotal * $vatPercent / 100;
    $netTotalWithVat = $netTotal + $vatAmount;
}

$updatedatetime = date('Y-m-d H:i:s');

// Prefix for quotation numbers, e.g. LVQ-1, LVQ-2, LVQ-3 ...
define('QUOTATION_NO_PREFIX', 'LVQ-');

$conn->begin_transaction();

try {
    // ---- Insert quotation header (quotation_no filled in after we know the new id) ----
    $stmt = $conn->prepare(
        "INSERT INTO `tbl_quotation` (
            `quotation_no`, `date`, `total`, `discounttotal`, `nettotal`,
            `vattype`, `vatpercent`, `vatamount`, `nettotal_with_vat`,
            `validity_days`, `remarks`, `status`, `converted_to_invoice`,
            `updatedatetime`, `tbl_user_idtbl_user`, `tbl_location_idtbl_location`,
            `tbl_customer_idtbl_customer`
        ) VALUES (
            '', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 0, ?, ?, ?, ?
        )"
    );

    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $stmt->bind_param(
        'sdddidddissiii',
        $quotationDate,
        $total,
        $discountTotal,
        $netTotal,
        $vatType,
        $vatPercent,
        $vatAmount,
        $netTotalWithVat,
        $validityDays,
        $remarks,
        $updatedatetime,
        $userID,
        $locationID,
        $customerID
    );
    $stmt->execute();
    $quotationID = $conn->insert_id;
    $stmt->close();

    // ---- Build quotation number now that we have a guaranteed-unique id ----
    $quotationNo = QUOTATION_NO_PREFIX . $quotationID;

    $updateStmt = $conn->prepare("UPDATE `tbl_quotation` SET `quotation_no` = ? WHERE `idtbl_quotation` = ?");
    $updateStmt->bind_param('si', $quotationNo, $quotationID);
    $updateStmt->execute();
    $updateStmt->close();

    // ---- Insert quotation detail lines. No stock check/deduction — a
    //      quotation does not commit stock, unlike an invoice. ----
    $detailStmt = $conn->prepare(
        "INSERT INTO `tbl_quotation_detail` (
            `description`, `qty`, `unitprice`, `amount`, `status`,
            `updatedatetime`, `tbl_user_idtbl_user`, `tbl_product_idtbl_product`,
            `tbl_quotation_idtbl_quotation`
        ) VALUES (?, ?, ?, ?, 1, ?, ?, ?, ?)"
    );

    if (!$detailStmt) {
        throw new Exception($conn->error);
    }

    foreach ($lines as $line) {
        $detailStmt->bind_param(
            'sdddsiii',
            $line['description'],
            $line['qty'],
            $line['unitprice'],
            $line['amount'],
            $updatedatetime,
            $userID,
            $line['productid'],
            $quotationID
        );
        $detailStmt->execute();
    }
    $detailStmt->close();

    $conn->commit();

    echo json_encode([
        'status'       => 'success',
        'message'      => 'Quotation saved successfully.',
        'quotationid'  => $quotationID,
        'quotationno'  => $quotationNo,
    ]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to save quotation: ' . $e->getMessage(),
    ]);
}