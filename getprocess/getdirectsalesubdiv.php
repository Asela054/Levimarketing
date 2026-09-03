<?php 
require_once('../connection/db.php');

$subcatid=$_POST['subcatid'];
$groupcatid=$_POST['groupcatid'];
$maincatid=$_POST['maincatid'];

$sqlproduct="SELECT * from `tbl_product` WHERE `tbl_product_category_idtbl_product_category` = '$maincatid' AND  `tbl_group_category_idtbl_group_category` = '$groupcatid' AND  `tbl_sub_product_category_idtbl_sub_product_category` = '$subcatid' AND `status` IN (1,2)";
$resultproduct =$conn-> query($sqlproduct);

?>
<table class="table table-striped table-bordered table-sm">
    <thead>
        <tr>
            <th>Product name</th>
            <th>Code</th>
            <th>Unit price</th>
            <th>Sale price</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        while($rowproduct = $resultproduct-> fetch_assoc()){ ?>
        <tr>
            <td><?php echo $rowproduct['product_name'] ?></td>
            <td><?php echo $rowproduct['product_code'] ?></td>
            <td><?php echo $rowproduct['unitprice'] ?></td>
            <td><?php echo $rowproduct['saleprice'] ?></td>
            <td>
                <button name="<?php echo $rowproduct['idtbl_product'] ?>" class="btn btn-outline-secondary cartBtn"><i
                        class="fa fa-shopping-cart"></i></button>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<!-- Modal Details View -->
<div class="modal fade" id="modalshopcloseview" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="staticlabel">Add to cart</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div id="dealercloseviewinfo"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(".cartBtn").click(function () {
        var id = $(this).attr('name');
        console.log(id)
        $.ajax({
            method: "post",
            data: {
                recordID: id
            },
            url: "getprocess/getmodaldetailsfordirectsale.php",
            success: function (result) {
                // alert(result)
                $('#dealercloseviewinfo').html(result);
                $('#modalshopcloseview').modal('show');
            }
        })
    })
</script>