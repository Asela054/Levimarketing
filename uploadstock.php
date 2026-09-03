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
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i class="fa fa-upload"></i></div>
                            <span>Upload Stock CSV</span>
                        </h1>
                    </div>
                </div>
            </div>

            <div class="container-fluid mt-2 p-0 p-2">
                <div class="card">
                    <div class="card-body p-2">
                        <form action="process/uploadstockprocess.php" method="post" enctype="multipart/form-data" autocomplete="off">
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-dark">Choose CSV file</label>
                                <div class="d-flex">
                                    <input type="file" class="form-control form-control-sm me-2" name="csv_file" id="csv_file" accept=".csv" required style="max-width: 300px;">
                                    <button type="submit" class="btn btn-outline-primary btn-sm" <?php if($addcheck==0){echo 'disabled';} ?>>
                                        <i class="far fa-save"></i> Upload
                                    </button>
                                </div>
                            </div>
                            <p class="mt-2 small text-muted">CSV format</p>
                        </form>
                    </div>
                </div>
            </div>
        </main>
        <?php include "include/footerbar.php"; ?>
    </div>
</div>
<?php include "include/footerscripts.php"; ?>
<?php include "include/footer.php"; ?>
