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
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <h1 class="page-header-title">
                                <div class="page-header-icon"><i class="fas fa-undo"></i></div>
                                    <span>&nbsp; GRN Return</span>
                                </h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    <div class="container-fluid mt-2 p-0 p-2">
      <?php if(isset($_SESSION['grnreturn_msg'])){ ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?php echo htmlspecialchars($_SESSION['grnreturn_msg']); unset($_SESSION['grnreturn_msg']); ?>
          <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
      </div>
      <?php } ?>
      <div class="card">
        <div class="card-body p-0 p-2">
            <ul class="nav nav-tabs mb-2" id="grnreturntabs">
                <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tabgrn">Approved GRNs</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tabreturn">GRN Returns</a></li>
            </ul>
            <div class="tab-content">
                <!-- Approved GRNs -->
                <div class="tab-pane fade show active" id="tabgrn">
                    <div class="table scrollbar" id="style-2">
                        <table id="tblgrn" style="width:100%" class="table table-bordered table-striped table-sm nowrap display">
                            <thead class="thead-dark">
                                <tr>
                                    <th>GRN No</th>
                                    <th>Date</th>
                                    <th>Supplier</th>
                                    <th>Invoice No</th>
                                    <th class="text-right">Total</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <!-- Returns -->
                <div class="tab-pane fade" id="tabreturn">
                    <div class="table scrollbar">
                        <table id="tblreturn" style="width:100%" class="table table-bordered table-striped table-sm nowrap display">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Return No</th>
                                    <th>Date</th>
                                    <th>GRN No</th>
                                    <th>Supplier</th>
                                    <th>Reason</th>
                                    <th class="text-right">Total</th>
                                    <th>Status</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div> 
        </main>
        <?php include "include/footerbar.php"; ?>
    </div>
</div>

<!-- Return modal -->
<div class="modal fade" id="returnModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="process/grnreturnprocess.php" method="post" id="returnform" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title">GRN Return</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-md-4"><span class="small font-weight-bold">GRN No :</span> <span id="lblgrn"></span></div>
                        <div class="col-md-4"><span class="small font-weight-bold">Supplier :</span> <span id="lblsupplier"></span></div>
                        <div class="col-md-4"><span class="small font-weight-bold">Invoice No :</span> <span id="lblinvoice"></span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label class="small font-weight-bold text-dark">Return Date :</label>
                            <input type="date" name="returndate" id="returndate" class="form-control form-control-sm" value="<?php echo date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-8">
                            <label class="small font-weight-bold text-dark">Reason :</label>
                            <input type="text" name="reason" id="reason" class="form-control form-control-sm" maxlength="250">
                        </div>
                    </div>
                    <table class="table table-bordered table-sm" id="tblreturnlines">
                        <thead class="thead-dark">
                            <tr>
                                <th>Product</th>
                                <th class="text-right">GRN Qty</th>
                                <th class="text-right">Returned</th>
                                <th class="text-right">Balance</th>
                                <th class="text-right">Unit Price</th>
                                <th style="width:120px;">Return Qty</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-right">Return Subtotal</th>
                                <th class="text-right" id="returnsubtotal">0.00</th>
                            </tr>
                        </tfoot>
                    </table>
                    <input type="hidden" name="grnID" id="grnID" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" id="btnsavereturn" class="btn btn-primary btn-sm">Save Return</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "include/footerscripts.php"; ?>
<?php include "include/footer.php"; ?>

<script>
    $(document).ready(function () {
        var addcheck='<?php echo $addcheck; ?>';
        var editcheck='<?php echo $editcheck; ?>';
        var deletecheck='<?php echo $deletecheck; ?>';

        // approved GRNs
        $('#tblgrn').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            ajax: {
                url: "scripts/grnreturngrnlist.php",
                type: "POST"
            },
            "order": [[ 0, "desc" ]],
            "columns": [
                { "data": "idtbl_grn" },
                { "data": "date" },
                { "data": "suppliername" },
                { "data": "invoicenum" },
                { "data": "total", "className": "text-right" },
                {
                    "data": null,
                    "className": "text-right",
                    "orderable": false,
                    "render": function(data, type, full) {
                        var button='';
                        button+='<button class="btn btn-outline-danger btn-sm btnReturn ';if(addcheck!=1){button+='disabled';}button+='" id="'+full['idtbl_grn']+'" title="Return"><i class="fas fa-undo"></i> Return</button>';
                        return button;
                    }
                }
            ]
        });

        // returns
        $('#tblreturn').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            ajax: {
                url: "scripts/grnreturnlist.php",
                type: "POST"
            },
            "order": [[ 0, "desc" ]],
            "columns": [
                { "data": "idtbl_grn_return" },
                { "data": "returnnum" },
                { "data": "date" },
                { "data": "tbl_grn_idtbl_grn" },
                { "data": "suppliername" },
                { "data": "reason" },
                { "data": "total", "className": "text-right" },
                {
                    "data": "confirm_status",
                    "render": function(data, type, full) {
                        if(full['confirm_status']==1){ return '<span class="badge badge-success">Approved</span>'; }
                        return '<span class="badge badge-warning">Pending</span>';
                    }
                },
                {
                    "data": null,
                    "className": "text-right",
                    "orderable": false,
                    "render": function(data, type, full) {
                        var button='';
                        if(full['confirm_status']==0){
                            button+='<a href="process/statusgrnreturn.php?record='+full['idtbl_grn_return']+'&type=1" onclick="return confirm(\'Are you sure you want to approve this return? Stock will be deducted.\');" target="_self" title="Approve" class="btn btn-outline-success btn-sm mr-1 ';if(editcheck==0){button+='disabled';}button+='"><i class="fas fa-check"></i></a>';
                            button+='<a href="process/statusgrnreturn.php?record='+full['idtbl_grn_return']+'&type=3" onclick="return confirm(\'Are you sure you want to remove this return?\');" target="_self" title="Remove" class="btn btn-outline-danger btn-sm ';if(deletecheck==0){button+='disabled';}button+='"><i class="fas fa-trash-alt"></i></a>';
                        }
                        return button;
                    }
                }
            ]
        });

        // fix column widths when a tab becomes visible
        $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });

        // open return modal
        $('#tblgrn tbody').on('click', '.btnReturn', function () {
            var id = $(this).attr('id');
            $.ajax({
                type: "POST",
                data: { grnID: id },
                url: 'getprocess/getgrnreturnlines.php',
                success: function (result) {
                    var obj = JSON.parse(result);
                    if(obj.error){ alert(obj.error); return; }

                    $('#grnID').val(obj.id);
                    $('#lblgrn').text(obj.id);
                    $('#lblsupplier').text(obj.suppliername);
                    $('#lblinvoice').text(obj.invoicenum);
                    $('#reason').val('');

                    var rows='';
                    $.each(obj.lines, function (i, line) {
                        var disabled = parseFloat(line.balance) <= 0 ? 'disabled' : '';
                        rows+='<tr>';
                        rows+='<td>'+line.product+'</td>';
                        rows+='<td class="text-right">'+line.qty+'</td>';
                        rows+='<td class="text-right">'+line.returned+'</td>';
                        rows+='<td class="text-right">'+line.balance+'</td>';
                        rows+='<td class="text-right unitprice">'+line.unitprice+'</td>';
                        rows+='<td><input type="hidden" name="detailid[]" value="'+line.id+'">';
                        rows+='<input type="number" step="0.01" min="0" max="'+line.balance+'" name="returnqty[]" class="form-control form-control-sm returnqty" value="0" '+disabled+'></td>';
                        rows+='<td class="text-right linetotal">0.00</td>';
                        rows+='</tr>';
                    });
                    $('#tblreturnlines tbody').html(rows);
                    $('#returnsubtotal').text('0.00');
                    $('#returnModal').modal('show');
                }
            });
        });

        // line total + subtotal
        $('#tblreturnlines').on('input', '.returnqty', function () {
            var max = parseFloat($(this).attr('max')) || 0;
            var qty = parseFloat($(this).val()) || 0;
            if(qty > max){ qty = max; $(this).val(max); }
            if(qty < 0){ qty = 0; $(this).val(0); }

            var price = parseFloat($(this).closest('tr').find('.unitprice').text()) || 0;
            $(this).closest('tr').find('.linetotal').text((qty * price).toFixed(2));

            var sub = 0;
            $('#tblreturnlines .linetotal').each(function () { sub += parseFloat($(this).text()) || 0; });
            $('#returnsubtotal').text(sub.toFixed(2));
        });

        // block empty returns
        $('#returnform').on('submit', function () {
            if(parseFloat($('#returnsubtotal').text()) <= 0){
                alert('Enter a return quantity for at least one item.');
                return false;
            }
            $('#btnsavereturn').prop('disabled', true);
            return true;
        });
    });
</script>