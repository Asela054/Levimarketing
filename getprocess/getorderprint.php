<?php
session_start();
require_once('../connection/db.php');

$orderID=$_POST['orderID'];

$sqlorder="SELECT p.`idtbl_porder`, p.`subtotal`, p.`disamount`, p.`nettotal`, p.`remark`, p.`orderdate`, s.`suppliername` FROM `tbl_porder` p LEFT JOIN `tbl_supplier` s ON s.`idtbl_supplier` = p.`tbl_supplier_idtbl_supplier` WHERE p.`idtbl_porder`='$orderID'";
$resultorder=$conn->query($sqlorder);
$roworder=$resultorder->fetch_assoc();

$orderdate=$roworder['orderdate'];

$sqlordercount="SELECT COUNT(*) AS `count` FROM `tbl_porder` WHERE `orderdate`='$orderdate'";
$resultordercount=$conn->query($sqlordercount);
$rowordercount=$resultordercount->fetch_assoc();

$sqlorderinfo="SELECT `tbl_porder_detail`.*, `tbl_product`.`product_name` FROM `tbl_porder_detail` LEFT JOIN `tbl_product` ON `tbl_product`.`idtbl_product`=`tbl_porder_detail`.`tbl_product_idtbl_product` WHERE `tbl_porder_detail`.`status`=1 AND `tbl_porder_detail`.`tbl_porder_idtbl_porder`='$orderID'";
$resultorderinfo=$conn->query($sqlorderinfo);

// $sqlorderproductfive="SELECT `refillqty`, `returnqty`, `newqty` FROM `tbl_porder_detail` WHERE `status`=1 AND `tbl_porder_idtbl_porder`='$orderID' AND `tbl_product_idtbl_product`=5";
// $resultorderproductfive=$conn->query($sqlorderproductfive);
// $roworderproductfive=$resultorderproductfive->fetch_assoc();

// $sqlcheque="SELECT `chequeno`, `chequedate` FROM `tbl_porder_payment` WHERE `tbl_porder_idtbl_porder`='$orderID' AND `status`=1";
// $resultcheque=$conn->query($sqlcheque);
// $rowcheque=$resultcheque->fetch_assoc();

$sqldelivery="SELECT * FROM `tbl_porder_delivery` WHERE `tbl_porder_idtbl_porder`='$orderID' AND `status`=1";
$resultdelivery=$conn->query($sqldelivery);
$rowdelivery=$resultdelivery->fetch_assoc();

$lorryID=$rowdelivery['vehicleid'];
$trailerID=$rowdelivery['trailerid'];

$sqlvehicle="SELECT `vehicleno` FROM `tbl_vehicle` WHERE `idtbl_vehicle`='$lorryID' AND `status`=1 AND `type`=0";
$resultvehicle=$conn->query($sqlvehicle);
$rowvehicle=$resultvehicle->fetch_assoc();

$sqltrailer="SELECT `vehicleno` FROM `tbl_vehicle` WHERE `idtbl_vehicle`='$trailerID' AND `status`=1 AND `type`=1";
$resulttrailer=$conn->query($sqltrailer);
$rowtrailer=$resulttrailer->fetch_assoc();

$arrayaccessories=array();
$sqlaccessories="SELECT `idtbl_product`, `product_name` FROM `tbl_product` WHERE `status`=1 AND `tbl_product_category_idtbl_product_category`=2";
$resultaccessories=$conn->query($sqlaccessories);
while($rowaccessories=$resultaccessories->fetch_assoc()){
    $objaccessories=new stdClass();
    $objaccessories->accessoriesID=$rowaccessories['idtbl_product'];
    $objaccessories->accessories=$rowaccessories['product_name'];

    array_push($arrayaccessories, $objaccessories);
}

?>
<div class="row">
    <div class="col-12 small">
        <table class="table table-borderless table-sm text-center w-100 tableprint">
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <h3 class="font-weight-light m-0">Levi Marketing Pvt Ltd</h3>
                        <h4 class="mt-2">Purchsing Order</h4>
                    </td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>            
        </table>        
    </div>
</div>
<div class="row">
    <div class="col-12">
        <table class="table table-bordered table-black bg-transparent table-sm small w-100 tableprint">
                <tr>
                <td class="small">Order Date</td>
                <td class="small"><?php echo $roworder['orderdate'] ?></td>
                <td class="small">Supplier</td>
                <td class="small" colspan="3"><?php echo isset($roworder['suppliername'])? $roworder['suppliername'] : '' ?></td>
                <td class="small">Code</td>
                <td class="small">1008684</td>
            </tr>
            <tr>
                <td class="small">ASM Name</td>
                <td class="small" colspan="7"><?php echo $_SESSION['name'] ?></td>
            </tr>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <table class="table table-striped table-bordered table-black table-sm small bg-transparent text-center mb-0 tableprint">
            <thead>
                <tr>
                    <td class="align-top small">#</td>
                    <td class="align-top small">Prodcut Name</td>
                    <td class="align-top small text-right">Unit Price</td>
                    <td class="align-top small text-center">Prodcut Qty</td>
                    <td class="align-top small text-right">Total</td>
                </tr>
            </thead>
            <tbody>
                <?php $i=1; while($roworderinfo=$resultorderinfo->fetch_assoc()){ ?>
                <tr>
                    <td class="align-top small"><?php echo $i ?></td>
                    <td class="align-top small"><?php echo $roworderinfo['product_name'] ?></td>
                    <td class="align-top small text-right"><?php echo number_format($roworderinfo['unitprice'],2) ?></td>
                    <td class="align-top small text-center"><?php echo $roworderinfo['qty'] ?></td>
                    <td class="align-top small text-right"><?php echo number_format(($roworderinfo['qty']*$roworderinfo['unitprice']), 2) ?></td>
                </tr>
                <?php $i++;} ?>
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-left" colspan="4">Net Total</th>
                    <th class="text-right"><?php echo number_format($roworder['nettotal'], 2) ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-12 small">
        <table class="table table-borderless table-sm text-center w-100 tableprint w-100">
            <tbody>
                <tr>
                    <td>
                        **Collection only<br>
                        **Time (For collections - approximate plant arrival time / for delivery - approximate time to arrive to distributor)
                    </td>
                </tr>
            </tbody>            
        </table>        
    </div>
</div>
<div class="row">
    <div class="col-12">
        <table class="table table-striped table-bordered table-black table-sm small bg-transparent text-center tableprint">
            <tbody>
                <tr>
                    <td><?php echo $roworder['remark'] ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-12 small">
        <table class="w-100 tableprint">
            <tbody>
                <tr>
                    <td class="align-top">
                        <h6 class="small">Cheque Information</h6>
                        Cheque no: <?php //echo $rowcheque['chequeno'] ?><br>
                        Cheque Date : <?php //echo $rowcheque['chequedate'] ?>
                    </td>
                    <td>&nbsp;</td>
                    <td class="text-center small align-bottom">
                        <hr class="border-dark m-0">
                        Confirm By: Signature of distribution manager
                    </td>
                    <td class="text-center small align-bottom">
                        <hr class="border-dark m-0">
                        Agreed by: Signature of ASM / DSE /DSO
                    </td>
                </tr>
            </tbody>            
        </table>          
    </div>
</div>
