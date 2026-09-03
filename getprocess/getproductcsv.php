<?php
require_once('../connection/db.php');


header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=products.csv');


$output = fopen('php://output', 'w');

fputcsv($output, ['Product ID', 'Product Name', 'Quantity' ]);


$sql = "SELECT p.idtbl_product AS product_id, 
               p.product_name
        FROM tbl_product p
        WHERE p.status = 1"; 

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

fclose($output);
exit;
