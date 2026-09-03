<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header("Location:../index.php");
    exit;
}
require_once('../connection/db.php');

$userID = $_SESSION['userid'];
$updatedatetime = date('Y-m-d h:i:s');
$today = date('Y-m-d');

$returnID = (int) $_POST['recordID'];

// 1. Load the return header, make sure it exists and isn't already approved
$check = $conn->prepare("SELECT approvestatus, tbl_location_idtbl_location 
                          FROM tbl_invoice_return 
                          WHERE idtbl_invoice_return = ?");
$check->bind_param("i", $returnID);
$check->execute();
$returnRow = $check->get_result()->fetch_assoc();
$check->close();

if (!$returnRow) {
    header("Location:../invoicereturn.php?action=5"); // not found
    exit;
}

if ((int) $returnRow['approvestatus'] === 1) {
    header("Location:../invoicereturn.php?action=7"); // already approved
    exit;
}

$locationID = (int) $returnRow['tbl_location_idtbl_location'];

// 2. Do the stock update + approval flag as one transaction
$conn->begin_transaction();

try {
    $detailStmt = $conn->prepare("SELECT tbl_product_idtbl_product, qty 
                                   FROM tbl_invoice_return_detail 
                                   WHERE tbl_invoice_return_idtbl_invoice_return = ? 
                                     AND status = 1");
    $detailStmt->bind_param("i", $returnID);
    $detailStmt->execute();
    $details = $detailStmt->get_result();

    if ($details->num_rows === 0) {
        throw new Exception("No return lines found for this return.");
    }

    $stockCheckStmt  = $conn->prepare("SELECT idtbl_stock, qty 
                                        FROM tbl_stock 
                                        WHERE tbl_product_idtbl_product = ? 
                                          AND tbl_location_idtbl_location = ?");
    $stockUpdateStmt = $conn->prepare("UPDATE tbl_stock 
                                        SET qty = ?, `update` = ?, updatedatetime = ?, tbl_user_idtbl_user = ? 
                                        WHERE idtbl_stock = ?");
    $stockInsertStmt = $conn->prepare("INSERT INTO tbl_stock 
                                        (qty, `update`, status, updatedatetime, tbl_user_idtbl_user, tbl_product_idtbl_product, tbl_location_idtbl_location) 
                                        VALUES (?, ?, 1, ?, ?, ?, ?)");

    while ($line = $details->fetch_assoc()) {
        $productID = (int) $line['tbl_product_idtbl_product'];
        $returnQty = (float) $line['qty'];

        if ($returnQty <= 0) {
            continue; // skip zero/invalid lines
        }

        $stockCheckStmt->bind_param("ii", $productID, $locationID);
        $stockCheckStmt->execute();
        $stockRow = $stockCheckStmt->get_result()->fetch_assoc();

        if ($stockRow) {
            // Existing stock row for this product/location -> add the returned qty
            $newQty = (float) $stockRow['qty'] + $returnQty;
            $stockID = (int) $stockRow['idtbl_stock'];
            $stockUpdateStmt->bind_param("dssii", $newQty, $today, $updatedatetime, $userID, $stockID);
            $stockUpdateStmt->execute();
        } else {
            // No stock row yet for this product/location -> create one
            $stockInsertStmt->bind_param("dssii", $returnQty, $today, $updatedatetime, $userID, $productID, $locationID);
            $stockInsertStmt->execute();
        }
    }

    $stockCheckStmt->close();
    $stockUpdateStmt->close();
    $stockInsertStmt->close();
    $detailStmt->close();

    // 3. Flag the return as approved
    $approveStmt = $conn->prepare("UPDATE tbl_invoice_return 
                                    SET approvestatus = 1, tbl_user_idtbl_user = ? 
                                    WHERE idtbl_invoice_return = ?");
    $approveStmt->bind_param("ii", $userID, $returnID);
    $approveStmt->execute();
    $approveStmt->close();

    $conn->commit();
    header("Location:../invoicereturn.php?action=4"); // success
    exit;

} catch (Exception $e) {
    $conn->rollback();
    header("Location:../invoicereturn.php?action=5"); // failure
    exit;
}