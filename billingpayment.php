<?php 
include "include/header.php"; 

// Customer list
$sqlcustomer="SELECT * FROM `tbl_customer` WHERE `status`=1";
$resultcustomer=$conn->query($sqlcustomer);

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
                                    <span>&nbsp; Billing Payment </span>
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
                            <div class="col-2">
                                <label class="small font-weight-bold text-dark">Search Type*</label>
                                <select class="form-control form-control-sm" name="search_type" id="search_type">
                                    <option value="0" selected>Select</option>
                                    <option value="1">Vehicle Load</option>
                                    <option value="2">Customer</option>
                                </select>
                            </div>
                            <div class="col-2">
                                <label class="small font-weight-bold text-dark">Date*</label>
                                <input type="date" name="searchdate" id="searchdate" class="form-control form-control-sm" readonly>
                            </div>
                            <div class="col-2">
                                <label class="small font-weight-bold text-dark">Vehicle Load</label>
                                <select class="form-control form-control-sm" name="vehicleload" id="vehicleload" disabled required>
                                    <option value="">Select</option>
                                </select>
                            </div>
                            <div class="col-2">
                                <label class="small font-weight-bold text-dark">Customer</label>
                                <select class="form-control form-control-sm" style="width: 100%;" name="customer" id="customer" disabled required>
                                    <option value="">Select</option>
                                </select>
                            </div>
                        </div>
                        <hr style="border: 1px solid;">
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div id="paymentsection">

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
<!-- payment model -->
<div class="modal fade" id="billpaymentmodel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Billing Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4">
                        <form method="post" id="editform" enctype="multipart/form-data">
                            <div class="form-group mb-1">
                                <label class="small font-weight-bold text-dark">Payment Type</label><br>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="paytype1" name="paytype" class="custom-control-input" value="1">
                                    <label class="custom-control-label" for="paytype1">Full Payment</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="paytype2" name="paytype" class="custom-control-input" value="2">
                                    <label class="custom-control-label" for="paytype2">Half Payment</label>
                                </div>
                            </div>
                            <div class="form-group mb-1">
                                <label class="small font-weight-bold text-dark">Total Pay Amount</label>
                                <input id="totalpayamount" name="totalpayamount" type="text" class="form-control form-control-sm" placeholder="Total Amount" readonly>
                            </div>
                            <div class="form-group mb-1">
                                <label class="small font-weight-bold text-dark">Payment Method</label><br>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="cashmethod" name="paymentMethod"
                                        class="custom-control-input" value="1" data-toggle="collapse"
                                        href="#collapseOne">
                                    <label class="custom-control-label" for="cashmethod">Cash</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="bankmethod" name="paymentMethod"
                                        class="custom-control-input" value="2" data-toggle="collapse"
                                        href="#collapseTwo">
                                    <label class="custom-control-label" for="bankmethod">Bank / Cheque</label>
                                </div>
                            </div>
                            <div class="accordion" id="accordionExample">
                                <div class="card shadow-none border-0">
                                    <div id="collapseOne" class="collapse" aria-labelledby="headingOne"
                                        data-parent="#accordionExample">
                                        <div class="card-body p-0">
                                            <div class="form-group mb-1">
                                                <label class="small font-weight-bold text-dark">Cash
                                                    Pay Amount</label>
                                                <input id="cashpayamount" name="cashpayamount" type="text"
                                                    class="form-control form-control-sm" placeholder=""
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card shadow-none border-0">
                                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                                        data-parent="#accordionExample">
                                        <div class="card-body p-0">
                                            <div class="form-group mb-1">
                                                <label class="small font-weight-bold text-dark">Cheque / Deposit
                                                    Amount</label>
                                                <input id="chequepayamount" name="chequepayamount" type="text"
                                                    class="form-control form-control-sm" placeholder=""
                                                    required>
                                            </div>
                                            <div class="form-group mb-1">
                                                <label class="small font-weight-bold text-dark">Cheque
                                                    Number</label>
                                                <input id="chequeno" name="chequeno" type="text"
                                                    class="form-control form-control-sm" placeholder="">
                                            </div>
                                            <div class="form-group mb-1">
                                                <label class="small font-weight-bold text-dark">Receipt
                                                    Number</label>
                                                <input id="reciptno" name="reciptno" type="text"
                                                    class="form-control form-control-sm" placeholder="">
                                                <!-- <small id="" class="form-text text-muted">Bank deposit receipt
                                                    number only</small> -->
                                            </div>
                                            <div class="form-group mb-1">
                                                <label class="small font-weight-bold text-dark">Cheque
                                                    Date</label>
                                                <input type="date" class="form-control form-control-sm"
                                                    name="chequedate" id="chequedate">
                                            </div>
                                            <div class="form-group mb-1">
                                                <label class="small font-weight-bold text-dark">Bank
                                                    Name</label>
                                                <input id="bank" name="bank" type="text"
                                                    class="form-control form-control-sm">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                <button name="submitBtnModal" type="button" id="submitBtnModal"
                                    class="btn btn-outline-primary btn-sm fa-pull-right"><i
                                        class="fas fa-file-invoice-dollar"></i>&nbsp;Add Payment</button>
                                <input type="submit" class="d-none" id="hideSubmitModal">
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-12 col-md-8 col-lg-8 col-xl-8">
                        <table class="table table-bordered table-sm table-striped" id="tblPaymentTypeModal">
                            <thead>
                                <th>Type</th>
                                <th>Cash</th>
                                <th class="text-right">Cheque / Deposit</th>
                                <th>Cheque No</th>
                                <th>Receipt</th>
                                <th>Cheque Date</th>
                                <th>Bank</th>
                                <!-- <th class="d-none">BankID</th> -->
                                <th class="d-none">paymethod</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <div class="row">
                            <div class="col-sm-12 col-md-9 col-lg-9 col-xl-9 text-right">Total Amount :</div>
                            <div class="col-sm-12 col-md-3 col-lg-3 col-xl-3 text-right">
                                <div id="totAmount"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-9 col-lg-9 col-xl-9 text-right">Pay Amount :</div>
                            <div class="col-sm-12 col-md-3 col-lg-3 col-xl-3 text-right">
                                <div id="payAmount"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-9 col-lg-9 col-xl-9 text-right">&nbsp;</div>
                            <div class="col-sm-12 col-md-3 col-lg-3 col-xl-3 text-right">
                                <hr class="border-dark">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-9 col-lg-9 col-xl-9 text-right">Balance :</div>
                            <div class="col-sm-12 col-md-3 col-lg-3 col-xl-3 text-right">
                                <div id="balanceAmount"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12" align="right">
                        <button class="btn btn-outline-primary" id="btnsubmitpayment" disabled><i class="fas fa-file-invoice-dollar"></i>&nbsp;Submit Payment</button>
                    </div>
                </div>
            </div>
            <input type="hidden" id="hidePayAmount" value="0">
            <input type="hidden" id="hideBalAmount" value="0">
            <input type="hidden" id="hideAllBalAmount" value="0">
            <input type="hidden" name="invoiceID" id="invoiceID" value="">
            </form>
        </div>
    </div>
</div>
<!-- billing view model -->
<div class="modal fade" id="billviewmodel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Billing View</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="billingview">

                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Payment Receipt -->
<div class="modal fade" id="modalpaymentreceipt" data-backdrop="static" data-keyboard="false" tabindex="-1"
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
<?php include "include/footerscripts.php"; ?>
<script>
$(document).ready(function() {
    $("#search_type").change(function() {
        var type=$(this).val();
        if(type==1){
            $('#searchdate').val('').prop('readonly', false);
        }
        else if(type==2){
            $('#vehicleload').val('').prop('disabled', true);
            $('#searchdate').val('').prop('readonly', true);
            $('#customer').val('').prop('disabled', false);
        }
        else{
            $('#vehicleload').val('').prop('disabled', true);
            $('#searchdate').val('').prop('readonly', true);
            $('#customer').val('').prop('disabled', true);
        }
    });

    $('#searchdate').change(function(){
        $('#vehicleload').prop('disabled', false);
        var searchdate=$('#searchdate').val();
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

    $('#vehicleload').change(function(){
        $("#customer").prop('disabled', false);
    });

    $("#customer").select2({
        //dropdownParent: $('#createRequest'),
        //placeholder: 'Select Customer',
        ajax: {
            url: 'getprocess/getcustomerselet2.php',
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    searchTerm: params.term,
                    searchType: $("#search_type").val(),
                    searchLoad: $('#vehicleload').val()
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

    $('#customer').change(function() {
        var customer = $("#customer").val();

        $.ajax({
            type: "POST",
            data: {
                customerID: customer,
                save: '1'
            },
            url: 'getprocess/getbillingpayment.php',
            success: function(result) {
                $('#paymentsection').html(result);
            }
        });
    })

    $(document).on('click', '.btnPayment', function() {
        var id = $(this).attr('id');
        $.ajax({
            type: "POST",
            data: {
                recordID: id
            },
            url: 'getprocess/getinvoicepaymentdetails.php',
            success: function(result) {
                var obj = JSON.parse(result);
                $('#invoiceID').val(obj.id);
                $('#invoicepayment').val(obj.paymentid);
                $('#totalpayamount').val(obj.total);
                $('#balance').val(obj.balance);
                $('#billpaymentmodel').modal('show');
            }
        });
    });

    $(document).on('click', '.btnView', function() {
        var id = $(this).attr('id');
        $.ajax({
            type: "POST",
            data: {
                recordID: id
            },
            url: 'getprocess/getbillingview.php',
            success: function(result) {
                $('#billingview').html(result);
                $('#billviewmodel').modal('show');
            }
        });
    });

    $("#submitBtnModal").click(function() {
        var paymenttype = $('input[type=radio][name=paytype]:checked').val();
        var paymentMethod = $('input[type=radio][name=paymentMethod]:checked').val();
        var chequepayamount = $('#chequepayamount').val();
        var chequeno = $('#chequeno').val();
        var reciptno = $('#reciptno').val();
        var chequedate = $('#chequedate').val();
        var bank = $("#bank").val();
        var cashpayamount = $('#cashpayamount').val();
        var totalpayamount = $('#totalpayamount').val();

        $('#paymenttype').val(paymenttype);

        if (paymentMethod == 1) {
            $('#tblPaymentTypeModal > tbody:last').append('<tr class="pointer"><td>Cash</td><td class="text-right">' + parseFloat(cashpayamount).toFixed(2) + '</td><td class="">-</td><td class="">-</td><td>-</td><td>-</td><td>-</td><td class="d-none">1</td></tr>');

            var paidAmount = parseFloat($('#hidePayAmount').val());
            var PayAmount = parseFloat(cashpayamount);
            $('#hideAllBalAmount').val(totalpayamount);
            var totalpayamount = parseFloat(totalpayamount).toFixed(2);
            $('#totAmount').html(totalpayamount);

            paidAmount = (paidAmount + PayAmount);
            var balance = (totalpayamount - paidAmount);
            $('#hideBalAmount').val(balance);
            $('#balanceAmount').html((balance).toFixed(2));
            $('#payAmount').html((paidAmount).toFixed(2));
            $('#hidePayAmount').val(paidAmount);

            $('#cashpayamount').val('').prop('readonly', true);
            $('#cashmethod').prop('checked', false);

            $('#btnsubmitpayment').prop('disabled', false);

        } else {
            $('#tblPaymentTypeModal > tbody:last').append('<tr class="pointer"><td>Bank / Cheque</td><td class="">-</td><td class="text-right">' + parseFloat(chequepayamount).toFixed(2) + '</td><td class="">' + chequeno + '</td><td>' + reciptno + '</td><td>' + chequedate + '</td><td>' + bank + '</td><td class="d-none">2</td></tr>');

            var paidAmount = parseFloat($('#hidePayAmount').val());
            var PayAmount = parseFloat(chequepayamount);
            $('#hideAllBalAmount').val(totalpayamount);
            var totalpayamount = parseFloat(totalpayamount).toFixed(2);
            $('#totAmount').html(totalpayamount);


            paidAmount = (paidAmount + PayAmount);
            var balance = (totalpayamount - paidAmount);
            $('#hideBalAmount').val(balance);
            $('#balanceAmount').html((balance).toFixed(2));
            $('#payAmount').html((paidAmount).toFixed(2));
            $('#hidePayAmount').val(paidAmount);

            $('#chequepayamount').val('').prop('readonly', true);
            $('#chequeno').val('').prop('readonly', true);
            $('#reciptno').val('').prop('readonly', true);
            $('#chequedate').val('').prop('readonly', true);
            $('#bank').val('').prop('readonly', true);
            $('#bankmethod').prop('checked', false);

            $('#btnsubmitpayment').prop('disabled', false);
        }

        $('#collapseOne').collapse('hide');
        $('#collapseTwo').collapse('hide');
    });
    $('#tblPaymentTypeModal').on('click', 'tr', function () {
        var r = confirm("Are you sure, You want to remove this product?");
        if (r == true) {
            var paytype = $(this).closest('tr').find('td:eq(7)').text();
            var cashamount = parseFloat($(this).closest('tr').find('td:eq(1)').text());
            var chequeamount = parseFloat($(this).closest('tr').find('td:eq(2)').text());
            var totalpayamount = parseFloat($('#hideAllBalAmount').val());
            var totalpaidamount = parseFloat($('#hidePayAmount').val());
            var totalbalanceamount = parseFloat($('#hideBalAmount').val());
            
            if(paytype==1){
                totalpaidamount=totalpaidamount-cashamount;
                totalbalanceamount=totalbalanceamount+cashamount;

                $('#hideBalAmount').val(totalbalanceamount);
                $('#hidePayAmount').val(totalpaidamount);
                $('#balanceAmount').html((totalbalanceamount).toFixed(2));
                $('#payAmount').html((totalpaidamount).toFixed(2));
            }
            else{
                totalpaidamount=totalpaidamount-chequeamount;
                totalbalanceamount=totalbalanceamount+chequeamount;

                $('#hideBalAmount').val(totalbalanceamount);
                $('#hidePayAmount').val(totalpaidamount);
                $('#balanceAmount').html((totalbalanceamount).toFixed(2));
                $('#payAmount').html((totalpaidamount).toFixed(2));
            }
            
            if($('#hidePayAmount').val()==0){
                $('#btnsubmitpayment').prop('disabled', true);
            }
            $(this).closest('tr').remove();
        }
    });

    $('#btnsubmitpayment').click(function(){
        var tbodysecond = $('#tblPaymentTypeModal tbody');
        jsonObjOne = [];
        if (tbodysecond.children().length > 0) {
            $("#tblPaymentTypeModal tbody tr").each(function() {
                item = {}
                $(this).find('td').each(function(col_idx) {
                    item["col_" + (col_idx + 1)] = $(this).text();
                });
                jsonObjOne.push(item);
            });
            //  console.log(jsonObjOne);

            var totAmount = $('#hideAllBalAmount').val();
            var payAmount = $('#hidePayAmount').val();
            var balAmount = $('#hideBalAmount').val();
            var invoiceID = $('#invoiceID').val();
            
            $.ajax({
                type: "POST",
                data: {
                    tblPayData: jsonObjOne,
                    invoiceID: invoiceID,
                    totAmount: totAmount,
                    payAmount: payAmount,
                    balAmount: balAmount
                },
                url: 'process/billingpaymentprocess.php',
                success: function(result) { //alert(result);
                    var obj = JSON.parse(result);
                    if(obj.paymentinvoice>0){
                        $('#billpaymentmodel').modal('hide');
                        paymentreceiptview(obj.paymentinvoice);
                        $('#modalpaymentreceipt').modal('show');
                    }
                    action(obj.action);
                }
            });
        }
    });

    document.getElementById('btnreceiptprint').addEventListener ("click", print);
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

function paymentreceiptview(paymentinoiceID){
    $('#viewreceiptprint').html('<div class="card border-0 shadow-none bg-transparent"><div class="card-body text-center"><img src="images/spinner.gif" alt="" srcset=""></div></div>');

    $.ajax({
        type: "POST",
        data: {
            paymentinoiceID: paymentinoiceID
        },
        url: 'getprocess/getpaymentreceipt.php',
        success: function(result) { //alert(result);
            $('#viewreceiptprint').html(result);
        }
    });
}

function print() {
    printJS({
        printable: 'viewreceiptprint',
        type: 'html',
        targetStyles: ['*']
    })
}
</script>
<?php include "include/footer.php"; ?>