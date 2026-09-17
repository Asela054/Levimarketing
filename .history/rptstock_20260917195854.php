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

// Only open the escaping connection when actually needed
$escConn = null;
if (!empty($_POST['search_keyword'])) {
    $keyword = trim($_POST['search_keyword']);
    if ($keyword !== '') {
        $escConn = new mysqli($db_host, $db_username, $db_password, $db_name);
        if (!$escConn->connect_error) {
            $keyword_esc = $escConn->real_escape_string($keyword);
            $extraWhere .= " AND `ud`.`product_name` LIKE '%" . $keyword_esc . "%'";
        }
    }
}

<?php include "include/footer.php"; ?>