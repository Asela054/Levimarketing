<?php
session_start();
if(!isset($_SESSION['userid'])){header ("Location:../index.php"); exit;}
require_once('../connection/db.php');

$paymentID=isset($_POST['paymentid']) ? intval($_POST['paymentid']) : 0;

$sqlhead="SELECT p.*, s.`suppliername`
          FROM `tbl_supplier_payment` p
          JOIN `tbl_supplier` s ON s.`idtbl_supplier`=p.`tbl_supplier_idtbl_supplier`
          WHERE p.`idtbl_supplier_payment`='$paymentID'";
$resulthead=$conn->query($sqlhead);

if(!$resulthead || $resulthead->num_rows==0){
    echo '<div class="text-center text-muted">Payment not found</div>';
    exit;
}
$head=$resulthead->fetch_assoc();

$method=($head['paymethod']==1) ? 'Cash' : (($head['paymethod']==2) ? 'Cheque' : 'Bank Transfer');
$reference='-';
if($head['paymethod']==2){ $reference=htmlspecialchars($head['chequeno']).' ('.$head['chequedate'].')'; }
if($head['paymethod']==3){ $reference=htmlspecialchars($head['bankref']); }

$sqldetail="SELECT g.`idtbl_grn`, g.`date`, g.`invoicenum`, g.`total`, d.`amount`
            FROM `tbl_supplier_payment_detail` d
            JOIN `tbl_grn` g ON g.`idtbl_grn`=d.`tbl_grn_idtbl_grn`
            WHERE d.`tbl_supplier_payment_idtbl_supplier_payment`='$paymentID' AND d.`status`=1
            ORDER BY g.`date`, g.`idtbl_grn`";
$resultdetail=$conn->query($sqldetail);
?>
<div class="row mb-2 small">
    <div class="col-md-6">
        <div><span class="font-weight-bold">Supplier :</span> <?php echo htmlspecialchars($head['suppliername']); ?></div>
        <div><span class="font-weight-bold">Date :</span> <?php echo $head['date']; ?></div>
        <div><span class="font-weight-bold">Status :</span> <?php echo ($head['status']==3) ? 'Cancelled' : (($head['confirm_status']==1) ? 'Approved' : 'Pending Approval'); ?></div>
    </div>
    <div class="col-md-6">
        <div><span class="font-weight-bold">Method :</span> <?php echo $method; ?></div>
        <div><span class="font-weight-bold">Reference :</span> <?php echo $reference; ?></div>
        <div><span class="font-weight-bold">Remark :</span> <?php echo htmlspecialchars($head['remark']); ?></div>
    </div>
</div>
<table class="table table-bordered table-sm table-striped small">
    <thead>
        <tr>
            <th>GRN</th>
            <th>Date</th>
            <th>Invoice</th>
            <th class="text-right">GRN Total</th>
            <th class="text-right">Paid In This Payment</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row=$resultdetail->fetch_assoc()){ ?>
        <tr>
            <td><?php echo 'GRN-'.$row['idtbl_grn']; ?></td>
            <td><?php echo $row['date']; ?></td>
            <td><?php echo htmlspecialchars($row['invoicenum']); ?></td>
            <td class="text-right"><?php echo number_format($row['total'],2); ?></td>
            <td class="text-right"><?php echo number_format($row['amount'],2); ?></td>
        </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4" class="text-right">Total Payment</th>
            <th class="text-right"><?php echo number_format($head['totalpayment'],2); ?></th>
        </tr>
    </tfoot>
</table>