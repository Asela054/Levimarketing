<?php
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');

$customerID = isset($_POST['customerID']) ? (int)$_POST['customerID'] : 0;

$outstanding = 0;

if($customerID > 0){
    // Sum of nettotal for all this customer's active invoices, minus sum of all payments made against them.
    // tbl_invoice_payment_has_tbl_invoice links payments to invoices; we sum payamount per invoice.
    $sqlinvoices = "SELECT `idtbl_invoice`, `nettotal` FROM `tbl_invoice` WHERE `status`=1 AND `customerid`='$customerID'";
    $resultinvoices = $conn->query($sqlinvoices);

    if($resultinvoices && $resultinvoices->num_rows > 0){
        while($rowinvoice = $resultinvoices->fetch_assoc()){
            $invoiceID = $rowinvoice['idtbl_invoice'];
            $nettotal = $rowinvoice['nettotal'];

            $sqlpaid = "SELECT SUM(`payamount`) AS `sumpaid` FROM `tbl_invoice_payment_has_tbl_invoice` WHERE `tbl_invoice_idtbl_invoice`='$invoiceID'";
            $resultpaid = $conn->query($sqlpaid);
            $rowpaid = $resultpaid->fetch_assoc();
            $paid = (!empty($rowpaid['sumpaid'])) ? $rowpaid['sumpaid'] : 0;

            $balance = $nettotal - $paid;
            if($balance > 0){
                $outstanding += $balance;
            }
        }
    }
}

$obj = new stdClass();
$obj->outstanding = $outstanding;
$obj->customerID = $customerID;

echo json_encode($obj);
?>
