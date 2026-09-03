<?php
require_once('../connection/db.php');

if(!empty($_POST['recordID'])){$recordID=$_POST['recordID'];}

$type=$_POST['type'];

$sqlmaincat = "SELECT `idtbl_product_category`, `category` FROM `tbl_product_category` WHERE `status`= '1'";
$resultmaincat = $conn->query($sqlmaincat);

if($type == 0){
?>

<h6 class="title-style mb-3"><span>Choose Main Category</span></h6>
<div id="categoryinfo">
    <div class="row row-cols-1 row-cols-md-4">
        <?php if($resultmaincat->num_rows > 0) {while ($rowmaincat = $resultmaincat-> fetch_assoc()) { ?>

        <div class="col mb-4 categorydiv" name="<?php echo $rowmaincat['idtbl_product_category'] ?>">
            <div class="card h-100 shadow-none bg-primary border-primary">
                <div class="card-body p-2 text-center pointer">
                    <h4 class="text-light font-weight-light">
                        <?php echo $rowmaincat['category'] ?></h4>
                    <hr class="border-light my-1">
                </div>
            </div>
        </div>
        <?php }} ?>
    </div>

</div>
<?php }else if($type == 1){ 
    
    $sqlsubcat = "SELECT `idtbl_sub_product_category`, `category` FROM `tbl_sub_product_category` WHERE `status`= '1' AND `tbl_product_category_idtbl_product_category` = '$recordID'";
    $resultsubcat = $conn->query($sqlsubcat);  
?>

<h6 class="title-style mb-3"><span>Choose Sub Category</span></h6>
<button id="backOne" class="btn btn-primary btn-md float-right"><i class="fa fa-eye mr-1"></i>Back</button>
<div id="categoryinfo">
    <div class="row row-cols-1 row-cols-md-4">
        <?php if($resultsubcat->num_rows > 0) {while ($rowsubcat = $resultsubcat-> fetch_assoc()) { ?>

        <div class="col mb-4 subcategorydiv" name="<?php echo $rowsubcat['idtbl_sub_product_category'] ?>">
            <div class="card h-100 shadow-none bg-primary border-primary">
                <div class="card-body p-2 text-center pointer">
                    <h4 class="text-light font-weight-light">
                        <?php echo $rowsubcat['category'] ?></h4>
                    <hr class="border-light my-1">
                </div>
            </div>
        </div>
        <?php }}else{ ?>
        <h4 class="text-danger font-weight-light">No results found</h4>
        <?php } ?>
    </div>
</div>
<?php }else{ 
       $sqlsubcat = "SELECT `idtbl_group_category`, `category` FROM `tbl_group_category` WHERE `status`= '1' AND `tbl_product_category_idtbl_product_category` = '$recordID'";
       $resultsubcat = $conn->query($sqlsubcat);?>
<h6 class="title-style mb-3"><span>Choose Group Category</span></h6>
<button id="backTwo" class="btn btn-primary btn-md float-right"><i class="fa fa-eye mr-1"></i>Back</button>
<div id="categoryinfo">
    <div class="row row-cols-1 row-cols-md-4">
        <?php if($resultsubcat->num_rows > 0) {while ($rowsubcat = $resultsubcat-> fetch_assoc()) { ?>

        <div class="col mb-4 groupcategorydiv" name="<?php echo $rowsubcat['idtbl_group_category'] ?>">
            <div class="card h-100 shadow-none bg-primary border-primary">
                <div class="card-body p-2 text-center pointer">
                    <h4 class="text-light font-weight-light">
                        <?php echo $rowsubcat['category'] ?></h4>
                    <hr class="border-light my-1">
                </div>
            </div>
        </div>
        <?php }}else{ ?>
        <h4 class="text-danger font-weight-light">No results found</h4>
        <?php } ?>
    </div>
</div>
<?php } ?>

<script>
    $('.categorydiv').click(function () {
        var catid = $(this).attr("name")
        let type = 1;
        $('#hiddencategoryID').val(catid);

        $.ajax({
            method: "POST",
            data: {
                recordID: catid,
                type: type
            },
            url: "getprocess/getdirectsalediv.php",
            success: function (result) { //alert(result)
                $('#maindiv').empty();
                $('#maindiv').html(result);
            }
        })
    })
    $('.subcategorydiv').click(function () {
        var subcatid = $(this).attr('name');

        var catid = $('#hiddencategoryID').val();
        let type = 2;
        $('#hiddensubID').val(subcatid);
        $.ajax({
            method: "POST",
            data: {
                recordID: catid,
                type: type
            },
            url: "getprocess/getdirectsalediv.php",
            success: function (result) { //alert(result)
                $('#maindiv').empty();
                $('#maindiv').html(result);
            }
        })
    })
    $('.groupcategorydiv').click(function () {
        var groupcatid = $(this).attr("name")
        var maincatid = $('#hiddencategoryID').val();
        var subcatid = $('#hiddensubID').val();

        $('#hiddengroupID').val(groupcatid);

        $.ajax({
            method: "POST",
            data: {
                recordID: groupcatid,
                groupcatid: groupcatid,
                maincatid: maincatid,
                subcatid: subcatid,
            },
            url: "getprocess/getdirectsalesubdiv.php",
            success: function (result) { //alert(result)
                $('#subdiv').empty();
                $('#subdiv').html(result);
            }
        })
    })

    function loadSubCat() {
        var catid = $('#hiddencategoryID').val();
        let type = 1;
        $.ajax({
            method: "POST",
            data: {
                recordID: catid,
                type: type
            },
            url: "getprocess/getdirectsalediv.php",
            success: function (result) { //alert(result)
                $('#maindiv').empty();
                $('#maindiv').html(result);
            }
        })
    }

    $('#backOne').click(function () {
        onloads()

    })
    $('#backTwo').click(function () {
        loadSubCat()
        $('#subdiv').empty();

    })
</script>



