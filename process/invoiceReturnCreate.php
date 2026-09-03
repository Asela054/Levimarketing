<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header("Location:../index.php");
    exit;
}
require_once('../connection/db.php');

$userID = $_SESSION['userid'];
$insertdatetime = date('Y-m-d h:i:s');
$today = date('Y-m-d');

$invoiceID = (int) $_POST['invoiceID'];
$remarks   = $conn->real_escape_string($_POST['remarks'] ?? '');

// Expecting productID[] and qty[] arrays from the form, one entry per
// line the user chose to return.
$productIDs = $_POST['productID'] ?? [];
$qtys       = $_POST['qty'] ?? [];

if (empty($productIDs) || count($productIDs) !== count($qtys)) {
    header("Location:../invoicereturnadd.php?action=5");
    exit;
}

// 1. Confirm the invoice exists, get its location
$invStmt = $conn->prepare("SELECT tbl_location_idtbl_location FROM tbl_invoice WHERE idtbl_invoice = ? AND status = 1");
$invStmt->bind_param("i", $invoiceID);
$invStmt->execute();
$invoice = $invStmt->get_result()->fetch_assoc();
$invStmt->close();

if (!$invoice) {
    header("Location:../invoicereturnadd.php?action=5");
    exit;
}

$locationID = (int) $invoice['tbl_location_idtbl_location'];

$conn->begin_transaction();

try {
    $validLines = [];

    // 2. Re-validate every requested line against qty already returned,
    //    server-side, so the browser can't be trusted to enforce this.
    $availStmt = $conn->prepare("
        SELECT d.qty AS sold_qty, d.unitprice,
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
        WHERE d.tbl_invoice_idtbl_invoice = ?
          AND d.tbl_product_idtbl_product = ?
          AND d.status = 1
    ");

    for ($i = 0; $i < count($productIDs); $i++) {
        $productID = (int) $productIDs[$i];
        $reqQty = (float) $qtys[$i];

        if ($reqQty <= 0) {
            continue; // ignore blank/zero rows from the form
        }

        $availStmt->bind_param("ii", $invoiceID, $productID);
        $availStmt->execute();
        $line = $availStmt->get_result()->fetch_assoc();

        if (!$line) {
            throw new Exception("Product $productID was not found on this invoice.");
        }

        $availableQty = (float) $line['sold_qty'] - (float) $line['already_returned'];

        if ($reqQty > $availableQty) {
            throw new Exception("Requested return qty for product $productID exceeds what's available to return.");
        }

        $validLines[] = [
            'productID' => $productID,
            'qty'       => $reqQty,
            'unitprice' => (float) $line['unitprice'],
            'nettotal'  => $reqQty * (float) $line['unitprice'],
        ];
    }
    $availStmt->close();

    if (empty($validLines)) {
        throw new Exception("No valid return lines submitted.");
    }

    $total = array_sum(array_column($validLines, 'nettotal'));

    // 3. Insert the return header (pending approval)
    $headerStmt = $conn->prepare("INSERT INTO tbl_invoice_return
        (date, total, remarks, approvestatus, status, insertdatetime, tbl_user_idtbl_user, tbl_invoice_idtbl_invoice, tbl_location_idtbl_location)
        VALUES (?, ?, ?, 0, 1, ?, ?, ?, ?)");
    $headerStmt->bind_param("sdssiii", $today, $total, $remarks, $insertdatetime, $userID, $invoiceID, $locationID);
    $headerStmt->execute();
    $returnID = $conn->insert_id;
    $headerStmt->close();

    // 4. Insert each return line
    $lineStmt = $conn->prepare("INSERT INTO tbl_invoice_return_detail
        (unitprice, qty, nettotal, comment, status, insertdatetime, tbl_user_idtbl_user, tbl_product_idtbl_product, tbl_invoice_return_idtbl_invoice_return)
        VALUES (?, ?, ?, '', 1, ?, ?, ?, ?)");

    foreach ($validLines as $line) {
        $lineStmt->bind_param(
            "dddsiii",
            $line['unitprice'],
            $line['qty'],
            $line['nettotal'],
            $insertdatetime,
            $userID,
            $line['productID'],
            $returnID
        );
        $lineStmt->execute();
    }
    $lineStmt->close();

    $conn->commit();
    header("Location:../invoicereturn.php?action=4");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    header("Location:../invoicereturnadd.php?action=5");
    exit;
}