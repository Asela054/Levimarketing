<?php 
include "include/header.php"; 

// Supplier list for the filter dropdown
$sqlsupplier = "SELECT `idtbl_supplier`, `suppliername` FROM `tbl_supplier` WHERE `status` = 1 ORDER BY `suppliername` ASC";
$resultsupplier = $conn->query($sqlsupplier);

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
                                    <span class="bi bi-cash-coin">&nbsp; Supplier Payment Report</span>
                                </h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid mt-2 p-0 p-2">
                <div class="card">
                    <div class="card-body">

                        <!-- Filters -->
                        <div class="row mb-2">
                            <div class="col-12">
                                <form action="#" method="post" autocomplete="off" id="filterForm">
                                    <div class="form-row align-items-end">

                                        <div class="col-auto" style="min-width: 240px;">
                                            <label class="small font-weight-bold text-dark mb-1">Supplier</label>
                                            <select class="form-control form-control-sm selecter2 px-0" id="filtersupplier">
                                                <option value="">All Suppliers</option>
                                                <?php if ($resultsupplier->num_rows > 0) { while ($rowsupplier = $resultsupplier->fetch_assoc()) { ?>
                                                <option value="<?php echo $rowsupplier['idtbl_supplier']; ?>"><?php echo $rowsupplier['suppliername']; ?></option>
                                                <?php }} ?>
                                            </select>
                                        </div>

                                        <div class="col-auto">
                                            <label class="small font-weight-bold text-dark mb-1">Payment Method</label>
                                            <select id="filterpaymethod" class="form-control form-control-sm">
                                                <option value="">All Methods</option>
                                                <option value="1">Cash</option>
                                                <option value="2">Cheque</option>
                                                <option value="3">Bank Transfer</option>
                                            </select>
                                        </div>

                                        <div class="col-auto">
                                            <label class="small font-weight-bold text-dark mb-1">Status</label>
                                            <select id="filterpaystatus" class="form-control form-control-sm">
                                                <option value="">All Status</option>
                                                <option value="pending">Pending</option>
                                                <option value="approved">Approved</option>
                                                <option value="cancelled">Cancelled</option>
                                            </select>
                                        </div>

                                        <div class="col-auto">
                                            <button type="button" class="btn btn-primary btn-sm" id="btnSearch"><i class="fas fa-search"></i>&nbsp;Search</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnResetFilter"><i class="fas fa-redo"></i>&nbsp;Reset</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <hr class="border-dark">

                        <!-- Summary (totals for the active filters, all pages) -->
                        <div class="row mb-3">
                            <div class="col-md-4 mb-2">
                                <div class="border rounded p-2 bg-light">
                                    <div class="small text-muted font-weight-bold">APPROVED</div>
                                    <div class="h5 mb-0 text-success" id="sumApproved">Rs 0.00</div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="border rounded p-2 bg-light">
                                    <div class="small text-muted font-weight-bold">PENDING</div>
                                    <div class="h5 mb-0 text-warning" id="sumPending">Rs 0.00</div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="border rounded p-2 bg-light">
                                    <div class="small text-muted font-weight-bold">CANCELLED</div>
                                    <div class="h5 mb-0 text-danger" id="sumCancelled">Rs 0.00</div>
                                </div>
                            </div>
                        </div>

                        <div class="scrollbar pb-3" id="style-2">
                            <table class="table table-striped table-bordered table-sm nowrap" id="supplierPaymentTable" style="width:100%">
                                <thead class="thead-light">
                                <tr>
                                    <th>PAYMENT NO</th>
                                    <th>DATE</th>
                                    <th>SUPPLIER</th>
                                    <th>METHOD</th>
                                    <th>CHEQUE NO</th>
                                    <th>CHEQUE DATE</th>
                                    <th>BANK REF</th>
                                    <th>AMOUNT</th>
                                    <th>STATUS</th>
                                    <th>APPROVED ON</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="6"></th>
                                    <th style="text-align:right">Page Total:</th>
                                    <th class="text-right"></th>
                                    <th colspan="2"></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php include "include/footerbar.php"; ?>
    </div>
</div>
<?php include "include/footerscripts.php"; ?>

<script>

let today = new Date().toISOString().slice(0, 10);
var supplierPaymentTable;

function formatMoney(v) {
    var n = parseFloat(v) || 0;
    return 'Rs ' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

$(document).ready(function () {

    $('#filtersupplier').select2({
        width: '100%',
        placeholder: 'All Suppliers',
        allowClear: true
    });

    supplierPaymentTable = $('#supplierPaymentTable').DataTable({
        "destroy": true,
        "processing": true,
        "serverSide": true,
        ajax: {
            url: "scripts/supplierpaymentreportlist.php",
            type: "POST",
            "data": function (d) {
                d.supplier  = $('#filtersupplier').val();
                d.paymethod = $('#filterpaymethod').val();
                d.paystatus = $('#filterpaystatus').val();
            }
        },
        "order": [
            [0, "desc"]
        ],
        "columns": [
            {
                "data": "idtbl_supplier_payment",
                "render": function (data, type, full) {
                    return (type === 'display' || type === 'filter') ? 'SP-' + data : data;
                }
            },
            { "data": "date" },
            {
                "data": "suppliername",
                "render": function (data) { return data ? data : '-'; }
            },
            {
                "data": "paymethod",
                "render": function (data) {
                    switch (parseInt(data)) {
                        case 1: return 'Cash';
                        case 2: return 'Cheque';
                        case 3: return 'Bank Transfer';
                        default: return '-';
                    }
                }
            },
            {
                "data": "chequeno",
                "render": function (data) { return data ? data : '-'; }
            },
            {
                "data": "chequedate",
                "render": function (data) { return data ? data : '-'; }
            },
            {
                "data": "bankref",
                "render": function (data) { return data ? data : '-'; }
            },
            {
                "data": "totalpayment",
                "className": 'text-right',
                render: $.fn.dataTable.render.number(',', '.', 2, '')
            },
            {
                "data": "status",
                "render": function (data, type, full) {
                    var s = parseInt(data);
                    var c = parseInt(full.confirm_status);
                    var label, cls;
                    if (s === 3)              { label = 'Cancelled'; cls = 'badge-danger'; }
                    else if (s === 1 && c === 1) { label = 'Approved';  cls = 'badge-success'; }
                    else if (s === 1)         { label = 'Pending';   cls = 'badge-warning'; }
                    else                      { label = '-';         cls = 'badge-secondary'; }
                    if (type === 'display') {
                        return '<span class="badge ' + cls + '">' + label + '</span>';
                    }
                    return label;
                }
            },
            {
                "data": "approvedatetime",
                "render": function (data) { return data ? data : '-'; }
            }
        ],
        dom: "<'row'<'col-sm-4'B><'col-sm-3'l><'col-sm-5'f>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        responsive: true,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, 'All'],
        ],
        buttons: [
            {
                extend: 'pdf',
                className: 'btn btn-primary btn-sm',
                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                title: 'Levi Marketing Pvt Ltd',
                filename: 'Supplier Payment Report' + today,
                footer: true,
                orientation: 'landscape',
                messageTop: { text: 'Supplier Payment Report',
                    fontSize: 15,
                    bold: true,
                    alignment: 'center' },
                customize: function (doc) {
                    doc.styles.title = {
                        color: 'black',
                        fontSize: '30',
                        alignment: 'center',
                    }
                }
            },
            {
                extend: 'excel',
                className: 'btn btn-success btn-sm',
                filename: 'Supplier Payment Report' + today,
                text: '<i class="fas fa-file-excel mr-2"></i> EXCEL',
                footer: true
            },
            {
                extend: 'csv',
                className: 'btn btn-info btn-sm',
                filename: 'Supplier Payment Report' + today,
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                footer: true
            },
            {
                extend: 'print',
                className: 'btn btn-warning btn-sm',
                text: '<i class="fas fa-print mr-2"></i> PRINT',
                title: 'Levi Marketing Pvt Ltd',
                filename: 'Supplier Payment Report' + today,
                footer: true,
                messageTop: 'Supplier Payment Report',
                customize: function (doc) {
                    doc.styles.title = {
                        color: 'black',
                        fontSize: '30',
                        alignment: 'center',
                    }
                }
            }
        ],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();

            // Page total (column 7), excluding cancelled payments
            var pageTotal = 0;
            api.rows({ page: 'current' }).data().each(function (r) {
                if (parseInt(r.status) !== 3) {
                    pageTotal += parseFloat(r.totalpayment) || 0;
                }
            });
            $(api.column(7).footer()).html('Rs ' + pageTotal.toFixed(2));
        },
        drawCallback: function (settings) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });

    // Summary cards come back in the same JSON response as the table data
    $('#supplierPaymentTable').on('xhr.dt', function (e, settings, json, xhr) {
        if (json && json.summary) {
            $('#sumApproved').text(formatMoney(json.summary.approved));
            $('#sumPending').text(formatMoney(json.summary.pending));
            $('#sumCancelled').text(formatMoney(json.summary.cancelled));
        }
    });

    $('#btnSearch').click(function () {
        supplierPaymentTable.ajax.reload();
    });

    $('#btnResetFilter').click(function () {
        $('#filterForm')[0].reset();
        $('#filtersupplier').val(null).trigger('change');
        $('#filterpaymethod').val('');
        $('#filterpaystatus').val('');
        supplierPaymentTable.ajax.reload();
    });
});
</script>

<?php include "include/footer.php"; ?>