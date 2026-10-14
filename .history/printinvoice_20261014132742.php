<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header("Location: index.php");
    exit;
}
require_once('connection/db.php');

$invoiceID = intval($_GET['id'] ?? 0);
$locationID = $_SESSION['location_id'];

if ($invoiceID <= 0) {
    die('Invalid invoice.');
}

$sql = "SELECT i.*, c.`name` AS customername, c.`address` AS customeraddress,
               c.`phone` AS customerphone, c.`vat_num` AS customervat
        FROM `tbl_invoice` i
        INNER JOIN `tbl_customer` c ON c.`idtbl_customer` = i.`customerid`
        WHERE i.`idtbl_invoice` = $invoiceID";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    die('Invoice not found.');
}
$invoice = $result->fetch_assoc();

// 'exclusive' = line prices did NOT include VAT (VAT was added to the totals)
// 'inclusive' = line prices already included VAT (VAT was extracted, not added again)
$vatType = strtolower(trim($invoice['vattype'] ?? 'inclusive'));
if (!in_array($vatType, ['inclusive', 'exclusive'], true)) {
    $vatType = 'inclusive';
}
$isVatInclusive = ($vatType === 'inclusive');

$detailSql = "SELECT d.`qty`, d.`unitprice`, d.`saleprice`, p.`product_code`, p.`product_name`
              FROM `tbl_invoice_detail` d
              INNER JOIN `tbl_product` p ON p.`idtbl_product` = d.`tbl_product_idtbl_product`
              WHERE d.`tbl_invoice_idtbl_invoice` = $invoiceID AND d.`status` = 1
              ORDER BY d.`idtbl_invoice_detail` ASC";
$detailResult = $conn->query($detailSql);
$invoiceDetails = [];
while ($row = $detailResult->fetch_assoc()) {
    $invoiceDetails[] = $row;
}

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

// ---- Number-to-words for "Total Amount in words" ----
function convertAmountToWords($amount)
{
    $ones = array(
        0 => '', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five',
        6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine', 10 => 'ten',
        11 => 'eleven', 12 => 'twelve', 13 => 'thirteen', 14 => 'fourteen',
        15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
        19 => 'nineteen'
    );
    $tens = array(
        2 => 'twenty', 3 => 'thirty', 4 => 'forty', 5 => 'fifty',
        6 => 'sixty', 7 => 'seventy', 8 => 'eighty', 9 => 'ninety'
    );

    $amount = str_replace(',', '', $amount);
    $whole = intval($amount);
    $cents = intval(round(($amount - $whole) * 100));

    $numberToWords = function ($num) use (&$numberToWords, $ones, $tens) {
        $str = '';

        if ($num >= 1000000000) {
            $str .= $numberToWords(intval($num / 1000000000)) . ' billion ';
            $num %= 1000000000;
        }
        if ($num >= 1000000) {
            $str .= $numberToWords(intval($num / 1000000)) . ' million ';
            $num %= 1000000;
        }
        if ($num >= 1000) {
            $str .= $numberToWords(intval($num / 1000)) . ' thousand ';
            $num %= 1000;
        }
        if ($num >= 100) {
            $str .= $ones[intval($num / 100)] . ' hundred ';
            $num %= 100;
        }
        if ($num > 0) {
            if ($str !== '') { $str .= ' '; }
            if ($num < 20) {
                $str .= $ones[$num];
            } else {
                $str .= $tens[intval($num / 10)];
                if ($num % 10 > 0) { $str .= '-' . $ones[$num % 10]; }
            }
        }
        return trim($str);
    };

    $words = '';
    if ($whole > 0) { $words .= $numberToWords($whole) . ' rupees'; }
    if ($cents > 0) {
        if ($words !== '') { $words .= ' and '; }
        $words .= $numberToWords($cents) . ' cents';
    }
    if ($words === '') { $words = 'zero rupees'; }

    return ucfirst(trim($words)) . ' only';
}

$totalWords = convertAmountToWords(round($invoice['nettotal_with_vat'], 2));

// Items table's "Amount" column header changes depending on whether the
// entered line prices already had VAT in them.
$amountColumnLabel = $isVatInclusive ? 'Amount Incl. VAT (Rs.)' : 'Amount Excl. VAT (Rs.)';
$vatTypeLabel       = $isVatInclusive ? 'Inclusive' : 'Exclusive';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Tax Invoice <?php echo htmlspecialchars($invoice['taxinvoice_no']); ?></title>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    @page {
        size: A4;
        margin: 15mm;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        color: #000;
    }

    /* ── Header ── */
    .header {
        text-align: center;
        margin-bottom: 15px;
    }

    .company-name {
        font-size: 16px;
        font-weight: bold;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .company-sub {
        font-size: 12px;
        margin-bottom: 2px;
    }

    /* ── Title ── */
    .title-box {
        text-align: center;
        margin-bottom: 15px;
    }
    .title-box table {
        margin: 0 auto;
        border-collapse: collapse;
    }
    .title-box td {
        border: 2px solid #000;
        padding: 8px 30px;
        font-size: 16px;
        font-weight: bold;
        letter-spacing: 2px;
        text-align: center;
    }

    /* ── Info Table ── */
    .info-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }

    .info-table td {
        border: 1px solid #000;
        padding: 5px 8px;
        vertical-align: top;
        width: 50%;
    }

    /* Inner layout tables (no border of their own — sit inside info-table cells) */
    .inner-details-table {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
    }

    .inner-details-table th {
        border: none;
        text-align: left;
        vertical-align: top;
        font-weight: bold;
        padding: 0;
    }

    .inner-details-table td {
        border: none;
        text-align: left;
        vertical-align: top;
        padding: 0;
        padding-top: 0;
    }

    .label-col { width: 38%; }
    .sep-col   { width: 2%; }

    /* ── Additional Info ── */
    .additional-info {
        border: 1px solid #000;
        padding: 6px 8px;
        margin-bottom: 10px;
        min-height: 30px;
    }

    /* ── Items Table ── */
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }

    .items-table th {
        border: 1px solid #000;
        padding: 6px 5px;
        text-align: center;
        font-weight: bold;
        background-color: #f0f0f0;
        font-size: 12px;
    }

    .items-table td {
        border: 1px solid #000;
        padding: 5px;
        vertical-align: top;
        font-size: 12px;
    }

    .items-table .col-ref       { width: 8%;  text-align: center; }
    .items-table .col-code      { width: 14%; text-align: left;   }
    .items-table .col-desc      { width: 34%; text-align: left;   }
    .items-table .col-qty       { width: 10%; text-align: center; }
    .items-table .col-unitprice { width: 17%; text-align: right;  }
    .items-table .col-amount    { width: 17%; text-align: right;  }

    /* ── Summary ── */
    .summary-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }
    .summary-label { text-align: right; padding: 4px 8px; font-size: 12px; }
    .summary-value { text-align: right; padding: 4px 8px; font-size: 12px; font-weight: bold; width: 140px; }
    .grand-total-row td { border-top: 2px solid #000; font-size: 14px; }

    /* ── Total Words & Mode ── */
    .total-words-box {
        border: 1px solid #000;
        padding: 6px 8px;
        min-height: 28px;
    }

    .mode-payment-box {
        border: 1px solid #000;
        border-top: 0;
        padding: 6px 8px;
        min-height: 28px;
        margin-bottom: 15px;
    }

    /* ── Footer / signatures ── */
    footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 100px;
    }

    .footertable {
        width: 100%;
        text-align: center;
        border-collapse: collapse;
    }
    .footertable td {
        width: 33.33%;
        padding-top: 45px;
        vertical-align: bottom;
    }
    .sig-line {
        border-top: 1px dotted #000;
        width: 80%;
        margin: 0 auto 5px auto;
    }

    /* Reserve space at the bottom of the page content so it never
       overlaps the fixed footer */
    body { padding-bottom: 110px; }

    .no-print { margin-top: 20px; }
    @media print { .no-print { display: none; } }
</style>
</head>
<body onload="window.print();">

    <footer>
        <table class="footertable">
            <tr>
                <td><div class="sig-line"></div>Prepared By</td>
                <td><div class="sig-line"></div>Checked By</td>
                <td><div class="sig-line"></div>For <?php echo htmlspecialchars($companyName); ?></td>
            </tr>
        </table>
    </footer>

    <!-- ══ HEADER ══════════════════════════════════════════════════════ -->
    <div class="header">
        <div class="company-name"><?php echo htmlspecialchars($companyName); ?></div>
        <div class="company-sub"><?php echo htmlspecialchars($companyAddress); ?></div>
        <div class="company-sub">Tel: <?php echo htmlspecialchars($companyTel); ?> | E-Mail: <?php echo htmlspecialchars($companyEmail); ?></div>
    </div>

    <!-- ══ TITLE ════════════════════════════════════════════════════════ -->
    <div class="title-box">
        <table><tr><td>TAX INVOICE</td></tr></table>
    </div>

    <!-- ══ SUPPLIER & PURCHASER INFO ════════════════════════════════════ -->
    <table class="info-table">

        <!-- Row 1: Date of Invoice | Tax Invoice No -->
        <tr>
            <td>
                <table class="inner-details-table">
                    <tr>
                        <th class="label-col">Date of Invoice</th>
                        <th class="sep-col">:</th>
                        <td><?php echo date('m/d/Y', strtotime($invoice['date'])); ?></td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="inner-details-table">
                    <tr>
                        <th class="label-col">Tax Invoice No.</th>
                        <th class="sep-col">:</th>
                        <td><?php echo htmlspecialchars($invoice['taxinvoice_no']); ?></td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Row 2: Supplier details | Purchaser details -->
        <tr>
            <td>
                <table class="inner-details-table">
                    <tr><th class="label-col">Supplier's TIN</th><th class="sep-col">:</th><td><?php echo htmlspecialchars($companyTin); ?></td></tr>
                    <tr><th class="label-col">Supplier's Name</th><th class="sep-col">:</th><td><?php echo htmlspecialchars($companyName); ?></td></tr>
                    <tr><th class="label-col">Address</th><th class="sep-col">:</th><td><?php echo htmlspecialchars($companyAddress); ?></td></tr>
                    <tr><th class="label-col">Telephone No.</th><th class="sep-col">:</th><td><?php echo htmlspecialchars($companyTel); ?></td></tr>
                </table>
            </td>
            <td>
                <table class="inner-details-table">
                    <tr><th class="label-col">Purchaser's TIN</th><th class="sep-col">:</th><td><?php echo htmlspecialchars($invoice['customervat']); ?></td></tr>
                    <tr><th class="label-col">Purchaser's Name</th><th class="sep-col">:</th><td><?php echo htmlspecialchars($invoice['customername']); ?></td></tr>
                    <tr><th class="label-col">Address</th><th class="sep-col">:</th><td><?php echo nl2br(htmlspecialchars($invoice['customeraddress'])); ?></td></tr>
                    <tr><th class="label-col">Telephone No.</th><th class="sep-col">:</th><td><?php echo htmlspecialchars($invoice['customerphone']); ?></td></tr>
                </table>
            </td>
        </tr>

        <!-- Row 3: Date of Supply | Place of Supply -->
        <tr>
            <td>
                <table class="inner-details-table">
                    <tr><th class="label-col">Date of Supply</th><th class="sep-col">:</th><td><?php echo date('m/d/Y', strtotime($invoice['date'])); ?></td></tr>
                </table>
            </td>
            <td>
                <table class="inner-details-table">
                    <tr><th class="label-col">Place of Supply</th><th class="sep-col">:</th><td><?php echo htmlspecialchars($invoice['customername']); ?></td></tr>
                </table>
            </td>
        </tr>

        <!-- Row 4: VAT Type -->
        <tr>
            <td>
                <table class="inner-details-table">
                    <tr><th class="label-col">VAT Type</th><th class="sep-col">:</th><td><?php echo htmlspecialchars($vatTypeLabel); ?></td></tr>
                </table>
            </td>
            <td>
                <table class="inner-details-table">
                    <tr><th class="label-col">VAT Rate</th><th class="sep-col">:</th><td><?php echo number_format($invoice['vatpercent'], 2); ?>%</td></tr>
                </table>
            </td>
        </tr>

    </table>

    <!-- ══ ADDITIONAL INFORMATION ════════════════════════════════════════ -->
    <div class="additional-info">
        <strong>Additional Information if any:</strong>
    </div>

    <!-- ══ ITEMS TABLE ════════════════════════════════════════════════════ -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="col-ref">Ref</th>
                <th class="col-code">Code</th>
                <th class="col-desc">Description of Goods or Services</th>
                <th class="col-qty">Quantity</th>
                <th class="col-unitprice">Unit Price</th>
                <th class="col-amount"><?php echo htmlspecialchars($amountColumnLabel); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; foreach ($invoiceDetails as $d) { ?>
            <tr>
                <td class="col-ref"><?php echo $i++; ?></td>
                <td class="col-code"><?php echo htmlspecialchars($d['product_code']); ?></td>
                <td class="col-desc"><?php echo htmlspecialchars($d['product_name']); ?></td>
                <td class="col-qty"><?php echo rtrim(rtrim(number_format($d['qty'], 2), '0'), '.'); ?></td>
                <td class="col-unitprice"><?php echo number_format($d['unitprice'], 2, '.', ','); ?></td>
                <td class="col-amount"><?php echo number_format($d['saleprice'], 2, '.', ','); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- ══ SUMMARY ════════════════════════════════════════════════════════ -->
    <table class="summary-table">
        <?php if ($isVatInclusive) { ?>
            <!-- VAT already included in entered prices: VAT is extracted for
                 display only, and is NOT added again to the grand total. -->
            <tr>
                <td class="summary-label" colspan="5">Total Value of Supply (Incl. VAT) :</td>
                <td class="summary-value"><?php echo number_format($invoice['total'], 2, '.', ','); ?></td>
            </tr>
            <?php if (floatval($invoice['discounttotal']) > 0) { ?>
            <tr>
                <td class="summary-label" colspan="5">Discount :</td>
                <td class="summary-value"><?php echo number_format($invoice['discounttotal'], 2, '.', ','); ?></td>
            </tr>
            <?php } ?>
            <tr>
                <td class="summary-label" colspan="5">Net Value of Supply (Excl. VAT) :</td>
                <td class="summary-value"><?php echo number_format($invoice['nettotal'], 2, '.', ','); ?></td>
            </tr>
            <tr>
                <td class="summary-label" colspan="5">VAT Amount (@ <?php echo number_format($invoice['vatpercent'], 2); ?>%, included above) :</td>
                <td class="summary-value"><?php echo number_format($invoice['vatamount'], 2, '.', ','); ?></td>
            </tr>
            <tr class="grand-total-row">
                <td class="summary-label" colspan="5">Total Amount / Consideration including VAT :</td>
                <td class="summary-value"><?php echo number_format($invoice['nettotal_with_vat'], 2, '.', ','); ?></td>
            </tr>
        <?php } else { ?>
            <!-- VAT was not in entered prices: VAT is added on top of the
                 discounted net total to arrive at the grand total. -->
            <tr>
                <td class="summary-label" colspan="5">Total Value of Supply :</td>
                <td class="summary-value"><?php echo number_format($invoice['total'], 2, '.', ','); ?></td>
            </tr>
            <?php if (floatval($invoice['discounttotal']) > 0) { ?>
            <tr>
                <td class="summary-label" colspan="5">Discount :</td>
                <td class="summary-value"><?php echo number_format($invoice['discounttotal'], 2, '.', ','); ?></td>
            </tr>
            <?php } ?>
            <tr>
                <td class="summary-label" colspan="5">VAT Amount (Net Value of Supply @ <?php echo number_format($invoice['vatpercent'], 2); ?>%) :</td>
                <td class="summary-value"><?php echo number_format($invoice['vatamount'], 2, '.', ','); ?></td>
            </tr>
            <tr class="grand-total-row">
                <td class="summary-label" colspan="5">Total Amount / Consideration including VAT :</td>
                <td class="summary-value"><?php echo number_format($invoice['nettotal_with_vat'], 2, '.', ','); ?></td>
            </tr>
        <?php } ?>
    </table>

    <!-- ══ TOTAL IN WORDS & MODE OF PAYMENT ═════════════════════════════════ -->
    <div class="total-words-box">
        <table class="inner-details-table">
            <tr>
                <th class="label-col" style="width:24%">Total Amount in words</th>
                <th class="sep-col">:</th>
                <td><?php echo $totalWords; ?></td>
            </tr>
        </table>
    </div>
    <div class="mode-payment-box">
        <table class="inner-details-table">
            <tr>
                <th class="label-col" style="width:24%">Mode of Payment</th>
                <th class="sep-col">:</th>
                <td></td>
            </tr>
        </table>
    </div>

    <div class="no-print" style="text-align:center;">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>

</body>
</html>