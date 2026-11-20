<?php
session_start();
require_once('connection/db.php');

$cashier    = $_SESSION['name'];
$locationID = (int)$_SESSION['location_id'];
$recordID   = (int)$_GET['recordID'];

// ---------- Company ----------
$query = mysqli_query($conn, "SELECT companyname, address, contact1, contact2, contact3, email FROM tbl_location WHERE idtbl_location = $locationID");
$company = mysqli_fetch_assoc($query);

$companyName    = $company['companyname'] ?? '';
$companyAddress = $company['address'] ?? '';
$companyTel     = implode(' / ', array_filter([
    $company['contact1'] ?? '', $company['contact2'] ?? '', $company['contact3'] ?? ''
]));
$companyEmail   = $company['email'] ?? '';

// ---------- Payment header ----------
$sqlpay = "SELECT `idtbl_supplier_payment`, `paymentno`, `date`, `paymethod`, `chequeno`, `chequedate`, `bankref`,
                  `totalpayment`, `remark`, `status`, `confirm_status`, `tbl_supplier_idtbl_supplier`
           FROM `tbl_supplier_payment`
           WHERE `idtbl_supplier_payment` = '$recordID'";
$resultpay = $conn->query($sqlpay);
if (!$resultpay) { die('Payment SQL error: ' . $conn->error); }
$pay = $resultpay->fetch_assoc();
if (!$pay) { die('Payment not found'); }

// ---------- Supplier ----------
$resultsup = $conn->query("SELECT * FROM `tbl_supplier` WHERE `idtbl_supplier` = '" . (int)$pay['tbl_supplier_idtbl_supplier'] . "'");
$sup = $resultsup ? ($resultsup->fetch_assoc() ?: []) : [];
$supplierName    = $sup['suppliername'] ?? '';
$supplierAddress = $sup['address'] ?? ($sup['supplieraddress'] ?? '');

// ---------- Payment detail (GRNs paid) ----------
$detailTable = 'tbl_supplier_payment_detail';

// Detect column names from the table itself
$rc = $conn->query("SHOW COLUMNS FROM `$detailTable`");
if (!$rc) { die('Columns SQL error: ' . $conn->error); }
$cols = [];
while ($c = $rc->fetch_assoc()) { $cols[] = $c['Field']; }

$payFk = ''; $grnFk = ''; $amtCol = '';
foreach ($cols as $c) {
    if (strpos($c, 'tbl_') === 0 && stripos($c, 'supplier_payment') !== false && $payFk === '') { $payFk = $c; }
    elseif (strpos($c, 'tbl_') === 0 && stripos($c, 'grn') !== false && $grnFk === '') { $grnFk = $c; }
}
foreach (['payamount', 'amount', 'paidamount', 'payment'] as $a) {
    if (in_array($a, $cols)) { $amtCol = $a; break; }
}
if ($payFk === '' || $grnFk === '' || $amtCol === '') {
    die('Could not detect detail columns. Columns found: ' . implode(', ', $cols));
}

$resultdetail = $conn->query("SELECT * FROM `$detailTable` WHERE `$payFk` = '$recordID'");
if (!$resultdetail) { die('Detail SQL error: ' . $conn->error); }

$rows = [];
while ($d = $resultdetail->fetch_assoc()) {
    $rg = $conn->query("SELECT * FROM `tbl_grn` WHERE `idtbl_grn` = '" . (int)$d[$grnFk] . "'");
    $g  = $rg ? ($rg->fetch_assoc() ?: []) : [];
    $g['idtbl_grn'] = $g['idtbl_grn'] ?? $d[$grnFk];
    $g['payamount'] = $d[$amtCol];
    $rows[] = $g;
}

// ---------- Display helpers ----------
$methodNames = [1 => 'Cash', 2 => 'Cheque', 3 => 'Bank Transfer'];
$methodText  = $methodNames[$pay['paymethod']] ?? '';
$refText     = '-';
if ($pay['paymethod'] == 2) { $refText = $pay['chequeno'] . ' (' . $pay['chequedate'] . ')'; }
if ($pay['paymethod'] == 3) { $refText = $pay['bankref']; }

$statusText   = ($pay['status'] == 3) ? 'CANCELLED' : (($pay['confirm_status'] == 1) ? 'APPROVED' : 'PENDING');
$displayPayNo = 'SP-' . $pay['idtbl_supplier_payment'];

// ---------- Paging (same logic as the wholesale invoice) ----------
$itemsPerPage         = 6;
$maxItemsOnTotalsPage = 3;
$itemPages = array_chunk($rows, $itemsPerPage);
if (empty($itemPages)) {
    $itemPages = [[]];
} else {
    $lastIdx   = count($itemPages) - 1;
    $lastChunk = $itemPages[$lastIdx];
    if (count($lastChunk) > $maxItemsOnTotalsPage) {
        $split = count($lastChunk) - $maxItemsOnTotalsPage;
        array_splice($itemPages, $lastIdx, 1, [array_slice($lastChunk, 0, $split), array_slice($lastChunk, $split)]);
    }
}
$lastPageIndex = count($itemPages) - 1;
$rowNo = 0;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Supplier Payment</title>
    <style media="print">
        @page { size: 220mm 140mm; margin: 5mm; }
        body { font-family: Arial, sans-serif; line-height: 1.5; text-align: left; margin: 0; }
        header { position: fixed; top: 0; left: 0; right: 0; height: 160px; }
        footer { position: fixed; bottom: 0; left: 0; right: 0; height: 100px; }
        table, tr, th, td { font-family: Arial, sans-serif; font-size: 13px; }
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
        tr { page-break-inside: avoid; break-inside: avoid; }
        main { display: block; padding-top: 160px; padding-bottom: 100px; }
    </style>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.5; text-align: left; margin: 0; }
        header { position: fixed; top: 0; left: 0; right: 0; height: 160px; }
        footer { position: fixed; bottom: 0; left: 0; right: 0; height: 100px; }
        table, tr, th, td { font-family: Arial, sans-serif; font-size: 13px; }
        main { display: block; padding-top: 160px; padding-bottom: 100px; }
    </style>
</head>
<body>
    <header>
        <table style="width:100%;border-collapse:collapse;">
            <tr>
                <td width="55%" style="vertical-align:top;padding:0;">
                    <p style="margin:0;font-size:16px;font-weight:bold;">SUPPLIER PAYMENT VOUCHER</p>
                    <p style="margin:0;font-size:13px;font-weight:bold;">Paid To: <?php echo htmlspecialchars($supplierName); ?></p>
                    <p style="margin:0;font-size:13px;padding-left:24px;"><?php echo nl2br(htmlspecialchars($supplierAddress)); ?></p>
                    <p style="margin:6px 0 0;font-size:13px;">Method: <b><?php echo htmlspecialchars($methodText); ?></b></p>
                    <p style="margin:0;font-size:13px;">Reference: <?php echo htmlspecialchars($refText); ?></p>
                </td>
                <td width="45%" style="vertical-align:top;padding:0;">
                    <img src="images/levilogobw.png" alt="Levi Marketing Logo" style="width:250px;height:auto;display:block;margin:0 0 6px 0;">
                    <p style="margin:0;font-size:13px;text-transform:uppercase;"><?php echo $companyAddress; ?></p>
                    <p style="margin:0;font-size:13px;"><?php echo $companyTel; ?></p>
                    <p style="margin:0;font-size:13px;"><u>E-Mail: <?php echo $companyEmail; ?></u></p>
                    <p style="margin:0;font-size:13px;">Payment No: <?php echo $displayPayNo; ?> (<?php echo $statusText; ?>)</p>
                    <p style="margin:0;font-size:13px;">Date: <?php echo htmlspecialchars($pay['date']); ?></p>
                </td>
            </tr>
        </table>
    </header>

    <footer>
        <table style="width:100%;">
            <tr>
                <td style="vertical-align:top;">
                    <table style="width:100%;font-size:12px;margin-top:15px;">
                        <tr><td>Prepared by</td><td style="width:5%;">:</td><td><?php echo htmlspecialchars($cashier); ?></td></tr>
                        <tr><td style="padding-top:15px;">Checked by</td><td style="width:5%;padding-top:15px;">:</td><td style="padding-top:15px;">...................................</td></tr>
                    </table>
                </td>
                <td style="text-align:center;vertical-align:top;">
                    <p style="margin:0;font-size:12px;text-transform:uppercase;font-weight:bold;">Levi Marketing Pvt Ltd</p>
                    <p style="margin:0;margin-top:25px;font-size:12px;">..........................................................</p>
                    <p style="margin:0;font-size:12px;">Authorise Officer</p>
                </td>
                <td style="text-align:center;vertical-align:top;">
                    <p style="margin:0;font-size:12px;text-transform:uppercase;font-weight:bold;">Supplier</p>
                    <p style="margin:0;margin-top:25px;font-size:12px;">..........................................................</p>
                    <p style="margin:0;font-size:12px;">Received By / Signature</p>
                </td>
            </tr>
        </table>
    </footer>

    <?php foreach ($itemPages as $pageIndex => $pageRows): ?>
        <?php if ($pageIndex > 0): ?><div style="page-break-before:always;"></div><?php endif; ?>
        <main>
            <table style="table-layout:fixed;padding:3px;width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr>
                        <th style="width:6%;text-align:center;border:1px solid #000;">#</th>
                        <th style="width:16%;text-align:center;border:1px solid #000;">GRN</th>
                        <th style="width:16%;text-align:center;border:1px solid #000;">GRN Date</th>
                        <th style="width:22%;text-align:center;border:1px solid #000;">Supplier Invoice</th>
                        <th style="width:20%;text-align:right;border:1px solid #000;padding-right:10px;">GRN Total</th>
                        <th style="width:20%;text-align:right;border:1px solid #000;padding-right:10px;">Paid Now</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $cnt = count($pageRows); $i = 0;
                    foreach ($pageRows as $r):
                        $i++; $rowNo++;
                        $bb = ($i === $cnt && $pageIndex !== $lastPageIndex) ? 'border-bottom:1px solid #000;' : '';

                        $grnDate  = $r['date'] ?? '';
                        $grnInv   = $r['invoicenum'] ?? '';
                        $grnTotal = $r['total'] ?? 0;
                    ?>
                    <tr>
                        <td style="text-align:center;border-left:1px solid #000;border-right:1px solid #000;<?php echo $bb; ?>"><?php echo $rowNo; ?></td>
                        <td style="text-align:center;border-right:1px solid #000;<?php echo $bb; ?>">GRN-<?php echo htmlspecialchars($r['idtbl_grn'] ?? ''); ?></td>
                        <td style="text-align:center;border-right:1px solid #000;<?php echo $bb; ?>"><?php echo htmlspecialchars($grnDate); ?></td>
                        <td style="padding-left:10px;border-right:1px solid #000;<?php echo $bb; ?>"><?php echo htmlspecialchars($grnInv); ?></td>
                        <td style="text-align:right;padding-right:10px;border-right:1px solid #000;<?php echo $bb; ?>"><?php echo number_format((float)$grnTotal, 2); ?></td>
                        <td style="text-align:right;padding-right:10px;border-right:1px solid #000;<?php echo $bb; ?>"><?php echo number_format((float)$r['payamount'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <?php if ($pageIndex === $lastPageIndex): ?>
                <tfoot>
                    <tr>
                        <td colspan="3" style="border-top:1px solid #000;vertical-align:top;font-size:12px;">
                            <?php if (!empty($pay['remark'])): ?>Remark: <?php echo htmlspecialchars($pay['remark']); ?><?php endif; ?>
                        </td>
                        <td colspan="3" style="border:1px solid #000;">
                            <table style="width:100%;border-collapse:collapse;"><tr>
                                <td style="text-align:left;padding-left:35px;font-weight:bold;">Total Payment</td>
                                <td style="text-align:right;padding-right:10px;font-weight:bold;"><?php echo number_format((float)$pay['totalpayment'], 2); ?></td>
                            </tr></table>
                        </td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </main>
    <?php endforeach; ?>

    <script>
        window.print();
        setTimeout(() => { window.close(); }, 5000);
    </script>
</body>
</html>