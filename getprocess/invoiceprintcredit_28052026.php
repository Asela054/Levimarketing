<?php
session_start();
require_once('../connection/db.php');

$recordID=$_GET['recordID'];
$cashier=$_SESSION['name'];
// $recordID='20';

$sqlinvoiceinfo="SELECT `tbl_invoice`.`idtbl_invoice`, `tbl_invoice`.`date`, `tbl_invoice`.`total`, `tbl_invoice`.`discounttotal`, `tbl_invoice`.`nettotal`, `tbl_invoice`.`paymentcomplete`, `tbl_customer`.`name`, `tbl_customer`.`address` FROM `tbl_invoice` LEFT JOIN `tbl_customer` ON `tbl_customer`.`idtbl_customer`=`tbl_invoice`.`customerid` WHERE `tbl_invoice`.`status`=1 AND `tbl_invoice`.`idtbl_invoice`='$recordID'";
$resultinvoiceinfo =$conn-> query($sqlinvoiceinfo); 
$rowinvoiceinfo = $resultinvoiceinfo-> fetch_assoc();

$sqlinvoicedetail="SELECT `tbl_product`.`product_name`, `tbl_invoice_detail`.`qty`, `tbl_invoice_detail`.`saleprice`, `tbl_invoice_detail`.`discountpresentage` FROM `tbl_invoice_detail` LEFT JOIN `tbl_product` ON `tbl_product`.`idtbl_product`=`tbl_invoice_detail`.`tbl_product_idtbl_product` WHERE `tbl_invoice_detail`.`tbl_invoice_idtbl_invoice`='$recordID' AND `tbl_invoice_detail`.`status`=1";
$resultinvoicedetail=$conn->query($sqlinvoicedetail);

$sqlpayment="SELECT SUM(`payment`) AS `sumpayment` FROM `tbl_invoice_payment` LEFT JOIN `tbl_invoice_payment_has_tbl_invoice` ON `tbl_invoice_payment_has_tbl_invoice`.`tbl_invoice_payment_idtbl_invoice_payment`=`tbl_invoice_payment`.`idtbl_invoice_payment` WHERE `tbl_invoice_payment_has_tbl_invoice`.`tbl_invoice_idtbl_invoice`='$recordID'";
$resultpayment =$conn-> query($sqlpayment); 
$rowpayment = $resultpayment-> fetch_assoc();

if(empty($rowpayment)){$totalpay=0;}
else{$totalpay=$rowpayment['sumpayment'];}
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Cutive+Mono&display=swap" rel="stylesheet">
    <!-- <link href="https://fonts.googleapis.com/css2?family=Fira+Mono&display=swap" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css">
    <title>Invoice</title>
    <!-- <style media="print">
        * {
            font-family: 'Fira Mono', monospace;
        }
        table,tr,th,td{
            font-family: 'Fira Mono', monospace;
        }
        img{
            width:200px;
            height:100px;
        }
    </style>
    <style>
        * {
            font-family: 'Fira Mono', monospace;
        }
        table,tr,th,td{
            font-family: 'Fira Mono', monospace;
        }
        img{
            width:100px;
            height:100px;
        }
    </style> -->
    <style media="print">
        * {
            font-family: 'Cutive Mono', monospace;
            font-weight: 600;
        }
        table,tr,th,td{
            font-family: 'Cutive Mono', monospace;
            font-weight: 600;
        }
        img{
            width:200px;
            height:100px;
        }
    </style>
    <style>
        * {
            font-family: 'Cutive Mono', monospace;
            font-weight: 600;
        }
        table,tr,th,td{
            font-family: 'Cutive Mono', monospace;
            font-weight: 600;
        }
        img{
            width:100px;
            height:100px;
        }
    </style>
</head>

<body>
    <?php //print_r($invoiceproduct->result()); ?>
    <div id='DivIdToPrint'>
        <table style="width:100%;">
            <tr>
                <td style="text-align: center; font-size:16px;border-bottom:1px dotted black;" colspan="2">
                    <h2 class="font-weight-light" style="margin-top:0;margin-bottom:0;"><span style="font-size:14px;vertical-align: top;">New </span>Levi Marketing Pvt Ltd</h2>
                    No.61/1/C,Jayaweera Mawatha,Gonawala,Kelaniya<br>
                    Tel: +94 75 7000 700 / +94 75 7000 300
                </td>
            </tr>
            <tr>
                <td style="text-align: left; font-size:14px;">Date: <?php echo $rowinvoiceinfo['date'] ?></td>
                <td style="text-align: right; font-size:14px;">Customer Name: <?php echo $rowinvoiceinfo['name'] ?></td>
            </tr>
            <tr>
                <td style="text-align: left; font-size:14px;">Invoice No: INV-<?php echo $rowinvoiceinfo['idtbl_invoice'] ?></td>
                <td style="text-align: right; font-size:14px;">Address: <?php echo $rowinvoiceinfo['address'] ?></td>
            </tr>
            <tr>
                <td style="text-align: left; font-size:14px;border-bottom:1px dotted black;">Cashier: <?php echo $cashier; ?></td>
                <td style="text-align: right; font-size:14px;border-bottom:1px dotted black;"></td>
            </tr>
            <tr>
                <td style="text-align: center;border-bottom:1px dotted black;" colspan="2">
                    <table style="width:100%;">
                        <tr>
                            <td style="text-align: left; font-size:14px;border-bottom:1px dotted black;">Name</td>
                            <td style="text-align: center; font-size:14px;border-bottom:1px dotted black;">Price</td>
                            <td style="text-align: center; font-size:14px;border-bottom:1px dotted black;">Qty</td>
                            <td style="text-align: right; font-size:14px;border-bottom:1px dotted black;">Total</td>
                        </tr>
                        <?php 
                            $itemtotal=0;
                            while($rowinvoicedetail=$resultinvoicedetail->fetch_assoc()){
                                $saleprice=$rowinvoicedetail['saleprice']*(100-$rowinvoicedetail['discountpresentage'])/100;
                                $totamount=$rowinvoicedetail['qty']*$saleprice;
                                $total=number_format(($totamount), 2); ?>
                                <tr>
                                    <td style="text-align: left; font-size:14px;"><?php echo $rowinvoicedetail['product_name']; ?></td>
                                    <td style="text-align: center; font-size:14px;"><?php echo number_format($saleprice,2); ?></td>
                                    <td style="text-align: center; font-size:14px;"><?php echo $rowinvoicedetail['qty']; ?></td>
                                    <td style="text-align: right; font-size:14px;"><?php echo $total; ?></td>
                                </tr>
                        <?php 
                            $itemtotal++;} 
                        ?>
                        
                    </table>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <table style="width:100%;">
                        <tr>
                            <td style="text-align: right; font-size:16px;font-weight: bold;"></td>
                            <td style="text-align: right; font-size:16px;font-weight: bold;"><?php echo number_format($rowinvoiceinfo['total'], 2) ?></td>
                        </tr>
                        <tr>
                            <td style="text-align: right; font-size:16px;font-weight: bold;">Total Discount</td>
                            <td style="text-align: right; font-size:16px;font-weight: bold;"><?php echo number_format($rowinvoiceinfo['discounttotal'], 2) ?></td>
                        </tr>
                        <tr>
                            <td style="text-align: right; font-size:16px;font-weight: bold;">Paid Amount</td>
                            <td style="text-align: right; font-size:16px;font-weight: bold;"><?php echo number_format($totalpay, 2) ?></td>
                        </tr>
                        <tr>
                            <td style="text-align: right; font-size:16px;font-weight: bold;">Credit</td>
                            <td style="text-align: right; font-size:16px;font-weight: bold;"><?php if($totalpay<$rowinvoiceinfo['nettotal']){echo number_format($rowinvoiceinfo['nettotal']-$totalpay);}else{echo '0.00';} ?></td>
                        </tr>
                        <tr>
                            <td style="text-align: right; font-size:16px;font-weight: bold;">Balance Amount</td>
                            <td style="text-align: right; font-size:16px;font-weight: bold;">(<?php if($totalpay<$rowinvoiceinfo['nettotal']){echo number_format($rowinvoiceinfo['nettotal']-$totalpay);}else{echo '0.00';} ?>)</td>
                        </tr>
                        <tr>
                            <td style="text-align: right; font-size:16px;font-weight: bold;">Net Total</td>
                            <td style="text-align: right; font-size:16px;font-weight: bold; border-bottom:1px double black; border-top:1px solid black;"><?php echo number_format($rowinvoiceinfo['nettotal']); ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- <tr>
                <td style="text-align: left; font-size:14px;border-bottom:1px dotted black;">Total Item Count </td>
                <td style="text-align: right; font-size:14px;"><?php // echo $itemtotal; ?></td>
            </tr> -->
            <!-- <tr>
                <td style="text-align: justify; font-size:10px;" colspan="2">In case of price discrepancy, return the item & bll within 7 days for refund of difference. Check the product warranty with shop owner.</td>
            </tr> -->
            <tr>
                <td style="text-align: center; font-size:10px;" colspan="2"><span style="text-align: center; font-size:16px;font-weight: bold;">Thank You. Come again</span><br><i style="text-align: center; font-size:16px;" class='lab la-facebook'></i> Levi Marketing Pvt Ltd  / Copyright © ERav Technology</td>
            </tr>
        </table>
    </div>
    <!-- <p>Do not print.</p>
    <button type='button' id='btn'>Print</button> -->

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
    <script>
        window.print();
        setTimeout(() => {
            window.close();
        }, 5000);
    </script>
</body>

</html>