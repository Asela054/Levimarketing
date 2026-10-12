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
                                    <span> <i class="fas fa-file"></i>&nbsp; Daily Sales &amp; Payment Reconciliation (දෛනික විකුණුම් සහ මුදල් ලැබීම්)</span>
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
                                        <div class="col-auto">
                                            <label class="small font-weight-bold text-dark mb-1">From Date</label>
                                            <input type="date" class="form-control form-control-sm" id="filterfrom" value="<?php echo date('Y-m-d'); ?>">
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
                            <table class="table table-striped table-bordered table-sm nowrap" id="dailyReconTable" style="width:100%">
                                <thead class="thead-light">
                                <tr>
                                    <th>DATE</th>
                                    <th>CUSTOMER</th>
                                    <th>ITEM / PRODUCT</th>
                                    <th class="text-right">QUANTITY</th>
                                    <th class="text-right">RATE PER UNIT (LKR)</th>
                                    <th class="text-right">TOTAL SALES VALUE (LKR)</th>
                                    <th class="text-right">PAYMENT RECEIVED (LKR)</th>
                                    <th>PAYMENT METHOD</th>
                                    <th class="text-right">OUTSTANDING / BALANCE (LKR)</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>Total (this page):</strong></td>
                                        <td class="text-right"></td>
                                        <td class="text-right"></td>
                                        <td></td>
                                        <td class="text-right"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <small class="text-muted">
                            Payment received, method and outstanding are shown on the first line of each invoice only.
                        </small>
                    </div>
                </div>
            </div>
        </main>
        <?php include "include/footerbar.php"; ?>
    </div>
</div>
<?php include "include/footerscripts.php"; ?>

<script>
var today = new Date().toISOString().slice(0, 10);
var dailyReconTable;

$(document).ready(function () {

    var num = $.fn.dataTable.render.number(',', '.', 2, '');

    function rangeText() {
        var f = $('#filterfrom').val(), t = $('#filterto').val();
        return f === t ? f : f + ' to ' + t;
    }

    dailyReconTable = $('#dailyReconTable').DataTable({
        "destroy": true,
        "processing": true,
        "serverSide": true,
        ajax: {
            url: "scripts/rptdailysalespaymentlist.php",
            type: "POST",
            "data": function (d) {
                d.search_from = $('#filterfrom').val();
                d.search_to   = $('#filterto').val();
            }
        },
        "order": [[0, "desc"]],
        "columns": [
            { "data": "date" },
            { "data": "name" },
            { "data": "product" },
            { "data": "qty", "className": "text-right" },
            { "data": "rate", "className": "text-right", render: num },
            { "data": "linetotal", "className": "text-right", render: num },
            { "data": "paid", "className": "text-right", render: num },
            { "data": "method" },
            { "data": "balance", "className": "text-right", render: num }
        ],
        dom: "<'row'<'col-sm-4'B><'col-sm-3'l><'col-sm-5'f>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        responsive: true,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
        buttons: [
            {
                extend: 'pdf',
                className: 'btn btn-primary btn-sm',
                text: '<i class="fas fa-file-pdf mr-2"></i> PDF',
                orientation: 'landscape',
                footer: true,
                title: 'Levi Marketing Pvt Ltd',
                filename: function () { return 'Daily Sales Payment Reconciliation ' + rangeText(); },
                messageTop: function () { return 'Daily Sales & Payment Reconciliation - ' + rangeText(); },
                customize: function (doc) {
                    doc.styles.title = { color: 'black', fontSize: '30', alignment: 'center' };
                }
            },
            {
                extend: 'excel',
                className: 'btn btn-success btn-sm',
                text: '<i class="fas fa-file-excel mr-2"></i> EXCEL',
                filename: function () { return 'Daily Sales Payment Reconciliation ' + rangeText(); },
                footer: true
            },
            {
                extend: 'csv',
                className: 'btn btn-info btn-sm',
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                filename: function () { return 'Daily Sales Payment Reconciliation ' + rangeText(); }
            },
            {
                extend: 'print',
                className: 'btn btn-warning btn-sm',
                text: '<i class="fas fa-print mr-2"></i> PRINT',
                title: 'Levi Marketing Pvt Ltd',
                footer: true,
                messageTop: function () { return 'Daily Sales & Payment Reconciliation - ' + rangeText(); }
            }
        ],
        footerCallback: function () {
            var api = this.api();
            var intVal = function (i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };
            var fmt = function (v) {
                return '<strong>Rs ' + v.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</strong>';
            };
            [5, 6, 8].forEach(function (col) {
                var total = api.column(col, { page: 'current' }).data()
                    .reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                $(api.column(col).footer()).html(fmt(total));
            });
        }
    });

    $('#btnSearch').click(function () {
        dailyReconTable.ajax.reload();
    });

    $('#btnResetFilter').click(function () {
        $('#filterfrom').val(today);
        $('#filterto').val(today);
        dailyReconTable.ajax.reload();
    });
});
</script>

<?php include "include/footer.php"; ?>