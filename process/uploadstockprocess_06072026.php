<?php
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');

$userID=$_SESSION['userid'];
$updatedatetime=date('Y-m-d h:i:s');
$status = 1;

if (isset($_FILES['csv_file']['tmp_name']) && isset($_POST['location']) ) {
    $file = $_FILES['csv_file']['tmp_name'];
    $location_id = intval($_POST['location']);

 
    if (($handle = fopen($file, "r")) !== FALSE) {
        $row = 0;
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $row++;

            if ($row == 1) continue;

            $product_id   = $data[0]; 
            $product_name = $data[1]; 
            $quantity = $data[2];

            $checkSql = "SELECT * FROM tbl_stock 
                         WHERE tbl_product_idtbl_product = ? AND tbl_location_idtbl_location = ?";
            $stmt = $conn->prepare($checkSql);
            $stmt->bind_param("ii", $product_id, $location_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {

                $updateSql = "UPDATE tbl_stock 
                              SET qty = qty + ? 
                              WHERE tbl_product_idtbl_product = ? AND tbl_location_idtbl_location = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("iii", $quantity, $product_id, $location_id);
                $updateStmt->execute();
            } else {

               $insertSql = "INSERT INTO tbl_stock 
                (tbl_product_idtbl_product, tbl_location_idtbl_location, qty, status, updatedatetime, tbl_user_idtbl_user) 
                 VALUES (?, ?, ?, ?, ?, ?)";
                $insertStmt = $conn->prepare($insertSql);
                if (!$insertStmt) {
                    die("Insert Prepare failed: " . $conn->error);
                }
                $insertStmt->bind_param("iiiisi", $product_id, $location_id, $quantity, $status, $updatedatetime, $userID);
                $insertStmt->execute();

            }
            $logSql = "INSERT INTO tbl_upload_stock 
                (tbl_product_idtbl_product, tbl_location_idtbl_location, qty, status, insertdatetime, tbl_user_idtbl_user) 
                VALUES (?, ?, ?, ?, ?, ?)";
            $logStmt = $conn->prepare($logSql);
            if (!$logStmt) {
                die("Upload Stock Prepare failed: " . $conn->error);
            }
            $logStmt->bind_param("iiiisi", $product_id, $location_id, $quantity, $status, $updatedatetime, $userID);
            $logStmt->execute();
        }
        fclose($handle);
    }

    header("Location: ../managestock.php?action=7");
    exit;
} else {
    echo "File or location not provided.";
}


