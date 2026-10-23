<?php
session_start();
if(!isset($_SESSION['userid'])){header ("Location:../index.php"); exit;}
require_once('../connection/db.php');

$supplierID=isset($_POST['supplier']) ? intval($_POST['supplier']) : 0;

// Only approved, active GRNs that still have a balance.
// pendingamount = payments already created for the GRN that are still waiting for approval.
$sql="SELECT g.`idtbl_grn`, g.`date`, g.`invoicenum`, g.`total`, g.`subtotal`, g.`vattype`, g.`vatpercentage`, g.`vatamount`, g.`paidamount`,
        (SELECT IFNULL(SUM(d.`amount`),0) FROM `tbl_supplier_payment_detail` d
            JOIN `tbl_supplier_payment` p ON p.`idtbl_supplier_payment`=d.`tbl_supplier_payment_idtbl_supplier_payment`
            WHERE d.`tbl_grn_idtbl_grn`=g.`idtbl_grn` AND d.`status`=1 AND p.`status`=1 AND p.`confirm_status`=0) AS pendingamount,
        (SELECT v.`vat` FROM `tbl_vat_info` v WHERE v.`status`=1 AND v.`date_from`<=g.`date` ORDER BY v.`date_from` DESC LIMIT 1) AS stdvat
      FROM `tbl_grn` g
      WHERE g.`tbl_supplier_idtbl_supplier`='$supplierID' AND g.`status`=1 AND g.`confirm_status`=1 AND g.`paymentcomplete`=0
      ORDER BY g.`date`, g.`idtbl_grn`";
$result=$conn->query($sql);

$count=0;
if($result){
    while($row=$result->fetch_assoc()){
        $total=round($row['total'],2);
        $paid=round($row['paidamount'],2);
        $pending=round($row['pendingamount'],2);
        $balance=round($total-$paid-$pending,2);
        if($balance<=0){ continue; } // fully covered by payments waiting for approval
        $count++;

        // vattype: 0 = none (old GRNs), 1 = Exclusive, 2 = Inclusive
        $vattype=intval($row['vattype']);
        $vatlabel=($vattype==2) ? 'Incl' : (($vattype==1) ? 'Excl' : 'None');
        $vatpercentage=floatval($row['vatpercentage']);
        $subtotal=(floatval($row['subtotal'])==0) ? $total : floatval($row['subtotal']);
        $vatamount=floatval($row['vatamount']);

        // Reference only: standard VAT on GRN date, shown when the GRN itself was saved with 0%
        $std='';
        if($vattype>0 && $vatpercentage==0 && floatval($row['stdvat'])>0){
            $std=' <small class="text-muted" title="Standard VAT on GRN date">(std '.number_format($row['stdvat'],2).'%)</small>';
        }
        ?>
        <tr data-grn="<?php echo $row['idtbl_grn']; ?>" data-balance="<?php echo number_format($balance,2,'.',''); ?>">
            <td><input type="checkbox" class="chkgrn"></td>
            <td><?php echo 'GRN-'.$row['idtbl_grn']; ?></td>
            <td><?php echo $row['date']; ?></td>
            <td><?php echo htmlspecialchars($row['invoicenum']); ?></td>
            <td class="text-center"><?php echo $vatlabel.' ('.number_format($vatpercentage,2).'%)'.$std; ?></td>
            <td class="text-right c-sub" data-v="<?php echo $subtotal; ?>"><?php echo number_format($subtotal,2); ?></td>
            <td class="text-right c-vat" data-v="<?php echo $vatamount; ?>"><?php echo number_format($vatamount,2); ?></td>
            <td class="text-right c-total" data-v="<?php echo $total; ?>"><?php echo number_format($total,2); ?></td>
            <td class="text-right c-paid" data-v="<?php echo $paid; ?>"><?php echo number_format($paid,2); ?><?php if($pending>0){ echo '<br><small class="text-warning">+ '.number_format($pending,2).' pending</small>'; } ?></td>
            <td class="text-right c-bal" data-v="<?php echo $balance; ?>"><?php echo number_format($balance,2); ?></td>
            <td class="text-right"><input type="text" class="form-control form-control-sm text-right payamount ml-auto" style="width:110px" value="0.00" disabled></td>
        </tr>
        <?php
    }
}

if($count==0){
    echo '<tr><td colspan="11" class="text-center text-muted">No outstanding GRNs</td></tr>';
}