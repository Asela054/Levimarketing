<?php 
include "include/header.php";  

$sqlProduct = "SELECT `idtbl_product`, `product_name` FROM `tbl_product` WHERE `status`=1";
$resultProduct = $conn->query($sqlProduct);

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
                            <span>Stock Generate</span>
                        </h1>
                    </div>
                </div>
            </div>

            <div class="container-fluid mt-2 p-0 p-2">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header"><strong>Add Stock</strong></div>
                            <div class="card-body">
                                <form id="stockForm" autocomplete="off">
                                    <div class="form-group mb-2">
                                        <label class="small font-weight-bold text-dark">Product*</label>
                                        <select name="product" id="product" class="form-control form-control-sm select2" required>
                                            <option value="">Select</option>
                                            <?php 
                                            if ($resultProduct->num_rows > 0) {
                                                while ($rowproduct = $resultProduct->fetch_assoc()) { 
                                            ?>
                                                <option value="<?php echo $rowproduct['idtbl_product']; ?>">
                                                    <?php echo $rowproduct['product_name']; ?>
                                                </option>
                                            <?php 
                                                } 
                                            } 
                                            ?>
                                        </select>
                                    </div>

                                    <div class="form-group mb-2">
                                        <label class="small font-weight-bold text-dark">Location*</label>
                                        <select name="location" id="location" class="form-control form-control-sm" required>
                                            <option value="">Select</option>
                                            <?php 
                                            if ($resultloc->num_rows > 0) {
                                                while ($rowloc = $resultloc->fetch_assoc()) { 
                                            ?>
                                                <option value="<?php echo $rowloc['idtbl_location']; ?>">
                                                    <?php echo $rowloc['location'] .'-'. $rowloc['code']; ?>
                                                </option>
                                            <?php 
                                                } 
                                            } 
                                            ?>
                                        </select>
                                    </div>

                                    <div class="form-group mb-2">
                                        <label class="small font-weight-bold text-dark">Qty*</label>
                                        <input type="number" class="form-control form-control-sm" name="qty" id="qty" required>
                                    </div>

                                    <div class="form-group mt-3">
                                        <button type="submit" class="btn btn-outline-primary btn-sm w-100" <?php if($addcheck==0){echo 'disabled';} ?>>
                                            <i class="far fa-save"></i> Add
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <strong>Stock Records</strong>
                                <button id="downloadCsv" class="btn btn-success btn-sm">
                                    <i class="fa fa-download"></i> Download CSV
                                </button>
                            </div>
                            <div class="card-body p-2">
                                <table class="table table-bordered table-sm" id="stockTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Location</th>
                                            <th>Qty</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
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

<script>
$(document).ready(function() {
    $('#product').select2({
        placeholder: "Select a product",
        allowClear: true,
        width: '100%' 
    });

    window.stockTable = $('#stockTable').DataTable({
        paging: true,
        searching: false,
        info: false,
        lengthChange: false,
        pageLength: 5
    });
});

let records = [];

document.getElementById("stockForm").addEventListener("submit", function(e) {
    e.preventDefault();

    let product     = document.getElementById("product");
    let productID   = product.value;
    let productText = product.options[product.selectedIndex].text;

    let location    = document.getElementById("location");
    let locationID  = location.value;
    let locationText= location.options[location.selectedIndex].text;

    let qty         = document.getElementById("qty").value;

    if(productID && locationID && qty) {
        let newRecord = {
            productID: productID,
            product: productText,
            locationID: locationID,
            location: locationText,
            qty: qty,
        };
        records.push(newRecord);

        let rowNode = stockTable.row.add([
            newRecord.product,
            newRecord.location,
            newRecord.qty,
            `<button class="btn btn-danger btn-sm btn-remove">Remove</button>`
        ]).draw(false).node();

        $(rowNode).find('.btn-remove').on('click', function() {
            let rowIndex = stockTable.row($(this).parents('tr')).index();
            records.splice(rowIndex, 1);
            stockTable.row($(this).parents('tr')).remove().draw();
        });

        document.getElementById("stockForm").reset();
        $('#product').val(null).trigger('change');
    }
});

document.getElementById("downloadCsv").addEventListener("click", function() {
    if(records.length === 0) {
        alert("No records to download!");
        return;
    }

    let csv = "ProductID,Product,LocationID,Location,Qty\n";
    records.forEach(r => {
        csv += `${r.productID},${r.product},${r.locationID},${r.location},${r.qty}\n`;
    });

    let blob = new Blob([csv], { type: "text/csv" });
    let url = URL.createObjectURL(blob);
    let a = document.createElement("a");
    a.href = url;
    a.download = "stock.csv";
    a.click();
    URL.revokeObjectURL(url);
});
</script>
