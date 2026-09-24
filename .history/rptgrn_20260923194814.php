<?php 
include "include/header.php"; 

include "include/topnavbar.php"; 
?>

<style>
    content-display{
        display: none;
    }
</style>


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
                                    <span><i class="fas fa-warehouse"></i>&nbsp; GRN INFO</span>
                                </h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid mt-2 p-0 p-2">
                <div class="card">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-12">
                                <form id="search">
                                    <div class="form-row">
                                        <div class="col-3">
                                            <label class="small font-weight-bold text-dark">GRN Number</label>
                                            <input type="text" class="form-control form-control-sm" name="grnno" id="grnno" placeholder="e.g. GRN-12" autocomplete="off">
                                        </div>
                                        <div class="col-3 mt-2">&nbsp;<br>
                                            <button type="submit" class="btn btn-primary btn-sm" id="btnSearch"><i class="fas fa-search"></i>&nbsp;Search</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnreset"><i class="fas fa-redo"></i>&nbsp;Reset</button>

                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-12">
                                <hr class="border-dark">
                                <div class="scrollbar pb-3" id="style-2">
                                    <table class="table table-striped table-bordered table-sm nowrap" id="invoiceDetailTable" style="width:100%">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>DATE</th>
                                                <th>GRN NO</th>
                                                <th>INVOICE NO</th>
                                                <th>DISPATCH NO</th>
                                                <th>PRODUCT</th>
                                                <th>TYPE</th>
                                                <th>QTY</th>
                                                <th>UNIT PRICE</th>
                                                <th>TOTAL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="8"></th>
                                                <th style="text-align:right">Total:</th>
                                                <th class="text-right"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
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
<?php include "include/footerscripts.php"; ?>

<script>
let today = new Date().toISOString().slice(0, 10);

$(document).ready(function () {

    var grnTable = $('#invoiceDetailTable').DataTable({
        "destroy": true,
        "processing": true,
        "serverSide": true,
        ajax: {
            url: "scripts/grnlist.php",
            type: "POST",
            "data": function (d) {
                return $.extend({}, d, {
                    "search_grn": $("#grnno").val()
                });
            }
        },
        "order": [[0, "desc"]],
        "columns": [
            { "data": "idtbl_grndetail" },
            { "data": "date" },
            {
                "data": "idtbl_grn",
                "render": function (data) {
                    return 'GRN-' + data;
                }
            },
            { "data": "invoicenum" },
            { "data": "dispatchnum" },
            { "data": "product_name" },
            { "data": "type" },
            { "data": "qty" },
            { "className": 'text-right', "data": "unitprice", "render": formatMoney },
            { "className": 'text-right', "data": "total", "render": formatMoney }
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
                text: '<i class="fas fa-file-pdf mr-2"></i>PDF',
                title: 'Levi Marketing',
                filename: 'GRN Information Report' + today,
                footer: true,
                messageTop: { text: 'GRN Information Report', fontSize: 15, bold: true, alignment: 'center' },
                customize: function (doc) {
                    doc.styles.title = { color: 'black', fontSize: '30', alignment: 'center' }
                }
            },
            {
                extend: 'excel',
                className: 'btn btn-success btn-sm',
                filename: 'Levi Marketing Report' + today,
                text: '<i class="fas fa-file-excel mr-2"></i> EXCEL',
                footer: true
            },
            {
                extend: 'csv',
                className: 'btn btn-info btn-sm',
                filename: 'Levi Marketing Report' + today,
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                footer: true
            },
            {
                extend: 'print',
                className: 'btn btn-warning btn-sm',
                text: '<i class="fas fa-print mr-2"></i> PRINT',
                title: 'Levi Marketing Pvt Ltd',
                filename: 'GRN Information Report' + today,
                footer: true,
                messageTop: 'GRN Information Report',
                customize: function (doc) {
                    doc.styles.title = { color: 'black', fontSize: '30', alignment: 'center' }
                }
            }
        ],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();

            // Strip commas/currency symbols so values can be summed
            var intVal = function (i) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '') * 1 :
                    typeof i === 'number' ? i : 0;
            };

            // Total column is index 9
            var pageTotal = api
                .column(9, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            $(api.column(9).footer()).html('Rs ' + addCommas(pageTotal.toFixed(2)));
        },
        drawCallback: function (settings) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });

    $("#search").submit(function (event) {
        event.preventDefault();
        grnTable.ajax.reload();
    });

    $("#btnreset").click(function () {
        $("#grnno").val('');
        grnTable.ajax.reload();
    });
});

function addCommas(nStr) {
    nStr += '';
    var x = nStr.split('.');
    var x1 = x[0];
    var x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

// Formats for display only, so sorting still uses the raw number
function formatMoney(data, type) {
    if (type !== 'display') { return data; }
    var n = parseFloat(data);
    return addCommas((isNaN(n) ? 0 : n).toFixed(2));
}
</script>

<?php include "include/footer.php"; ?>