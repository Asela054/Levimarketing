<?php 
include "include/header.php"; 

// Category list — used for both the filter dropdown and the per-category tables
$categories = array();
$sqlcategory = "SELECT `idtbl_product_category`, `category` FROM `tbl_product_category` WHERE `status` = 1 ORDER BY `category` ASC";
$resultcategory = $conn->query($sqlcategory);
if ($resultcategory && $resultcategory->num_rows > 0) {
    while ($rowcategory = $resultcategory->fetch_assoc()) {
        $categories[] = $rowcategory;
    }
}

include "include/topnavbar.php"; 
?>
<style>
    div.dataTables_processing {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        top: 50% !important;
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
                                                <?php foreach ($categories as $rowcategory): ?>
                                                <option value="<?php echo $rowcategory['idtbl_product_category']; ?>"><?php echo htmlspecialchars($rowcategory['category']); ?></option>
                                                <?php endforeach; ?>
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

                        <!-- One table per category. All render/load on page load; #filtercategory just shows/hides + re-filters them -->
                        <?php foreach ($categories as $cat): 
                            $catId   = (int)$cat['idtbl_product_category'];
                            $catName = htmlspecialchars($cat['category']);
                        ?>
                        <div class="category-block mb-4" data-category-id="<?php echo $catId; ?>">
                            <h5 class="font-weight-bold text-primary mb-2">
                                <i class="fas fa-tag"></i>&nbsp; <?php echo $catName; ?>
                            </h5>
                            <div class="scrollbar pb-3">
                                <table class="table table-striped table-bordered table-sm nowrap stock-category-table"
                                       id="stockDetailTable_<?php echo $catId; ?>"
                                       data-category-id="<?php echo $catId; ?>"
                                       data-category-name="<?php echo $catName; ?>"
                                       style="width:100%">
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
                                    <tbody></tbody>
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
                        <?php endforeach; ?>

                        <?php if (empty($categories)): ?>
                            <p class="text-muted">No categories found.</p>
                        <?php endif; ?>

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
var stockTables = {};     // catId (string) => DataTable instance
var tableInitialized = {}; // catId (string) => bool, so we don't double-init

function initStockTable(catId, catName) {
    if (tableInitialized[catId]) return; // guard against double init
    tableInitialized[catId] = true;

    stockTables[catId] = $('#stockDetailTable_' + catId).DataTable({
        "destroy": true,
        "processing": true,
        "serverSide": true,
        // Optional but cheap win: avoids a duplicate COUNT(*) query on every draw
        "deferLoading": null,
        "language": {
            "processing": '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="sr-only">Loading...</span></div>'
        },
        ajax: {
            url: "scripts/stocklist.php",
            type: "POST",
            "data": function (d) {
                d.search_category = catId;
                d.search_product  = $('#filterproduct').val();
                d.search_keyword  = $('#filterkeyword').val();
            }
        },
        "order": [[0, "desc"]],
        rowCallback: function (row, data) {
            var qty = parseFloat(data.qty) || 0;
            if (qty <= 0) $(row).addClass('table-danger');
        },
        "columns": [
            { "data": "idtbl_stock" },
            { "data": "product_name", "render": function (data) { return data ? data : '-'; } },
            { "data": "location", "render": function (data) { return data ? data : '-'; } },
            { "data": "qty", "className": 'text-right' },
            {
                "className": 'text-right',
                "data": "unitprice",
                "render": function (data) {
                    var price = parseFloat(data) || 0;
                    return addCommas(price.toFixed(2));
                }
            },
            {
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
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
        buttons: [
            {
                extend: 'pdf',
                className: 'btn btn-primary btn-sm',
                text: '<i class="fas fa-file-pdf mr-2"></i>PDF',
                title: 'Levi Marketing Pvt Ltd',
                filename: 'Stock Information Report - ' + catName + ' ' + today,
                footer: true,
                messageTop: { text: 'Stock Information Report - ' + catName, fontSize: 15, bold: true, alignment: 'center' },
                customize: function (doc) {
                    doc.styles.title = { color: 'black', fontSize: '30', alignment: 'center' };
                }
            },
            {
                extend: 'excel',
                className: 'btn btn-success btn-sm',
                filename: 'Stock Information Report - ' + catName + ' ' + today,
                text: '<i class="fas fa-file-excel mr-2"></i> EXCEL',
                footer: true
            },
            {
                extend: 'csv',
                className: 'btn btn-info btn-sm',
                filename: 'Stock Information Report - ' + catName + ' ' + today,
                text: '<i class="fas fa-file-csv mr-2"></i> CSV',
                footer: true
            },
            {
                extend: 'print',
                className: 'btn btn-warning btn-sm',
                text: '<i class="fas fa-print mr-2"></i> PRINT',
                title: 'Levi Marketing Pvt Ltd',
                footer: true,
                messageTop: { text: 'Stock Information Report - ' + catName, fontSize: 15, bold: true, alignment: 'center' },
                customize: function (doc) {
                    doc.styles.title = { color: 'black', fontSize: '30', alignment: 'center' };
                }
            }
        ],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
            var intVal = function (i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 :
                       typeof i === 'number' ? i : 0;
            };
            var qtyPageTotal = api.column(3, { page: 'current' }).data()
                .reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            $(api.column(3).footer()).html(qtyPageTotal);

            var totalPageTotal = api.column(5, { page: 'current' }).data()
                .reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            $(api.column(5).footer()).html(addCommas(totalPageTotal.toFixed(2)));
        },
        drawCallback: function () {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
}

function applyFilters() {
    var selectedCat = $('#filtercategory').val(); // '' = all

    $('.category-block').each(function () {
        var $block   = $(this);
        var blockCat = String($block.data('category-id'));

        if (!selectedCat || selectedCat === blockCat) {
            $block.show();
            if (tableInitialized[blockCat]) {
                stockTables[blockCat].ajax.reload();
            } else if (!selectedCat || selectedCat === blockCat) {
                var $tbl = $block.find('.stock-category-table');
                initStockTable(String($tbl.data('category-id')), $tbl.data('category-name'));
            }
        } else {
            $block.hide();
        }
    });
}

$(document).ready(function () {

    $('#filtercategory').select2({ width: '100%' });

    $('#filterproduct').select2({
        width: '100%',
        placeholder: 'All Products',
        allowClear: true,
        ajax: {
            url: 'getprocess/getproductselect2.php',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) { return { searchTerm: params.term }; },
            processResults: function (data) { return { results: data }; },
            cache: true
        }
    });

    // --- Lazy load: only init a table once its block scrolls into view ---
    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    var $block = $(entry.target);
                    var $tbl   = $block.find('.stock-category-table');
                    var catId   = String($tbl.data('category-id'));
                    var catName = $tbl.data('category-name');
                    initStockTable(catId, catName);
                    observer.unobserve(entry.target); // only need to trigger once
                }
            });
        }, { rootMargin: '200px 0px' }); // start loading a bit before it's fully visible

        $('.category-block').each(function () {
            observer.observe(this);
        });
    } else {
        // Fallback for old browsers: init everything up front (old behavior)
        $('.stock-category-table').each(function () {
            initStockTable(String($(this).data('category-id')), $(this).data('category-name'));
        });
    }

    $('#btnSearch').click(function () {
        applyFilters();
    });

    $('#filterkeyword').on('keyup', function (e) {
        if (e.key === 'Enter') applyFilters();
    });

    $('#btnResetFilter').click(function () {
        $('#filterForm')[0].reset();
        $('#filtercategory').val(null).trigger('change');
        $('#filterproduct').val(null).trigger('change');
        $('#filterkeyword').val('');

        $('.category-block').show();
        $.each(stockTables, function (catId, table) {
            table.ajax.reload();
        });
        // any block not yet initialized will pick it up via the observer
    });
});

function addCommas(nStr) {
    nStr += '';
    var x = nStr.split('.');
    var x1 = x[0];
    var x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) x1 = x1.replace(rgx, '$1' + ',' + '$2');
    return x1 + x2;
}
</script>

<?php include "include/footer.php"; ?>