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
                                    <span class="bi bi-graph-up"><i class="fas fa-hand-holding-usd"></i>&nbsp; Lubricant Customer Credit Analysis</span>
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
                                            <label class="small font-weight-bold text-dark">Customer*</label>
                                            <div class="input-group input-group-sm">
                                                <select class="form-control form-control-sm" name="customer_id" id="customer_id">
                                                    <option value="">All Customers</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="small font-weight-bold text-dark">From Date*</label>
                                            <input type="date" class="form-control form-control-sm" placeholder="" name="date_from" id="date_from">
                                        </div>
                                        <div class="col-2">
                                            <label class="small font-weight-bold text-dark">To Date*</label>
                                            <input type="date" class="form-control form-control-sm" placeholder="" name="date_to" id="date_to">
                                        </div>
                                        <div class="col-2">&nbsp;<br>
                                            <button type="submit" class="btn btn-info mb-2"><span id="boot-icon" class="bi bi-search" style="font-size: 15px;">&nbsp;Search</span></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-12">
                                <hr class="border-dark">
                                <div class="scrollbar pb-3" id="style-2">
                                    <table class="table table-striped table-bordered table-sm nowrap" id="creditAnalysisTable" style="width:100%">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Customer Name</th>
                                                <th>Invoice Date</th>
                                                <th>Invoice No.</th>
                                                <th class="text-right">Invoice Amount (Rs.)</th>
                                                <th class="text-right">Amount Paid (Rs.)</th>
                                                <th class="text-right">Outstanding (Rs.)</th>
                                                <th>Due Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" style="text-align:right">Total:</th>
                                                <th class="text-right"></th>
                                                <th class="text-right"></th>
                                                <th class="text-right"></th>
                                                <th></th>
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

<script type="text/javascript">
let today = new Date().toISOString().slice(0, 10);

// Load customers in dropdown
$(document).ready(function() {
    // Set default dates
    let fromDate = new Date();
    fromDate.setMonth(fromDate.getMonth() - 1);
    $('#date_from').val(fromDate.toISOString().slice(0, 10));
    $('#date_to').val(today);

    $.ajax({
        url: 'getprocess/getcustomerselect2.php',
        type: 'POST',
        dataType: 'json',
        data: { searchTerm: '' },
        success: function(data) {
            let options = '<option value="">All Customers</option>';
            if (data && data.length > 0) {
                $.each(data, function(key, value) {
                    options += '<option value="' + value.id + '">' + value.text + '</option>';
                });
            }
            $('#customer_id').html(options);
        },
        error: function() {
            console.log('Error loading customers');
        }
    });

    // Initialize DataTable
    initializeTable();
});

function initializeTable() {
    $("#search").submit(function (event) {
        event.preventDefault();

        $('#creditAnalysisTable').DataTable( {
            "destroy": true,
            "processing": true,
            "serverSide": true,
            ajax: {
                url: "scripts/customercreditanalysislist.php",
                type: "POST",
                "data": function ( d ) {
                    return $.extend( {}, d, {
                        "search_customer_id": $("#customer_id").val(),
                        "search_from_date": $("#date_from").val(),
                        "search_to_date": $("#date_to").val()
                    } );
                }
            },
            "order": [
                [1, "desc"]
            ],
            "columns": [
                {
                    "data": "customer_name"
                },
                {
                    "data": "invoice_date"
                },
                {
                    "targets": -1,
                    "className": '',
                    "data": null,
                    "render": function(data, type, full) {
                        if (full['invtype'] == 1) {
                            return full['taxinvoice_no'];
                        } else {
                            return 'INV-' + full['manuelinvno'];
                        }
                    }
                },
                {
                    "data": "invoice_amount",
                    "class": "text-right"
                },
                {
                    "data": "amount_paid",
                    "class": "text-right"
                },
                {
                    "data": "outstanding",
                    "class": "text-right"
                },
                {
                    "data": "due_date"
                }
            ],
            dom: 'Bfrtip',
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
                    filename: 'Lubricant Customer Credit Analysis_'+today,
                    footer: true,
                    messageTop: { text: 'Lubricant Customer Credit Analysis', 
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
                    filename: 'Lubricant Customer Credit Analysis_'+today,
                    text: '<i class="fas fa-file-excel mr-2"></i> EXCEL',
                    footer: true
                },
                {
                    extend: 'csv',
                    className: 'btn btn-info btn-sm',
                    filename: 'Lubricant Customer Credit Analysis_'+today,
                    text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                    footer: true
                },
                {
                    extend: 'print',
                    className: 'btn btn-warning btn-sm',
                    text: '<i class="fas fa-print mr-2"></i> PRINT',
                    title: 'Levi Marketing Pvt Ltd',
                    filename: 'Lubricant Customer Credit Analysis_'+today,
                    footer: true,
                    messageTop: 'Lubricant Customer Credit Analysis',
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

                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                // Total invoice amount over all pages
                let invoice_amount = api
                    .column( 3 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                // Total over this page
                let invoice_amount_pageTotal = api
                    .column( 3, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return parseFloat(intVal(a) + intVal(b)).toFixed(2);
                    }, 0 );

                // Update footer
                $( api.column( 3 ).footer() ).html(
                    'Rs ' + parseFloat(invoice_amount_pageTotal).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})
                );

                // Total amount paid over all pages
                let amount_paid = api
                    .column( 4 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                // Total over this page
                let amount_paid_pageTotal = api
                    .column( 4, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return parseFloat(intVal(a) + intVal(b)).toFixed(2);
                    }, 0 );

                // Update footer
                $( api.column( 4 ).footer() ).html(
                    'Rs ' + parseFloat(amount_paid_pageTotal).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})
                );

                // Total outstanding over all pages
                let outstanding = api
                    .column( 5 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                // Total over this page
                let outstanding_pageTotal = api
                    .column( 5, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return parseFloat(intVal(a) + intVal(b)).toFixed(2);
                    }, 0 );

                // Update footer
                $( api.column( 5 ).footer() ).html(
                    'Rs ' + parseFloat(outstanding_pageTotal).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})
                );
            }
        });
    });
}
</script>
