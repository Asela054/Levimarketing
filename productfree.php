<?php 
include "include/header.php";  

$sql="SELECT `u`.*, `ua`.`product_name` AS `issueproduct`, `ub`.`product_name` AS `freeproduct` FROM `tbl_product_free` AS `u` LEFT JOIN `tbl_product` AS `ua` ON `ua`.`idtbl_product`=`u`.`issueproductid` LEFT JOIN `tbl_product` AS `ub` ON `ub`.`idtbl_product`=`u`.`freeproductid` WHERE `u`.`status` IN (1,2)";
$result =$conn-> query($sql); 

$productarray=array();
$sqlproduct="SELECT `idtbl_product`, `product_name` FROM `tbl_product` WHERE `status`=1";
$resultproduct =$conn-> query($sqlproduct);
while ($rowproduct = $resultproduct-> fetch_assoc()) {
    $obj=new stdClass();
    $obj->productID=$rowproduct['idtbl_product'];
    $obj->product=$rowproduct['product_name'];

    array_push($productarray, $obj);
}

include "include/topnavbar.php"; 
?>
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <?php include "include/menubar.php"; ?>
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="page-header page-header-light bg-white shadow">
                <div class="container-fluid">
                    <div class="page-header-content py-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="shopping-cart"></i></div>
                            <span>Product Free Issue</span>
                        </h1>
                    </div>
                </div>
            </div>
            <div class="container-fluid mt-2 p-0 p-2">
                <div class="card">
                    <div class="card-body p-0 p-2">
                        <div class="row">
                            <div class="col-3">
                                <form action="process/productfreeprocess.php" method="post" autocomplete="off">
                                    <div class="form-group mb-1">
                                        <label class="small font-weight-bold text-dark">Issue Product*</label>
                                        <select class="form-control form-control-sm" name="issueproduct" id="issueproduct" required>
                                            <option value="">Select</option>
                                            <?php foreach($productarray as $rowproductlist) { ?>
                                            <option value="<?php echo $rowproductlist->productID ?>"><?php echo $rowproductlist->product ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-1">
                                        <label class="small font-weight-bold text-dark">Free Product*</label>
                                        <select class="form-control form-control-sm" name="freeproduct" id="freeproduct" required>
                                            <option value="">Select</option>
                                            <?php foreach($productarray as $rowproductlist) { ?>
                                            <option value="<?php echo $rowproductlist->productID ?>"><?php echo $rowproductlist->product ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-1">
                                        <label class="small font-weight-bold text-dark">Issue Qty*</label>
                                        <input type="text" id="issueqty" name="issueqty" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="form-group mb-1">
                                        <label class="small font-weight-bold text-dark">Free Qty*</label>
                                        <input type="text" id="freeqty" name="freeqty" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="form-group mt-3">
                                        <button type="submit" id="submitBtn" class="btn btn-outline-primary btn-sm w-50 fa-pull-right" <?php if($addcheck==0){echo 'disabled';} ?>><i class="far fa-save"></i>&nbsp;Add</button>
                                    </div>
                                    <input type="hidden" name="recordOption" id="recordOption" value="1">
                                    <input type="hidden" name="recordID" id="recordID" value="">
                                </form>
                            </div>
                            <div class="col-9">
                                <table class="table table-bordered table-striped table-sm nowrap" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Issue Product</th>
                                            <th>Free Product</th>
                                            <th>Issue Qty</th>
                                            <th>Free Qty</th>
                                            <th class="text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if($result->num_rows > 0) {while ($row = $result-> fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo $row['idtbl_product_free'] ?></td>
                                            <td><?php echo $row['issueproduct'] ?></td>
                                            <td><?php echo $row['freeproduct'] ?></td>
                                            <td><?php echo $row['qtycount'] ?></td>
                                            <td><?php echo $row['freecount'] ?></td>
                                            <td class="text-right">
                                                <button class="btn btn-outline-primary btn-sm btnEdit <?php if($editcheck==0){echo 'd-none';} ?>" id="<?php echo $row['idtbl_product_free'] ?>"><i data-feather="edit-2"></i></button>
                                                <?php if($row['status']==1){ ?>
                                                <a href="process/statusproductfree.php?record=<?php echo $row['idtbl_product_free'] ?>&type=2" onclick="return confirm('Are you sure you want to deactive this?');" target="_self" class="btn btn-outline-success btn-sm <?php if($statuscheck==0){echo 'd-none';} ?>"><i data-feather="check"></i></a>
                                                <?php }else{ ?>
                                                <a href="process/statusproductfree.php?record=<?php echo $row['idtbl_product_free'] ?>&type=1" onclick="return confirm('Are you sure you want to active this?');" target="_self" class="btn btn-outline-warning btn-sm <?php if($statuscheck==0){echo 'd-none';} ?>"><i data-feather="x-square"></i></a>
                                                <?php } ?>
                                                <a href="process/statusproductfree.php?record=<?php echo $row['idtbl_product_free'] ?>&type=3" onclick="return confirm('Are you sure you want to remove this?');" target="_self" class="btn btn-outline-danger btn-sm <?php if($deletecheck==0){echo 'd-none';} ?>"><i data-feather="trash-2"></i></a>
                                            </td>
                                        </tr>
                                        <?php }} ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php include "include/footerbar.php"; ?>
    </div>
</div>
<?php include "include/footerscripts.php"; ?>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
        $('#dataTable tbody').on('click', '.btnEdit', function() {
            var r = confirm("Are you sure, You want to Edit this ? ");
            if (r == true) {
                var id = $(this).attr('id');
                $.ajax({
                    type: "POST",
                    data: {
                        recordID: id
                    },
                    url: 'getprocess/getproductfree.php',
                    success: function(result) { //alert(result);
                        var obj = JSON.parse(result);
                        $('#recordID').val(obj.id);
                        $('#issueqty').val(obj.qtycount);                       
                        $('#freeqty').val(obj.freecount);                       
                        $('#issueproduct').val(obj.issueproductid);                       
                        $('#freeproduct').val(obj.freeproductid);                       

                        $('#recordOption').val('2');
                        $('#submitBtn').html('<i class="far fa-save"></i>&nbsp;Update');
                    }
                });
            }
        });
    });

</script>
<?php include "include/footer.php"; ?>
