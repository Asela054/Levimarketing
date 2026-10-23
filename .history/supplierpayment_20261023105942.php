<?php 
include "include/header.php";  

$sqlsupplier="SELECT `idtbl_supplier`, `suppliername` FROM `tbl_supplier` WHERE `status`=1 ORDER BY `suppliername`";
$resultsupplier =$conn-> query($sqlsupplier);

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
                            <div class="page-header-icon"><i data-feather="credit-card"></i></div>
                            <span>Supplier Payment</span>
                        </h1>
                    </div>
                </div>
            </div>
            <div class="container-fluid mt-2 p-0 p-2">
                <div class="card">
                    <div class="card-body p-0 p-2">
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col">
                                        <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right" id="btnpaymentcreate"><i class="fas fa-plus"></i>&nbsp;Create Payment</button>
                                    </div>
                                </div>
                                <hr>
                                <table class="table table-bordered table-striped table-sm nowrap" id="dataTablePayment">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Payment No</th>
                                            <th>Supplier</th>
                                            <th>Method</th>
                                            <th>Reference</th>
                                            <th class="text-right">Total</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-right">Actions</th>
                                        </tr>
                                    </thead>
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

<!-- Modal Create Payment -->
<div class="modal fade" id="modalcreatepayment" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title">Create Supplier Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                        <form action="#" method="post" autocomplete="off" id="paymentFrom">
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Supplier*</label>
                                    <select name="paysupplier" id="paysupplier" class="form-control form-control-sm px-0" required>
                                        <option value="">Select</option>
                                        <?php if($resultsupplier->num_rows > 0) {while ($rowsupplier = $resultsupplier->fetch_assoc()) { ?>
                                        <option value="<?php echo $rowsupplier['idtbl_supplier'] ?>"><?php echo $rowsupplier['suppliername'] ?></option>
                                        <?php }} ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Payment Date*</label>
                                    <input type="date" class="form-control form-control-sm" name="paydate" id="paydate" value="<?php echo date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Payment Method*</label>
                                    <select name="paymethod" id="paymethod" class="form-control form-control-sm" required>
                                        <option value="1">Cash</option>
                                        <option value="2">Cheque</option>
                                        <option value="3">Bank Transfer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row mb-1 d-none" id="divcheque">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Cheque No*</label>
                                    <input type="text" class="form-control form-control-sm" name="chequeno" id="chequeno">
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Cheque Date*</label>
                                    <input type="date" class="form-control form-control-sm" name="chequedate" id="chequedate">
                                </div>
                            </div>
                            <div class="form-row mb-1 d-none" id="divbank">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Bank Reference*</label>
                                    <input type="text" class="form-control form-control-sm" name="bankref" id="bankref">
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Remark</label>
                                    <input type="text" class="form-control form-control-sm" name="payremark" id="payremark" maxlength="400">
                                </div>
                            </div>
                            <hr>
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Lump Sum (allocate oldest first)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control form-control-sm" id="lumpsum" value="">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-dark btn-sm" id="btnallocate">Allocate</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mt-2">
                                <input name="submitBtn" type="submit" value="Save" id="submitBtn" class="d-none">
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                        <h6 class="title-style small font-weight-bold mt-2"><span>Outstanding GRN</span></h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm table-striped small nowrap" id="tablePaymentList">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>GRN</th>
                                        <th>Date</th>
                                        <th>Invoice</th>
                                        <th class="text-center">VAT</th>
                                        <th class="text-right">Sub Total</th>
                                        <th class="text-right">VAT Amount</th>
                                        <th class="text-right">Total</th>
                                        <th class="text-right">Paid</th>
                                        <th class="text-right">Balance</th>
                                        <th class="text-right">Pay Now</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodypaymentcreate"></tbody>
                                <tfoot>
                                    <tr class="font-weight-bold">
                                        <th colspan="5" class="text-right">Total of all GRNs</th>
                                        <th class="text-right" id="sumSub">0.00</th>
                                        <th class="text-right" id="sumVat">0.00</th>
                                        <th class="text-right" id="sumTotal">0.00</th>
                                        <th class="text-right" id="sumPaid">0.00</th>
                                        <th class="text-right" id="sumBal">0.00</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-9 col-lg-9 col-xl-9 text-right"><h4>Total Payment : </h4></div>
                            <div class="col-sm-12 col-md-3 col-lg-3 col-xl-3 text-right"><h3 class="text-dark" id="showPayTotal">0.00</h3></div>
                            <input type="hidden" id="txtPayTotal" value="">
                            <div class="col-12">
                                <hr class="border-dark">
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right px-5" id="btnSavePayment"><i class="far fa-save"></i>&nbsp;Save Payment</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Payment Detail -->
<div class="modal fade" id="modalpaymentdetail" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h6 class="modal-title" id="paymentdetailtitle"></h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="viewpaymentdetail"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Warning -->
<div class="modal fade" id="warningModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body bg-danger text-white text-center">
                <div id="warningdesc"></div>
            </div>
            <div class="modal-footer bg-danger rounded-0">
                <button type="button" class="btn btn-outline-light btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php include "include/footerscripts.php"; ?>
<script>
    $(document).ready(function() {

        $('#paysupplier').select2({
            dropdownParent: $('#modalcreatepayment'),
            width: '100%',
            placeholder: 'Select Supplier'
        });

        var payTable = $('#dataTablePayment').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "pageLength": 25,
            "stateSave": true,
            ajax: {
                url: "scripts/supplierpaymentlist.php",
                type: "POST",
                cache: true
            },
            "order": [[0, "desc"]],
            "columns": [
                { "data": "idtbl_supplier_payment" },
                { "data": "date" },
                {
                    "data": null,
                    "render": function (data, type, full) {
                        return 'SP-' + full['idtbl_supplier_payment'];
                    }
                },
                { "data": "suppliername" },
                {
                    "data": null,
                    "render": function (data, type, full) {
                        // paymethod: 1 = Cash, 2 = Cheque, 3 = Bank Transfer
                        return full['paymethod'] == 1 ? 'Cash' : (full['paymethod'] == 2 ? 'Cheque' : 'Bank Transfer');
                    }
                },
                {
                    "data": null,
                    "render": function (data, type, full) {
                        if (full['paymethod'] == 2) { return full['chequeno'] + ' (' + full['chequedate'] + ')'; }
                        if (full['paymethod'] == 3) { return full['bankref']; }
                        return '-';
                    }
                },
                {
                    "className": 'text-right', "data": null,
                    "render": function (data, type, full) {
                        return addCommas((parseFloat(full['totalpayment']) || 0).toFixed(2));
                    }
                },
                {
                    "className": 'text-center', "data": null,
                    "render": function (data, type, full) {
                        if (full['status'] == 3) {
                            return '<i class="fas fa-times text-danger"></i>&nbsp;Cancelled';
                        }
                        if (full['confirm_status'] == 1) {
                            return '<i class="fas fa-check text-success"></i>&nbsp;Approved';
                        }
                        return '<i class="fas fa-clock text-warning"></i>&nbsp;Pending';
                    }
                },
                {
                    "className": 'text-right', "orderable": false, "data": null,
                    "render": function (data, type, full) {
                        var button = '';
                        button += '<button class="btn btn-outline-dark btn-sm mr-1 btnviewpayment" data-toggle="tooltip" data-placement="bottom" title="View Payment" id="' + full['idtbl_supplier_payment'] + '"><i class="far fa-eye"></i></button>';
                        if (full['status'] == 1) {
                            if (full['confirm_status'] == 0) {
                                button += '<button class="btn btn-outline-orange btn-sm mr-1 btnapprovepayment" data-toggle="tooltip" data-placement="bottom" title="Approve Payment" id="' + full['idtbl_supplier_payment'] + '"><i class="fas fa-times"></i></button>';
                            } else {
                                button += '<button class="btn btn-outline-success btn-sm mr-1" disabled data-toggle="tooltip" data-placement="bottom" title="Approved"><i class="fas fa-check"></i></button>';
                            }
                            button += '<button class="btn btn-outline-danger btn-sm mr-1 btncancelpayment" data-toggle="tooltip" data-placement="bottom" title="Cancel Payment" id="' + full['idtbl_supplier_payment'] + '"><i class="fas fa-ban"></i></button>';
                        }
                        return button;
                    }
                }
            ]
        });

        $('body').tooltip({ selector: '[data-toggle="tooltip"]' });

        $('#btnpaymentcreate').click(function() {
            $('#modalcreatepayment').modal('show');
        });

        $('#modalcreatepayment').on('hidden.bs.modal', function() {
            $('#paymentFrom')[0].reset();
            $('#paydate').val('<?php echo date("Y-m-d"); ?>');
            try { $('#paysupplier').val(null).trigger('change'); } catch(e) {}
            $('#divcheque').addClass('d-none');
            $('#divbank').addClass('d-none');
            $('#tbodypaymentcreate').html('');
            $('#showPayTotal').html('0.00');
            $('#txtPayTotal').val('');
            grnsums();
        });

        // Show / hide cheque and bank fields
        $('#paymethod').change(function() {
            var method = $(this).val();
            $('#divcheque').toggleClass('d-none', method != '2');
            $('#divbank').toggleClass('d-none', method != '3');
        });

        // Load outstanding GRNs for the supplier
        $('#paysupplier').change(function() {
            var supplier = $(this).val();
            if (!supplier) { $('#tbodypaymentcreate').html(''); paytotal(); return; }

            $.ajax({
                type: "POST",
                data: { supplier: supplier },
                url: 'getprocess/getsuppliergrnoutstanding.php',
                success: function(result) {
                    $('#tbodypaymentcreate').html(result);
                    paytotal();
                }
            });
        });

        // Tick a GRN -> full balance (full payment); edit the amount for a partial payment
        $('#tbodypaymentcreate').on('change', '.chkgrn', function() {
            var tr = $(this).closest('tr');
            var on = $(this).is(':checked');
            var balance = parseFloat(tr.data('balance')) || 0;
            tr.find('.payamount').prop('disabled', !on).val(on ? balance.toFixed(2) : '0.00');
            paytotal();
        });

        $('#tbodypaymentcreate').on('input', '.payamount', function() {
            var balance = parseFloat($(this).closest('tr').data('balance')) || 0;
            var val = parseFloat($(this).val());
            if (!isNaN(val) && val > balance) { $(this).val(balance.toFixed(2)); }
            if (!isNaN(val) && val < 0) { $(this).val('0.00'); }
            paytotal();
        });

        // Lump sum: clear oldest GRNs first, remainder becomes a partial payment on the next one
        $('#btnallocate').click(function() {
            var left = parseFloat($('#lumpsum').val()) || 0;
            if (left <= 0) { return; }

            $('#tbodypaymentcreate tr').each(function() {
                var tr = $(this);
                if (!tr.data('grn')) { return; }
                var balance = parseFloat(tr.data('balance')) || 0;
                var pay = Math.min(balance, left);
                tr.find('.chkgrn').prop('checked', pay > 0);
                tr.find('.payamount').prop('disabled', pay <= 0).val(pay.toFixed(2));
                left = Math.round((left - pay) * 100) / 100;
            });
            paytotal();

            if (left > 0) {
                $('#warningdesc').html('Lump sum is more than the outstanding balance by ' + addCommas(left.toFixed(2)));
                $('#warningModal').modal('show');
            }
        });

        $('#btnSavePayment').click(function() {
            if (!$("#paymentFrom")[0].checkValidity()) {
                $("#submitBtn").click();
                return;
            }

            var paymethod = $('#paymethod').val();
            if (paymethod == '2' && ($('#chequeno').val() == '' || $('#chequedate').val() == '')) {
                $('#warningdesc').html('Cheque number and cheque date are required.');
                $('#warningModal').modal('show');
                return;
            }
            if (paymethod == '3' && $('#bankref').val() == '') {
                $('#warningdesc').html('Bank reference is required.');
                $('#warningModal').modal('show');
                return;
            }

            var jsonObj = [];
            $('#tbodypaymentcreate tr').each(function() {
                var tr = $(this);
                if (tr.find('.chkgrn').is(':checked')) {
                    var amount = parseFloat(tr.find('.payamount').val()) || 0;
                    if (amount > 0) {
                        jsonObj.push({ grnid: tr.data('grn'), amount: amount });
                    }
                }
            });

            if (jsonObj.length === 0) {
                $('#warningdesc').html('Please select at least one GRN and enter an amount.');
                $('#warningModal').modal('show');
                return;
            }

            var $btn = $(this).prop('disabled', true);

            $.ajax({
                type: "POST",
                data: {
                    tableData: jsonObj,
                    paysupplier: $('#paysupplier').val(),
                    paydate: $('#paydate').val(),
                    paymethod: paymethod,
                    chequeno: $('#chequeno').val(),
                    chequedate: $('#chequedate').val(),
                    bankref: $('#bankref').val(),
                    payremark: $('#payremark').val()
                },
                url: 'process/supplierpaymentprocess.php',
                success: function(result) {
                    action(result);
                    $('#modalcreatepayment').modal('hide');
                    payTable.ajax.reload(null, false);
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        });

        $('#dataTablePayment tbody').on('click', '.btnviewpayment', function() {
            var paymentid = $(this).attr('id');

            $('#paymentdetailtitle').html('SP-' + paymentid);
            $('#modalpaymentdetail').modal('show');

            $.ajax({
                type: "POST",
                data: { paymentid: paymentid },
                url: 'getprocess/getsupplierpaymentdetail.php',
                success: function(result) {
                    $('#viewpaymentdetail').html(result);
                }
            });
        });

        $('#dataTablePayment tbody').on('click', '.btncancelpayment', function() {
            var paymentid = $(this).attr('id');
            if (!confirm("Are you sure you want to cancel this payment? GRN balances will be restored.")) { return; }

            $.ajax({
                type: "POST",
                data: { paymentid: paymentid },
                url: 'process/supplierpaymentcancel.php',
                success: function(result) {
                    action(result);
                    payTable.ajax.reload(null, false);
                }
            });
        });

        $('#dataTablePayment tbody').on('click', '.btnapprovepayment', function() {
            var paymentid = $(this).attr('id');
            if (!confirm("Are you sure you want to approve this payment? GRN paid amounts will be updated.")) { return; }

            $.ajax({
                type: "POST",
                data: { paymentid: paymentid },
                url: 'process/supplierpaymentapprove.php',
                success: function(result) {
                    action(result);
                    payTable.ajax.reload(null, false);
                }
            });
        });
    });

    // Totals of ALL listed GRNs (not only the ticked ones)
    function grnsums(){
        var cols = { sumSub: 'c-sub', sumVat: 'c-vat', sumTotal: 'c-total', sumPaid: 'c-paid', sumBal: 'c-bal' };
        $.each(cols, function(id, cls) {
            var s = 0;
            $('#tbodypaymentcreate td.' + cls).each(function() {
                s += parseFloat($(this).data('v')) || 0;
            });
            $('#' + id).html(addCommas(s.toFixed(2)));
        });
    }

    function paytotal(){
        grnsums();
        var sum = 0;
        $('#tbodypaymentcreate tr').each(function() {
            if ($(this).find('.chkgrn').is(':checked')) {
                sum += parseFloat($(this).find('.payamount').val()) || 0;
            }
        });
        $('#showPayTotal').html(addCommas(sum.toFixed(2)));
        $('#txtPayTotal').val(sum.toFixed(2));
    }

    function action(data) {
        var obj = JSON.parse(data);
        $.notify({
            icon: obj.icon, title: obj.title, message: obj.message, url: obj.url, target: obj.target
        }, {
            element: 'body', position: null, type: obj.type, allow_dismiss: true, newest_on_top: false,
            showProgressbar: false, placement: { from: "top", align: "center" }, offset: 100, spacing: 10,
            z_index: 1031, delay: 5000, timer: 1000, url_target: '_blank', mouse_over: null,
            animate: { enter: 'animated fadeInDown', exit: 'animated fadeOutUp' },
            onShow: null, onShown: null, onClose: null, onClosed: null, icon_type: 'class',
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

    function addCommas(nStr){
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
</script>
<?php include "include/footer.php"; ?>