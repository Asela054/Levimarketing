<?php
// same includes/session lines as your other getprocess files (must provide $conn)

function esc_chq($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

$detailID = (int)$_POST['chequeid'];
$today    = date('Y-m-d');

// Method names shown in the payment methods table. Add your other method names here.
$methodnames = array(
    3 => 'Cheque'
);

// The cheque being returned + its receipt
$res = $conn->query("SELECT d.`idtbl_invoice_payment_detail`, d.`amount`, d.`bank`, d.`chequeno`, d.`chequedate`, d.`chequereturnstatus`,
                            p.`idtbl_invoice_payment`, p.`date` AS paydate, p.`payment`, p.`balance`
                     FROM `tbl_invoice_payment_detail` d
                     INNER JOIN `tbl_invoice_payment` p ON p.`idtbl_invoice_payment` = d.`tbl_invoice_payment_idtbl_invoice_payment`
                     WHERE d.`idtbl_invoice_payment_detail`='$detailID' AND d.`status`=1 AND d.`method`=3");
$cheque = $res->fetch_assoc();

if (!$cheque) {
    echo '<div class="alert alert-danger mb-0">Cheque payment record not found.</div>';
    exit;
}
if ($cheque['chequereturnstatus'] == 1) {
    echo '<div class="alert alert-warning mb-0">This cheque is already returned.</div>';
    exit;
}

$paymentID = (int)$cheque['idtbl_invoice_payment'];
$minReturn = date('Y-m-d', strtotime($cheque['chequedate'] . ' +1 day'));

if ($minReturn > $today) {
    echo '<div class="alert alert-warning mb-0">Cheque can only be returned after the cheque date (' . esc_chq($cheque['chequedate']) . ').</div>';
    exit;
}

// All payment methods of this receipt
$methodRes = $conn->query("SELECT `idtbl_invoice_payment_detail`, `method`, `amount`, `bank`, `chequeno`, `chequedate`, `chequereturnstatus`
                           FROM `tbl_invoice_payment_detail`
                           WHERE `tbl_invoice_payment_idtbl_invoice_payment`='$paymentID' AND `status`=1");

// Invoices paid by this receipt
$invRes = $conn->query("SELECT i.`invtype`, i.`manuelinvno`, i.`taxinvoice_no`, i.`date`, i.`nettotal_with_vat`, i.`paymentcomplete`,
                               h.`total`, h.`discount`, h.`payamount`
                        FROM `tbl_invoice_payment_has_tbl_invoice` h
                        INNER JOIN `tbl_invoice` i ON i.`idtbl_invoice` = h.`tbl_invoice_idtbl_invoice`
                        WHERE h.`tbl_invoice_payment_idtbl_invoice_payment`='$paymentID'");
?>
<div id="chequereturninfo" class="d-none"
     data-chequeid="<?php echo $detailID; ?>"
     data-chequedate="<?php echo esc_chq($cheque['chequedate']); ?>"
     data-minreturn="<?php echo $minReturn; ?>"
     data-maxreturn="<?php echo $today; ?>"></div>

<h6 class="font-weight-bold text-dark">Payment Receipt</h6>
<table class="table table-bordered table-sm mb-3">
    <tr>
        <th width="20%">Receipt No</th>
        <td width="30%">PR-<?php echo $paymentID; ?></td>
        <th width="20%">Payment Date</th>
        <td width="30%"><?php echo esc_chq($cheque['paydate']); ?></td>
    </tr>
    <tr>
        <th>Total Payment</th>
        <td><?php echo number_format($cheque['payment'], 2); ?></td>
        <th>Balance</th>
        <td><?php echo number_format($cheque['balance'], 2); ?></td>
    </tr>
</table>

<h6 class="font-weight-bold text-dark">Payment Methods</h6>
<table class="table table-bordered table-sm mb-3">
    <thead>
        <tr>
            <th>Method</th>
            <th>Bank</th>
            <th>Cheque No</th>
            <th>Cheque Date</th>
            <th class="text-right">Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($m = $methodRes->fetch_assoc()) {
            $isThis = ($m['idtbl_invoice_payment_detail'] == $detailID);
            $mname  = isset($methodnames[$m['method']]) ? $methodnames[$m['method']] : 'Method ' . $m['method'];
        ?>
        <tr class="<?php echo $isThis ? 'table-warning' : ''; ?>">
            <td><?php echo esc_chq($mname); ?><?php echo $isThis ? ' <span class="badge badge-warning">Returning</span>' : ''; ?></td>
            <td><?php echo $m['method'] == 3 ? esc_chq($m['bank']) : '-'; ?></td>
            <td><?php echo $m['method'] == 3 ? esc_chq($m['chequeno']) : '-'; ?></td>
            <td><?php echo $m['method'] == 3 ? esc_chq($m['chequedate']) : '-'; ?></td>
            <td class="text-right"><?php echo number_format($m['amount'], 2); ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<h6 class="font-weight-bold text-dark">Related Invoice(s)</h6>
<div class="table-responsive">
    <table class="table table-bordered table-sm mb-0">
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>Invoice Date</th>
                <th class="text-right">Invoice Total</th>
                <th class="text-right">Total</th>
                <th class="text-right">Discount</th>
                <th class="text-right">Paid (this receipt)</th>
                <th>Payment Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($inv = $invRes->fetch_assoc()) {
                $invno = ($inv['invtype'] == 1) ? $inv['taxinvoice_no'] : 'INV-' . $inv['manuelinvno'];
            ?>
            <tr>
                <td><?php echo esc_chq($invno); ?></td>
                <td><?php echo esc_chq($inv['date']); ?></td>
                <td class="text-right"><?php echo number_format($inv['nettotal_with_vat'], 2); ?></td>
                <td class="text-right"><?php echo number_format($inv['total'], 2); ?></td>
                <td class="text-right"><?php echo number_format($inv['discount'], 2); ?></td>
                <td class="text-right"><?php echo number_format($inv['payamount'], 2); ?></td>
                <td><?php echo $inv['paymentcomplete'] == 1 ? '<span class="badge badge-success">Completed</span>' : '<span class="badge badge-secondary">Pending</span>'; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>