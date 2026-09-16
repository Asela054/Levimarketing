<?php 
include "include/header.php"; 

// Customer list for the filter dropdown
$sqlcustomer = "SELECT `idtbl_customer`, `type`, `name`, `nic`, `phone`, `email`, `address`, `vat_num`, `s_vat`, `creditlimit`, `credittype`, `creditperiod`, `emergencydate`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_area_idtbl_area` FROM `tbl_customer` WHERE `status` = 1 ORDER BY `name` ASC";
$resultcustomer = $conn->query($sqlcustomer);

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
                                    <span class="bi bi-receipt">&nbsp; Invoice Payment</span>
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
                                            <label class="small font-weight-bold text-dark mb-1">Filter By*</label><br>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="filterdate" name="filtertype" class="custom-control-input" value="date" checked>
                                                <label class="custom-control-label" for="filterdate">Date</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="filterweek" name="filtertype" class="custom-control-input" value="week">
                                                <label class="custom-control-label" for="filterweek">Week</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="filtermonth" name="filtertype" class="custom-control-input" value="month">
                                                <label class="custom-control-label" for="filtermonth">Month</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="filterrange" name="filtertype" class="custom-control-input" value="range">
                                                <label class="custom-control-label" for="filterrange">Date Range</label>
                                            </div>
                                        </div>

                                        <div class="col-auto" id="divsearchdate">
                                            <label class="small font-weight-bold text-dark mb-1">Date</label>
                                            <input type="date" class="form-control form-control-sm" id="date" value="<?php echo date('Y-m-d'); ?>">
                                        </div>

                                        <div class="col-auto d-none" id="divsearchweek">
                                            <label class="small font-weight-bold text-dark mb-1">Week</label>
                                            <input type="week" class="form-control form-control-sm" id="week">
                                        </div>

                                        <div class="col-auto d-none" id="divsearchmonth">
                                            <label class="small font-weight-bold text-dark mb-1">Month</label>
                                            <input type="month" class="form-control form-control-sm" id="month">
                                        </div>

                                        <div class="col-auto d-none" id="divsearchfromdate">
                                            <label class="small font-weight-bold text-dark mb-1">From Date</label>
                                            <input type="date" class="form-control form-control-sm" id="date_from">
                                        </div>
                                        <div class="col-auto d-none" id="divsearchtodate">
                                            <label class="small font-weight-bold text-dark mb-1">To Date</label>
                                            <input type="date" class="form-control form-control-sm" id="date_to">
                                        </div>

                                        <div class="col-auto" style="min-width: 220px;">
                                            <label class="small font-weight-bold text-dark mb-1">Customer</label>
                                            <select class="form-control form-control-sm selecter2 px-0" id="filtercustomer">
                                                <option value="">All Customers</option>
                                                <?php if ($resultcustomer->num_rows > 0) { while ($rowcustomer = $resultcustomer->fetch_assoc()) { ?>
                                                <option value="<?php echo $rowcustomer['idtbl_customer']; ?>"><?php echo $rowcustomer['name']; ?></option>
                                                <?php }} ?>
                                            </select>
                                        </div>

                                        <div class="col-auto">
                                            <label class="small font-weight-bold text-dark mb-1">Payment Method</label>
                                            <select id="filterpaymentmethod" class="form-control form-control-sm">
                                                <option value="">All Payment Methods</option>
                                                <option value="1">Cash</option>
                                                <option value="2">Card</option>
                                                <option value="3">Cheque</option>
                                                <option value="4">Online Transfer</option>
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

                        <div class="scrollbar pb-3" id="style-2">
                            <table class="table table-striped table-bordered table-sm nowrap" id="invoiceDetailTable" style="width:100%">
                                <thead class="thead-light">
                                <tr>
                                    <th>PAYMENT ID</th>
                                    <th>INVOICE ID</th>
                                    <th>CUSTOMER</th>
                                    <th>DATE</th>
                                    <th>TOTAL</th>
                                    <th>DISCOUNT</th>
                                    <th>PAYAMOUNT</th>
                                    <th>PAYMENT</th>
                                    <th>BALANCE</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="5"></th>
                                    <th style="text-align:right">Total:</th>
                                    <th class="text-right"></th>
                                    <th class="text-right"></th>
                                    <th class="text-right"></th>
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

let today = new Date().toISOString().slice(0, 10)
var invoiceDetailTable;

$(document).ready(function () {

    $('#filtercustomer').select2({
        width: '100%',
        placeholder: 'All Customers',
        allowClear: true
    });

    invoiceDetailTable = $('#invoiceDetailTable').DataTable( {
        "destroy": true,
        "processing": true,
        "serverSide": true,
        ajax: {
            url: "scripts/invoicepaymentlist.php",
            type: "POST",
            "data": function ( d ) {
                var filtertype = $('input[name="filtertype"]:checked').val();

                if (filtertype === 'date') {
                    d.search_date = $('#date').val();
                } else if (filtertype === 'week') {
                    d.search_week = $('#week').val();
                } else if (filtertype === 'month') {
                    d.search_month = $('#month').val();
                } else if (filtertype === 'range') {
                    d.search_from_date = $('#date_from').val();
                    d.search_to_date = $('#date_to').val();
                }

                d.search_customer = $('#filtercustomer').val();
                d.filterpaymentmethod = $('#filterpaymentmethod').val();
            }
        },
        "order": [
            [0, "desc"]
        ],
        "columns": [
            { "data": "idtbl_invoice_payment" },
            {
                "data": "invoiceno",
                "render": function (data, type, full) {
                    if (type === 'display' || type === 'filter') {
                        return data ? ('INV-' + data) : '-';
                    }
                    return data;
                }
            },
            {
                "data": "customername",
                "render": function (data, type, full) {
                    return data ? data : '-';
                }
            },
            { "data": "date" },
            {
                "data": "total",
                "className": 'text-right',
                render: $.fn.dataTable.render.number(',', '.', 2, '')
            },
            {
                "data": "discount",
                "className": 'text-right',
                render: $.fn.dataTable.render.number(',', '.', 2, '')
            },
            {
                "data": "payamount",
                "className": 'text-right',
                render: $.fn.dataTable.render.number(',', '.', 2, '')
            },
            {
                "data": "payment",
                "className": 'text-right',
                render: $.fn.dataTable.render.number(',', '.', 2, '')
            },
            {
                "data": "balance",
                "className": 'text-right',
                render: $.fn.dataTable.render.number(',', '.', 2, '')
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
                filename: 'Invoice Payment Report'+today,
                footer: true,
                messageTop: { text: 'Invoice Payment Report',
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
                filename: 'Invoice Payment Report'+today,
                text: '<i class="fas fa-file-excel mr-2"></i> EXCEL',
                footer: true
            },
            {
                extend: 'csv',
                className: 'btn btn-info btn-sm',
                filename: 'Invoice Payment Report'+today,
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                footer: true
            },
            {
                extend: 'print',
                className: 'btn btn-warning btn-sm',
                text: '<i class="fas fa-print mr-2"></i> PRINT',
                title: 'Levi Marketing Pvt Ltd',
                filename: 'Invoice Payment Report'+today,
                footer: true,
                messageTop: 'Invoice Payment Report',
                customize: function (doc) {
                    doc.styles.title = {
                        color: 'black',
                        fontSize: '30',
                        alignment: 'center',
                    }
                }
            }
        ],
        footerCallback : function ( row, data, start, end, display ) {
            var api = this.api();

            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };

            // Payamount total (column 6) over the current page
            var payamount_pageTotal = api
                .column( 6, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            $( api.column( 6 ).footer() ).html( 'Rs ' + payamount_pageTotal.toFixed(2) );

            // Payment total (column 7) over the current page
            var payment_pageTotal = api
                .column( 7, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            $( api.column( 7 ).footer() ).html( 'Rs ' + payment_pageTotal.toFixed(2) );

            // Balance total (column 8) over the current page
            var balance_pageTotal = api
                .column( 8, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            $( api.column( 8 ).footer() ).html( 'Rs ' + balance_pageTotal.toFixed(2) );
        },
        drawCallback: function (settings) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    } );

    // Toggle date-type inputs
    $('input[name="filtertype"]').change(function() {
        $('#divsearchdate, #divsearchweek, #divsearchmonth, #divsearchfromdate, #divsearchtodate').addClass('d-none');

        var filtertype = $(this).val();
        if (filtertype === 'date') {
            $('#divsearchdate').removeClass('d-none');
        } else if (filtertype === 'week') {
            $('#divsearchweek').removeClass('d-none');
        } else if (filtertype === 'month') {
            $('#divsearchmonth').removeClass('d-none');
        } else if (filtertype === 'range') {
            $('#divsearchfromdate, #divsearchtodate').removeClass('d-none');
        }
    });

    $('#btnSearch').click(function() {
        invoiceDetailTable.ajax.reload();
    });

    $('#btnResetFilter').click(function() {
        $('#filterForm')[0].reset();
        $('#filterdate').prop('checked', true).trigger('change');
        $('#date').val(today);
        $('#filtercustomer').val(null).trigger('change');
        $('#filterpaymentmethod').val('');
        invoiceDetailTable.ajax.reload();
    });
});
</script>

<?php include "include/footer.php"; ?>