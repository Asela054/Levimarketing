<?php
/*
 RECONSTRUCTED FILE - your original was not provided.
 Renders the GRN header (once) + its line items, matching:

 SELECT idtbl_grn, date, total, subtotal, vattype, vatpercentage, vatamount,
        invoicenum, dispatchnum, porder_id, status, confirm_status, updatedatetime,
        tbl_user_idtbl_user, tbl_location_idtbl_location, tbl_supplier_idtbl_supplier
 FROM tbl_grn WHERE 1

 SELECT idtbl_grndetail, date, type, qty, unitprice, total, status, updatedatetime,
        tbl_user_idtbl_user, tbl_product_idtbl_product, tbl_grn_idtbl_grn
 FROM tbl_grndetail WHERE 1

 VAT display rule:
  - vattype = 1 (Exclusive): line totals are VAT-exclusive. Show Sub Total, VAT Amount,
    and Grand Total as three separate figures (VAT added on top).
  - vattype = 2 (Inclusive): line totals already include VAT. Show one Total that already
    includes VAT, with the VAT amount broken out underneath for reference only (not added again).
*/
session_start();
if(!isset($_SESSION['userid'])){ exit; }
require_once('../connection/db.php');

$grnid = isset($_POST['grnid']) ? intval($_POST['grnid']) : 0;
if (!$grnid) { echo '<div class="alert alert-danger">Invalid GRN.</div>'; exit; }

$sqlHeader = "SELECT
        g.`idtbl_grn`, g.`date`, g.`total`, g.`subtotal`, g.`vattype`, g.`vatpercentage`,
        g.`vatamount`, g.`invoicenum`, g.`dispatchnum`, g.`porder_id`, g.`status`,
        g.`confirm_status`, g.`updatedatetime`, g.`tbl_user_idtbl_user`,
        g.`tbl_location_idtbl_location`, g.`tbl_supplier_idtbl_supplier`,
        s.`suppliername`, l.`locationname`
    FROM `tbl_grn` g
    LEFT JOIN `tbl_supplier` s ON s.`idtbl_supplier` = g.`tbl_supplier_idtbl_supplier`
    LEFT JOIN `tbl_location` l ON l.`idtbl_location` = g.`tbl_location_idtbl_location`
    WHERE g.`idtbl_grn` = '$grnid'";
$resultHeader = $conn->query($sqlHeader);

if (!$resultHeader || $resultHeader->num_rows == 0) {
    echo '<div class="alert alert-danger">GRN not found.</div>';
    exit;
}
$header = $resultHeader->fetch_assoc();

$sqlDetail = "SELECT
        d.`idtbl_grndetail`, d.`date`, d.`type`, d.`qty`, d.`unitprice`, d.`total`,
        d.`status`, d.`updatedatetime`, d.`tbl_user_idtbl_user`,
        d.`tbl_product_idtbl_product`, d.`tbl_grn_idtbl_grn`,
        p.`productname`
    FROM `tbl_grndetail` d
    LEFT JOIN `tbl_product` p ON p.`idtbl_product` = d.`tbl_product_idtbl_product`
    WHERE d.`tbl_grn_idtbl_grn` = '$grnid'
    ORDER BY d.`idtbl_grndetail` ASC";
$resultDetail = $conn->query($sqlDetail);

$isInclusive = (intval($header['vattype']) === 2);
$vatLabel = $isInclusive ? 'Inclusive' : 'Exclusive';
?>
<div class="row mb-2">
    <div class="col-6">
        <strong>GRN No:</strong> GRN-<?php echo $header['idtbl_grn']; ?><br>
        <strong>Date:</strong> <?php echo htmlspecialchars($header['date']); ?><br>
        <strong>PO:</strong> <?php echo ($header['porder_id'] && $header['porder_id'] > 0) ? 'PO-'.$header['porder_id'] : '-'; ?><br>
        <strong>Supplier:</strong> <?php echo htmlspecialchars($header['suppliername'] ?: '-'); ?>
    </div>
    <div class="col-6 text-right">
        <strong>Invoice No:</strong> <?php echo htmlspecialchars($header['invoicenum']); ?><br>
        <strong>Dispatch No:</strong> <?php echo htmlspecialchars($header['dispatchnum']); ?><br>
        <strong>Location:</strong> <?php echo htmlspecialchars($header['locationname']); ?><br>
        <strong>Status:</strong>
        <?php echo (intval($header['confirm_status']) === 1)
            ? '<span class="text-success"><i class="fas fa-check"></i> Approved</span>'
            : '<span class="text-danger"><i class="fas fa-times"></i> Pending</span>'; ?>
    </div>
</div>
<hr>
<table class="table table-bordered table-sm table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Product</th>
            <th class="text-right">Unit Price</th>
            <th class="text-center">Qty</th>
            <th class="text-right">Total<?php echo $isInclusive ? ' (VAT Incl.)' : ''; ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        if ($resultDetail && $resultDetail->num_rows > 0) {
            while ($row = $resultDetail->fetch_assoc()) {
                ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($row['productname'] ?: ('Product #'.$row['tbl_product_idtbl_product'])); ?></td>
                    <td class="text-right"><?php echo number_format($row['unitprice'], 2); ?></td>
                    <td class="text-center"><?php echo rtrim(rtrim(number_format($row['qty'], 2), '0'), '.'); ?></td>
                    <td class="text-right"><?php echo number_format($row['total'], 2); ?></td>
                </tr>
                <?php
            }
        } else {
            echo '<tr><td colspan="5" class="text-center text-muted">No line items found.</td></tr>';
        }
        ?>
    </tbody>
</table>

<div class="row">
    <div class="col-12">
        <span class="badge badge-secondary">VAT <?php echo $vatLabel; ?> (<?php echo number_format($header['vatpercentage'], 2); ?>%)</span>
    </div>
</div>
<div class="row mt-2">
    <?php if ($isInclusive) { ?>
        <!-- Inclusive: the Total already contains VAT. Show Total, and break out
             the VAT portion underneath purely for reference - it is NOT added again. -->
        <div class="col-9 text-right"><h6>Sub Total (excl. VAT) : </h6></div>
        <div class="col-3 text-right"><h6><?php echo number_format($header['subtotal'], 2); ?></h6></div>

        <div class="col-9 text-right"><h6>VAT Amount (included) : </h6></div>
        <div class="col-3 text-right"><h6><?php echo number_format($header['vatamount'], 2); ?></h6></div>

        <div class="col-9 text-right"><h5>Total (VAT Inclusive) : </h5></div>
        <div class="col-3 text-right"><h5 class="text-dark"><?php echo number_format($header['total'], 2); ?></h5></div>
    <?php } else { ?>
        <!-- Exclusive: VAT is added on top of the subtotal to reach the total. -->
        <div class="col-9 text-right"><h6>Sub Total : </h6></div>
        <div class="col-3 text-right"><h6><?php echo number_format($header['subtotal'], 2); ?></h6></div>

        <div class="col-9 text-right"><h6>VAT Amount (<?php echo number_format($header['vatpercentage'], 2); ?>%) : </h6></div>
        <div class="col-3 text-right"><h6><?php echo number_format($header['vatamount'], 2); ?></h6></div>

        <div class="col-9 text-right"><h5>Grand Total : </h5></div>
        <div class="col-3 text-right"><h5 class="text-dark"><?php echo number_format($header['total'], 2); ?></h5></div>
    <?php } ?>
</div>