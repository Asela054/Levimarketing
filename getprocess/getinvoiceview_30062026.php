<?php
session_start();
require_once('../connection/db.php');

$cashier=$_SESSION['name'];
$recordID=$_POST['recordID'];

$sqlinvoiceinfo="SELECT `tbl_invoice`.`idtbl_invoice`, `tbl_invoice`.`date`, `tbl_invoice`.`total`, `tbl_invoice`.`discounttotal`, `tbl_invoice`.`nettotal`, `tbl_invoice`.`paymentcomplete`, `tbl_customer`.`name`, `tbl_customer`.`address` FROM `tbl_invoice` LEFT JOIN `tbl_customer` ON `tbl_customer`.`idtbl_customer`=`tbl_invoice`.`customerid` WHERE `tbl_invoice`.`status`=1 AND `tbl_invoice`.`idtbl_invoice`='$recordID'";
$resultinvoiceinfo =$conn-> query($sqlinvoiceinfo); 
$rowinvoiceinfo = $resultinvoiceinfo-> fetch_assoc();

$sqlinvoicedetail="SELECT `tbl_product`.`product_name`, `tbl_invoice_detail`.`qty`, `tbl_invoice_detail`.`saleprice`, `tbl_invoice_detail`.`editstatus` FROM `tbl_invoice_detail` LEFT JOIN `tbl_product` ON `tbl_product`.`idtbl_product`=`tbl_invoice_detail`.`tbl_product_idtbl_product` WHERE `tbl_invoice_detail`.`tbl_invoice_idtbl_invoice`='$recordID' AND `tbl_invoice_detail`.`status`=1";
$resultinvoicedetail=$conn->query($sqlinvoicedetail);

?>
<div class="row">
    <div class="col-12 text-center">
        <h2 class="font-weight-light m-0">Levi Marketing Pvt Ltd</h2>
        No.61/1/C,Jayaweera Mawatha,Gonawala,Kelaniya<br>
        Tel: +94 75 7000 700 / +94 75 7000 300
    </div>
</div>
<div class="row">
    <div class="col-12">Date: <?php echo $rowinvoiceinfo['date'] ?></div>
    <div class="col-12">Invoice No: INV-<?php echo $rowinvoiceinfo['idtbl_invoice'] ?></div>
    <div class="col-12">Cashier: <?php echo $cashier; ?></div>
</div>
<div class="row">
    <div class="col-12">
        <table class="table table-striped table-bordered small">
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
            <?php 
                $itemtotal=0;
                while($rowinvoicedetail=$resultinvoicedetail->fetch_assoc()){
                    $totamount=$rowinvoicedetail['qty']*$rowinvoicedetail['saleprice'];
                    $total=number_format(($totamount), 2); ?>
                    <tr class="<?php if($rowinvoicedetail['editstatus']==1){echo 'table-info';} ?>">
                        <td colspan="4"><?php echo $rowinvoicedetail['product_name']; ?></td>
                    </tr>
                    <tr class="<?php if($rowinvoicedetail['editstatus']==1){echo 'table-info';} ?>">
                        <td>&nbsp;</td>
                        <td class="text-center"><?php echo number_format($rowinvoicedetail['saleprice'],2); ?></td>
                        <td class="text-right"><?php echo $rowinvoicedetail['qty']; ?></td>
                        <td class="text-right"><?php echo $total; ?></td>
                    </tr>
            <?php 
                $itemtotal++;} 
            ?>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-12 text-right">
        <div class="">Gross Amount: <?php echo number_format($rowinvoiceinfo['total'], 2) ?></div>
        <div class="">Discount: <?php echo number_format($rowinvoiceinfo['discounttotal'], 2) ?></div>
        <div class="display-4">Net Total: <?php echo number_format($rowinvoiceinfo['nettotal'], 2) ?></div>
    </div>
</div>