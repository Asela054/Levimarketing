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
            <div class="container-fluid p-0 p-2">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-7 col-xl-7" style="border-right: 1px dotted #000;">
                                <h6 class="title-style mb-3"><span>Choose Product</span></h6>
                                <div id="categoryinfo">
                                    <div class="row row-cols-1 row-cols-md-4">
                                        <div class="col mb-4">
                                            <div class="card h-100 shadow-none bg-primary border-primary">
                                                <div class="card-body p-2 text-center pointer">
                                                    <h4 class="text-light font-weight-light">Card title</h4>
                                                    <hr class="border-light my-1">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col mb-4">
                                            <div class="card h-100 shadow-none bg-primary border-primary">
                                                <div class="card-body p-2 text-center pointer">
                                                    <h4 class="text-light font-weight-light">Card title</h4>
                                                    <hr class="border-light my-1">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col mb-4">
                                            <div class="card h-100 shadow-none bg-primary border-primary">
                                                <div class="card-body p-2 text-center pointer">
                                                    <h4 class="text-light font-weight-light">Card title</h4>
                                                    <hr class="border-light my-1">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col mb-4">
                                            <div class="card h-100 shadow-none bg-primary border-primary">
                                                <div class="card-body p-2 text-center pointer">
                                                    <h4 class="text-light font-weight-light">Card title</h4>
                                                    <hr class="border-light my-1">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-5 col-xl-5">
                                <h6 class="title-style mb-3"><span>Cart Information</span></h6>
                                <table class="table table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-right">Unit</th>
                                            <th class="text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="row">
                                    <div class="col-12 text-right">
                                        <div class="display-4">0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 text-right">
                                <button class="btn btn-danger btn-sm"><i class="fas fa-cash-register fa-3x mr-2"></i> <h1 class="font-weight-normal mt-2 text-light">PAYMENT</h1></button>
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
    $(document).ready(function () {
        
    });
</script>

<?php include "include/footer.php"; ?>
