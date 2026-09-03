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
                            <div class="page-header-icon"><i class="fa fa-history"></i></div>
                            <span>Stock Activity Log Report</span>
                        </h1>
                    </div>
                </div>
            </div>

            <div class="container-fluid mt-2 p-0 p-2">
                <div class="card">
                    <div class="card-body p-2">

                        <!-- ==================== Filters ==================== -->
                        <form id="filterForm" class="row g-2 align-items-end mb-3" autocomplete="off">

                            <div class="col-auto">
                                <label class="small mb-1">Action Type</label>
                                <select class="form-control form-control-sm" id="f_action_type" name="action_type">
                                    <option value="">All</option>
                                    <option value="EDIT">EDIT</option>
                                    <option value="DELETE">DELETE</option>
                                </select>
                            </div>

                            <div class="col-auto">
                                <label class="small mb-1">Location</label>
                                <select class="form-control form-control-sm" id="f_location" name="location">
                                    <option value="">All</option>
                                    <?php
                                    // Populate from tbl_location so the filter always matches real locations
                                    $locres = mysqli_query($conn, "SELECT idtbl_location, location FROM tbl_location ORDER BY location ASC");
                                    if ($locres) {
                                        while ($loc = mysqli_fetch_assoc($locres)) {
                                            echo '<option value="' . htmlspecialchars($loc['idtbl_location']) . '">' . htmlspecialchars($loc['location']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-auto">
                                <label class="small mb-1">From Date</label>
                                <input type="date" class="form-control form-control-sm" id="f_date_from" name="date_from">
                            </div>

                            <div class="col-auto">
                                <label class="small mb-1">To Date</label>
                                <input type="date" class="form-control form-control-sm" id="f_date_to" name="date_to">
                            </div>

                            <div class="col-auto">
                                <label class="small mb-1">Product / User</label>
                                <input type="text" class="form-control form-control-sm" id="f_keyword" name="keyword" placeholder="Search product or user">
                            </div>

                            <div class="col-auto">
                                <button type="button" id="btnFilter" class="btn btn-outline-primary btn-sm px-3">
                                    <i class="fas fa-filter mr-2"></i>Apply
                                </button>
                            </div>

                            <div class="col-auto">
                                <button type="button" id="btnResetFilter" class="btn btn-outline-secondary btn-sm px-3">
                                    <i class="fas fa-times mr-2"></i>Reset
                                </button>
                            </div>

                            <div class="col-auto ml-auto">
                                <a href="getprocess/getstockactivitylogcsv.php" id="exportCsvLink" class="btn btn-outline-success btn-sm px-3" target="_blank">
                                    <i class="fas fa-file-csv mr-2"></i>Export CSV
                                </a>
                            </div>

                        </form>

                        <hr>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm nowrap" id="dataTable" style="width:100%">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Action</th>
                                        <th>Product</th>
                                        <th>Location</th>
                                        <th>Old Qty</th>
                                        <th>New Qty</th>
                                        <th>Change</th>
                                        <th>Reason</th>
                                        <th>User</th>
                                        <th>IP Address</th>
                                        <th>Date/Time</th>
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
var stockActivityTable;

function buildExportUrl() {
    var params = $.param({
        action_type: $('#f_action_type').val(),
        location: $('#f_location').val(),
        date_from: $('#f_date_from').val(),
        date_to: $('#f_date_to').val(),
        keyword: $('#f_keyword').val()
    });
    $('#exportCsvLink').attr('href', 'getprocess/getstockactivitylogcsv.php?' + params);
}

$(document).ready(function() {

    buildExportUrl();

    stockActivityTable = $('#dataTable').DataTable({
        "destroy": true,
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: "scripts/rptstockactivitylog_list.php",
            type: "POST",
            data: function(d) {
                d.action_type = $('#f_action_type').val();
                d.location = $('#f_location').val();
                d.date_from = $('#f_date_from').val();
                d.date_to = $('#f_date_to').val();
                d.keyword = $('#f_keyword').val();
            }
        },
        "order": [
            [0, "desc"]
        ],
        "columns": [
            { "data": "idtbl_stock_activity_log" },
            { "data": "action_type" },
            { "data": "product_name_snapshot" },
            { "data": "location_name_snapshot" },
            { "data": "old_qty" },
            { "data": "new_qty" },
            { "data": "qty_change" },
            { "data": "reason" },
            { "data": "username_snapshot" },
            { "data": "ip_address" },
            { "data": "action_datetime" }
        ],
        "createdRow": function(row, data, dataIndex) {
            if (data.action_type === 'DELETE') {
                $(row).addClass('table-danger');
            } else if (data.action_type === 'EDIT') {
                $(row).addClass('table-warning');
            }
        },
        drawCallback: function(settings) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });

    $('#btnFilter').on('click', function() {
        buildExportUrl();
        stockActivityTable.ajax.reload();
    });

    $('#btnResetFilter').on('click', function() {
        $('#filterForm')[0].reset();
        buildExportUrl();
        stockActivityTable.ajax.reload();
    });
});
</script>

<?php include "include/footer.php"; ?>