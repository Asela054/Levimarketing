<?php
require_once('../connection/db.php');

$categoryID=$_POST['categoryID'];

$sql="SELECT `idtbl_sub_product_category`, `category` FROM `tbl_sub_product_category` WHERE `tbl_product_category_idtbl_product_category`='$categoryID' AND `status`=1";
$result=$conn->query($sql);
?>
<div class="row row-cols-1 row-cols-md-4">
    <?php if($result->num_rows > 0) {while ($row = $result-> fetch_assoc()) { ?>
    <div class="col mb-4 subcategorydiv" id="<?php echo $row['idtbl_sub_product_category'] ?>">
        <div class="card h-100 shadow-none bg-primary border-primary">
            <div class="card-body p-2 text-center pointer">
                <h4 class="text-light font-weight-light">
                    <?php echo $row['category'] ?></h4>
                <hr class="border-light my-1">
            </div>
        </div>
    </div>
    <?php }} ?>
</div>