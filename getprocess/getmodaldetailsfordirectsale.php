<?php 
require_once('../connection/db.php');

$recordID = $_POST['recordID'];

$sql = "SELECT * FROM `tbl_product` WHERE `idtbl_product` = '$recordID'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$product = $row['product_name'];
$saleprice = $row['saleprice'];
;

?>

<div class="form-row mb-1">
    <form id = "cartform">

        <div class="col">
            <label class="small font-weight-bold text-dark"><?php echo $product ?></label>

            <div class="row">
                <div class="col-md-6">
                    <label class="small font-weight-bold text-dark">Sale price*</label>
                    <input type="text" value = "<?php echo $saleprice ?>" class="form-control form-control-sm" id="price" name="price" readonly>
                </div>
                <div class="col-md-6">
                    <label class="small font-weight-bold text-dark">Enter quantity*</label>
                    <input type="text" value = "0" class="form-control form-control-sm" id="quantity" name="quantity" required>
                </div>

            </div>
            <br>
            <label id = "totaltext" class="small font-weight-bold text-red">Rs.0.00</label>

            <div class="form-group mt-2">
                <button type="button" id="submitbtn" class="btn btn-outline-primary btn-sm px-4 fa-pull-right"><i class="far fa-save"></i>&nbsp;Submit</button>
                <input type="submit" class="d-none" id="hiddensubmitbtn" value="">
            </div>

        </div>
        <input type="hidden" id = "hiddentotal">
        <input type="hidden" value = "<?php echo $recordID ?>" id = "hiddenid">
        <input type="hidden" value = "<?php echo $product ?>" id = "hiddenproductname">
    </form>

</div>

<script>
    $('#quantity').keyup(function(){
        var qty = $(this).val();
        var price = $('#price').val()

        console.log(qty)
        var tot = price * qty;

        $('#hiddentotal').val(tot)
        $('#totaltext').html("Rs."+tot+".00");
    })

    $("#submitbtn").click(function() {
            if (!$("#cartform")[0].checkValidity()) {
                $("#hiddensubmitbtn").click();
            } else {   
                var unitprice = $('#price').val();
                var qty = parseFloat($('#quantity').val());
                var showtotal = parseFloat($('#hiddentotal').val());
                var productID = parseFloat($('#hiddenid').val());
                var productname = parseFloat($('#hiddenproductname').val());


                $('#carttable > tbody:last').append('<tr class="pointer"><td>' + productname + '</td><td class="d-none">' + productID + '</td><td class="text-center">' + qty + '</td><td class="text-center">' + unitprice + '</td><td class="text-right total">' + showtotal + '</td><td class="d-none">' + showtotal + '</td></tr>');


                var sum = 0;
                $(".total").each(function(){
                    sum += parseFloat($(this).text());
                });
                
                // var showsum = addCommas(parseFloat(sum).toFixed(2));

                $('#labeltotal').html('Rs. '+sum);
                $('#hiddenfulltotal').val(sum);
                // $('#modalshopcloseview').modal('close');

            }
        }); 
</script>