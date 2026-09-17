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
                                    <span> <i class="fas fa-file"></i>&nbsp; Invoice Report</span>
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
                                                <option value="3">Card</option>
                                                <option value="2">Cheque</option>
                                                <option value="4">Online Transfer</option>
                                            </select>
                                        </div>

                                        <div class="col-auto">
                                            <label class="small font-weight-bold text-dark mb-1">Invoice Type</label>
                                            <select id="filterinvtype" class="form-control form-control-sm">
                                                <option value="">All Invoices</option>
                                                <option value="0">Non-Tax Invoice</option>
                                                <option value="1">Tax Invoice</option>
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
                                    <th>INVOICE ID</th>       
                                    <th>CUSTOMER</th>                    
                                    <th>DATE</th> 
                                    <th>SALE TYPE</th>     
                                    <th class="text-right">TOTAL PAYMENT</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                    <td colspan="3"></td>
                                    <td style="text-align:right">Total:</td>
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
            url: "scripts/rptinvoiceviewlist.php",
            type: "POST",
            "data": function ( d ) {
                d.search_customer = $('#filtercustomer').val();
                d.filterpaymentmethod = $('#filterpaymentmethod').val();
                d.filterinvtype = $('#filterinvtype').val();
            }
        },
        "order": [
            [0, "desc"]
        ],
        "columns": [
            {
                "data": "id",
                "render": function (data, type, full) {
                    if (type === 'display' || type === 'filter') {
                        return data ? ('INV-' + data) : '-';
                    }
                    return data;
                }
            },
            { "data": "name" },
            { "data": "date" },
            {
                "targets": -1,
                "className": 'text-left',
                "data": "saletype ",
                "render": function(data, type,full){
                    var label='';
                    if(full['saletype']==1){
                        label+='<label >Retail Sale</label>';
                    }else {
                        label+='<label >Whole Sale</label>';
                    }
                    return label;
                }
            },
            {
                "data": "total",
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
        footer: true,
        title: 'Levi Marketing Pvt Ltd',
        filename: 'Invoice Report'+today,
        messageTop: { text: 'Invoice Report', 
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
        text: '<i class="fas fa-file-excel mr-2"></i> EXCEL',
        filename: 'Invoice Report'+today,
        footer: true
    },
    {
        extend: 'csv',
        className: 'btn btn-info btn-sm',
        filename: 'Invoice Report'+today,
        text: '<i class="fas fa-file-csv mr-2"></i> CSV'
    },
    {
        extend: 'print',
        className: 'btn btn-warning btn-sm',
        text: '<i class="fas fa-print mr-2"></i> PRINT',
        filename: 'Invoice Report'+today,
        title: 'Levi Marketing Pvt Ltd',
        footer: true,
        messageTop:'Invoice Report', 
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
    total = api
        .column( 4 )
        .data()
        .reduce( function (a, b) {
            return intVal(a) + intVal(b);
        }, 0 );

    pageTotal = api
        .column( 4, { page: 'current'} )
        .data()
        .reduce( function (a, b) {
            return parseFloat(intVal(a) + intVal(b)).toFixed(2);
        }, 0 );

    $( api.column( 4 ).footer() ).html(
        'Rs '+pageTotal
    );
},
    drawCallback: function (settings) {
                $('[data-toggle="tooltip"]').tooltip();
            }
        } );

    $('#btnSearch').click(function() {
        invoiceDetailTable.ajax.reload();
    });

    $('#btnResetFilter').click(function() {
        $('#filterForm')[0].reset();
        $('#filtercustomer').val(null).trigger('change');
        $('#filterpaymentmethod').val('');
        $('#filterinvtype').val('');
        invoiceDetailTable.ajax.reload();
    });
});
</script>

<?php include "include/footer.php"; ?>