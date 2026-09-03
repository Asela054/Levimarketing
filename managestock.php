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
                                            <th>Action</th>
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

<!-- ==================== Edit Stock Modal ==================== -->
<div class="modal fade" id="editStockModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="editStockForm" autocomplete="off">
        <div class="modal-header">
          <h5 class="modal-title">Edit Stock</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">

          <input type="hidden" name="idtbl_stock" id="edit_idtbl_stock">

          <div class="form-group">
            <label>Product</label>
            <input type="text" class="form-control" id="edit_product_name" readonly>
          </div>

          <div class="form-group">
            <label>Location</label>
            <input type="text" class="form-control" id="edit_location" readonly>
          </div>

          <div class="form-group">
            <label>Current Quantity</label>
            <input type="text" class="form-control" id="edit_current_qty" readonly>
          </div>

          <div class="form-group">
            <label>New Quantity <span class="text-danger">*</span></label>
            <input type="number" step="any" class="form-control" name="new_qty" id="edit_new_qty" required>
          </div>

          <div class="form-group">
            <label>Reason for Edit <span class="text-danger">*</span></label>
            <textarea class="form-control" name="reason" id="edit_reason" rows="3" required
                      placeholder="e.g. Physical stock count correction, damaged units removed, etc."></textarea>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ==================== Delete Stock Modal ==================== -->
<div class="modal fade" id="deleteStockModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="deleteStockForm" autocomplete="off">
        <div class="modal-header">
          <h5 class="modal-title text-danger">Delete Stock Record</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">

          <input type="hidden" name="idtbl_stock" id="delete_idtbl_stock">

          <div class="alert alert-warning">
            This will mark the stock record as deleted. It will no longer appear
            in stock lists, but it is <strong>never</strong> physically removed
            from the database — every delete is permanently logged.
          </div>

          <div class="form-group">
            <label>Product</label>
            <input type="text" class="form-control" id="delete_product_name" readonly>
          </div>

          <div class="form-group">
            <label>Quantity being removed</label>
            <input type="text" class="form-control" id="delete_current_qty" readonly>
          </div>

          <div class="form-group">
            <label>Reason for Deletion <span class="text-danger">*</span></label>
            <textarea class="form-control" name="reason" id="delete_reason" rows="3" required
                      placeholder="e.g. Duplicate entry, expired stock written off, wrong location, etc."></textarea>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Confirm Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include "include/footerscripts.php"; ?>

<script>
var stockTable;

function action(data) {
    var obj = JSON.parse(data);
    $.notify({
        icon: obj.icon,
        title: obj.title,
        message: obj.message,
        url: obj.url,
        target: obj.target
    }, {
        element: 'body',
        position: null,
        type: obj.type,
        allow_dismiss: true,
        newest_on_top: false,
        showProgressbar: false,
        placement: {
            from: "top",
            align: "center"
        },
        offset: 100,
        spacing: 10,
        z_index: 1031,
        delay: 5000,
        timer: 1000,
        url_target: '_blank',
        mouse_over: null,
        animate: {
            enter: 'animated fadeInDown',
            exit: 'animated fadeOutUp'
        },
        onShow: null,
        onShown: null,
        onClose: null,
        onClosed: null,
        icon_type: 'class',
        template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0}" role="alert">' +
            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
            '<span data-notify="icon"></span> ' +
            '<span data-notify="title">{1}</span> ' +
            '<span data-notify="message">{2}</span>' +
            '<div class="progress" data-notify="progressbar">' +
            '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
            '</div>' +
            '<a href="{3}" target="{4}" data-notify="url"></a>' +
            '</div>'
    });
}

$(document).ready(function() {
    stockTable = $('#dataTable').DataTable({
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
            { "data": "idtbl_stock" },
            { "data": "product_name" },
            { "data": "qty" },
            { "data": "location" },
            { "data": "date" },
            { "data": "action", "orderable": false, "searchable": false }
        ],
        drawCallback: function (settings) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });

    // ---------- Open Edit modal ----------
    $('#dataTable').on('click', '.btn-edit-stock', function() {
        // Buttons rendered with the 'disabled' class from the server (no
        // edit privilege) should not open the modal — a CSS class alone
        // doesn't stop a click event the way a real disabled attribute would.
        if ($(this).hasClass('disabled')) { return; }

        var id = $(this).data('id');

        $.ajax({
            url: 'scripts/geteditstock.php',
            type: 'GET',
            data: { idtbl_stock: id },
            dataType: 'json',
            success: function(res) {
                if (res.type !== 'success') {
                    alert(res.message || 'Unable to load stock record.');
                    return;
                }
                $('#edit_idtbl_stock').val(res.data.idtbl_stock);
                $('#edit_product_name').val(res.data.product_name);
                $('#edit_location').val(res.data.location);
                $('#edit_current_qty').val(res.data.qty);
                $('#edit_new_qty').val(res.data.qty);
                $('#edit_reason').val('');
                $('#editStockModal').modal('show');
            },
            error: function() {
                alert('Server error while loading stock record.');
            }
        });
    });

    // ---------- Submit Edit ----------
    $('#editStockForm').on('submit', function(e) {
        e.preventDefault();

        if ($('#edit_reason').val().trim() === '') {
            alert('A reason is required to edit stock.');
            return;
        }

        $.ajax({
            url: 'process/editstockprocess.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                action(JSON.stringify(res));
                if (res.type === 'success') {
                    $('#editStockModal').modal('hide');
                    stockTable.ajax.reload(null, false);
                }
            },
            error: function() {
                alert('Server error while saving changes.');
            }
        });
    });

    // ---------- Open Delete modal ----------
    $('#dataTable').on('click', '.btn-delete-stock', function() {
        // Same guard as the edit button above.
        if ($(this).hasClass('disabled')) { return; }

        var id = $(this).data('id');

        $.ajax({
            url: 'scripts/geteditstock.php',
            type: 'GET',
            data: { idtbl_stock: id },
            dataType: 'json',
            success: function(res) {
                if (res.type !== 'success') {
                    alert(res.message || 'Unable to load stock record.');
                    return;
                }
                $('#delete_idtbl_stock').val(res.data.idtbl_stock);
                $('#delete_product_name').val(res.data.product_name);
                $('#delete_current_qty').val(res.data.qty);
                $('#delete_reason').val('');
                $('#deleteStockModal').modal('show');
            },
            error: function() {
                alert('Server error while loading stock record.');
            }
        });
    });

    // ---------- Submit Delete ----------
    $('#deleteStockForm').on('submit', function(e) {
        e.preventDefault();

        if ($('#delete_reason').val().trim() === '') {
            alert('A reason is required to delete stock.');
            return;
        }

        if (!confirm('Are you sure you want to delete this stock record? This action is permanently logged.')) {
            return;
        }

        $.ajax({
            url: 'process/deletestockprocess.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                action(JSON.stringify(res));
                if (res.type === 'success') {
                    $('#deleteStockModal').modal('hide');
                    stockTable.ajax.reload(null, false);
                }
            },
            error: function() {
                alert('Server error while deleting record.');
            }
        });
    });
});
</script>

<?php include "include/footer.php"; ?>