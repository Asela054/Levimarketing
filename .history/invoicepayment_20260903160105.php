<?php 
include "include/header.php";  

$sql="SELECT * FROM `tbl_vehicle` WHERE `status` IN (1,2)";
$result =$conn-> query($sql); 

$sqlcustomer="SELECT `idtbl_customer`, `name` FROM `tbl_customer` WHERE `status`=1 ORDER BY `name` ASC";
$resultcustomer =$conn-> query($sqlcustomer);

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
                            <div class="page-header-icon"><i data-feather="file"></i></div>
                            <span>Invoice Payment</span>
                        </h1>
                    </div>
                </div>
            </div>
            <div class="container-fluid mt-2 p-0 p-2">
                <div class="card">
                    <div class="card-body p-0 p-2">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-row">
                                    <div class="col-3">
                                        <label class="small font-weight-bold text-dark">Customer*</label>
                                        <select class="form-control form-control-sm" name="customer" id="customer" required>
                                            <option value="">Select</option>
                                            <?php if($resultcustomer->num_rows > 0) {while ($rowcustomer = $resultcustomer-> fetch_assoc()) { ?>
                                            <option value="<?php echo $rowcustomer['idtbl_customer'] ?>"><?php echo $rowcustomer['name']; ?></option>
                                            <?php }} ?>
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <label class="small font-weight-bold text-dark">Invoice / Tax Invoice Search</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control" placeholder="Invoice No or Tax Invoice No" aria-label="Invoice Number" aria-describedby="button-addon2" id="formInvNum">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-dark" type="button" id="formSearchBtn"><i class="fas fa-search"></i>&nbsp;Search</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">&nbsp;</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <hr class="border-dark">
                                <div id="invoiceviewdetail"></div>
                                <hr class="border-dark">
                                <button type="button" class="btn btn-outline-danger btn-sm fa-pull-right" disabled id="invPaymentCreateBtn"><i class="fas fa-receipt"></i>&nbsp;Issue Payment Receipt</button>
                                <button type="button" class="btn btn-outline-dark btn-sm fa-pull-right mr-2" id="invPaymentCheckBtn" disabled><i class="fas fa-tasks"></i>&nbsp;Check Payment</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php include "include/footerbar.php"; ?>
    </div>
</div>
<!-- Modal Alert -->
<div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content bg-danger">
            <div class="modal-body text-white">
                <div class="row">
                    <div class="col" id="bodyAlert"></div>
                </div>
                <button type="button" class="btn btn-outline-light btn-sm fa-pull-right pl-4 pr-4" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
<!--Issue Payment Receipt Modal-->
<div class="modal fade" id="paymentmodal" tabindex="-1" role="dialog" aria-labelledby="oLevel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header p-0 p-2">
                <h5 class="modal-title" id="oLevelTitle">Issue Payment Receipt</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 col-md-3 col-lg-3 col-xl-3">
                        <form id="formModal">
                            <div class="form-group mb-1">
                                <label class="small font-weight-bold text-dark">Payment Amount</label>
                                <input id="paymentPayAmount" name="paymentPayAmount" type="text" class="form-control form-control-sm" placeholder="Total Amount" readonly>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="paymentMethod1" name="paymentMethod" class="custom-control-input" value="1" data-toggle="collapse" href="#collapseOne">
                                <label class="custom-control-label" for="paymentMethod1">Cash</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="paymentMethod2" name="paymentMethod" class="custom-control-input" value="2" data-toggle="collapse" href="#collapseTwo">
                                <label class="custom-control-label" for="paymentMethod2">Bank / Cheque</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="paymentMethod3" name="paymentMethod" class="custom-control-input" value="3" data-toggle="collapse" href="#collapseThree">
                                <label class="custom-control-label" for="paymentMethod3">Card</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="paymentMethod4" name="paymentMethod" class="custom-control-input" value="4" data-toggle="collapse" href="#collapseFour">
                                <label class="custom-control-label" for="paymentMethod4">Online Transfer</label>
                            </div>
                            <div class="accordion" id="accordionExample">
                                <div class="card shadow-none border-0">
                                    <div id="collapseOne" class="collapse" aria-labelledby="headingOne"
                                        data-parent="#accordionExample">
                                        <div class="card-body p-0">
                                            <div class="form-group mb-1">
                                                <label class="small font-weight-bold text-dark">Cash Advance</label>
                                                <input id="paymentCash" name="paymentCash" type="text" class="form-control form-control-sm" placeholder="" required readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card shadow-none border-0">
                                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                                        data-parent="#accordionExample">
                                        <div class="card-body p-0">
                                            <div class="form-group mb-1">
                                                <label class="small font-weight-bold text-dark">Cheque / Deposit Advance</label>
                                                <input id="paymentCheque" name="paymentCheque" type="text" class="form-control form-control-sm" placeholder="" required readonly>
                                            </div>
                                            <div class="form-group mb-1">
                                                <label class="small font-weight-bold text-dark">Cheque Number</label>
                                                <input id="paymentChequeNum" name="paymentChequeNum" type="text" class="form-control form-control-sm" placeholder="" readonly>
                                            </div>
                                            <div class="form-group mb-1">
                                                <label class="small font-weight-bold text-dark">Receipt Number</label>
                                                <input id="paymentReceiptNum" name="paymentReceiptNum" type="text" class="form-control form-control-sm" placeholder="" readonly>
                                                <small id="" class="form-text text-muted">Bank deposit receipt number only</small>
                                            </div>
                                            <div class="form-group mb-1">
                                                <label class="small font-weight-bold text-dark">Cheque Date</label>
                                                <input type="date" class="form-control form-control-sm" name="paymentchequeDate" id="paymentchequeDate" readonly>
                                            </div>
                                            <div class="form-group mb-1">
                                                <label class="small font-weight-bold text-dark">Bank Name</label>
                                                <input id="paymentBank" name="paymentBank" type="text" class="form-control form-control-sm" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card shadow-none border-0">
                                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree"
                                            data-parent="#accordionExample">
                                            <div class="card-body p-0">
                                                <div class="form-group mb-1">
                                                    <label class="small font-weight-bold text-dark">Card Amount</label>
                                                    <input id="paymentCardAmount" name="paymentCardAmount" type="text" class="form-control form-control-sm" placeholder="" required readonly>
                                                </div>
                                                <div class="form-group mb-1">
                                                    <label class="small font-weight-bold text-dark">Card Last 4 Digits</label>
                                                    <input id="paymentCardLast4" name="paymentCardLast4" type="text" class="form-control form-control-sm" maxlength="4" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card shadow-none border-0">
                                        <div id="collapseFour" class="collapse" aria-labelledby="headingFour"
                                            data-parent="#accordionExample">
                                            <div class="card-body p-0">
                                                <div class="form-group mb-1">
                                                    <label class="small font-weight-bold text-dark">Transfer Amount</label>
                                                    <input id="paymentOnlineAmount" name="paymentOnlineAmount" type="text" class="form-control form-control-sm" placeholder="" required readonly>
                                                </div>
                                                <div class="form-group mb-1">
                                                    <label class="small font-weight-bold text-dark">Reference Number</label>
                                                    <input id="paymentOnlineRef" name="paymentOnlineRef" type="text" class="form-control form-control-sm" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                <button name="submitBtnModal" type="button" id="submitBtnModal" class="btn btn-outline-primary btn-sm fa-pull-right"><i class="fas fa-file-invoice-dollar"></i>&nbsp;Add Payment</button>
                                <input type="submit" class="d-none" id="hideSubmitModal">
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-12 col-md-9 col-lg-9 col-xl-9">
                        <table class="table table-bordered table-sm table-striped" id="tblPaymentTypeModal">
                            <thead>
                                <th>Type</th>
                                <th class="text-right">Cash</th>
                                <th class="text-right">Amount</th>
                                <th>Ref / Che No</th>
                                <th>Receipt</th>
                                <th>Che Date</th>
                                <th>Bank</th>
                                <th class="d-none">BankID</th>
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
                        <input type="hidden" id="hidePayAmount" value="0">
                        <input type="hidden" id="hideBalAmount" value="0">
                        <input type="hidden" id="hideAllBalAmount" value="0">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12" align="right">
                        <button class="btn btn-outline-danger btn-sm" id="btnIssueInv" disabled><i class="fas fa-file-pdf"></i>&nbsp;Issue Payment Receipt</button>
                    </div>
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
        $('#customer').select2();
        $('#customer').change(function(){
            var customerID = $(this).val();
            $('#invoiceviewdetail').html('<div class="card border-0 shadow-none bg-transparent"><div class="card-body text-center"><img src="images/spinner.gif" alt="" srcset=""></div></div>');

            $.ajax({
                type: "POST",
                data: {
                    customerID: customerID
                },
                url: 'getprocess/getinvoicepayment.php',
                success: function(result) {//alert(result);
                    $('#invoiceviewdetail').html(result);
                    $('#invPaymentCheckBtn').prop("disabled", false);
                    tblcheckboxevent();
                }
            });  
        });

        $('#formSearchBtn').click(function(){
            $('#invoiceviewdetail').html('<div class="card border-0 shadow-none bg-transparent"><div class="card-body text-center"><img src="images/spinner.gif" alt="" srcset=""></div></div>');

            var invoiceno = $('#formInvNum').val();
            if(invoiceno!=''){
                $('#formInvNum').removeClass('bg-danger-soft');
                $.ajax({
                    type: "POST",
                    data: {
                        invoiceno: invoiceno
                    },
                    url: 'getprocess/getinvoicepayment.php',
                    success: function(result) {//alert(result);
                        $('#invoiceviewdetail').html(result);
                        $('#invPaymentCheckBtn').prop("disabled", false);
                        tblcheckboxevent();
                    }
                });
            }
            else{
                $('#formInvNum').addClass('bg-danger-soft');
            }
        });

        $('#invPaymentCheckBtn').click(function() {
            var promise = tblTextRemove();

            promise.then(function() {
                var value = 0;

                var table = $("#paymentDetailTable tbody");
                table.find('tr').each(function(i, el) {
                    var row = $(this);
                    var tds = $(this).find('td');
                    value += parseFloat(tds.eq(8).text());
                });

                if (value == '0.00') {
                    $('#bodyAlert').html('<i class="fas fa-exclamation-triangle fa-pull-left fa-3x"></i><p>Please enter the payment value or uncheck full payment | halfpayment check box.</p>');
                    $('#alertModal').modal({
                        keyboard: false,
                        backdrop: 'static'
                    });
                    $('#invPaymentCreateBtn').prop("disabled", true);
                } else {
                    $('#invPaymentCreateBtn').prop("disabled", false);
                }
            });

        });
        $('#invPaymentCreateBtn').click(function() {
            var table = $("#paymentDetailTable tbody");
            var total = '0';
            var invnetBal = '0';
            var invBal = '0';
            table.find('tr').each(function(i, el) {
                var $tds = $(this).find('td');
                var value = parseFloat($tds.eq(9).text());

                total = parseFloat(total);
                total = (total + value);

                var bal = parseFloat($tds.eq(4).text());
                invBal = parseFloat(invBal);
                invBal = (invBal + invBal);
            });

            total = parseFloat(total).toFixed(2);
            invBal = parseFloat(invBal).toFixed(2);
            invnetBal = parseFloat(total + invBal).toFixed(2);

            if (invnetBal == '0.00') {
                $('#bodyAlert').html('<i class="fas fa-exclamation-triangle fa-pull-left fa-3x"></i><p>Please enter the payment value and press the create payment button</p>');
                $('#alertModal').modal({
                    keyboard: false,
                    backdrop: 'static'
                });
                $('#invPaymentCreateBtn').prop("disabled", true);
            } else {
                $('#paymentPayAmount').val(invnetBal);
                $('#totAmount').html(invnetBal);
                $('#hideAllBalAmount').val(invnetBal);
                $('#paymentmodal').modal({
                    keyboard: false,
                    backdrop: 'static'
                });
            }
        });
        $('input[type=radio][name=paymentMethod]').change(function() {
            $('#paymentCash, #paymentCheque, #paymentChequeNum, #paymentReceiptNum, #paymentchequeDate, #paymentBank, #paymentCardAmount, #paymentCardLast4, #paymentOnlineAmount, #paymentOnlineRef').prop("readonly", true);

            if (this.value == '1') {
                $('#paymentCash').prop("readonly", false);
            }
            else if (this.value == '2') {
                $('#paymentCheque, #paymentChequeNum, #paymentReceiptNum, #paymentchequeDate, #paymentBank').prop("readonly", false);
            }
            else if (this.value == '3') {
                $('#paymentCardAmount, #paymentCardLast4').prop("readonly", false);
            }
            else if (this.value == '4') {
                $('#paymentOnlineAmount, #paymentOnlineRef').prop("readonly", false);
            }
        });
        $("#submitBtnModal").click(function() {
            if (!$("#formModal")[0].checkValidity()) {
                $("#hideSubmitModal").click();
            } else {
                var paymenttype = $('input[type=radio][name=paymentMethod]:checked').val();
                var paymentCheque = $('#paymentCheque').val();
                var paymentChequeNum = $('#paymentChequeNum').val();
                var paymentReceiptNum = $('#paymentReceiptNum').val();
                var paymentchequeDate = $('#paymentchequeDate').val();
                var paymentBank = $("#paymentBank").val();
                var paymentCash = $('#paymentCash').val();
                var paymentCardAmount = $('#paymentCardAmount').val();     
                var paymentCardLast4 = $('#paymentCardLast4').val();       
                var paymentOnlineAmount = $('#paymentOnlineAmount').val(); 
                var paymentOnlineRef = $('#paymentOnlineRef').val();       

                if(paymenttype==1){
                    $('#tblPaymentTypeModal > tbody:last').append('<tr class="pointer"><td>Cash</td><td class="text-right">' + parseFloat(paymentCash).toFixed(2) + '</td><td class="">-</td><td class="">-</td><td>-</td><td>-</td><td>-</td><td class="d-none">1</td><td class="d-none">1</td></tr>');
                    var paidAmount = parseFloat($('#hidePayAmount').val());
                    var PayAmount = parseFloat(paymentCash);
                    var paymentPayAmount = parseFloat($('#hideAllBalAmount').val());

                    paidAmount = (paidAmount + PayAmount);
                    var balance = (paymentPayAmount - paidAmount);
                    $('#hideBalAmount').val(balance);
                    $('#balanceAmount').html((balance).toFixed(2));
                    $('#payAmount').html((paidAmount).toFixed(2));
                    $('#hidePayAmount').val(paidAmount);

                    $('#paymentCash').val('').prop('readonly', true);
                    $('#paymentMethod1').prop('checked', false);

                    $('#btnIssueInv').prop('disabled', false);
                }
                else if(paymenttype==2){
                    $('#tblPaymentTypeModal > tbody:last').append('<tr class="pointer"><td>Bank / Cheque</td><td class="">-</td><td class="text-right">' + parseFloat(paymentCheque).toFixed(2) + '</td><td class="">'+paymentChequeNum+'</td><td>'+paymentReceiptNum+'</td><td>'+paymentchequeDate+'</td><td>'+paymentBank+'</td><td class="d-none"></td><td class="d-none">2</td></tr>');

                    var paidAmount = parseFloat($('#hidePayAmount').val());
                    var PayAmount = parseFloat(paymentCheque);
                    var paymentPayAmount = parseFloat($('#hideAllBalAmount').val());

                    paidAmount = (paidAmount + PayAmount);
                    var balance = (paymentPayAmount - paidAmount);
                    $('#hideBalAmount').val(balance);
                    $('#balanceAmount').html((balance).toFixed(2));
                    $('#payAmount').html((paidAmount).toFixed(2));
                    $('#hidePayAmount').val(paidAmount);

                    $('#paymentCheque').val('').prop('readonly', true);
                    $('#paymentChequeNum').val('').prop('readonly', true);
                    $('#paymentReceiptNum').val('').prop('readonly', true);
                    $('#paymentchequeDate').val('').prop('readonly', true);
                    $('#paymentBank').val('').prop('readonly', true);
                    $('#paymentMethod2').prop('checked', false);

                    $('#btnIssueInv').prop('disabled', false);
                }
                else if(paymenttype==3){                                                     
                    $('#tblPaymentTypeModal > tbody:last').append('<tr class="pointer"><td>Card</td><td class="">-</td><td class="text-right">' + parseFloat(paymentCardAmount).toFixed(2) + '</td><td class="">'+paymentCardLast4+'</td><td>-</td><td>-</td><td>-</td><td class="d-none"></td><td class="d-none">3</td></tr>');

                    var paidAmount = parseFloat($('#hidePayAmount').val());
                    var PayAmount = parseFloat(paymentCardAmount);
                    var paymentPayAmount = parseFloat($('#hideAllBalAmount').val());

                    paidAmount = (paidAmount + PayAmount);
                    var balance = (paymentPayAmount - paidAmount);
                    $('#hideBalAmount').val(balance);
                    $('#balanceAmount').html((balance).toFixed(2));
                    $('#payAmount').html((paidAmount).toFixed(2));
                    $('#hidePayAmount').val(paidAmount);

                    $('#paymentCardAmount').val('').prop('readonly', true);
                    $('#paymentCardLast4').val('').prop('readonly', true);
                    $('#paymentMethod3').prop('checked', false);

                    $('#btnIssueInv').prop('disabled', false);
                }
                else if(paymenttype==4){                                                     
                    $('#tblPaymentTypeModal > tbody:last').append('<tr class="pointer"><td>Online Transfer</td><td class="">-</td><td class="text-right">' + parseFloat(paymentOnlineAmount).toFixed(2) + '</td><td class="">'+paymentOnlineRef+'</td><td>-</td><td>-</td><td>-</td><td class="d-none"></td><td class="d-none">4</td></tr>');

                    var paidAmount = parseFloat($('#hidePayAmount').val());
                    var PayAmount = parseFloat(paymentOnlineAmount);
                    var paymentPayAmount = parseFloat($('#hideAllBalAmount').val());

                    paidAmount = (paidAmount + PayAmount);
                    var balance = (paymentPayAmount - paidAmount);
                    $('#hideBalAmount').val(balance);
                    $('#balanceAmount').html((balance).toFixed(2));
                    $('#payAmount').html((paidAmount).toFixed(2));
                    $('#hidePayAmount').val(paidAmount);

                    $('#paymentOnlineAmount').val('').prop('readonly', true);
                    $('#paymentOnlineRef').val('').prop('readonly', true);
                    $('#paymentMethod4').prop('checked', false);

                    $('#btnIssueInv').prop('disabled', false);
                }

                $('#collapseOne').collapse('hide');
                $('#collapseTwo').collapse('hide');
                $('#collapseThree').collapse('hide');   
                $('#collapseFour').collapse('hide');    
            }
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
                    $('#btnIssueInv').prop('disabled', true);
                }
                $(this).closest('tr').remove();
            }
        });
        $('#btnIssueInv').click(function(){
            jsonObj = [];
            $("#paymentDetailTable tbody tr").each(function() {
                item = {}
                $(this).find('td').each(function(col_idx) {
                    item["col_" + (col_idx + 1)] = $(this).text();
                });
                jsonObj.push(item);
            });
            // console.log(jsonObj);

            jsonObjOne = [];
            $("#tblPaymentTypeModal tbody tr").each(function() {
                item = {}
                $(this).find('td').each(function(col_idx) {
                    item["col_" + (col_idx + 1)] = $(this).text();
                });
                jsonObjOne.push(item);
            });
            // console.log(jsonObjOne);

            var totAmount = $('#paymentPayAmount').val();
            var payAmount = $('#hidePayAmount').val();
            var balAmount = $('#hideBalAmount').val();

            $.ajax({
                type: "POST",
                data: {
                    tblData: jsonObj,
                    tblPayData: jsonObjOne,
                    totAmount: totAmount,
                    payAmount: payAmount,
                    balAmount: balAmount
                },
                url: 'process/invoicepaymentprocess.php',
                success: function(result) { //alert(result);
                    var obj = JSON.parse(result);
                    if(obj.paymentinvoice>0){
                        $('#paymentmodal').modal('hide');
                        paymentreceiptview(obj.paymentinvoice);
                        $('#paymentmodal').modal('hide');
                        $('#modalpaymentreceipt').modal('show');
                    }
                    action(obj.action);
                }
            });
        });

        document.getElementById('btnreceiptprint').addEventListener ("click", print);

        $('#modalpaymentreceipt').on('hidden.bs.modal', function (e) {
            location.reload();
        });
    });

    function tblcheckboxevent() {
        $('#paymentDetailTable tbody').on('click', '.fullAmount', function() {
            var row = $(this);
            if ((row.closest('.fullAmount')).is(':checked')) {
                var fullAmount = row.closest("tr").find('td:eq(4)').text();
                row.closest("tr").find('td:eq(9)').text(fullAmount);
                row.closest("tr").find('td:eq(8)').find('.halfAmount').prop('checked', false);
            } else {
                (row.closest("tr").find('td:eq(9)')).text('0.00');
            }
        });

        $('#paymentDetailTable tbody').on('click', '.halfAmount', function() {
            var row = $(this);
            if ((row.closest('.halfAmount')).is(':checked')) {
                tblpayamount();
                row.closest("tr").find('td:eq(9)').text('0.00');
                row.closest("tr").find('td:eq(7)').find('.fullAmount').prop('checked', false);
            } else {
                tblTextRemove();
                row.closest("tr").find('td:eq(9)').text('0.00');
            }
        });
    }

    function tblTextRemove() {
        $('#paymentDetailTable .editfield').each(function() {
            $this = $(this);
            var val = $this.val();
            var td = $this.closest('td');
            td.empty().html(parseFloat(val).toFixed(2)).data('editing', false);
        });

        var deferred = $.Deferred();

        setTimeout(function() {
            // completes status
            deferred.resolve();
        }, 1000);

        // returns complete status
        return deferred.promise();
    }

    function tblpayamount() {
        $('.paidAmount').click(function(e) {
            if (($(this).closest('tr')).find('td:eq(8) .halfAmount').is(':checked')) {
                e.preventDefault();
                e.stopImmediatePropagation();

                $this = $(this);
                if ($this.data('editing')) return;

                var val = $this.text();

                $this.empty();
                $this.data('editing', true);

                $('<input type="Text" class="form-control form-control-sm editfieldpay">').val(val).appendTo($this);
            }
        });
        putOldValueBack = function() {
            $('.editfieldpay').each(function() {
                $this = $(this);
                var val = $this.val();
                var td = $this.closest('td');
                td.empty().html(parseFloat(val).toFixed(2)).data('editing', false);
            });
        }
        $(document).click(function(e) {
            putOldValueBack();
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

</script>
<?php include "include/footer.php"; ?>