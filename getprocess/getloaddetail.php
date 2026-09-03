<?php
require_once('../connection/db.php');

$loadID=$_POST['loadID'];

$sql="SELECT `tbl_vehicle_load_detail`.`qty`, `tbl_product`.`product_name` FROM `tbl_vehicle_load_detail` LEFT JOIN `tbl_product` ON `tbl_product`.`idtbl_product`=`tbl_vehicle_load_detail`.`tbl_product_idtbl_product` WHERE `tbl_vehicle_load_detail`.`status`=1 AND `tbl_vehicle_load_detail`.`tbl_vehicle_load_idtbl_vehicle_load`='$loadID'";
$result=$conn->query($sql);
?>
<table class="table table-striped table-bordered table-sm" id="grnlisttable">
    <thead>
        <tr>
            <th>Product</th>
            <th class="text-center">Qty</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row=$result->fetch_assoc()){ ?>
        <tr>
            <td><?php echo $row['product_name']; ?></td>
            <td class="text-center"><?php echo $row['qty']; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>