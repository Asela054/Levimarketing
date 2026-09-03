<?php
session_start();
require_once('../connection/db.php');

$categoryID=$_POST['categoryID'];
$subcategoryID=$_POST['subcategoryID'];
$groupcategoryID=$_POST['groupcategoryID'];
$saletype=$_POST['saletype'];
$locationID=$_SESSION['location_id'];

// $sql="SELECT `idtbl_product`, `product_code`, `barcode`, `product_name`, `unitprice`, `saleprice` FROM `tbl_product` WHERE `tbl_product_category_idtbl_product_category`='$categoryID' AND `tbl_sub_product_category_idtbl_sub_product_category`='$subcategoryID' AND `tbl_group_category_idtbl_group_category`='$groupcategoryID' AND `status`=1";
$sql="SELECT `idtbl_product`, `product_code`, `barcode`, `product_name`, `unitprice`, `saleprice`, `wholesaleprice`, `maxdiscount` FROM `tbl_product` WHERE `tbl_product_category_idtbl_product_category`='$categoryID' AND `tbl_group_category_idtbl_group_category`='$groupcategoryID' AND `status`=1";
$result=$conn->query($sql);
?>
<div class="row">
    <div class="col-12">
        <div id="style-2" style="max-height:500px; overflow-y:auto; overflow-x:auto;">
            <table class="table table-bordered table-striped" id="tableproductpricelist">
            <thead>
                <tr>
                    <th>#</th>
                    <th>CODE</th>
                    <th>PRODUCT</th>
                    <th>STOCK</th>
                    <th class="text-right d-none">UNIT PRICE</th>
                    <th class="text-right">RETAIL / WHOLE SALE PRICE</th>
                    <th class="d-none">MAX DISCOUNT</th>
                </tr>
            </thead>
            <tbody>
                    <?php 
                    $i=0;
                    if($result->num_rows > 0) {while ($row = $result-> fetch_assoc()) { 
                        $productID=$row['idtbl_product'];
                        $sqlstockcheck="SELECT SUM(CASE WHEN `status` = 1 THEN `qty` ELSE 0 END) AS `active_qty` FROM `tbl_stock` WHERE `tbl_product_idtbl_product`='$productID' AND `tbl_location_idtbl_location`='$locationID' AND `status` = 1";
                        $resultstockcheck=$conn->query($sqlstockcheck);
                        $rowstockcheck = $resultstockcheck-> fetch_assoc();

                        $stockcount = isset($rowstockcheck['active_qty']) && $rowstockcheck['active_qty'] !== null ? (float)$rowstockcheck['active_qty'] : 0;
                    ?>
                    <tr tabindex="<?php echo $i; ?>" class="classfocus <?php if($stockcount<=0){echo 'table-danger';}else{echo 'pointer';} ?>" id="<?php echo $row['idtbl_product'] ?>">
                        <td><?php echo $row['idtbl_product'] ?></td>
                        <td><?php echo $row['product_code'] ?></td>
                        <td><?php echo $row['product_name'] ?></td>
                        <td><?php echo $stockcount ?></td>
                        <td class="text-right d-none"><?php echo number_format($row['unitprice'], 2); ?></td>
                        <td class="text-right"><?php if($saletype==1){echo number_format($row['saleprice'], 2);}else{echo number_format($row['wholesaleprice'], 2);} ?></td>
                        <td class="d-none"><?php echo $row['maxdiscount']; ?></td>
                    </tr>
                    <?php $i++;}}else{ ?>
                <tr>
                    <td colspan="5">No Product To Show</td>
                </tr> 
                <?php } ?>
            </tbody>
        </table>
            </div>
    </div>
</div>