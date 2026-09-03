<?php
require_once('../connection/db.php');

$barcode=$_POST['barcode'];
$saletype=$_POST['saletype'];

$sql="SELECT `idtbl_product`, `product_code`, `barcode`, `product_name`, `unitprice`, `saleprice`, `wholesaleprice`, `maxdiscount`, `tbl_product_category_idtbl_product_category`, `tbl_group_category_idtbl_group_category` FROM `tbl_product` WHERE `barcode`='$barcode' AND `status`=1";
$result=$conn->query($sql);

$categoryID=0;
$groupcategoryID=0;
$html='';
$html.='
<div class="row">
    <div class="col-12">
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
            <tbody>';
                $i=0;
                if($result->num_rows > 0) {while ($row = $result-> fetch_assoc()) { 
                    $productID=$row['idtbl_product'];
                    $categoryID=$row['tbl_product_category_idtbl_product_category'];
                    $groupcategoryID=$row['tbl_group_category_idtbl_group_category'];
                    $sqlstockcheck="SELECT `qty` FROM `tbl_stock` WHERE `tbl_product_idtbl_product`='$productID'"; 
                    $resultstockcheck=$conn->query($sqlstockcheck);
                    $rowstockcheck = $resultstockcheck-> fetch_assoc();

                    if(!empty($rowstockcheck['qty'])){$stockcount=$rowstockcheck['qty'];}
                    else{$stockcount=0;}
                $html.='<tr tabindex="'.$i.'" class="classfocus '; if($stockcount==0){$html.='table-danger';}else{$html.='pointer';}$html.='" id="'.$row['idtbl_product'].'">
                    <td>'.$row['idtbl_product'].'</td>
                    <td>'.$row['product_code'].'</td>
                    <td>'.$row['product_name'].'</td>
                    <td>'.$stockcount.'</td>
                    <td class="text-right d-none">'.number_format($row['unitprice'], 2).'</td>
                    <td class="text-right">';if($saletype==1){$html.=number_format($row['saleprice'], 2);}else{$html.=number_format($row['wholesaleprice'], 2);}$html.='</td>
                    <td class="d-none">'.$row['maxdiscount'].'</td>
                </tr>';
                $i++;}}else{
                $html.='<tr>
                    <td colspan="5">No Product To Show</td>
                </tr>';
                }
            $html.='</tbody>
        </table>
    </div>
</div>';

$obj=new stdClass();
$obj->html=$html;
$obj->categoryID=$categoryID;
$obj->groupcategoryID=$groupcategoryID;

echo json_encode($obj);