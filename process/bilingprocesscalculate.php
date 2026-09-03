<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sale = floatval($_POST['sale']);
    $discountpercentage = floatval($_POST['discountpercentage']);
    $qty = floatval($_POST['qty']);

    $total = $sale * $qty;
    $netAmount = $total - ($total * $discountpercentage / 100);

    echo json_encode(['netAmount' => number_format($netAmount, 2)]);
}
?>
