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
                                    <span> <i class="fas fa-file"></i>&nbsp; Full Detail Sale Report</span>
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
                                            <label class="small font-weight-bold text-dark mb-1">Month</label>
                                            <input type="month" class="form-control form-control-sm" id="filtermonth" value="<?php echo date('Y-m'); ?>">
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
                            <table class="table table-striped table-bordered table-sm nowrap" id="fullSaleTable" style="width:100%">
                                <thead class="thead-light">
                                <tr>
                                    <th>INVOICE ID</th>
                                    <th>DATE</th>
                                    <th>CUSTOMER</th>
                                    <th>SALE TYPE</th>
                                    <th>PRODUCT</th>
                                    <th class="text-right">QTY</th>
                                    <th class="text-right">FREE QTY</th>
                                    <th class="text-right">UNIT PRICE</th>
                                    <th class="text-right">SALE PRICE</th>
                                    <th class="text-right">DISCOUNT</th>
                                    <th class="text-right">LINE TOTAL</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10" class="text-right"><strong>Page Total:</strong></td>
                                        <td class="text-right"></td>
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
var currentMonth = new Date().toISOString().slice(0, 7);
var fullSaleTable;

$(document).ready(function () {

    var num = $.fn.dataTable.render.number(',', '.', 2, '');

    fullSaleTable = $('#fullSaleTable').DataTable({
        "destroy": true,
        "processing": true,
        "serverSide": true,
        ajax: {
            url: "scripts/rptfullsalereportlist.php",
            type: "POST",
            "data": function (d) {
                d.search_month = $('#filtermonth').val();
            }
        },
        "order": [[1, "desc"]],
        "columns": [
            {
                "data": "id",
                "render": function (data, type) {
                    return (type === 'display' || type === 'filter') ? (data ? 'INV-' + data : '-') : data;
                }
            },
            { "data": "date" },
            { "data": "name" },
            {
                "data": "saletype",
                "render": function (data, type) {
                    if (type !== 'display') return data;
                    return data == 1 ? 'Retail Sale' : 'Whole Sale';
                }
            },
            { "data": "product" },
            { "data": "qty", "className": "text-right" },
            { "data": "freeqty", "className": "text-right" },
            { "data": "unitprice", "className": "text-right", render: num },
            { "data": "saleprice", "className": "text-right", render: num },
            { "data": "discount", "className": "text-right", render: num },
            { "data": "linetotal", "className": "text-right", render: num }
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
                filename: function () { return 'Full Sale Report ' + $('#filtermonth').val(); },
                messageTop: function () { return 'Full Detail Sale Report - ' + $('#filtermonth').val(); },
                customize: function (doc) {
                    doc.styles.title = { color: 'black', fontSize: '30', alignment: 'center' };
                }
            },
            {
                extend: 'excel',
                className: 'btn btn-success btn-sm',
                text: '<i class="fas fa-file-excel mr-2"></i> EXCEL',
                filename: function () { return 'Full Sale Report ' + $('#filtermonth').val(); },
                footer: true
            },
            {
                extend: 'csv',
                className: 'btn btn-info btn-sm',
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                filename: function () { return 'Full Sale Report ' + $('#filtermonth').val(); }
            },
            {
                extend: 'print',
                className: 'btn btn-warning btn-sm',
                text: '<i class="fas fa-print mr-2"></i> PRINT',
                title: 'Levi Marketing Pvt Ltd',
                footer: true,
                messageTop: function () { return 'Full Detail Sale Report - ' + $('#filtermonth').val(); }
            }
        ],
        footerCallback: function () {
            var api = this.api();
            var intVal = function (i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };
            var pageTotal = api.column(10, { page: 'current' }).data()
                .reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            $(api.column(10).footer()).html('<strong>Rs ' + pageTotal.toFixed(2) + '</strong>');
        }
    });

    $('#btnSearch').click(function () {
        fullSaleTable.ajax.reload();
    });

    $('#btnResetFilter').click(function () {
        $('#filtermonth').val(currentMonth);
        fullSaleTable.ajax.reload();
    });
});
</script>

<?php include "include/footer.php"; ?>