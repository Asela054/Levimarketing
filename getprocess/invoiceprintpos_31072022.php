<?php
session_start();
require_once('../connection/db.php');

$recordID=$_POST['recordID'];

$sqlinvoiceinfo="SELECT `tbl_invoice`.`idtbl_invoice`, `tbl_invoice`.`date`, `tbl_invoice`.`total`, `tbl_invoice`.`paymentcomplete`, `tbl_customer`.`name`, `tbl_customer`.`address` FROM `tbl_invoice` LEFT JOIN `tbl_customer` ON `tbl_customer`.`idtbl_customer`=`tbl_invoice`.`customerid` WHERE `tbl_invoice`.`status`=1 AND `tbl_invoice`.`idtbl_invoice`='$recordID'";
$resultinvoiceinfo =$conn-> query($sqlinvoiceinfo); 
$rowinvoiceinfo = $resultinvoiceinfo-> fetch_assoc();

$sqlinvoicedetail="SELECT `tbl_product`.`product_name`, `tbl_invoice_detail`.`qty`, `tbl_invoice_detail`.`saleprice` FROM `tbl_invoice_detail` LEFT JOIN `tbl_product` ON `tbl_product`.`idtbl_product`=`tbl_invoice_detail`.`tbl_product_idtbl_product` WHERE `tbl_invoice_detail`.`tbl_invoice_idtbl_invoice`='$recordID' AND `tbl_invoice_detail`.`status`=1";
$resultinvoicedetail=$conn->query($sqlinvoicedetail);

?>
<div class="row">
    <div class="col-12">
        <table class="w-100 tableprint">
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="5" class="text-center small align-middle">
                        <h2 class="font-weight-light m-0">Lional Trade Center</h2>
                        Main St Marawila, Sri Lanka<br>
                        Tel: 0094--32-2254347 | info@lioneltradecenter.lk<br>
                    </td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>            
        </table>  
    </div>
</div>
<div class="row mt-3">
    <div class="col-12">Invoice: INV-<?php echo $rowinvoiceinfo['idtbl_invoice'] ?></div>
    <div class="col-12">Date: <?php echo $rowinvoiceinfo['date'] ?></div>
</div>
<div class="row mt-3">
    <div class="col-12">
        <table class="table table-striped table-bordered table-black bg-transparent table-sm w-100 tableprint text-center">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th class="text-right">Sale Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    while($rowinvoicedetail=$resultinvoicedetail->fetch_assoc()){
                        $totamount=$rowinvoicedetail['qty']*$rowinvoicedetail['saleprice'];
                        $total=number_format(($totamount), 2);
                ?>
                <tr>
                    <td colspan="4" class="text-left"><?php echo $rowinvoicedetail['product_name']; ?></td>
                </tr>
                <tr>
                    <td colspan='2' class="text-center"><?php echo $rowinvoicedetail['qty']; ?></td>
                    <td class="text-right"><?php echo number_format($rowinvoicedetail['saleprice'],2); ?></td>
                    <td class="text-right"><?php echo $total; ?></td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-left">Net Total</th>
                    <th class="text-right"><?php echo number_format($rowinvoiceinfo['total'], 2) ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<div class="row mt-4">
    <div class="col-12 text-center">
        <h3 class="font-weight-normal">Thank You</h3>
        <h5 class="font-weight-normal">Come again</h5>
        <small>Copyright © Lionel Trade Center 2022 ERav Technology</small>
    </div>
</div>