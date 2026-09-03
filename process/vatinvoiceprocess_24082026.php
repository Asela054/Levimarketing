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
$invoiceDate   = $_POST['invoicedate'] ?? date('Y-m-d');
$discountTotal = floatval($_POST['discounttotal'] ?? 0);
$vatPercent    = floatval($_POST['vatpercent'] ?? 0);
$saleType      = intval($_POST['saletype'] ?? 1); // 1 = Retail, 2 = Wholesale

// 'exclusive' = entered prices do NOT include VAT -> VAT is added to the totals
// 'inclusive' = entered prices already include VAT -> VAT is NOT added again
$vatType = strtolower(trim($_POST['vattype'] ?? 'inclusive'));
if (!in_array($vatType, ['inclusive', 'exclusive'], true)) {
    $vatType = 'inclusive';
}

// Line items arrive as parallel arrays: productid[], qty[], unitprice[]
$productIds = $_POST['productid'] ?? [];
$qtys       = $_POST['qty'] ?? [];
$unitPrices = $_POST['unitprice'] ?? [];

if ($customerID <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Please select a customer.']);
    exit;
}

if (empty($productIds)) {
    echo json_encode(['status' => 'error', 'message' => 'Please add at least one product line.']);
    exit;
}

// ---- Validate line items and compute totals server-side. Never trust
//      totals sent from the browser - always recalculate them here. ----
$lines = [];
$total = 0.0;

foreach ($productIds as $i => $pid) {
    $pid       = intval($pid);
    $qty       = floatval($qtys[$i] ?? 0);
    $unitPrice = floatval($unitPrices[$i] ?? 0);

    if ($pid <= 0 || $qty <= 0) {
        continue; // skip incomplete rows instead of failing the whole invoice
    }

    $lineTotal = $qty * $unitPrice;
    $total += $lineTotal;

    $lines[] = [
        'productid' => $pid,
        'qty'       => $qty,
        'unitprice' => $unitPrice,
        'saleprice' => $lineTotal,
    ];
}

if (empty($lines)) {
    echo json_encode(['status' => 'error', 'message' => 'No valid product lines to save.']);
    exit;
}

if ($discountTotal < 0) {
    $discountTotal = 0;
}
if ($discountTotal > $total) {
    $discountTotal = $total;
}

// ---- Whole-bill VAT calculation (mirrors the frontend logic) ----
if ($vatType === 'inclusive') {
    // Entered prices already include VAT. Extract VAT for display/reporting,
    // but do NOT add it again on top of the total.
    $totalWithVat    = $total - $discountTotal;
    $netTotal        = $vatPercent > 0 ? ($totalWithVat / (1 + ($vatPercent / 100))) : $totalWithVat;
    $vatAmount       = $totalWithVat - $netTotal;
    $netTotalWithVat = $totalWithVat; // VAT not added on top
} else {
    // Entered prices do NOT include VAT. Add VAT on top of the discounted net total.
    $netTotal        = $total - $discountTotal;
    $vatAmount        = $netTotal * $vatPercent / 100;
    $netTotalWithVat = $netTotal + $vatAmount; // VAT added on top
}

$updatedatetime = date('Y-m-d H:i:s');

$conn->begin_transaction();

try {
    // ---- Insert invoice header (taxinvoice_no filled in after we know the new id) ----
    $stmt = $conn->prepare(
        "INSERT INTO `tbl_invoice` (
            `invtype`, `manuelinvno`, `taxinvoice_no`, `date`, `total`, `discounttotal`, `nettotal`,
            `vattype`, `vatpercent`, `vatamount`, `nettotal_with_vat`, `saletype`, `paymentmethod`, `paymentcomplete`,
            `payment_created`, `chequesend`, `companydiffsend`, `ref_id`, `trackingnumber`, `deliverystatus`,
            `addtoaccountstatus`, `status`, `qtycancelstatus`, `qtyreason`, `qty_checked_user`,
            `qty_updatedatetime`, `updatedatetime`, `tbl_user_idtbl_user`, `customerid`, `tbl_location_idtbl_location`
        ) VALUES (
            1, 0, '', ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, '', 0, CURDATE(), ?, ?, ?, ?
        )"
    );

    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $stmt->bind_param(
        'sdddsdddisiii',
        $invoiceDate,
        $total,
        $discountTotal,
        $netTotal,
        $vatType,
        $vatPercent,
        $vatAmount,
        $netTotalWithVat,
        $saleType,
        $updatedatetime,
        $userID,
        $customerID,
        $locationID
    );
    $stmt->execute();
    $invoiceID = $conn->insert_id;
    $stmt->close();

    // ---- Now that we have a guaranteed-unique id, build the invoice number ----
    // Format: {yy}{MMM}_LV1_{00000}  e.g. 26JUL_LV1_00007
    $companyID = 1; // Levi Marketing Pvt Ltd
    $qqqqMap = [
        1 => 'LV1',
    ];
    $qqqq = $qqqqMap[$companyID] ?? 'GEN1';

    $yy  = date('y', strtotime($invoiceDate));
    $mmm = strtoupper(date('M', strtotime($invoiceDate)));
    $taxDatePrefix = $yy . $mmm . '_' . $qqqq . '_';

    $taxInvoiceNo = $taxDatePrefix . sprintf('%05d', $invoiceID);

    $updateStmt = $conn->prepare("UPDATE `tbl_invoice` SET `taxinvoice_no` = ? WHERE `idtbl_invoice` = ?");
    $updateStmt->bind_param('si', $taxInvoiceNo, $invoiceID);
    $updateStmt->execute();
    $updateStmt->close();

    // ---- Insert invoice detail lines ----
    $detailStmt = $conn->prepare(
        "INSERT INTO `tbl_invoice_detail` (
            `qty`, `freeqty`, `freeproductid`, `unitprice`, `editedprice`, `saleprice`,
            `discountpresentage`, `discountamount`, `editstatus`, `status`, `updatedatetime`,
            `tbl_user_idtbl_user`, `tbl_product_idtbl_product`, `tbl_invoice_idtbl_invoice`
        ) VALUES (?, 0, 0, ?, 0, ?, 0, 0, 0, 1, ?, ?, ?, ?)"
    );

    if (!$detailStmt) {
        throw new Exception($conn->error);
    }

    foreach ($lines as $line) {
        $detailStmt->bind_param(
            'dddsiii',
            $line['qty'],
            $line['unitprice'],
            $line['saleprice'],
            $updatedatetime,
            $userID,
            $line['productid'],
            $invoiceID
        );
        $detailStmt->execute();
    }
    $detailStmt->close();

    // ---- Reduce stock IN PLACE. Lock the existing stock row for this
    //      product/location (FOR UPDATE, so two invoices can't both read the
    //      same qty before either commits), re-validate availability
    //      server-side, then decrement that row's qty. No new row is created. ----
    $stockSelectStmt = $conn->prepare(
        "SELECT `idtbl_stock`, `qty`
         FROM `tbl_stock`
         WHERE `tbl_product_idtbl_product` = ? AND `tbl_location_idtbl_location` = ? AND `status` = 1
         ORDER BY `idtbl_stock` DESC
         LIMIT 1
         FOR UPDATE"
    );
    $stockUpdateStmt = $conn->prepare(
        "UPDATE `tbl_stock`
         SET `qty` = ?, `updatedatetime` = ?, `tbl_user_idtbl_user` = ?
         WHERE `idtbl_stock` = ?"
    );

    if (!$stockSelectStmt || !$stockUpdateStmt) {
        throw new Exception($conn->error);
    }

    foreach ($lines as $line) {
        $stockSelectStmt->bind_param('ii', $line['productid'], $locationID);
        $stockSelectStmt->execute();
        $stockRow = $stockSelectStmt->get_result()->fetch_assoc();

        if (!$stockRow) {
            throw new Exception(
                'No stock record found for product ID ' . $line['productid'] . ' at this location.'
            );
        }

        $availableQty = floatval($stockRow['qty']);

        if ($line['qty'] > $availableQty) {
            throw new Exception(
                'Insufficient stock for product ID ' . $line['productid'] .
                ' (available: ' . $availableQty . ', requested: ' . $line['qty'] . ')'
            );
        }

        $newQty = $availableQty - $line['qty'];
        $stockIdToUpdate = intval($stockRow['idtbl_stock']);

        $stockUpdateStmt->bind_param(
            'dsii',
            $newQty,
            $updatedatetime,
            $userID,
            $stockIdToUpdate
        );
        $stockUpdateStmt->execute();
    }
    $stockSelectStmt->close();
    $stockUpdateStmt->close();

    $conn->commit();

    echo json_encode([
        'status'       => 'success',
        'message'      => 'Invoice saved successfully.',
        'invoiceid'    => $invoiceID,
        'taxinvoiceno' => $taxInvoiceNo,
    ]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to save invoice: ' . $e->getMessage(),
    ]);
}