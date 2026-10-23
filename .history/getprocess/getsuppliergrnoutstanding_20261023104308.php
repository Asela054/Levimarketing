<?php
session_start();
if(!isset($_SESSION['userid'])){header ("Location:../index.php"); exit;}
require_once('../connection/db.php');

$supplierID=isset($_POST['supplier']) ? intval($_POST['supplier']) : 0;

// Only approved, active GRNs that still have a balance
$sql="SELECT g.`idtbl_grn`, g.`date`, g.`invoicenum`, g.`total`, g.`subtotal`, g.`vattype`, g.`vatpercentage`, g.`vatamount`, g.`paidamount`,
        (SELECT v.`vat` FROM `tbl_vat_info` v WHERE v.`status`=1 AND v.`date_from`<=g.`date` ORDER BY v.`date_from` DESC LIMIT 1) AS stdvat
      FROM `tbl_grn` g
      WHERE g.`tbl_supplier_idtbl_supplier`='$supplierID' AND g.`status`=1 AND g.`confirm_status`=1 AND g.`paymentcomplete`=0
      ORDER BY g.`date`, g.`idtbl_grn`";
$result=$conn->query($sql);

if(!$result || $result->num_rows==0){
    echo '<tr><td colspan="11" class="text-center text-muted">No outstanding GRNs</td></tr>';
    exit;
}

while($row=$result->fetch_assoc()){
    $total=round($row['total'],2);
    $paid=round($row['paidamount'],2);
    $balance=round($total-$paid,2);

    // vattype: 0 = none (old GRNs), 1 = Exclusive, 2 = Inclusive
    $vattype=intval($row['vattype']);
    $vatlabel=($vattype==2) ? 'Incl' : (($vattype==1) ? 'Excl' : 'None');
    $vatpercentage=floatval($row['vatpercentage']);
    $subtotal=(floatval($row['subtotal'])==0) ? $total : floatval($row['subtotal']);

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
        <td class="text-right"><?php echo number_format($subtotal,2); ?></td>
        <td class="text-right"><?php echo number_format($row['vatamount'],2); ?></td>
        <td class="text-right"><?php echo number_format($total,2); ?></td>
        <td class="text-right"><?php echo number_format($paid,2); ?></td>
        <td class="text-right"><?php echo number_format($balance,2); ?></td>
        <td class="text-right"><input type="text" class="form-control form-control-sm text-right payamount" style="width:110px" value="0.00" disabled></td>
    </tr>
    <?php
}