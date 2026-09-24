<?php 
include "include/header.php";  

$locationid=$_SESSION['location_id'];

$sqlGrnNum="SELECT `idtbl_grn` FROM `tbl_grn` ORDER BY `idtbl_grn` DESC LIMIT 1";   
$resultGrnNum=$conn->query($sqlGrnNum);
$rowGrnNum=$resultGrnNum->fetch_assoc();
$numRowsGrnNum=$rowGrnNum['idtbl_grn']+1;
if($numRowsGrnNum>0){$GRNNum="GRN-".($numRowsGrnNum);}else{$GRNNum="GRN-1";}

$sqlorder="SELECT `idtbl_porder` FROM `tbl_porder` WHERE `status`=1 AND `confirmstatus`=1 AND `grnissuestatus`=0 AND `tbl_location_idtbl_location`='$locationid'";
$resultorder =$conn-> query($sqlorder); 

$sqlsupplier="SELECT `idtbl_supplier`, `suppliername` FROM `tbl_supplier` WHERE `status`=1";
$resultsupplier =$conn-> query($sqlsupplier);

$sqlVat = "SELECT `vat` FROM `tbl_vat_info` WHERE `status`=1 ORDER BY `date_from` DESC LIMIT 1";
$resultVat = $conn->query($sqlVat);
$rowVat = $resultVat ? $resultVat->fetch_assoc() : null;
$defaultVat = $rowVat ? $rowVat['vat'] : 0;

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
                            <span>GRN Information</span>
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
                                        <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right" id="btngrncreate"><i class="fas fa-plus"></i>&nbsp;Create GRN</button>
                                    </div>
                                </div>
                                <hr>
                                <table class="table table-bordered table-striped table-sm nowrap" id="dataTableGrn">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>GRN No</th>
                                            <th>PO</th>
                                            <th>Supplier</th>
                                            <th>Location</th>
                                            <th>Invoice</th>
                                            <th>Dispatch</th>
                                            <th class="text-right">Sub Total</th>
                                            <th class="text-center">VAT</th>
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

<!-- Modal Create GRN -->
<div class="modal fade" id="modalcreategrn" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title">Create GRN</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                        <form action="#" method="post" autocomplete="off" id="grnFrom">
                            <div class="form-group mb-2">
                                <label class="small font-weight-bold text-dark">GRN Source*</label><br>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="grnsourcepo" name="grnsource" class="custom-control-input" value="1" checked>
                                    <label class="custom-control-label font-weight-bold" for="grnsourcepo">With PO</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="grnsourcenopo" name="grnsource" class="custom-control-input" value="0">
                                    <label class="custom-control-label font-weight-bold" for="grnsourcenopo">Without PO</label>
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">GRN Number</label>
                                    <input type="text" class="form-control form-control-sm" name="grnnum" id="grnnum" value="<?php echo $GRNNum; ?>" readonly>
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">GRN Date*</label>
                                    <input type="date" class="form-control form-control-sm" name="grndate" id="grndate" value="<?php echo date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            <div class="form-row mb-1" id="divponumber">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Purchase Order*</label>
                                    <select name="ponumber" id="ponumber" class="form-control form-control-sm">
                                        <option value="">Select</option>
                                        <?php if($resultorder->num_rows > 0) {while ($roworder = $resultorder-> fetch_assoc()) { ?>
                                        <option value="<?php echo $roworder['idtbl_porder'] ?>"><?php echo 'PO-'.$roworder['idtbl_porder'] ?></option>
                                        <?php }} ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row mb-1 d-none" id="divsupplier">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Supplier*</label>
                                    <select name="grnsupplier" id="grnsupplier" class="form-control form-control-sm selecter2 px-0">
                                        <option value="">Select</option>
                                        <?php if($resultsupplier->num_rows > 0) {while ($rowsupplier = $resultsupplier->fetch_assoc()) { ?>
                                        <option value="<?php echo $rowsupplier['idtbl_supplier'] ?>"><?php echo $rowsupplier['suppliername'] ?></option>
                                        <?php }} ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Invoice Number*</label>
                                    <input type="text" class="form-control form-control-sm" name="grninvoice" id="grninvoice" required>
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Delivery Number*</label>
                                    <input type="text" class="form-control form-control-sm" name="grndispatch" id="grndispatch" required>
                                </div>
                            </div>

                            <hr>
                            <div class="form-group mb-2">
                                <label class="small font-weight-bold text-dark">VAT Type*</label><br>
                                <!-- vattype: 1 = Exclusive, 2 = Inclusive (matches tbl_grn.vattype convention) -->
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="vatexclusive" name="vattype" class="custom-control-input" value="1" checked>
                                    <label class="custom-control-label font-weight-bold" for="vatexclusive">VAT Exclusive</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="vatinclusive" name="vattype" class="custom-control-input" value="2">
                                    <label class="custom-control-label font-weight-bold" for="vatinclusive">VAT Inclusive</label>
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">VAT %</label>
                                    <input type="text" class="form-control form-control-sm" name="grnvatpercentage" id="grnvatpercentage" value="<?php echo $defaultVat; ?>">
                                </div>
                            </div>

                            <!-- Fields used only in "Without PO" mode to add a product row -->
                            <div id="divnopoproduct" class="d-none">
                                <hr>
                                <div class="form-group mb-2">
                                    <label class="small font-weight-bold text-dark">Product*</label>
                                    <select class="form-control form-control-sm selecter2 px-0" name="grnproduct" id="grnproduct">
                                        <option value="">Select</option>
                                    </select>
                                </div>
                                <div class="form-row mb-1">
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Qty*</label>
                                        <input type="text" id="grnnewqty" name="grnnewqty" class="form-control form-control-sm">
                                    </div>
                                    <div class="col">
                                        <label class="small font-weight-bold text-dark">Unit Price</label>
                                        <input type="text" id="grnunitprice" name="grnunitprice" class="form-control form-control-sm" value="0">
                                    </div>
                                </div>
                                <div class="form-group mt-2">
                                    <button type="button" id="btnaddgrnproduct" class="btn btn-outline-primary btn-sm fa-pull-right"><i class="fas fa-plus"></i>&nbsp;Add Product</button>
                                </div>
                            </div>

                            <div class="form-group mt-2">
                                <input name="submitBtn" type="submit" value="Save" id="submitBtn" class="d-none">
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                        <h6 class="title-style small font-weight-bold mt-2"><span>GRN Detail</span></h6>
                        <table class="table table-bordered table-sm table-striped" id="tableGrnList">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="d-none">ProductID</th>
                                    <th class="d-none">Unitprice</th>
                                    <th class="text-right">Unit Price</th>
                                    <th class="text-center">Qty</th>
                                    <th class="d-none">Hidetotal</th>
                                    <th class="text-right">Total</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbodygrncreate"></tbody>
                        </table>
                        <div class="row">
                            <div class="col-sm-12 col-md-9 col-lg-9 col-xl-9 text-right"><h6>Sub Total : </h6></div>
                            <div class="col-sm-12 col-md-3 col-lg-3 col-xl-3 text-right"><h6 id="showSubTotal">0.00</h6></div>

                            <div class="col-sm-12 col-md-9 col-lg-9 col-xl-9 text-right"><h6>VAT Amount : </h6></div>
                            <div class="col-sm-12 col-md-3 col-lg-3 col-xl-3 text-right"><h6 id="showVatAmount">0.00</h6></div>

                            <div class="col-sm-12 col-md-9 col-lg-9 col-xl-9 text-right"><h4>Grand Total : </h4></div>
                            <div class="col-sm-12 col-md-3 col-lg-3 col-xl-3 text-right"><h3 class="text-dark" id="showPrice">0.00</h3></div>

                            <input type="hidden" id="txtShowSubTotal" value="">
                            <input type="hidden" id="txtShowVatAmount" value="">
                            <input type="hidden" id="txtShowPrice" value="">

                            <div class="col-12">
                                <hr class="border-dark">
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right px-5" id="btnSaveGrn"><i class="far fa-save"></i>&nbsp;Save GRN</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal GRN Detail / Approve -->
<div class="modal fade" id="modalgrndetail" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h6 class="modal-title" id="grndetailtitle"></h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="viewgrndetail"></div>
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

<!-- Modal Day End Warning -->
<div class="modal fade" id="warningDayEndModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body bg-danger text-white text-center">
                <div id="viewmessage"></div>
            </div>
            <div class="modal-footer bg-danger rounded-0">
                <a href="dayend.php" class="btn btn-outline-light btn-sm">Go To Day End</a>
            </div>
        </div>
    </div>
</div>
<?php include "include/footerscripts.php"; ?>
<script>
    $(document).ready(function() {
        // checkdayendprocess();

        $('#grnproduct').select2({
            dropdownParent: $('#modalcreategrn'),
            width: '100%',
            placeholder: 'Select Product',
            ajax: {
                url: 'getprocess/getproductselect2.php',
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { searchTerm: params.term };
                },
                processResults: function (data) {
                    return { results: data };
                },
                cache: true
            }
        });

        $('#grnsupplier').select2({
            dropdownParent: $('#modalcreategrn'),
            width: '100%',
            placeholder: 'Select Supplier'
        });

        var grnTable = $('#dataTableGrn').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "pageLength": 25,
            "stateSave": true,
            ajax: {
                url: "scripts/goodreceivelist.php",
                type: "POST",
                cache: true
            },
            "order": [[0, "desc"]],
            "columns": [
                { "data": "idtbl_grn" },
                { "data": "date" },
                {
                    "targets": -1, "className": '', "data": null,
                    "render": function (data, type, full) {
                        return 'GRN-' + full['idtbl_grn'];
                    }
                },
                {
                    "targets": -1, "className": '', "data": null,
                    "render": function (data, type, full) {
                        return (full['porder_id'] && full['porder_id'] > 0) ? 'PO-' + full['porder_id'] : '-';
                    }
                },
                {
                    "targets": -1, "className": '', "data": null,
                    "render": function (data, type, full) {
                        return full['suppliername'] ? full['suppliername'] : '-';
                    }
                },
                { "data": "location" },
                { "data": "invoicenum" },
                { "data": "dispatchnum" },
                {
                    "targets": -1, "className": 'text-right', "data": null,
                    "render": function (data, type, full) {
                        return parseFloat(full['subtotal']).toFixed(2);
                    }
                },
                {
                    "targets": -1, "className": 'text-center', "data": null,
                    "render": function (data, type, full) {
                        // vattype: 1 = Exclusive, 2 = Inclusive
                        var label = (full['vattype'] == 2) ? 'Incl' : 'Excl';
                        return label + ' (' + parseFloat(full['vatpercentage']).toFixed(2) + '%)';
                    }
                },
                {
                    "targets": -1, "className": 'text-right', "data": null,
                    "render": function (data, type, full) {
                        return parseFloat(full['total']).toFixed(2);
                    }
                },
                {
                    "targets": -1, "className": 'text-center', "data": null,
                    "render": function (data, type, full) {
                        if (full['confirm_status'] == 1) {
                            return '<i class="fas fa-check text-success"></i>&nbsp;Approved';
                        }
                        return '<i class="fas fa-times text-danger"></i>&nbsp;Pending';
                    }
                },
                {
                    "targets": -1, "className": 'text-right', "data": null,
                    "render": function (data, type, full) {
                        var button = '';
                        button += '<button class="btn btn-outline-dark btn-sm mr-1 btnviewgrn" data-toggle="tooltip" data-placement="bottom" title="View GRN" id="' + full['idtbl_grn'] + '" name="' + full['confirm_status'] + '"><i class="far fa-eye"></i></button>';

                        if (full['confirm_status'] == 0) {
                            button += '<a href="process/statusgrnconfirm.php?record=' + full['idtbl_grn'] + '" onclick="return grn_confirm()" target="_self" class="btn btn-outline-orange btn-sm mr-1" data-toggle="tooltip" data-placement="bottom" title="Approve GRN"><i class="fas fa-times"></i></a>';
                        } else {
                            button += '<button class="btn btn-outline-success btn-sm mr-1" disabled data-toggle="tooltip" data-placement="bottom" title="Approved"><i class="fas fa-check"></i></button>';
                        }
                        return button;
                    }
                }
            ]
        });

        $('body').tooltip({ selector: '[data-toggle="tooltip"]' });

        // Toggle With PO / Without PO
        $('input[name="grnsource"]').change(function() {
            var withPO = $('#grnsourcepo').is(':checked');
            if (withPO) {
                $('#divponumber').removeClass('d-none');
                $('#divsupplier').addClass('d-none');
                $('#divnopoproduct').addClass('d-none');
                $('#ponumber').prop('required', true);
                $('#grnsupplier').prop('required', false);
            } else {
                $('#divponumber').addClass('d-none');
                $('#divsupplier').removeClass('d-none');
                $('#divnopoproduct').removeClass('d-none');
                $('#ponumber').prop('required', false).val('');
                $('#grnsupplier').prop('required', true);
            }
            $('#tbodygrncreate').html('');
            tabletotal();
        });

        $('#btngrncreate').click(function() {
            $('#modalcreategrn').modal('show');
        });

        $('#modalcreategrn').on('hidden.bs.modal', function() {
            $('#grnFrom')[0].reset();
            $('#grndate').val('<?php echo date("Y-m-d"); ?>');
            $('#grnsourcepo').prop('checked', true).trigger('change');
            $('#vatexclusive').prop('checked', true);
            $('#grnvatpercentage').val('<?php echo $defaultVat; ?>');
            try { $('#grnsupplier').val(null).trigger('change'); } catch(e) {}
            try { $('#grnproduct').val(null).trigger('change'); } catch(e) {}
            $('#tbodygrncreate').html('');
            $('#showSubTotal').html('0.00');
            $('#showVatAmount').html('0.00');
            $('#showPrice').html('0.00');
            $('#txtShowSubTotal').val('');
            $('#txtShowVatAmount').val('');
            $('#txtShowPrice').val('');
        });

        $('#ponumber').change(function(){
            var ponumber = $(this).val();
            if (!ponumber) { $('#tbodygrncreate').html(''); tabletotal(); return; }

            $.ajax({
                type: "POST",
                data: { ponumber : ponumber },
                url: 'getprocess/getporderinfoforgrn.php',
                success: function(result) {
                    $('#tbodygrncreate').html(result);
                    normalizeGrnRows();
                    tabletotal();
                    orderoption();
                }
            });
        });

        // Product price lookup (Without PO mode) - reuses same endpoint as PO create
        $('#grnproduct').change(function () {
            var productID = $(this).val();
            if (!productID) { return; }

            $.ajax({
                type: "POST",
                data: { productID: productID },
                url: 'getprocess/getsalpriceaccoproduct.php',
                success: function (result) {
                    var obj = JSON.parse(result);
                    $('#grnunitprice').val(obj.unitprice);
                    $('#grnnewqty').focus();
                }
            });
        });

        $('#grnnewqty').keyup(function(e) {
            if (e.keyCode === 13) { $('#btnaddgrnproduct').click(); }
        });

        $('#btnaddgrnproduct').click(function() {
            var productID = $('#grnproduct').val();
            var product = $("#grnproduct option:selected").text();
            var unitprice = parseFloat($('#grnunitprice').val());
            var newqty = parseFloat($('#grnnewqty').val());

            if (!productID || !newqty || isNaN(newqty) || newqty <= 0) {
                $('#warningdesc').html('Please select a product and enter a valid quantity.');
                $('#warningModal').modal('show');
                return;
            }
            if (isNaN(unitprice) || unitprice < 0) { unitprice = 0; }

            var total = parseFloat(unitprice * newqty);
            var showtotal = addCommas(parseFloat(total).toFixed(2));

            $('#tbodygrncreate').append(
                '<tr><td>' + product +
                '</td><td class="d-none">' + productID +
                '</td><td class="d-none">' + unitprice +
                '</td><td class="text-right editunitprice">' + parseFloat(unitprice).toFixed(2) +
                '</td><td class="text-center editnewqty">' + newqty +
                '</td><td class="total d-none">' + total +
                '</td><td class="text-right">' + showtotal +
                '</td><td class="text-center no-capture"><button type="button" class="btn btn-outline-danger btn-sm btndeleterow" title="Remove"><i class="fas fa-trash"></i></button></td></tr>'
            );

            $('#grnproduct').val(null).trigger('change');
            $('#grnunitprice').val('0');
            $('#grnnewqty').val('');

            tabletotal();
            orderoption();
        });

        // Recalculate whenever VAT type or VAT % changes
        $('input[name="vattype"]').on('change', function() {
            tabletotal();
        });
        $('#grnvatpercentage').on('input change', function() {
            tabletotal();
        });

        $('#btnSaveGrn').click(function(){
            if (!$("#grnFrom")[0].checkValidity()) {
                $("#submitBtn").click();
                return;
            }

            if ($('#tbodygrncreate tr').length === 0) {
                $('#warningdesc').html('Please add at least one product line before saving.');
                $('#warningModal').modal('show');
                return;
            }

            jsonObj = [];
            $("#tableGrnList tbody tr").each(function() {
                item = {}
                $(this).find('td:not(".no-capture")').each(function(col_idx) {
                    item["col_" + (col_idx + 1)] = $(this).text();
                });
                jsonObj.push(item);
            });

            var withPO = $('#grnsourcepo').is(':checked') ? 1 : 0;

            var grnnum = $('#grnnum').val();
            var ponumber = withPO ? $('#ponumber').val() : '';
            var grnsupplier = withPO ? '' : $('#grnsupplier').val();
            var grndate = $('#grndate').val();
            var grninvoice = $('#grninvoice').val();
            var grndispatch = $('#grndispatch').val();
            var grnvattype = $('input[name="vattype"]:checked').val(); // "1" or "2"
            var grnvatpercentage = $('#grnvatpercentage').val();
            var grnsubtotal = $('#txtShowSubTotal').val();
            var grnvatamount = $('#txtShowVatAmount').val();
            var grnnettotal = $('#txtShowPrice').val();
            var grnlocation = $('#grnlocation').val();

            $.ajax({
                type: "POST",
                data: {
                    tableData: jsonObj,
                    grnnum: grnnum,
                    withpo: withPO,
                    ponumber: ponumber,
                    grnsupplier: grnsupplier,
                    grndate: grndate,
                    grninvoice: grninvoice,
                    grndispatch: grndispatch,
                    grnvattype: grnvattype,
                    grnvatpercentage: grnvatpercentage,
                    grnsubtotal: grnsubtotal,
                    grnvatamount: grnvatamount,
                    grnnettotal: grnnettotal,
                    grnlocation: grnlocation
                },
                url: 'process/grnprocess.php',
                success: function(result) {
                    action(result);
                    $('#modalcreategrn').modal('hide');
                    grnTable.ajax.reload(null, false);
                }
            });
        });

        $('#dataTableGrn tbody').on('click', '.btnviewgrn', function() {
            var grnid = $(this).attr('id');
            var confirmstatus = $(this).attr('name');

            $('#grndetailtitle').html('GRN-' + grnid);
            $('#modalgrndetail').modal('show');

            $.ajax({
                type: "POST",
                data: { grnid: grnid, confirmstatus: confirmstatus },
                url: 'getprocess/getgrndetail.php',
                success: function(result) {
                    $('#viewgrndetail').html(result);
                }
            });
        });
    });

    // Ensures every row in #tbodygrncreate - whether built here in JS (Without PO)
    // or injected from getporderinfoforgrn.php (With PO) - has editable price/qty
    // classes and a delete button, without needing to touch that PHP file.
    function normalizeGrnRows(){
        $('#tbodygrncreate tr').each(function(){
            var tr = $(this);
            var tds = tr.find('td');
            if (tds.length < 7) { return; }

            var priceTd = tds.eq(3);
            var qtyTd = tds.eq(4);

            if (!priceTd.hasClass('editunitprice')) { priceTd.addClass('editunitprice'); }
            if (!qtyTd.hasClass('editnewqty')) { qtyTd.addClass('editnewqty'); }

            if (tds.length < 8) {
                tr.append('<td class="text-center no-capture"><button type="button" class="btn btn-outline-danger btn-sm btndeleterow" title="Remove"><i class="fas fa-trash"></i></button></td>');
            }
        });
    }

    function orderoption(){
        $('#tbodygrncreate').off('click', '.editnewqty').on('click', '.editnewqty', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            makeCellEditable($(this), 'qty');
        });

        $('#tbodygrncreate').off('click', '.editunitprice').on('click', '.editunitprice', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            makeCellEditable($(this), 'price');
        });

        $('#tbodygrncreate').off('click', '.btndeleterow').on('click', '.btndeleterow', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            $(this).closest('tr').remove();
            tabletotal();
        });
    }

    // Turns a qty/price cell into an input, recalculates totals live as the
    // user types, and commits back to plain text on blur or Enter.
    function makeCellEditable(td, type) {
        if (td.data('editing')) return;

        var val = td.text();
        td.empty();
        td.data('editing', true);

        var inputClass = (type === 'qty') ? 'optionnewqty' : 'optionunitprice';
        var $input = $('<input type="text" class="form-control form-control-sm ' + inputClass + '">').val(val);
        $input.appendTo(td);
        $input.trigger('focus');

        function liveRecalc() {
            var newVal = parseFloat($input.val());
            if (isNaN(newVal) || newVal < 0) { newVal = 0; }

            var tr = td.closest('tr');
            var qty, price;

            if (type === 'qty') {
                qty = newVal;
                price = parseFloat(tr.find('td:eq(2)').text()) || 0;
            } else {
                price = newVal;
                qty = parseFloat(tr.find('td:eq(4)').text()) || 0;
            }

            var totnew = price * qty;
            tr.find('td:eq(5)').text(totnew);
            tr.find('td:eq(6)').text(addCommas(totnew.toFixed(2)));

            tabletotal();
        }

        function commit() {
            var newVal = parseFloat($input.val());
            if (isNaN(newVal) || newVal < 0) { newVal = 0; }

            var tr = td.closest('tr');
            td.data('editing', false);

            if (type === 'qty') {
                td.html(newVal);
            } else {
                td.html(newVal.toFixed(2));
                tr.find('td:eq(2)').text(newVal); // hidden unitprice cell used for save
            }

            liveRecalc();
        }

        $input.on('input', liveRecalc);
        $input.on('keyup', function(e) {
            if (e.keyCode === 13) { commit(); }
        });
        $input.on('blur', commit);
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

    function tabletotal(){
        var sum = 0;
        $("#tbodygrncreate .total").each(function(){
            sum += parseFloat($(this).text());
        });
        if (isNaN(sum)) sum = 0;

        var vatPercent = parseFloat($('#grnvatpercentage').val());
        if (isNaN(vatPercent)) vatPercent = 0;
        var isInclusive = $('#vatinclusive').is(':checked');

        var subTotal, vatAmount, grandTotal;

        if (isInclusive) {
            // Line amounts already include VAT
            grandTotal = sum;
            subTotal = vatPercent > 0 ? (sum / (1 + (vatPercent / 100))) : sum;
            vatAmount = grandTotal - subTotal;
        } else {
            // Line amounts are before VAT
            subTotal = sum;
            vatAmount = subTotal * (vatPercent / 100);
            grandTotal = subTotal + vatAmount;
        }

        var showSubTotal = addCommas(subTotal.toFixed(2));
        var showVatAmount = addCommas(vatAmount.toFixed(2));
        var showGrandTotal = addCommas(grandTotal.toFixed(2));

        $('#showSubTotal').html(showSubTotal);
        $('#showVatAmount').html(showVatAmount);
        $('#showPrice').html(showGrandTotal);

        $('#txtShowSubTotal').val(subTotal.toFixed(2));
        $('#txtShowVatAmount').val(vatAmount.toFixed(2));
        $('#txtShowPrice').val(grandTotal.toFixed(2));
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

    function grn_confirm() {
        return confirm("Are you sure you want to approve this GRN? Stock quantities will be updated.");
    }

    function checkdayendprocess(){
        $.ajax({
            type: "POST",
            data: {},
            url: 'getprocess/getstatuslastdayendinfo.php',
            success: function(result) {
                if(result==1){
                    $('#viewmessage').html("Can't create anything, because today transaction is end");
                    $('#warningDayEndModal').modal('show');
                }
                else if(result==0){
                    $('#viewmessage').html("Can't create anythind, because yesterday day end process end not yet.");
                    $('#warningDayEndModal').modal('show');
                }
            }
        });
    }
</script>
<?php include "include/footer.php"; ?>