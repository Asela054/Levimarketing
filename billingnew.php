<?php 
include "include/header.php"; 

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
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <h1 class="page-header-title">
                                    <div class="page-header-icon"><i class="fas fa-file-alt"></i></div>
                                    <span>&nbsp; Billing New </span>
                                </h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid mt-2 p-0 p-2">
                <div class="card">
                    <div class="card-body p-0 p-2">
                        <div class="row">
                            <div class="col-12 pt-3">
                                <h6 class="title-style small"><span>Vehicle Loading Information</span></h6>
                            </div>
                            <div class="col-2">
                                <label class="small font-weight-bold text-dark">Date*</label>
                                <input type="date" class="form-control form-control-sm" name="loadingdate" id="loadingdate">
                            </div>
                            <div class="col-3">
                                <label class="small font-weight-bold text-dark">Vehicle Load</label>
                                <select class="form-control form-control-sm" name="vehicleload" id="vehicleload" disabled required>
                                    <option value="">Select</option>
                                </select>
                            </div>
                            <div class="col-1">
                                <label class="small font-weight-bold text-dark">Ava Qty</label>
                                <input type="text" class="form-control form-control-sm" name="avaqty" id="avaqty" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 pt-3">
                                <h6 class="title-style small"><span>Billing Information</span></h6>
                                <div id="billingsection" class="d-none">
                                    <div class="row">
                                        <div class="col-3">
                                            <form id="createform">
                                                <div class="form-group mb-1">
                                                    <label class="small font-weight-bold text-dark">Product</label>
                                                    <select class="form-control form-control-sm" name="productselect" id="productselect" required>
                                                            <option value="">Select</option>
                                                    </select>
                                                    <input type="hidden" name="recorddate" id="recorddate" value="">
                                                    <input type="hidden" name="recordlorry" id="recordlorry" value="">
                                                    <input type="hidden" name="recorddriver" id="recorddriver" value="">
                                                </div>
                                                <div class="form-group mb-1">
                                                    <label class="small font-weight-bold text-dark">Sale Price</label>
                                                    <input type="text" name="saleprice" class="form-control form-control-sm" id="saleprice">
                                                </div>
                                                <div class="form-group mb-1">
                                                    <label class="small font-weight-bold text-dark">Customer</label>
                                                    <select class="form-control form-control-sm" style="width: 100%;" name="customer[]" id="customer" multiple required>
                                                        <option value="">Select</option>
                                                    </select>
                                                </div>
                                                <div class="form-group mt-2 text-right">
                                                    <button type="button" name="BtnAdd" id="BtnAdd" class="btn btn-primary btn-sm px-4"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                                </div>
                                                <button type="reset" name="hiddenreset" id="hiddenreset" class="d-none"></button>
                                                <input type="hidden" name="productname" id="productname">
                                                <input type="hidden" name="productcode" id="productcode">
                                                <input type="hidden" name="productid" id="productid">
                                                <input type="hidden" name="unitprice" id="unitprice">
                                                <input type="hidden" name="loadqty" id="loadqty">
                                                <input type="hidden" id="saletype" name="saletype">
                                            </form>
                                        </div>
                                        <div class="col-9">
                                            <div class="table scrollbar" id="style-2">
                                                <table class="table table-bordered table-striped table-sm small nowrap" id="tblinvoice">
                                                    <thead>
                                                        <th>Customer Name</th>
                                                        <th>Product Name</th>
                                                        <th class="text-center">Qty</th>
                                                        <th class="text-right">Sale Price</th>
                                                        <th class="text-right">Total</th>
                                                        <th class="text-right">Discount</th>
                                                        <th class="text-right">Net Total Amount</th>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 text-right">
                                                    <h6 class="" id="labeldistotal"></h6>
                                                    <h3 class="" id="labelnettotal"></h3>
                                                    <h5 class="" id="htmlbillamount"></h5>
                                                </div>
                                            </div>
                                            <hr>
                                            <button type="button" name="Btnsubmit" id="Btnsubmit" class="btn btn-primary btn-sm fa-pull-right px-4"><i class="far fa-save"></i>&nbsp;Create Invoices</button>
                                            <input type="hidden" id="hiddenfulltotal" name="hiddenfulltotal">
                                            <input type="hidden" id="hiddenfulldistotal"  name="hiddenfulldistotal">
                                            <input type="hidden" id="hiddenfullnettotal" name="hiddenfullnettotal">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php include "include/footerbar.php"; ?>
    </div>
</div>
<div class="modal fade bd-example-modal-sm" id="saletypemodel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <button type="button" id="retailbutton"
                    class="btn btn-primary btn-lg btn-block fa-pull-center"><i
                        class="fas fa-cash-register fa-2x"></i>&nbsp; RETAIL SALE</button><br><br>
                <button type="button" id="wholesalebutton"
                    class="btn btn-danger btn-lg btn-block  fa-pull-center"><i
                        class="fas fa-cash-register fa-2x"></i>&nbsp; WHOLE SALE</button>
            </div>
        </div>
    </div>
</div>
<?php include "include/footerscripts.php"; ?>
<script>
$(document).ready(function() {
    $('#loadingdate').change(function(){
        $('#vehicleload').prop('disabled', false);
        var searchdate=$('#loadingdate').val();
        $.ajax({
            type: "POST",
            data: {
                searchdate: searchdate
            },
            url: 'getprocess/getvehicleloadlistaccodate.php',
            success: function(result) {
                var objfirst = JSON.parse(result);
                var html = '';
                html += '<option value="">Select</option>';
                $.each(objfirst, function (i, item) {
                    //alert(objfirst[i].id);
                    html += '<option value="' + objfirst[i].id + '">';
                    html += 'VL0'+objfirst[i].id+' ('+objfirst[i].vehicleno+')';
                    html += '</option>';
                });

                $('#vehicleload').empty().append(html);
            }
        });
    });

    // new bill function
    $('#vehicleload').change(function(){
        var id = $(this).val();
        $.ajax({
            type: "POST",
            data: {
                recordID: id
            },
            url: 'getprocess/getvehicleloaddetails.php',
            success: function(result) {
                // console.log(result);
                var obj = JSON.parse(result);
                $('#recorddate').val(obj.loaddate);
                $('#recordlorry').val(obj.lorry);
                $('#recorddriver').val(obj.driver);

                var objfirst = obj.productlist;
                var html = '';
                html += '<option value="">Select</option>';
                $.each(objfirst, function (i, item) {
                    //alert(objfirst[i].id);
                    html += '<option value="' + objfirst[i].idtbl_product + '">';
                    html += objfirst[i].product_name;
                    html += '</option>';
                });

                $('#productselect').empty().append(html);
                // $('#productlist').html(result);
                $('#saletypemodel').modal('show');
                $("#billingsection").removeClass('d-none');
            }
        });
    });

    // select multiple customers
    $("#customer").select2({
        //dropdownParent: $('#createRequest'),
        // placeholder: 'Select Customer',
        ajax: {
            url: 'getprocess/getcustomeraccoloading.php',
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    searchTerm: params.term, // search term
                    searchLoad: $('#vehicleload').val(),
                    searchDate: $('#loadingdate').val()
                };
            },
            processResults: function(response) {
                return {
                    results: response
                };
            },
            cache: true
        }
    });

    // get product details according to product select
    $(document).on("change", "#productselect", function() {
        var productid = $("#productselect").val();
        var typesale = $("#saletype").val();
        var loadID = $('#vehicleload').val();
        $.ajax({
            type: "POST",
            data: {
                recordID: productid,
                saletypes: typesale,
                loadID: loadID
            },
            url: 'getprocess/getproductlistdetails.php',
            success: function(result) {
                // console.log(result);
                
                var obj = JSON.parse(result);
                $('#productid').val(obj.id);
                $('#saleprice').val(obj.saleprice);
                $('#productname').val(obj.product);
                $('#productcode').val(obj.procode);
                $('#unitprice').val(obj.unitprice);
                $('#loadqty').val(obj.availableqty); // Set available quantity
                $('#avaqty').val(obj.availableqty);

                if(obj.availableqty==0){$('#BtnAdd').prop('disabled', true);}
                else{$('#BtnAdd').prop('disabled', false)}
            }
        });
    });

    // set sale type 
    $(document).on("click", "#retailbutton", function() {
        var saletype = 1;
        $('#saletype').val(saletype);
        $('#saletypemodel').modal('hide');
    });

    $(document).on("click", "#wholesalebutton", function() {
        var saletype = 2;
        $('#saletype').val(saletype);
        $('#saletypemodel').modal('hide');
    });

    // Function to check availability using AJAX
    // function checkAvailability(productID, enterqty, callback) {
    //     var date = $('#recorddate').val();
    //     var driver = $('#recorddriver').val();
    //     var lorry = $('#recordlorry').val();

    //     $.ajax({
    //         type: "POST",
    //         data: {
    //             save: '1',
    //             product: productID,
    //             qty: enterqty,
    //             driver: driver,
    //             lorry: lorry,
    //             recordDate: date
    //         },
    //         url: "getprocess/getbillproductqtycheck.php",
    //         success: function(rest) {
    //             var obj = JSON.parse(rest);
    //             $('#loadqty').val(obj.inserted_qty); // Update the available quantity
    //             callback(obj.checkqty);
    //         }
    //     });
    // }

    // Add button click event
    $(document).on("click", "#BtnAdd", function() {
        var productID = $('#productselect').val();
        //console.log(productID);
        var product = $('#productname').val();
        var productcode = $('#productcode').val();
        var sale = parseFloat($('#saleprice').val());
        var unitprice = parseFloat($('#unitprice').val());
        var qty = parseFloat($('#avaqty').val());
        var discountpercentage = parseFloat($('#discountpercentage').val());

        // Check availability and proceed if valid
        // checkAvailability(productID, qty, function(isAvailable) {
        if(qty>0){
            var customqty = parseFloat('0');
            var total = sale * customqty;
            var totalWithDis = total - (total * discountpercentage / 100);
            var distotal = totalWithDis - total;


            // Append row to table for each selected customer
            var customers = [];
            $('#customer option:selected').each(function() {
                var customerID = $(this).val();
                var customerName = $(this).text();
                customers.push({
                    id: customerID,
                    name: customerName
                });
            });

            customers.forEach(function(customer) {
                $('#tblinvoice tbody').append('<tr>' +
                    '<td class="customer-id">' + customer.name + '</td>' +
                    '<td class="product-id">' + product + '</td>' +
                    '<td><input type="number" class="qty form-control form-control-sm" value="' +
                    customqty + '"></td>' +
                    '<td><input type="number" class="saleprice form-control form-control-sm" value="' +
                    sale.toFixed(2) + '"></td>' +
                    '<td class="total text-right">' + total.toFixed(2) + '</td>' +
                    '<td><input type="number" class="discountpercentage form-control form-control-sm" value="' +
                    discountpercentage + '"></td>' +
                    '<td class="netamount text-right">' + totalWithDis.toFixed(2) + '</td>' +
                    '<td class="distotal d-none">' + distotal.toFixed(2) + '</td>' +
                    '<td class="unitprice d-none">' + unitprice.toFixed(2) +
                    '</td>' +
                    '<td class="productid d-none">' + productID + '</td>' +
                    '<td class="customerid d-none">' + customer.id + '</td>' +
                    '</tr>');
            });

            calculateTotals();
        }
        else{
            alert("Insufficient stock available.");
        }
        // });
    });
    
    $(document).on("input", ".qty, .discountpercentage, .saleprice", function() {
        var totalQty = 0;
        $('#tblinvoice tbody tr').each(function() {
            var rowQty = parseFloat($(this).find('.qty').val()) || 0;
            totalQty += rowQty;
        });

        var availableQty = parseFloat($('#loadqty').val());
        if (isNaN(availableQty)) {
            console.error("Available quantity is not a valid number.");
            return;
        }
        else if (totalQty > availableQty) {
            alert("Total quantity exceeds available stock.");
            $('#Btnsubmit').prop('disabled', true);
        }
        else{
            calculateTotals();
            $('#Btnsubmit').prop('disabled', false);
        }
    });

    function calculateTotals() {
        var sum = 0;
        var totalDiscount = 0;

        $('#tblinvoice tbody tr').each(function() {
            var row = $(this);
            var rowQty = parseFloat(row.find('.qty').val()) || 0;
            var rowDiscountPercentage = parseFloat(row.find('.discountpercentage').val()) || 0;
            var rowSalePrice = parseFloat(row.find('.saleprice').val()) || 0;

            if (isNaN(rowSalePrice)) {
                console.error("Sale price is not a valid number.");
                return;
            }

            var rowTotal = rowQty * rowSalePrice;
            var rowTotalWithDis = rowTotal - (rowTotal * rowDiscountPercentage / 100);
            var distotal = rowTotal - rowTotalWithDis;


            row.find('.netamount').text(rowTotalWithDis.toFixed(2));
            row.find('.total').text(rowTotal.toFixed(2));
            row.find('.distotal').text(distotal.toFixed(2));


            sum += rowTotalWithDis;
            totalDiscount += rowTotal * (rowDiscountPercentage / 100);
        });

        var netTotal = sum - totalDiscount;
        //var total = rowTotal;

        $('#labeltotal').text('Gross Amount: ' + sum.toFixed(2));
        $('#labeldistotal').text('Discount: ' + totalDiscount.toFixed(2));
        $('#labelnettotal').text('Net Total: ' + netTotal.toFixed(2));
        $('#htmlbillamount').text('Rs. ' + netTotal.toFixed(2));

        $('#hiddenfulltotal').val(sum.toFixed(2));
        $('#hiddenfulldistotal').val(totalDiscount.toFixed(2));
        $('#hiddenfullnettotal').val(netTotal.toFixed(2));
    }

    // Submit button click event
    $(document).on("click", "#Btnsubmit", function() {
        var tableData = [];
        var netqty = parseFloat('0');
        $("#tblinvoice tbody tr").each(function() {
            var row = {
                customerID: $(this).find('.customerid').text().trim(),
                productID: $(this).find('.productid').text().trim(),
                qty: $(this).find('.qty').val().trim(),
                discountpercentage: $(this).find('.discountpercentage').val().trim(),
                saleprice: $(this).find('.saleprice').val().trim(),
                total: $(this).find('.total').text().trim(),
                distotal: $(this).find('.distotal').text().trim(),
                netamount: $(this).find('.netamount').text().trim(),
                unitprice: $(this).find('.unitprice').text().trim()
            };
            tableData.push(row);

            netqty = netqty+parseFloat($(this).find('.qty').val().trim());
        });

        var fulltotal = $('#hiddenfulltotal').val();
        var distotal = $('#hiddenfulldistotal').val();
        var nettotal = $('#hiddenfullnettotal').val();
        var salestype = $("#saletype").val();
        var recordDate = $('#recorddate').val();
        var RecordDriver = $('#recorddriver').val();
        var Recordlorry = $('#recordlorry').val();
        var vehicleload = $('#vehicleload').val();
        var avaqty = $('#avaqty').val();

        if(netqty<=avaqty){
            $.ajax({
                type: "POST",
                data: {
                    tableData: JSON.stringify(tableData),
                    fulltotal: fulltotal,
                    distotal: distotal,
                    nettotal: nettotal,
                    saletype: salestype,
                    recordDate: recordDate,
                    RecordDriver: RecordDriver,
                    Recordlorry: Recordlorry,
                    vehicleload: vehicleload
                },
                url: 'process/billingprocess.php',
                success: function(result) {
                    try {
                        var objfirst = JSON.parse(result);
                        if (objfirst.actiontype == 1) {
                            action(objfirst.action);
                            setTimeout(function() {
                                location.reload();
                            }, 3000);
                        } else {
                            action(objfirst.action);
                        }
                    } catch (e) {
                        console.error("Error parsing JSON response:", e);
                        console.error("Response:", result);
                        alert("An error occurred. Please check the console for details.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error:", error);
                    console.error("Response:", xhr.responseText);
                    alert("An error occurred. Please check the console for details.");
                }
            });
        }
        else{
            alert("Insufficient stock available.");
        }
    });

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
});
</script>
<?php include "include/footer.php"; ?>