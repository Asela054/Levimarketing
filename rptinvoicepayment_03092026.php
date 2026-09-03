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

                             <div class="row">
                                    <div class="col-12">
                                        <form id="search">
                                            <div class="form-row">
                                                <div class="col-3">
                                                    <label class="small font-weight-bold text-dark">Report Type*</label>
                                                    <div class="input-group input-group-sm">
                                                        <select class="form-control form-control-sm" name="report_type" id="report_type">
                                                            <option value="0">Select</option>
                                                            <option value="1">Daily</option>
                                                            <option value="2">Weekly</option>
                                                            <option value="3">Monthly</option>
                                                            <option value="4">Date Range</option>
                                                        </select>
                                                    </div>
                                                </div>
                                               
                                                   <div class="col-2" style="display: none" id="select_date">
                                                     <label class="small font-weight-bold text-dark"> Date*</label>
                                                     <input type="date" class="form-control form-control-sm "  placeholder="" name="date" id="date" >
                                                   </div>

                                                <div class="col-2" style="display: none" id="select_week">
                                                    <label class="small font-weight-bold text-dark"> Week*</label>
                                                    <input type="week" class="form-control form-control-sm" placeholder="" name="week" id="week" >
                                                </div>
                                                <div class="col-2" style="display: none" id="select_month">
                                                    <label class="small font-weight-bold text-dark"> Month*</label>
                                                    <input type="month" class="form-control form-control-sm" placeholder="" name="month" id="month" >
                                                </div>
                                                &nbsp; 
                                                <div class="col-2" style="display: none" id="select_from">
                                                    <label class="small font-weight-bold text-dark"> From*</label>
                                                    <input type="date" class="form-control form-control-sm" placeholder="" name="date_from" id="date_from" >
                                                </div>
                                                &nbsp;
                                                <div class="col-2" style="display: none" id="select_to">
                                                    <label class="small font-weight-bold text-dark"> To*</label>
                                                    <input type="date" class="form-control form-control-sm" placeholder="" name="date_to" id="date_to" >
                                                </div>
                                                <div class="col-2"  style="display: none;" id="hidesumbit">&nbsp;<br>
                                                <button type="submit" class="btn btn-info mb-2"><span id="boot-icon" class="bi bi-search" style="font-size: 15px;">&nbsp;Search</span></button></div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-12">
                                        <hr class="border-dark">
                                        <div class="scrollbar pb-3" id="style-2">
                                            <table class="table table-striped table-bordered table-sm nowrap" id="invoiceDetailTable" style="width:100%">
                                                <thead class="thead-light">
                                                <tr>
                                                    <th>PAYMENT ID</th>       
                                                    <th>INVOICE ID</th>                   
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
                                                    <th colspan="4" ></th>
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
                </div>
            </div>
        </main>
        
        <?php include "include/footerbar.php"; ?>
    </div>
</div>
<?php include "include/footerscripts.php"; ?>

<script type="text/javascript">
$(function () {
        $("#report_type").change(function () {
            if ($(this).val() == 1 ) {
                $("#select_date").show();
                $("#hidesumbit").show();
                $("#select_week").hide();
                $("#select_month").hide();
                $("#select_from").hide();
                $("#select_to").hide();
            } 
            else if ($(this).val() == 2) {
                $("#select_week").show();
                $("#hidesumbit").show();
                $("#select_date").hide();
                $("#select_month").hide();
                $("#select_from").hide();
                $("#select_to").hide();
            } 
            else if ($(this).val() == 3)
            {
                $("#select_month").show();
                $("#hidesumbit").show();
                $("#select_date").hide();
                $("#select_week").hide();
                $("#select_from").hide();
                $("#select_to").hide();
            } 
            else if  ($(this).val() == 4) 
            {
                $("#select_from").show();
                $("#select_to").show();
                $("#hidesumbit").show();
                $("#select_date").hide();
                $("#select_week").hide();
                $("#select_month").hide();
            } else{
                $("#select_date").hide();
                $("#select_week").hide();
                $("#select_month").hide();
                $("#select_from").hide();
                $("#select_to").hide();
                $("#hidesumbit").hide();
            }
        });
    });
</script>

<script>

let today = new Date().toISOString().slice(0, 10)


        
    $(document).ready(function () {
        $("#search").submit(function (event) {
            event.preventDefault();

            $('#invoiceDetailTable').DataTable( {
            "destroy": true,
            "processing": true,
            "serverSide": true,
            ajax: {
                url: "scripts/invoicepaymentlist.php",
                type: "POST", // you can use GET
                "data": function ( d ) {
                 return $.extend( {}, d, {
                 "search_date": $("#date").val(),
                 "search_week": $("#week").val(),
                 "search_month": $("#month").val(),
                 "search_from_date": $("#date_from").val(),
                 "search_to_date": $("#date_to").val()
         } );
       }
            },
            "order": [
                [0, "desc"]
            ],
            "columns": [
                {
                    "data": "idtbl_invoice_payment"
                },
                {
                    "data": "tbl_invoice_idtbl_invoice"
                },
                {
                    "data": "date"
                },
                {
                    "data": "total"
                },
                {
                    "data": "discount"
                },
                {
                    "data": "payamount"
                },
                {
                    "data": "payment"
                },
                {
                    "data": "balance"
                },
               
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
            title: 'Lionel Trade Center',
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
            title: 'Lionel Trade Center',
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

        // Remove the formatting to get integer data for summation
        var intVal = function ( i ) {
            return typeof i === 'string' ?
                i.replace(/[\$,]/g, '')*1 :
                typeof i === 'number' ?
                    i : 0;
        };

        // Total payamount over all pages
        payamount = api
            .column( 5 )
            .data()
            .reduce( function (a, b) {
                return intVal(a) + intVal(b);
            }, 0 );

        // Total over this page
        payamount_pageTotal = api
            .column( 5, { page: 'current'} )
            .data()
            .reduce( function (a, b) {
                return parseFloat(intVal(a) + intVal(b)).toFixed(2);
            }, 0 );

        // Update footer
        $( api.column( 5 ).footer() ).html(
            // pageTotal=parseFloat(pageTotal).toFixed(2);
            'Rs '+payamount_pageTotal
        );


          // Total payment over all pages
          payment = api
            .column( 6 )
            .data()
            .reduce( function (a, b) {
                return intVal(a) + intVal(b);
            }, 0 );

        // Total over this page
        payment_pageTotal = api
            .column( 6, { page: 'current'} )
            .data()
            .reduce( function (a, b) {
                return parseFloat(intVal(a) + intVal(b)).toFixed(2);
            }, 0 );

        // Update footer
        $( api.column( 6 ).footer() ).html(
            // pageTotal=parseFloat(pageTotal).toFixed(2);
            'Rs '+payment_pageTotal
        );

         // Total balance over all pages
         balance = api
            .column( 6 )
            .data()
            .reduce( function (a, b) {
                return intVal(a) + intVal(b);
            }, 0 );

        // Total over this page
        balance_pageTotal = api
            .column( 6, { page: 'current'} )
            .data()
            .reduce( function (a, b) {
                return parseFloat(intVal(a) + intVal(b)).toFixed(2);
            }, 0 );

        // Update footer
        $( api.column( 6 ).footer() ).html(
            // pageTotal=parseFloat(pageTotal).toFixed(2);
            'Rs '+balance_pageTotal
        );

    },
        drawCallback: function (settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            } );
            });
    });


   
</script>

<?php include "include/footer.php"; ?>
