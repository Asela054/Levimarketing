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
                            <div class="col-sm-12">
                                <h1 class="page-header-title">
                                    <span><i class="fas fa-file"></i>&nbsp; Bin Card</span>
                                </h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid mt-2 p-0 p-2">
                <div class="card">
                    <div class="card-body">

                        <div class="row mb-2">
                            <div class="col-12">
                                <form action="#" method="post" autocomplete="off" id="filterForm">
                                    <div class="form-row align-items-end">
                                        <div class="col-auto">
                                            <label class="small font-weight-bold text-dark mb-1">From Date</label>
                                            <input type="date" class="form-control form-control-sm" id="filterfrom" value="<?php echo date('Y-m-01'); ?>">
                                        </div>
                                        <div class="col-auto">
                                            <label class="small font-weight-bold text-dark mb-1">To Date</label>
                                            <input type="date" class="form-control form-control-sm" id="filterto" value="<?php echo date('Y-m-d'); ?>">
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

                        <div class="scrollbar pb-3" id="style-2">
                            <table class="table table-striped table-bordered table-sm nowrap" id="binCardTable" style="width:100%">
                                <thead class="thead-light">
                                <tr>
                                    <th>PRODUCT NAME</th>
                                    <th class="text-right">SALE PRICE</th>
                                    <th class="text-right">COST PRICE</th>
                                    <th class="text-right">GRN QTY</th>
                                    <th class="text-right">GRN VALUE</th>
                                    <th class="text-right">ISSUE QTY</th>
                                    <th class="text-right">ISSUE VALUE</th>
                                    <th class="text-right">CURRENT STOCK</th>
                                    <th class="text-right">REMAIN VALUE</th>
                                    <th class="text-center">DETAILS</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Total (this page):</strong></td>
                                        <td class="text-right"></td>
                                        <td class="text-right"></td>
                                        <td class="text-right"></td>
                                        <td class="text-right"></td>
                                        <td class="text-right"></td>
                                        <td class="text-right"></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <small class="text-muted">
                            GRN and Issue figures are for the selected date range. Current stock and Remain value (stock &times; cost price) are as of now.
                        </small>
                    </div>
                </div>
            </div>
        </main>
        <?php include "include/footerbar.php"; ?>
    </div>
</div>

<!-- Details modal -->
<div class="modal fade" id="binDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="binDetailTitle">Details</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <h6 class="font-weight-bold">GRN Numbers</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-bordered" id="grnDetailTable">
                        <thead class="thead-light">
                            <tr>
                                <th>GRN No</th><th>Date</th><th>Supplier Invoice</th><th>Dispatch No</th>
                                <th class="text-right">Qty</th><th class="text-right">Unit Price</th><th class="text-right">Value</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <h6 class="font-weight-bold">Invoice Numbers</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered" id="invDetailTable">
                        <thead class="thead-light">
                            <tr>
                                <th>Invoice ID</th><th>Tax Invoice No</th><th>Manual Inv No</th><th>Date</th><th>Customer</th>
                                <th class="text-right">Qty</th><th class="text-right">Free Qty</th><th class="text-right">Value</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include "include/footerscripts.php"; ?>

<script>
var today = new Date().toISOString().slice(0, 10);
var monthStart = today.slice(0, 8) + '01';
var binCardTable;

$(document).ready(function () {

    // money: commas + 2 decimals
    var num  = $.fn.dataTable.render.number(',', '.', 2, '');
    var numf = function (v) { return Number(v || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); };

    // qty: no commas, no trailing zeros
    var qtyf = function (v) { return String(parseFloat(Number(v || 0).toFixed(2))); };
    var qty  = function (data, type) { return (type === 'display' || type === 'filter') ? qtyf(data) : data; };

    function rangeText() {
        var f = $('#filterfrom').val(), t = $('#filterto').val();
        return f === t ? f : f + ' to ' + t;
    }

    binCardTable = $('#binCardTable').DataTable({
        "destroy": true,
        "processing": true,
        "serverSide": true,
        ajax: {
            url: "scripts/rptbincardlist.php",
            type: "POST",
            "data": function (d) {
                d.search_from = $('#filterfrom').val();
                d.search_to   = $('#filterto').val();
            }
        },
        "order": [[0, "asc"]],
        "columns": [
            { "data": "product" },
            { "data": "saleprice",   "className": "text-right", render: num },
            { "data": "costprice",   "className": "text-right", render: num },
            { "data": "grnqty",      "className": "text-right", render: qty },
            { "data": "grnvalue",    "className": "text-right", render: num },
            { "data": "issueqty",    "className": "text-right", render: qty },
            { "data": "issuevalue",  "className": "text-right", render: num },
            { "data": "stockqty",    "className": "text-right", render: qty },
            { "data": "remainvalue", "className": "text-right", render: num },
            {
                "data": "id", "orderable": false, "searchable": false, "className": "text-center",
                "render": function (data, type, row) {
                    if (type !== 'display') { return ''; }
                    return '<button type="button" class="btn btn-outline-primary btn-sm btnView" data-id="' + data +
                           '" data-name="' + $('<div>').text(row.product).html() + '"><i class="fas fa-eye"></i> View</button>';
                }
            }
        ],
        dom: "<'row'<'col-sm-4'B><'col-sm-3'l><'col-sm-5'f>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        responsive: true,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
        buttons: [
            {
                extend: 'pdf', className: 'btn btn-primary btn-sm',
                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                orientation: 'landscape', footer: true,
                exportOptions: { columns: [0,1,2,3,4,5,6,7,8] },
                title: 'Levi Marketing Pvt Ltd',
                filename: function () { return 'Bin Card ' + rangeText(); },
                messageTop: function () { return 'Bin Card - ' + rangeText(); },
                customize: function (doc) {
                    doc.styles.title = { color: 'black', fontSize: '30', alignment: 'center' };
                }
            },
            {
                extend: 'excel', className: 'btn btn-success btn-sm',
                text: '<i class="fas fa-file-excel mr-2"></i> EXCEL',
                exportOptions: { columns: [0,1,2,3,4,5,6,7,8] },
                filename: function () { return 'Bin Card ' + rangeText(); },
                footer: true
            },
            {
                extend: 'csv', className: 'btn btn-info btn-sm',
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                exportOptions: { columns: [0,1,2,3,4,5,6,7,8] },
                filename: function () { return 'Bin Card ' + rangeText(); }
            },
            {
                extend: 'print', className: 'btn btn-warning btn-sm',
                text: '<i class="fas fa-print mr-2"></i> PRINT',
                exportOptions: { columns: [0,1,2,3,4,5,6,7,8] },
                title: 'Levi Marketing Pvt Ltd', footer: true,
                messageTop: function () { return 'Bin Card - ' + rangeText(); }
            }
        ],
        footerCallback: function () {
            var api = this.api();
            var intVal = function (i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };
            // qty columns: 3, 5, 7  |  value columns: 4, 6, 8
            [3, 4, 5, 6, 7, 8].forEach(function (col) {
                var total = api.column(col, { page: 'current' }).data()
                    .reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                var isQty = (col === 3 || col === 5 || col === 7);
                $(api.column(col).footer()).html('<strong>' + (isQty ? qtyf(total) : numf(total)) + '</strong>');
            });
        }
    });

    $('#btnSearch').click(function () { binCardTable.ajax.reload(); });

    $('#btnResetFilter').click(function () {
        $('#filterfrom').val(monthStart);
        $('#filterto').val(today);
        binCardTable.ajax.reload();
    });

    // View GRN / Invoice numbers
    $('#binCardTable tbody').on('click', '.btnView', function () {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#binDetailTitle').text(name + '  (' + rangeText() + ')');
        $('#grnDetailTable tbody').html('<tr><td colspan="7" class="text-center">Loading...</td></tr>');
        $('#invDetailTable tbody').html('<tr><td colspan="8" class="text-center">Loading...</td></tr>');
        $('#binDetailModal').modal('show');

        $.ajax({
            url: 'scripts/rptbincarddetails.php',
            type: 'POST',
            dataType: 'json',
            data: { product_id: id, search_from: $('#filterfrom').val(), search_to: $('#filterto').val() },
            success: function (res) {
                var g = '', i = '';
                $.each(res.grn, function (k, r) {
                    g += '<tr><td>' + r.grnno + '</td><td>' + r.date + '</td><td>' + (r.invoicenum || '') + '</td><td>' + (r.dispatchnum || '') +
                         '</td><td class="text-right">' + qtyf(r.qty) + '</td><td class="text-right">' + numf(r.unitprice) +
                         '</td><td class="text-right">' + numf(r.total) + '</td></tr>';
                });
                $.each(res.inv, function (k, r) {
                    i += '<tr><td>' + r.invid + '</td><td>' + (r.taxinvoice_no || '') + '</td><td>' + (r.manuelinvno || '') +
                         '</td><td>' + r.date + '</td><td>' + (r.customer || '') +
                         '</td><td class="text-right">' + qtyf(r.qty) + '</td><td class="text-right">' + qtyf(r.freeqty) +
                         '</td><td class="text-right">' + numf(r.total) + '</td></tr>';
                });
                $('#grnDetailTable tbody').html(g || '<tr><td colspan="7" class="text-center text-muted">No GRNs in this range</td></tr>');
                $('#invDetailTable tbody').html(i || '<tr><td colspan="8" class="text-center text-muted">No invoices in this range</td></tr>');
            },
            error: function () {
                $('#grnDetailTable tbody').html('<tr><td colspan="7" class="text-center text-danger">Failed to load</td></tr>');
                $('#invDetailTable tbody').html('<tr><td colspan="8" class="text-center text-danger">Failed to load</td></tr>');
            }
        });
    });
});
</script>

<?php include "include/footer.php"; ?>