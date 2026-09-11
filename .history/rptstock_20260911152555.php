<?php 
include "include/header.php"; 

// Category list for the filter dropdown
$sqlcategory = "SELECT `idtbl_product_category`, `category` FROM `tbl_product_category` WHERE `status` = 1 ORDER BY `category` ASC";
$resultcategory = $conn->query($sqlcategory);

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
                                    <span><i class="fas fa-file"></i>&nbsp; Stock Info</span>
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
                                            <label class="small font-weight-bold text-dark mb-1">Category</label>
                                            <select class="form-control form-control-sm" id="filtercategory" style="width:100%;">
                                                <option value="">All Categories</option>
                                                <?php if ($resultcategory->num_rows > 0) { while ($rowcategory = $resultcategory->fetch_assoc()) { ?>
                                                <option value="<?php echo $rowcategory['idtbl_product_category']; ?>"><?php echo $rowcategory['category']; ?></option>
                                                <?php }} ?>
                                            </select>
                                        </div>

                                        <div class="col-auto" style="min-width: 260px;">
                                            <label class="small font-weight-bold text-dark mb-1">Product</label>
                                            <select class="form-control form-control-sm" id="filterproduct" style="width:100%;">
                                                <option value="">All Products</option>
                                            </select>
                                        </div>

                                        <div class="col-auto" style="min-width: 240px;">
                                            <label class="small font-weight-bold text-dark mb-1">Search Product Name</label>
                                            <input type="text" class="form-control form-control-sm" id="filterkeyword" placeholder="Type any keyword...">
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
                            <table class="table table-striped table-bordered table-sm nowrap" id="stockDetailTable" style="width:100%">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Product Name</th>
                                        <th>Location</th>
                                        <th class="text-right">Qty</th>
                                        <th class="text-right">Unit Price</th>
                                        <th class="text-right">Total</th>
                                        <th>Last Updated</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-right">Total Qty:</th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th class="text-right">Grand Total:</th>
                                        <th></th>
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
var stockDetailTable;

$(document).ready(function () {

    $('#filtercategory').select2({
        width: '100%'
    });

    $('#filterproduct').select2({
        width: '100%',
        placeholder: 'All Products',
        allowClear: true,
        ajax: {
            url: 'getprocess/getproductselect2.php',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    });

    stockDetailTable = $('#stockDetailTable').DataTable( {
        "destroy": true,
        "processing": true,
        "serverSide": true,
        ajax: {
            url: "scripts/stocklist.php",
            type: "POST",
            "data": function ( d ) {
                d.search_category = $('#filtercategory').val();
                d.search_product = $('#filterproduct').val();
                d.search_keyword = $('#filterkeyword').val();
            }
        },
        "order": [
            [0, "desc"]
        ],
        rowCallback: function (row, data) {
            var qty = parseFloat(data.qty) || 0;
            if (qty <= 0) {
                $(row).addClass('table-danger');
            }
        },
        "columns": [
            { "data": "idtbl_stock" },
            {
                "data": "product_name",
                "render": function (data, type, full) {
                    return data ? data : '-';
                }
            },
            {
                "data": "location",
                "render": function (data, type, full) {
                    return data ? data : '-';
                }
            },
            {
                "data": "qty",
                "className": 'text-right'
            },
            {
                "targets": -1,
                "className": 'text-right',
                "data": "unitprice",
                "render": function (data, type, full) {
                    var price = parseFloat(data) || 0;
                    return addCommas(price.toFixed(2));
                }
            },
            {
                "targets": -1,
                "className": 'text-right',
                "data": null,
                "render": function (data, type, full) {
                    var qty   = parseFloat(full['qty']) || 0;
                    var price = parseFloat(full['unitprice']) || 0;
                    return addCommas((qty * price).toFixed(2));
                }
            },
            { "data": "update" }
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
                title: 'Levi Marketing Pvt Ltd',
                filename: 'Stock Information Report'+today,
                footer: true,
                messageTop: { text: 'Stock Information Report',
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
                filename: 'Stock Information Report'+today,
                text: '<i class="fas fa-file-excel mr-2"></i> EXCEL',
                footer: true
            },
            {
                extend: 'csv',
                className: 'btn btn-info btn-sm',
                filename: 'Stock Information Report'+today,
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                footer: true
            },
            {
                extend: 'print',
                className: 'btn btn-warning btn-sm',
                text: '<i class="fas fa-print mr-2"></i> PRINT',
                title: 'Levi Marketing Pvt Ltd',
                filename: 'Stock Information Report'+today,
                footer: true,
                messageTop: { text: 'Stock Information Report',
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

            // Qty is now column index 3 (unchanged position)
            var qtyPageTotal = api
                .column( 3, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            $( api.column( 3 ).footer() ).html( qtyPageTotal );

            // Total is the new column, now at index 5
            var totalPageTotal = api
                .column( 5, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            $( api.column( 5 ).footer() ).html( addCommas(totalPageTotal.toFixed(2)) );
        },
        drawCallback: function (settings) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    } );

    $('#btnSearch').click(function() {
        stockDetailTable.ajax.reload();
    });

    // Also trigger search on Enter inside the keyword box
    $('#filterkeyword').on('keyup', function(e) {
        if (e.key === 'Enter') {
            stockDetailTable.ajax.reload();
        }
    });

    $('#btnResetFilter').click(function() {
        $('#filterForm')[0].reset();
        $('#filtercategory').val(null).trigger('change');
        $('#filterproduct').val(null).trigger('change');
        $('#filterkeyword').val('');
        stockDetailTable.ajax.reload();
    });
});

function addCommas(nStr){
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
</script>

<?php include "include/footer.php"; ?>