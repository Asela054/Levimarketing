<?php 
include "include/header.php"; 

include "include/topnavbar.php"; 

$sqlmaincat = "SELECT `idtbl_product_category`, `category` FROM `tbl_product_category` WHERE `status`= '1'";
$resultmaincat = $conn->query($sqlmaincat);
?>
<style>
    .pointer {cursor: pointer;}
</style>
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <?php include "include/menubar.php"; ?>
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid p-0 p-2">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <input type="hidden" id="hiddencategoryID" name="hiddencategoryID">
                            <input type="hidden" id="hiddensubID" name="hiddensubID">
                            <input type="hidden" id="hiddengroupID" name="hiddengroupID">
                            <div id="maindiv" class="col-sm-12 col-md-12 col-lg-7 col-xl-7" style="border-right: 1px dotted #000;">
                                <div class="accordion" id="accordionExample">
                                    <div class="card shadow-none border-0">
                                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                                            <div class="card-body border rounded">
                                                <div class="row row-cols-1 row-cols-md-4">
                                                    <?php if($resultmaincat->num_rows > 0) {while ($rowmaincat = $resultmaincat-> fetch_assoc()) { ?>
                                                    <div class="col mb-4 categorydiv" id="<?php echo $rowmaincat['idtbl_product_category'] ?>">
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
                                        </div>
                                    </div>
                                    <div class="card shadow-none border-0">
                                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                                            data-parent="#accordionExample">
                                            <div class="card-body border rounded">
                                                <div class="row">
                                                    <div class="col-12 text-right pb-3">
                                                        <button class="btn btn-danger px-4" id="btnbackone"><i class="fa fa-arrow-left mr-1"></i>Back</button>
                                                    </div>
                                                    <div class="col-12" id="divsubcategory">
                                                        <div class="row">
                                                            <div class="col-12 text-center">
                                                                <img src="images/spinner.gif" class="img-fluid">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card shadow-none border-0">
                                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree"
                                            data-parent="#accordionExample">
                                            <div class="card-body border rounded">
                                                <div  class="row">
                                                    <div class="col-12 text-right pb-3">
                                                        <button class="btn btn-danger px-4" id="btnbacktwo"><i class="fa fa-arrow-left mr-1"></i>Back</button>
                                                    </div>
                                                    <div class="col-12" id="divgroupcategory">
                                                        <div class="row">
                                                            <div class="col-12 text-center">
                                                                <img src="images/spinner.gif" class="img-fluid">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card shadow-none border-0">
                                        <div id="collapseFour" class="collapse" aria-labelledby="headingFour"
                                            data-parent="#accordionExample">
                                            <div class="card-body border rounded">
                                                <div  class="row">
                                                    <div class="col-12 text-right pb-3">
                                                        <button class="btn btn-danger px-4" id="btnbackthree"><i class="fa fa-arrow-left mr-1"></i>Back</button>
                                                    </div>
                                                    <div class="col-12" id="divproductlist">
                                                        <div class="row">
                                                            <div class="col-12 text-center">
                                                                <img src="images/spinner.gif" class="img-fluid">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-5 col-xl-5">
                                <h6 class="title-style mb-3"><span>Cart Information</span></h6>
                                <table class="table table-striped table-sm" id = "carttable">
                                    <thead>
                                        <tr>
                                            <th class="d-none">ProductID</th>
                                            <th class="d-none">ProductCode</th>
                                            <th>PRODUCT</th>
                                            <th class="text-center">QTY</th>
                                            <th class="text-right">SALE</th>
                                            <th class="d-none">sale</th>
                                            <th class="d-none">unit</th>
                                            <th class="d-none">total</th>
                                            <th class="text-right">TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                     
                                    </tbody>
                                </table>
                                <div class="row">
                                    <div class="col-12 text-right">
                                        <div id = "labeltotal" class="display-4">0.00</div>
                                        <input type="hidden" id = "hiddenfulltotal">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 text-right">
                                <button id = "paymentbtn" class="btn btn-danger btn-sm"><i class="fas fa-cash-register fa-3x mr-2"></i>
                                    <h1 class="font-weight-normal mt-2 text-light">PAYMENT</h1>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php include "include/footerbar.php"; ?>
    </div>
</div>
<!-- Modal Qty -->
<div class="modal fade" id="modalqty" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">ADD TO LIST</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <div class="card shadow-none bg-primary border-primary">
                            <div class="card-body p-2 text-center pointer">
                                <h4 class="text-light font-weight-light" id="selectproduct">Test</h4>
                                <hr class="border-light my-1">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <div class="row">
                            <div class="col-12">
                                <hr>
                                <form id="formqtyadd">
                                    <div class="form-group mb-1">
                                        <label class="small font-weight-bold text-dark">Qty</label>
                                        <input type="number" name="qtycount" id="qtycount" class="form-control" required>
                                    </div>
                                    <div class="form-group mt-3 text-right">
                                        <button type='button' class="btn btn-danger" id="btnaddtolist">ADD TO LIST</button>
                                    </div>
                                    <input type="submit" class="d-none" id="btnhideqtysubmit">
                                    <input type="reset" class="d-none" id="btnhideqtyreset">
                                    <input type="hidden" name="hideproductid" id="hideproductid">
                                    <input type="hidden" name="hideproduct" id="hideproduct">
                                    <input type="hidden" name="hideproductcode" id="hideproductcode">
                                    <input type="hidden" name="hideproductunit" id="hideproductunit">
                                    <input type="hidden" name="hideproductsale" id="hideproductsale">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Payment -->
<div class="modal fade" id="modalpayment" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">PAYMENT</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                        <form id="paymentform">
                            <div class="form-group mb-1">
                                <label class="small font-weight-bold text-dark">Bill Method</label>
                                <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                    <label class="btn btn-outline-dark active">
                                        <input type="radio" name="billtype" id="billtype1" value="1" checked> <i class="fas fa-money-bill mr-2"></i> Cash Payment
                                    </label>
                                    <label class="btn btn-outline-dark">
                                        <input type="radio" name="billtype" id="billtype2" value="2"> <i class="fas fa-file-alt mr-2"></i> Credit Payment
                                    </label>
                                </div>
                            </div>
                            <div class="form-group mb-1">
                                <div class="collapse" id="collapsecustomerinfo">
                                    <div class="card card-body shadow-none border-0 p-0">
                                        <div class="form-group mb-1">
                                            <label class="small font-weight-bold text-dark">Name</label>
                                            <input type="text" id="cusname" name="cusname" class="form-control">
                                        </div>
                                        <div class="form-group mb-1">
                                            <label class="small font-weight-bold text-dark">NIC</label>
                                            <input type="text" id="cusnic" name="cusnic" class="form-control">
                                        </div>
                                        <div class="form-group mb-1">
                                            <label class="small font-weight-bold text-dark">Mobile No</label>
                                            <input type="text" id="cusmobile" name="cusmobile" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-1">
                                <label class="small font-weight-bold text-dark">Payment Method</label>
                                <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                    <label class="btn btn-outline-dark">
                                        <input type="radio" name="paymentmethod" id="paymentmethod1" value="1"> Cash
                                    </label>
                                    <label class="btn btn-outline-dark">
                                        <input type="radio" name="paymentmethod" id="paymentmethod2" value="2" disabled> Credit Card
                                    </label>
                                    <label class="btn btn-outline-dark">
                                        <input type="radio" name="paymentmethod" id="paymentmethod3" value="3"> Cheque
                                    </label>
                                </div>
                            </div>
                            <div class="form-group mb-1">
                                <label class="small font-weight-bold text-dark">Cash / Cheque</label>
                                <input type="number" id="amount" name="amount" class="form-control" required>
                            </div>
                            <div class="form-group mb-1">
                                <label class="small font-weight-bold text-dark">Bank Name</label>
                                <input type="text" id="bank" name="bank" class="form-control" readonly>
                            </div>
                            <div class="form-group mb-1">
                                <label class="small font-weight-bold text-dark">Cheque No</label>
                                <input type="text" id="chequeno" name="chequeno" class="form-control" readonly>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Cheque Date</label>
                                    <input type="date" id="chequedate" name="chequedate" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group mt-3 text-right">
                                <button type="button" class="btn btn-danger" id="btnpayaddlist">ADD PAYMENT</button>
                                <input type="submit" id="btnhidepayaddlist" class="d-none">
                                <input type="reset" id="btnhidepayresetlist" class="d-none">
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                        <table class="table table-striped table-bordered" id="tablepayment">
                            <thead>
                                <tr>
                                    <th class="d-none">paymethod</th>
                                    <th>PAY METHOD</th>
                                    <th>BANK</th>
                                    <th>CHEQUE NO</th>
                                    <th>CHEQUE DATE</th>
                                    <th class="d-none">total</th>
                                    <th class="text-right">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <hr>
                        <h1 class="display-4 text-right" id="paynettotal"></h1>
                        <h3 class="font-weight-normal text-right text-danger" id="paybalance"></h3>
                        <input type="hidden" name="hidepaymenttotal" id="hidepaymenttotal" value="0">
                        <input type="hidden" name="hidecustomerID" id="hidecustomerID" value="1">
                        <hr>
                        <button type="button" class="btn btn-secondary fa-pull-right" id="paymentcomplete"><i class="fas fa-save mr-2"></i>DONE</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Already Cutomers Modal-->
<div class="modal fade" id="alreadyCustomerModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="oLevel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header p-0 p-2">
                <h5 class="modal-title" id="oLevelTitle">ALREADY CUSTOMERS</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <table id="alreadyCustomerTable" class="table table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>NAME</th>
                                    <th>NIC</th>
                                    <th>MOBILE</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12"><button class="btn btn-outline-danger btn-sm" id="btnAddToDB">ADD TO CUSTOMER LIST</button></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Invoice Receipt -->
<div class="modal fade" id="modalinvoicereceipt" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="viewreceiptprint"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger btn-sm fa-pull-right" id="btnreceiptprint"><i class="fas fa-print"></i>&nbsp;Print Receipt</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Invoice Pos Receipt -->
<div class="modal fade" id="modalinvoicereceiptpos" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="viewreceiptprintpos"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger btn-sm fa-pull-right" id="btnreceiptprintpos"><i class="fas fa-print"></i>&nbsp;Print Receipt</button>
            </div>
        </div>
    </div>
</div>
<?php include "include/footerscripts.php"; ?>

<script type="text/javascript">
    $(document).ready(function () {
        var dataTable = $('#alreadyCustomerTable').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            ajax: {
                url: "scripts/customerlist.php",
                type: "POST", // you can use GET
            },
            "order": [
                [0, "desc"]
            ],
            "columns": [{
                    "data": "idtbl_customer"
                },
                {
                    "data": "name"
                },
                {
                    "data": "nic"
                },
                {
                    "data": "phone"
                },
            ]
        });
        $('.categorydiv').click(function(){
            var categoryID=$(this).attr('id');
            $("#collapseTwo").collapse('show');
            $('#hiddencategoryID').val(categoryID);

            $.ajax({
                method: "POST",
                data: {
                    categoryID: categoryID
                },
                url: "getprocess/getsubcategoryaccocategory.php",
                success: function (result) { //alert(result)
                    $('#divsubcategory').html(result);
                    subcategoryoption();
                }
            });
        });
        $('#btnbackone').click(function(){
            $("#collapseOne").collapse('show');
        });        
        $('#btnbacktwo').click(function(){
            $("#collapseTwo").collapse('show');
        });        
        $('#btnbackthree').click(function(){
            $("#collapseThree").collapse('show');
        });  
        $('#qtycount').keypress(function (e) {
            var key = e.which;
            if(key == 13){
                $("#btnaddtolist").click();
                return false;  
            }
        });
        $("#btnaddtolist").click(function () {
            if (!$("#formqtyadd")[0].checkValidity()) {
                // If the form is invalid, submit it. The form won't actually submit;
                // this will just cause the browser to display the native HTML5 error messages.
                $("#btnhideqtysubmit").click();
            } else {
                var productID=$('#hideproductid').val();
                var product=$('#hideproduct').val();
                var productcode=$('#hideproductcode').val();
                var unit=parseFloat($('#hideproductunit').val());
                var sale=parseFloat($('#hideproductsale').val());
                var qty=parseFloat($('#qtycount').val());

                var total=sale*qty;
                var total = parseFloat(total);
                var showtotal = addCommas(parseFloat(total).toFixed(2));

                $('#carttable > tbody:last').append('<tr class="pointer"><td class="d-none">' + productID + '</td><td class="d-none">' + productcode + '</td><td>' + product + '</td><td class="text-center">' + qty + '</td><td class="text-right">' + addCommas(parseFloat(sale).toFixed(2)) + '</td><td class="d-none">' + sale + '</td><td class="d-none">' + unit + '</td><td class="d-none total">' + total + '</td><td class="text-right">' + showtotal + '</td></tr>');

                var sum = 0;
                $(".total").each(function () {
                    sum += parseFloat($(this).text());
                });

                var showsum = addCommas(parseFloat(sum).toFixed(2));

                $('#labeltotal').html('Rs. ' + showsum);
                $('#hiddenfulltotal').val(sum);
                $('#btnhideqtyreset').click();
                $('#modalqty').modal('hide');
                $("#collapseOne").collapse('show');
            }
        });
        $('#paymentbtn').click(function(){
            $('#modalpayment').modal('show');
        });
        $('input[type=radio][name=paymentmethod]').change(function() {
            if (this.value == '1') {
                $('#bank').prop('readonly', true).prop('required',false);
                $('#chequeno').prop('readonly', true).prop('required',false);
                $('#chequedate').prop('readonly', true).prop('required',false);
            }
            else if (this.value == '3') {
                $('#bank').prop('readonly', false).prop('required',true);
                $('#chequeno').prop('readonly', false).prop('required',true);
                $('#chequedate').prop('readonly', false).prop('required',true);
            }
        });
        $('#amount').keypress(function (e) {
            var key = e.which;
            if(key == 13){
                var paymentmethod = $("input[type=radio][name='paymentmethod']:checked").val();
                if(paymentmethod==1){
                    $("#btnpayaddlist").click();
                    return false;  
                }
            }
        });
        $('#btnpayaddlist').click(function(){
            if (!$("#paymentform")[0].checkValidity()) {
                // If the form is invalid, submit it. The form won't actually submit;
                // this will just cause the browser to display the native HTML5 error messages.
                $("#btnhidepayaddlist").click();
            } else {
                var amount=$('#amount').val();
                var bank=$('#bank').val();
                var chequeno=$('#chequeno').val();
                var chequedate=$('#chequedate').val();
                var paymentmethod = $("input[type=radio][name='paymentmethod']:checked").val();

                if(paymentmethod==1){var paymethod='Cash';}
                else if(paymentmethod==2){var paymethod='Credit Card';}
                else if(paymentmethod==3){var paymethod='Cheque';}


                $('#tablepayment > tbody:last').append('<tr class="pointer"><td class="d-none">' + paymentmethod + '</td><td>' + paymethod + '</td><td>' + bank + '</td><td>' + chequeno + '</td><td>' + chequedate + '</td><td class="d-none paytotal">' + amount + '</td><td class="text-right">' + addCommas(parseFloat(amount).toFixed(2)) + '</td></tr>');

                var sum = 0;
                $(".paytotal").each(function () {
                    sum += parseFloat($(this).text());
                });

                var netbilltotal=parseFloat($('#hiddenfulltotal').val());
                var showsum = addCommas(parseFloat(sum).toFixed(2));

                var baltotal = netbilltotal-sum;
                baltotal=addCommas(baltotal.toFixed(2));

                $('#paynettotal').html('Rs. ' + showsum);
                $('#paybalance').html('Rs. ' + baltotal);
                $('#hidepaymenttotal').val(sum);
                $('#btnhidepayresetlist').click();
                $("input[type=radio][name='paymentmethod']").prop('checked', false);
                $('#bank').prop('readonly', true).prop('required',false);
                $('#chequeno').prop('readonly', true).prop('required',false);
                $('#chequedate').prop('readonly', true).prop('required',false);
            }
        });
        $('#paymentcomplete').click(function(){
            var tbody = $('#carttable tbody');
            if (tbody.children().length > 0) {
                jsonObj = []
                $("#carttable tbody tr").each(function () {
                    item = {}
                    $(this).find('td').each(function (col_idx) {
                        item["col_" + (col_idx + 1)] = $(this).text();
                    });
                    jsonObj.push(item);
                });
            }        
            // console.log(jsonObj);

            var tbodysecond = $('#tablepayment tbody');
            jsonObjPay = []
            if (tbodysecond.children().length > 0) {
                $("#tablepayment tbody tr").each(function () {
                    item = {}
                    $(this).find('td').each(function (col_idx) {
                        item["col_" + (col_idx + 1)] = $(this).text();
                    });
                    jsonObjPay.push(item);
                });
            }        
            var total = $('#hiddenfulltotal').val();
            var paytotal = $('#hidepaymenttotal').val();
            var billtype = $("input[type=radio][name='billtype']:checked").val();
            var cusname = $('#cusname').val();
            var cusnic = $('#cusnic').val();
            var cusmobile = $('#cusmobile').val();
            var cusID = $('#hidecustomerID').val();
            // console.log(jsonObjPay);

            if(tbodysecond.children().length > 0 && billtype==1){var paystatus='1';}
            else if(tbodysecond.children().length == 0 && billtype==2){var paystatus='1';}
            else{var paystatus='0';}
            
            if(paystatus==1){
                $.ajax({
                    type: "POST",
                    data: {
                        tableData: jsonObj,
                        tableDataPay: jsonObjPay,
                        total: total,
                        paytotal: paytotal,
                        billtype: billtype,
                        cusname: cusname,
                        cusnic: cusnic,
                        cusmobile: cusmobile,
                        cusID: cusID
                    },
                    url: 'process/newdirectsaleprocess.php',
                    success: function (result) {
                        // console.log(result);
                        var objfirst = JSON.parse(result);
                        if(objfirst.actiontype==1){
                            $('#modalpayment').modal('hide');
                            action(objfirst.action);
                            if(objfirst.billtype==1){
                                posprintbill(objfirst.invoiceid);
                            }
                            else{
                                creditprintbill(objfirst.invoiceid);
                            }
                            // setTimeout(function() {   //calls click event after a certain time
                            //     location.reload();
                            // }, 2000);                            
                        }
                        else{
                            action(objfirst.action);
                        }
                    }
                });
            }
        });
        $('input[type=radio][name=billtype]').change(function() {
            if(this.value == '2'){
                $('#alreadyCustomerModal').modal('show');
                $('#alreadyCustomerModal').on('shown.bs.modal', function (e) {
                    var dataTable = $('#alreadyCustomerTable').DataTable();
                    $('div.dataTables_filter input', dataTable.table().container()).focus();
                });

                $('#collapsecustomerinfo').collapse('show');
            }
            else{
                $('#amount').prop('readonly', false);
                $('#cusname').val('');
                $('#cusnic').val('');
                $('#cusmobile').val('');
                $('#collapsecustomerinfo').collapse('hide');
            }
        });
        $('#alreadyCustomerTable').on('search.dt', function() {
            var value = $('.dataTables_filter input').val();
            $('#cusname').val(value);
        }); 
        $('#alreadyCustomerTable tbody').on('click', 'tr', function() { //alert('IN');
            if ($(this).hasClass('table-primary')) {
                $(this).removeClass('table-primary');
            } else {
                dataTable.$('tr.table-primary').removeClass('table-primary');
                $(this).addClass('table-primary');

                var data = $('#alreadyCustomerTable').DataTable().row('.table-primary').data();
                console.log(data);
                $('#hidecustomerID').val(data.idtbl_customer);
                $('#cusname').val(data.name);
                $('#cusnic').val(data.nic);
                $('#cusmobile').val(data.phone);
                $('#alreadyCustomerModal').modal('hide');
                $('#amount').prop('readonly', true);
            }
        });
        $('#btnAddToDB').click(function(){
            $('#alreadyCustomerModal').modal('hide');
            $('#amount').prop('readonly', true);
            $('#cusname').focus();
        });
        document.getElementById('btnreceiptprint').addEventListener ("click", print);
        document.getElementById('btnreceiptprintpos').addEventListener ("click", printpos);
        $('#modalinvoicereceiptpos').on('hidden.bs.modal', function (e) {
            location.reload();
        });
        $('#modalinvoicereceipt').on('hidden.bs.modal', function (e) {
            location.reload();
        });
    });

    function subcategoryoption(){
        $('.subcategorydiv').click(function(){
            var subcategoryID=$(this).attr('id');
            $('#hiddensubID').val(subcategoryID);
            var categoryID=$('#hiddencategoryID').val();
            $("#collapseThree").collapse('show');
            
            $.ajax({
                method: "POST",
                data: {
                    categoryID: categoryID
                },
                url: "getprocess/getgroupcategoryaccosubcategory.php",
                success: function (result) { //alert(result)
                    $('#divgroupcategory').html(result);
                    groupcategoryoption();
                }
            });
        });
    }

    function groupcategoryoption(){
        $('.groupcategorydiv').click(function(){
            var groupcategoryID=$(this).attr('id');
            $('#hiddengroupID').val(groupcategoryID);
            $("#collapseFour").collapse('show');

            var categoryID=$('#hiddencategoryID').val();
            var subcategoryID=$('#hiddensubID').val();
            
            $.ajax({
                method: "POST",
                data: {
                    categoryID: categoryID,
                    subcategoryID: subcategoryID,
                    groupcategoryID: groupcategoryID
                },
                url: "getprocess/getproductlistaccpallcategory.php",
                success: function (result) { //alert(result)
                    $('#divproductlist').html(result);
                    productlistoption();
                }
            });
        });
    }

    function productlistoption(){
        $("#tableproductpricelist").delegate("tr.pointer", "click", function(){
            var productID = $(this).children("td:eq(0)").text();
            var productcode = $(this).children("td:eq(1)").text();
            var product = $(this).children("td:eq(2)").text();
            var unit = $(this).children("td:eq(4)").text();
            var sale = $(this).children("td:eq(5)").text();

            var unit = unit.replace(",", ""); 
            var sale = sale.replace(",", ""); 

            $('#hideproductid').val(productID);
            $('#hideproduct').val(product);
            $('#hideproductcode').val(productcode);
            $('#hideproductunit').val(unit);
            $('#hideproductsale').val(sale);

            $('#selectproduct').html(product);

            $('#modalqty').modal('show');
            $('#modalqty').on('shown.bs.modal', function () {
                $('#qtycount').focus();
            })  
        });
    }

    function addCommas(nStr) {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }

    function action(data) { //alert(data);
        var obj = JSON.parse(data);
        $.notify({
            // options
            icon: obj.icon,
            title: obj.title,
            message: obj.message,
            url: obj.url,
            target: obj.target
        }, {
            // settings
            element: 'body',
            position: null,
            type: obj.type,
            allow_dismiss: true,
            newest_on_top: false,
            showProgressbar: false,
            placement: {
                from: "top",
                align: "center"
            },
            offset: 100,
            spacing: 10,
            z_index: 1031,
            delay: 5000,
            timer: 1000,
            url_target: '_blank',
            mouse_over: null,
            animate: {
                enter: 'animated fadeInDown',
                exit: 'animated fadeOutUp'
            },
            onShow: null,
            onShown: null,
            onClose: null,
            onClosed: null,
            icon_type: 'class',
            template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0}" role="alert">' +
                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                '<span data-notify="icon"></span> ' +
                '<span data-notify="title">{1}</span> ' +
                '<span data-notify="message">{2}</span>' +
                '<div class="progress" data-notify="progressbar">' +
                '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
                '</div>' +
                '<a href="{3}" target="{4}" data-notify="url"></a>' +
                '</div>'
        });
    }

    function posprintbill(invoiceid){
        $('#modalinvoicereceiptpos').modal('show');
        $('#viewreceiptprint').html('<div class="card border-0 shadow-none bg-transparent"><div class="card-body text-center"><img src="images/spinner.gif" alt="" srcset=""></div></div>');

        $.ajax({
            type: "POST",
            data: {
                recordID: invoiceid
            },
            url: 'getprocess/invoiceprintpos.php',
            success: function(result) { //alert(result);
                $('#viewreceiptprintpos').html(result);
            }
        });
    }
    function creditprintbill(invoiceid){
        $('#modalinvoicereceipt').modal('show');
        $('#viewreceiptprint').html('<div class="card border-0 shadow-none bg-transparent"><div class="card-body text-center"><img src="images/spinner.gif" alt="" srcset=""></div></div>');

        $.ajax({
            type: "POST",
            data: {
                recordID: invoiceid
            },
            url: 'getprocess/invoiceprintcredit.php',
            success: function(result) { //alert(result);
                $('#viewreceiptprint').html(result);
            }
        });
    }
    function print() {
        printJS({
            printable: 'viewreceiptprint',
            type: 'html',
            style: '@page { size: A5 portrait; margin:0.25cm; }',
            targetStyles: ['*']
        })
    }
    function printpos() {
        printJS({
            printable: 'viewreceiptprintpos',
            type: 'html',
            // style: '@page { size: A5 portrait; margin:0.25cm; }',
            targetStyles: ['*']
        })
    }
</script>

<?php include "include/footer.php"; ?>
