<?php
session_start();
require_once('../connection/db.php');

$cashier=$_SESSION['name'];
$locationID=$_SESSION['location_id'];
$recordID=$_GET['recordID'];

$query = mysqli_query($conn, "
    SELECT location, code, companyname, address, contact1, contact2, contact3, email
    FROM tbl_location
    WHERE idtbl_location = $locationID
");

$company = mysqli_fetch_assoc($query);

$companyName    = $company['companyname'] ?? '';
$companyAddress = $company['address'] ?? '';

$companyTel = implode(' / ', array_filter([
    $company['contact1'] ?? '',
    $company['contact2'] ?? '',
    $company['contact3'] ?? ''
]));

$companyEmail = $company['email'] ?? '';
$companyTin   = '103464978'; // Supplier's TIN

$sqlinvoiceinfo="SELECT `tbl_invoice`.`idtbl_invoice`, `tbl_invoice`.`date`, `tbl_invoice`.`total`, `tbl_invoice`.`discounttotal`, `tbl_invoice`.`nettotal`, `tbl_invoice`.`paymentcomplete`, `tbl_invoice`.`saletype`, `tbl_invoice`.`customerid`, `tbl_customer`.`name`, `tbl_customer`.`address` FROM `tbl_invoice` LEFT JOIN `tbl_customer` ON `tbl_customer`.`idtbl_customer`=`tbl_invoice`.`customerid` WHERE `tbl_invoice`.`status`=1 AND `tbl_invoice`.`idtbl_invoice`='$recordID'";
$resultinvoiceinfo =$conn-> query($sqlinvoiceinfo);
$rowinvoiceinfo = $resultinvoiceinfo-> fetch_assoc();

$sqlpayment="SELECT SUM(`payment`) AS `sumpayment` FROM `tbl_invoice_payment` LEFT JOIN `tbl_invoice_payment_has_tbl_invoice` ON `tbl_invoice_payment_has_tbl_invoice`.`tbl_invoice_payment_idtbl_invoice_payment`=`tbl_invoice_payment`.`idtbl_invoice_payment` WHERE `tbl_invoice_payment_has_tbl_invoice`.`tbl_invoice_idtbl_invoice`='$recordID'";
$resultpayment =$conn-> query($sqlpayment);
$rowpayment = $resultpayment-> fetch_assoc();
$totalpay = (!empty($rowpayment['sumpayment']) ? $rowpayment['sumpayment'] : 0);
$credit = max(0, $rowinvoiceinfo['nettotal'] - $totalpay);
$changedue = max(0, $totalpay - $rowinvoiceinfo['nettotal']);

// Customer's total outstanding balance across ALL their invoices (not just this one)
$customerID = $rowinvoiceinfo['customerid'];
$custoutstanding = 0;
if(!empty($customerID)){
    $sqlcustinvoices = "SELECT `idtbl_invoice`, `nettotal` FROM `tbl_invoice` WHERE `status`=1 AND `customerid`='$customerID'";
    $resultcustinvoices = $conn->query($sqlcustinvoices);
    if($resultcustinvoices && $resultcustinvoices->num_rows > 0){
        while($rowcustinv = $resultcustinvoices->fetch_assoc()){
            $invID = $rowcustinv['idtbl_invoice'];
            $sqlcustpaid = "SELECT SUM(`payamount`) AS `sumpaid` FROM `tbl_invoice_payment_has_tbl_invoice` WHERE `tbl_invoice_idtbl_invoice`='$invID'";
            $resultcustpaid = $conn->query($sqlcustpaid);
            $rowcustpaid = $resultcustpaid->fetch_assoc();
            $custpaid = (!empty($rowcustpaid['sumpaid'])) ? $rowcustpaid['sumpaid'] : 0;
            $custbal = $rowcustinv['nettotal'] - $custpaid;
            if($custbal > 0){
                $custoutstanding += $custbal;
            }
        }
    }
}

$sqlinvoicedetail="SELECT `tbl_product`.`product_code`, `tbl_product`.`product_name`, `tbl_invoice_detail`.`qty`, `tbl_invoice_detail`.`saleprice`, `tbl_invoice_detail`.`editedprice`, `tbl_invoice_detail`.`editstatus`, `tbl_invoice_detail`.`discountpresentage` FROM `tbl_invoice_detail` LEFT JOIN `tbl_product` ON `tbl_product`.`idtbl_product`=`tbl_invoice_detail`.`tbl_product_idtbl_product` WHERE `tbl_invoice_detail`.`tbl_invoice_idtbl_invoice`='$recordID' AND `tbl_invoice_detail`.`status`=1";
$resultinvoicedetail=$conn->query($sqlinvoicedetail);

// Determine which layout to use (1 = retail, 2 = wholesale)
$isRetail = ($rowinvoiceinfo['saletype'] == 1);

?>
<?php if ($isRetail): ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Cutive+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css">
    <title>Invoice</title>
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
    <div id='DivIdToPrint'>
        <table style="width:100%;">
            <tr>
                <td style="text-align: center; font-size:16px;border-bottom:1px dotted black;" colspan="2">
                    <img src="../images/levishortlogo.png" alt="Levi Marketing Logo" style="width:120px;height:auto;display:block;margin:0 auto 8px;">
                    <h2 class="font-weight-light" style="margin-top:0;margin-bottom:0;"><?php echo $companyName; ?></h2>
                    <?php echo $companyAddress; ?><br>
                    <?php echo $companyTel; ?>
                </td>
            </tr>
            <tr>
                <td style="text-align: left; font-size:14px;">Date</td>
                <td style="text-align: right; font-size:14px;"><?php echo $rowinvoiceinfo['date'] ?></td>
            </tr>
            <tr>
                <td style="text-align: left; font-size:14px;">Invoice No.</td>
                <td style="text-align: right; font-size:14px;">INV-<?php echo $rowinvoiceinfo['idtbl_invoice'] ?></td>
            </tr>
            <tr>
                <td style="text-align: left; font-size:14px;border-bottom:1px dotted black;">Cashier</td>
                <td style="text-align: right; font-size:14px;border-bottom:1px dotted black;"><?php echo $cashier; ?></td>
            </tr>
            <tr>
                <td style="text-align: center;" colspan="2">
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
                                // saleprice/editedprice are LINE AMOUNTS (qty already applied), not per-unit prices.
                                // Use the edited line amount when this line was overridden, otherwise the actual line amount.
                                $baselineamount = ($rowinvoicedetail['editstatus']==1 && $rowinvoicedetail['editedprice'] > 0)
                                    ? $rowinvoicedetail['editedprice']
                                    : $rowinvoicedetail['saleprice'];

                                $totamount = $baselineamount * (100 - $rowinvoicedetail['discountpresentage']) / 100;
                                $total = number_format($totamount, 2);

                                // Per-unit price for display only (line amount / qty)
                                $displayunitprice = ($rowinvoicedetail['qty'] > 0) ? ($baselineamount / $rowinvoicedetail['qty']) : 0;
                            ?>
                                <tr>
                                    <td style="text-align: left; font-size:14px;" colspan="4"><?php echo $rowinvoicedetail['product_name']; ?></td>
                                </tr>
                                <tr>
                                    <td style="text-align: left; font-size:14px;">&nbsp;</td>
                                    <td style="text-align: center; font-size:14px;"><?php echo number_format($displayunitprice,2); ?></td>
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
                <td style="text-align: left; font-size:16px;font-weight: bold;">Net Total</td>
                <td style="text-align: right; font-size:16px;font-weight: bold;"><?php echo number_format($rowinvoiceinfo['nettotal'], 2) ?></td>
            </tr>
            <tr>
                <td style="text-align: left; font-size:14px;">Paid Amount</td>
                <td style="text-align: right; font-size:14px;"><?php echo number_format($totalpay, 2) ?></td>
            </tr>
            <?php if($changedue > 0): ?>
            <tr>
                <td style="text-align: left; font-size:14px;">Change Due</td>
                <td style="text-align: right; font-size:14px;"><?php echo number_format($changedue, 2) ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td style="text-align: left; font-size:14px;border-bottom:1px dotted black;">Credit (This Bill)</td>
                <td style="text-align: right; font-size:14px;border-bottom:1px dotted black;"><?php echo number_format($credit, 2) ?></td>
            </tr>
            <?php if($custoutstanding > 0): ?>
            <tr>
                <td style="text-align: left; font-size:14px;border-bottom:1px dotted black;font-weight:bold;">Total Outstanding</td>
                <td style="text-align: right; font-size:14px;border-bottom:1px dotted black;font-weight:bold;"><?php echo number_format($custoutstanding, 2) ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td style="text-align: left; font-size:14px;border-bottom:1px dotted black;">Total Item Count </td>
                <td style="text-align: right; font-size:14px;border-bottom:1px dotted black;"><?php echo $itemtotal; ?></td>
            </tr>
            <tr>
                <td style="text-align: center; font-size:10px;" colspan="2"><span style="text-align: center; font-size:16px;font-weight: bold;">Thank You. Come again</span><br><i style="text-align: center; font-size:16px;" class='lab la-facebook'></i> Levi Marketing Pvt Ltd  / Copyright © ERav Technology</td>
            </tr>
        </table>
    </div>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
    <script>
        window.print();
        setTimeout(() => {
            window.close();
        }, 5000);
    </script>
</body>
</html>
<?php else: ?>
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
                    <img src="../images/levilogobw.png" alt="Levi Marketing Logo" style="width:250px;height:auto;display:block;margin:0 0 6px 0;">
                    <p style="margin:0px;font-size:13px;font-weight:normal;text-transform: uppercase;"><?php echo $companyAddress ?></p>
                    <p style="margin:0px;font-size:13px;font-weight:normal;"><?php echo $companyTel ?></p>
                    <p style="margin:0px;font-size:13px;font-weight:normal;"><u>E-Mail: <?php echo $companyEmail ?></u></p>
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
                    // saleprice/editedprice are LINE AMOUNTS (qty already applied), not per-unit prices.
                    $baselineamount = ($rowinvoicedetail['editstatus']==1 && $rowinvoicedetail['editedprice'] > 0)
                        ? $rowinvoicedetail['editedprice']
                        : $rowinvoicedetail['saleprice'];

                    $lineTotal = $baselineamount * (100 - $rowinvoicedetail['discountpresentage']) / 100;

                    // Per-unit price for display only (line amount / qty)
                    $displayunitprice = ($rowinvoicedetail['qty'] > 0) ? ($baselineamount / $rowinvoicedetail['qty']) : 0;
                ?>
                <tr>
                    <td style="text-align:center; border-right: 1px solid black; border-left: 1px solid #000;"><?php echo htmlspecialchars($rowinvoicedetail['product_code']); ?></td>
                    <td style="padding-left: 10px; border-right: 1px solid black;"><?php echo htmlspecialchars($rowinvoicedetail['product_name']); ?></td>
                    <td style="text-align:center; border-right: 1px solid black;"><?php echo htmlspecialchars($rowinvoicedetail['qty']); ?></td>
                    <td style="text-align:center; border-right: 1px solid black;">&nbsp;</td>
                    <td style="text-align:right; border-right: 1px solid black; padding-right: 10px;"><?php echo number_format($displayunitprice, 2); ?></td>
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
                <?php if($changedue > 0): ?>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2" style="border-left: 1px solid #000;border-right: 1px solid #000;text-align:left;padding-left:35px;">Change Due</td>
                    <td colspan="2" style="border-left: 1px solid #000;border-right: 1px solid #000;text-align:right;padding-right:10px;"><?php echo number_format($changedue, 2); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2" style="border-left: 1px solid #000;border-right: 1px solid #000;text-align:left;padding-left:35px;">Credit (This Bill)</td>
                    <td colspan="2" style="border-left: 1px solid #000;border-right: 1px solid #000;text-align:right;padding-right:10px;"><?php echo number_format($credit, 2); ?></td>
                </tr>
                <?php if($custoutstanding > 0): ?>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2" style="border-left: 1px solid #000;border-right: 1px solid #000;text-align:left;padding-left:35px;font-weight:bold;">Total Outstanding</td>
                    <td colspan="2" style="border-left: 1px solid #000;border-right: 1px solid #000;text-align:right;padding-right:10px;font-weight:bold;"><?php echo number_format($custoutstanding, 2); ?></td>
                </tr>
                <?php endif; ?>
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
<?php endif; ?>