<?php
session_start();
require_once('../connection/db.php');

$cashier=$_SESSION['name'];
$recordID=$_GET['recordID'];

$sqlinvoiceinfo="SELECT `tbl_invoice`.`idtbl_invoice`, `tbl_invoice`.`date`, `tbl_invoice`.`total`, `tbl_invoice`.`discounttotal`, `tbl_invoice`.`nettotal`, `tbl_invoice`.`paymentcomplete`, `tbl_customer`.`name`, `tbl_customer`.`address` FROM `tbl_invoice` LEFT JOIN `tbl_customer` ON `tbl_customer`.`idtbl_customer`=`tbl_invoice`.`customerid` WHERE `tbl_invoice`.`status`=1 AND `tbl_invoice`.`idtbl_invoice`='$recordID'";
$resultinvoiceinfo =$conn-> query($sqlinvoiceinfo);
$rowinvoiceinfo = $resultinvoiceinfo-> fetch_assoc();

$sqlpayment="SELECT SUM(`payment`) AS `sumpayment` FROM `tbl_invoice_payment` LEFT JOIN `tbl_invoice_payment_has_tbl_invoice` ON `tbl_invoice_payment_has_tbl_invoice`.`tbl_invoice_payment_idtbl_invoice_payment`=`tbl_invoice_payment`.`idtbl_invoice_payment` WHERE `tbl_invoice_payment_has_tbl_invoice`.`tbl_invoice_idtbl_invoice`='$recordID'";
$resultpayment =$conn-> query($sqlpayment);
$rowpayment = $resultpayment-> fetch_assoc();
$totalpay = (!empty($rowpayment['sumpayment']) ? $rowpayment['sumpayment'] : $rowinvoiceinfo['nettotal']);
$credit = max(0, $rowinvoiceinfo['nettotal'] - $totalpay);

$sqlinvoicedetail="SELECT `tbl_product`.`product_code`, `tbl_product`.`product_name`, `tbl_invoice_detail`.`qty`, `tbl_invoice_detail`.`saleprice`, `tbl_invoice_detail`.`discountpresentage` FROM `tbl_invoice_detail` LEFT JOIN `tbl_product` ON `tbl_product`.`idtbl_product`=`tbl_invoice_detail`.`tbl_product_idtbl_product` WHERE `tbl_invoice_detail`.`tbl_invoice_idtbl_invoice`='$recordID' AND `tbl_invoice_detail`.`status`=1";
$resultinvoicedetail=$conn->query($sqlinvoicedetail);

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Cutive+Mono&display=swap" rel="stylesheet">
    <title>Invoice</title>
    <style media="print">
        @page {
            size: 220mm 140mm;
            margin: 5mm;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.5;
            text-align: left;
            margin: 0;
            margin-top: 160px;
        }
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 160px;
        }
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100px;
        }
        table, tr, th, td {
            font-family: Arial, sans-serif;
            font-size: 13px;
        }
    </style>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.5;
            text-align: left;
            margin: 0;
            margin-top: 160px;
        }
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 160px;
        }
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100px;
        }
        table, tr, th, td {
            font-family: Arial, sans-serif;
            font-size: 13px;
        }
        .header-images {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .header-images img {
            width: 60px;
            height: 40px;
            object-fit: contain;
            display: block;
        }
    </style>
</head>

<body>
    <header>
        <table style="width:100%;border-collapse: collapse;">
            <tr>
                <!-- Top-left: 3 small stacked images -->
                <td width="18%" style="vertical-align: top; padding: 0px;">
                    <div class="header-images">
                        <img src="../images/invoice_logo.png" alt="Image 1">
                        <img src="../images/invoice_logo2.png" alt="Image 2">
                        <img src="../images/invoice_logo3.png" alt="Image 3">
                    </div>
                </td>
                <td width="37%" style="vertical-align: top;padding:0px;">
                    <p style="margin:0px;font-size:16px;font-weight: bold;">SALES INVOICE</p>
                    <p style="margin:0px;font-size:13px;font-weight: bold;">To: <?php echo htmlspecialchars($rowinvoiceinfo['name']); ?></p>
                    <p style="margin:0px;font-size:13px;padding-left: 24px;"><?php echo nl2br(htmlspecialchars($rowinvoiceinfo['address'])); ?></p>
                </td>
                <td width="45%" style="vertical-align: top;padding:0px;">
                    <p style="margin:0px;font-size:18px;font-weight:bold;text-transform: uppercase;">Levi Marketing Pvt Ltd</p>
                    <p style="margin:0px;font-size:13px;font-weight:normal;text-transform: uppercase;">No.61/1/C, Jayaweera Mawatha, Gonawala, Kelaniya</p>
                    <p style="margin:0px;font-size:13px;font-weight:normal;">Phone: +94 75 7000 700 / +94 75 7000 300</p>
                    <p style="margin:0px;font-size:13px;font-weight:normal;"><u>E-Mail: info@levimarketing.com</u></p>
                    <p style="margin:0px;font-size:13px;font-weight:normal;">Invoice No: INV-<?php echo htmlspecialchars($rowinvoiceinfo['idtbl_invoice']); ?></p>
                    <p style="margin:0px;font-size:13px;font-weight:normal;">Date: <?php echo htmlspecialchars($rowinvoiceinfo['date']); ?></p>
                </td>
            </tr>
        </table>
    </header>

    <footer>
        <table style="width:100%;">
            <tr>
                <td style="vertical-align: top;">
                    <table style="width:100%;font-size:12px;margin-top: 15px;">
                        <tr>
                            <td>Prepared by</td>
                            <td style="width: 5%;">:</td>
                            <td>...................................</td>
                        </tr>
                        <tr>
                            <td style="padding-top: 15px;">Checked by</td>
                            <td style="width: 5%;padding-top: 15px;">:</td>
                            <td style="padding-top: 15px;">...................................</td>
                        </tr>
                    </table>
                </td>
                <td style="vertical-align: top;">
                    <table style="width:100%;font-size:12px;margin-top: 15px;">
                        <tr>
                            <td>Contact Person</td>
                        </tr>
                        <tr>
                            <td style="padding-top: 15px;">Contact No</td>
                        </tr>
                    </table>
                </td>
                <td style="text-align: center;vertical-align: top;">
                    <p style="margin:0;font-size:12px;text-transform: uppercase;font-weight: bold;">Levi Marketing Pvt Ltd</p>
                    <p style="margin:0;margin-top:25px;font-size:12px;">..........................................................</p>
                    <p style="margin:0;font-size:12px;">Authorise Officer</p>
                </td>
            </tr>
        </table>
    </footer>

    <main>
        <table style="table-layout: fixed;padding:3px;width:100%;border-collapse: collapse;font-size: 13px;">
            <thead>
                <tr>
                    <th style="width: 10%;text-align:center; border: 1px solid #000;">Code</th>
                    <th style="width: 40%;text-align:center; border: 1px solid #000;">Item Description</th>
                    <th style="width: 10%;text-align:center; border: 1px solid #000;">Quantity</th>
                    <th style="width: 10%;text-align:center; border: 1px solid #000;">UOM</th>
                    <th style="width: 15%;text-align:right; border: 1px solid #000;padding-right: 10px;">Unit Price</th>
                    <th style="width: 15%;text-align:right; border: 1px solid #000;padding-right: 10px;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while($rowinvoicedetail=$resultinvoicedetail->fetch_assoc()){
                    $saleprice = $rowinvoicedetail['saleprice'] * (100 - $rowinvoicedetail['discountpresentage']) / 100;
                    $lineTotal = $rowinvoicedetail['qty'] * $saleprice;
                ?>
                <tr>
                    <td style="text-align:center; border-right: 1px solid black; border-left: 1px solid #000;"><?php echo htmlspecialchars($rowinvoicedetail['product_code']); ?></td>
                    <td style="padding-left: 10px; border-right: 1px solid black;"><?php echo htmlspecialchars($rowinvoicedetail['product_name']); ?></td>
                    <td style="text-align:center; border-right: 1px solid black;"><?php echo htmlspecialchars($rowinvoicedetail['qty']); ?></td>
                    <td style="text-align:center; border-right: 1px solid black;">&nbsp;</td>
                    <td style="text-align:right; border-right: 1px solid black; padding-right: 10px;"><?php echo number_format($saleprice, 2); ?></td>
                    <td style="text-align:right; padding-right: 10px; #000;border-right: 1px solid #000;"><?php echo number_format($lineTotal, 2); ?></td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="border-top: 1px solid #000;"></td>
                    <td colspan="2" style="border-top: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;text-align:left;padding-left:35px;">Total (Excl)</td>
                    <td colspan="2" style="border-top: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;text-align:right;padding-right:10px;"><?php echo number_format($rowinvoiceinfo['total'], 2); ?></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2" style="border-left: 1px solid #000;border-right: 1px solid #000;text-align:left;padding-left:35px;">Total Discount</td>
                    <td colspan="2" style="border-left: 1px solid #000;border-right: 1px solid #000;text-align:right;padding-right:10px;"><?php echo number_format($rowinvoiceinfo['discounttotal'], 2); ?></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2" style="border-left: 1px solid #000;border-right: 1px solid #000;text-align:left;padding-left:35px;">Paid Amount</td>
                    <td colspan="2" style="border-left: 1px solid #000;border-right: 1px solid #000;text-align:right;padding-right:10px;"><?php echo number_format($totalpay, 2); ?></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2" style="border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;text-align:left;font-weight:bold;padding-left:35px;">Net Total</td>
                    <th colspan="2" style="border-bottom: 1px solid #000;border-left: 1px solid #000;border-right: 1px solid #000;text-align:right;padding-right:10px;font-weight:bold;"><?php echo number_format($rowinvoiceinfo['nettotal'], 2); ?></th>
                </tr>
            </tfoot>
        </table>
    </main>

    <script>
        window.print();
        setTimeout(() => {
            window.close();
        }, 5000);
    </script>
</body>

</html>