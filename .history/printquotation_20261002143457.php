<?php
session_start();
if (!isset($_SESSION['userid'])) {
    die('Session expired. Please login again.');
}
require_once('connection/db.php');

$quotationID = intval($_GET['id'] ?? 0);
if ($quotationID <= 0) {
    die('Invalid quotation.');
}

// ---- Header + customer (shipper/receiver) ----
$sql = "SELECT q.*, c.`name` AS customer_name, c.`phone` AS customer_phone,
               c.`address` AS customer_address, c.`nic` AS customer_nic,
               c.`vat_num` AS customer_vat_num
        FROM `tbl_quotation` q
        LEFT JOIN `tbl_customer` c ON c.`idtbl_customer` = q.`tbl_customer_idtbl_customer`
        WHERE q.`idtbl_quotation` = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $quotationID);
$stmt->execute();
$quotation = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$quotation) {
    die('Quotation not found.');
}

// ---- Detail lines ----
$detailStmt = $conn->prepare(
    "SELECT `description`, `qty`, `unitprice`, `amount`
     FROM `tbl_quotation_detail`
     WHERE `tbl_quotation_idtbl_quotation` = ? AND `status` = 1
     ORDER BY `idtbl_quotation_detail` ASC"
);
$detailStmt->bind_param('i', $quotationID);
$detailStmt->execute();
$lines = $detailStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$detailStmt->close();

function money($n) {
    return number_format((float)$n, 2);
}

$displayDate = date('d/m/Y', strtotime($quotation['date']));
$vatType     = intval($quotation['vattype']); // 1 = inclusive, 2 = exclusive
$vatPercent  = floatval($quotation['vatpercent']);
// VAT breakdown rows only show for exclusive VAT (prices don't yet include
// VAT, so the customer needs to see it added). Inclusive quotations keep
// the simple Total-only layout, matching the original sample PDF.
$showVatRows = ($vatType === 2) && $vatPercent > 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Quotation <?php echo htmlspecialchars($quotation['quotation_no']); ?></title>
<style>
    * { box-sizing: border-box; }
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        color: #1a1a1a;
        margin: 0;
        padding: 40px 50px;
        font-size: 14px;
    }
    .toolbar { text-align: right; margin-bottom: 20px; }
    .toolbar button {
        background: #0d7890; color: #fff; border: none; padding: 8px 18px;
        border-radius: 4px; font-size: 13px; cursor: pointer;
    }
    .logo-block { margin-bottom: 10px; }
    .logo-block img { height: 150px; }
    h1.title {
        text-align: center;
        color: #d0021b;
        font-size: 22px;
        text-decoration: underline;
        margin: 10px 0 30px;
    }
    .meta { margin-bottom: 20px; }
    .meta div { margin-bottom: 4px; }
    .parties {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .parties .col { width: 48%; }
    .parties .label {
        color: #1a1a1a;
        font-weight: bold;
        margin-bottom: 4px;
    }
    .parties .col div { line-height: 1.5; }
    table.items {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 4px;
    }
    table.items th {
        background: #ffffff;
        color: #2e8b57;
        border: 1px solid #000;
        padding: 8px;
        font-size: 14px;
        text-align: left;
    }
    table.items td {
        border: 1px solid #000;
        padding: 8px;
        vertical-align: top;
        min-height: 20px;
    }
    table.items tr.spacer-row td {
        background: #e4e1f0;
        padding: 4px;
        border: 1px solid #000;
    }
    table.items td.num { text-align: center; }
    table.items td.amt { text-align: right; }
    table.items .filler td { height: 220px; border: 1px solid #000; }
    table.items .total-row td {
        border-top: 2px solid #000;
        font-weight: bold;
    }
    table.items .total-row td.no-border {
        border: none;
    }
    table.summary {
        width: 260px;
        margin-left: auto;
        margin-top: 10px;
        border-collapse: collapse;
    }
    table.summary td {
        padding: 4px 8px;
        font-size: 13px;
    }
    table.summary td.label { text-align: right; font-weight: bold; }
    table.summary td.val { text-align: right; width: 100px; }
    table.summary tr.grand td { border-top: 1px solid #000; font-size: 14px; }
    .thanks { margin-top: 50px; font-weight: bold; }
    .stamp { margin-top: 15px; }
    .stamp img { height: 90px; }
    @media print {
        .toolbar { display: none; }
        body { padding: 20px 40px; }
    }
</style>
</head>
<body>

<div class="toolbar">
    <button onclick="window.print()">Print</button>
</div>

<div class="logo-block">
    <img src="images/logo.jpg" alt="Levi Marketing Pvt Ltd">
</div>

<h1 class="title">Quotation</h1>

<div class="meta">
    <div>Date: <?php echo htmlspecialchars($displayDate); ?></div>
    <div>Quotation No: <?php echo htmlspecialchars($quotation['quotation_no']); ?></div>
</div>

<div class="parties">
    <div class="col">
        <div class="label">SHIPPER</div>
        <div>Levi Marketing Pvt Ltd</div>
        <div>No.61/1/G, Wellawidiya Road,</div>
        <div>Gonawala, Kelaniya.</div>
        <div>070 180 2080</div>
    </div>
    <div class="col">
        <div class="label">RECEVER</div>
        <div><?php echo htmlspecialchars($quotation['customer_name'] ?? ''); ?></div>
        <?php if (!empty($quotation['customer_address'])): ?>
            <div><?php echo nl2br(htmlspecialchars($quotation['customer_address'])); ?></div>
        <?php endif; ?>
        <?php if (!empty($quotation['customer_phone'])): ?>
            <div><?php echo htmlspecialchars($quotation['customer_phone']); ?></div>
        <?php endif; ?>
    </div>
</div>

<table class="items">
    <thead>
        <tr>
            <th style="width:50%">Description</th>
            <th style="width:12%">Qty</th>
            <th style="width:18%">Unit Price</th>
            <th style="width:20%">Amount</th>
        </tr>
    </thead>
    <tbody>
        <tr class="spacer-row">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <?php foreach ($lines as $line): ?>
        <tr>
            <td><?php echo htmlspecialchars($line['description']); ?></td>
            <td class="num"><?php echo rtrim(rtrim(number_format((float)$line['qty'], 2), '0'), '.'); ?></td>
            <td class="amt"><?php echo money($line['unitprice']); ?></td>
            <td class="amt"><?php echo money($line['amount']); ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="filler"><td></td><td></td><td></td><td></td></tr>
        <tr class="total-row">
            <td class="no-border"></td>
            <td class="no-border"></td>
            <td style="text-align:right">Total</td>
            <td class="amt"><?php echo money($quotation['total']); ?></td>
        </tr>
    </tbody>
</table>

<?php if ($showVatRows): ?>
<table class="summary">
    <tr>
        <td class="label">Discount</td>
        <td class="val"><?php echo money($quotation['discounttotal']); ?></td>
    </tr>
    <tr>
        <td class="label">Net Total</td>
        <td class="val"><?php echo money($quotation['nettotal']); ?></td>
    </tr>
    <tr>
        <td class="label">VAT (<?php echo rtrim(rtrim(number_format($vatPercent, 2), '0'), '.'); ?>%)</td>
        <td class="val"><?php echo money($quotation['vatamount']); ?></td>
    </tr>
    <tr class="grand">
        <td class="label">Total With VAT</td>
        <td class="val"><strong><?php echo money($quotation['nettotal_with_vat']); ?></strong></td>
    </tr>
</table>
<?php endif; ?>

<div class="thanks">Thank You For Your Business.</div>

<script>
    // Auto-open the print dialog once everything (including the logo/stamp
    // images) has actually loaded, so the preview isn't triggered on blank
    // images. Matches the GRN/PO print pages.
    window.addEventListener('load', function () {
        setTimeout(function () {
            window.print();
        }, 300);
    });
</script>

</body>
</html>