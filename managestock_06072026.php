<?php 
include "include/header.php"; 

$sqllocation = "SELECT `idtbl_location`, `location`, `code` FROM `tbl_location` WHERE `status`=1";
$resultloc = $conn->query($sqllocation);

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
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i class="fa fa-file"></i></div>
                            <span>Manage Stock</span>
                        </h1>
                    </div>
                </div>
            </div>

            <div class="container-fluid mt-2 p-0 p-2">
                <div class="card">
                    <div class="card-body p-2">

                       <form action="process/uploadstockprocess.php" method="post" enctype="multipart/form-data" autocomplete="off">
                        <div class="row g-2 align-items-center mb-3">

                            <div class="col-auto">
                                <a href="getprocess/getproductcsv.php" class="btn btn-outline-success btn-sm px-3">
                                    <i class="fas fa-file-csv mr-2"></i>Download All Products
                                </a>
                            </div>

                            <div class="col-auto">
                                <input type="file" class="form-control form-control-sm" name="csv_file" id="csv_file" accept=".csv">
                            </div>

                            <div class="col-auto">
                                <select name="location" id="location" class="form-control form-control-sm" style="width:220px;" required>
                                    <option value="">Select Location</option>
                                    <?php 
                                        if ($resultloc->num_rows > 0) {
                                            while ($rowloc = $resultloc->fetch_assoc()) { 
                                    ?>
                                        <option value="<?php echo $rowloc['idtbl_location']; ?>">
                                            <?php echo $rowloc['location'] . ' - ' . $rowloc['code']; ?>
                                        </option>
                                    <?php } } ?>
                                </select>
                            </div>

                            <div class="col-auto">
                                <button type="submit" class="btn btn-outline-primary btn-sm px-3">
                                    <i class="far fa-save mr-2"></i>Upload
                                </button>
                            </div>

                        </div>

                        </form>

                        <hr>

                        <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm nowrap" id="dataTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Location</th>
                                            <th>Date</th>
                                        </tr>
                                     </thead>
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
$(document).ready(function() {
        $('#dataTable').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            ajax: {
                url: "scripts/uploadstocklist.php",
                type: "POST", 
            },
            "order": [
                [0, "desc"]
            ],
            "columns": [
                {
                    "data": "idtbl_stock"
                },
                {
                    "data": "product_name"
                },
                {
                    "data": "qty"
                },
                {
                    "data": "location"
                },
                {
                    "data": "date"
                }

                
            ],
            drawCallback: function (settings) {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
});
</script>

<?php include "include/footer.php"; ?>
