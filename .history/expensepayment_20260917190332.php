<?php 
include "include/header.php"; 

include "include/topnavbar.php"; 
?>

<?php
       $sqltype="SELECT * FROM `tbl_expences_type` WHERE `status`='1'";
       $resulttype=$conn->query($sqltype);
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
                                <div class="page-header-icon"><i class="fas fa-money-bill"></i></div>
                                    <span>&nbsp; Expenses Payment</span>
                                </h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    <div class="container-fluid mt-2 p-0 p-2">
      <div class="card mb-2">
        <div class="card-body p-0 p-2">
            <form action="process/expensepaymentprocess.php" method="post" id="editform">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group mb-1">
                            <label class="small font-weight-bold text-dark">Reference No :</label>
                            <input type="text" name="refno" class="form-control form-control-sm" id="refno" maxlength="45" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-1">
                            <label class="small font-weight-bold text-dark">Expenses Type :</label>
                            <select name="expencestype" id="expencestype" class="form-control form-control-sm" required>
                                <option value="">-- Select --</option>
                                <?php if($resulttype->num_rows > 0) {while ($rowtype = $resulttype->fetch_assoc()) { ?>
                                <option value="<?php echo $rowtype['idtbl_expences_type'] ?>"><?php echo $rowtype['expencestype'] ?></option>
                                <?php }} ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-1">
                            <label class="small font-weight-bold text-dark">Payment Date :</label>
                            <input type="date" name="paymentdate" class="form-control form-control-sm" id="paymentdate" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-1">
                            <label class="small font-weight-bold text-dark">Amount :</label>
                            <input type="number" step="0.01" min="0" name="amount" class="form-control form-control-sm text-right" id="amount" required>
                        </div>
                    </div>
                </div>

                <div class="row mt-1">
                    <div class="col-md-3">
                        <div class="form-group mb-1">
                            <label class="small font-weight-bold text-dark">Payment Method :</label>
                            <select name="paymentmethod" id="paymentmethod" class="form-control form-control-sm" required>
                                <option value="1">Cash</option>
                                <option value="2">Card</option>
                                <option value="3">Cheque</option>
                                <option value="4">Bank Transfer</option>
                            </select>
                        </div>
                    </div>

                    <!-- Card -->
                    <div class="col-md-3 methodbox" id="boxcard" style="display:none;">
                        <div class="form-group mb-1">
                            <label class="small font-weight-bold text-dark">Card Last 4 :</label>
                            <input type="text" name="card_last4" class="form-control form-control-sm" id="card_last4" maxlength="4">
                        </div>
                    </div>

                    <!-- Cheque -->
                    <div class="col-md-3 methodbox chequebox" style="display:none;">
                        <div class="form-group mb-1">
                            <label class="small font-weight-bold text-dark">Cheque No :</label>
                            <input type="text" name="cheque_no" class="form-control form-control-sm" id="cheque_no" maxlength="45">
                        </div>
                    </div>
                    <div class="col-md-3 methodbox chequebox" style="display:none;">
                        <div class="form-group mb-1">
                            <label class="small font-weight-bold text-dark">Bank :</label>
                            <input type="text" name="cheque_bank_name" class="form-control form-control-sm" id="cheque_bank_name" maxlength="255">
                        </div>
                    </div>
                    <div class="col-md-3 methodbox chequebox" style="display:none;">
                        <div class="form-group mb-1">
                            <label class="small font-weight-bold text-dark">Branch :</label>
                            <input type="text" name="cheque_branch" class="form-control form-control-sm" id="cheque_branch" maxlength="45">
                        </div>
                    </div>
                    <div class="col-md-3 methodbox chequebox" style="display:none;">
                        <div class="form-group mb-1">
                            <label class="small font-weight-bold text-dark">Cheque Date :</label>
                            <input type="date" name="cheque_date" class="form-control form-control-sm" id="cheque_date">
                        </div>
                    </div>
                    <div class="col-md-3 methodbox chequebox" style="display:none;">
                        <div class="form-group mb-1">
                            <label class="small font-weight-bold text-dark">Cheque Status :</label>
                            <select name="cheque_status" id="cheque_status" class="form-control form-control-sm">
                                <option value="1">Pending</option>
                                <option value="2">Realized</option>
                                <option value="3">Returned</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mt-1">
                    <div class="col-md-9">
                        <div class="form-group mb-1">
                            <label class="small font-weight-bold text-dark">Remarks :</label>
                            <textarea name="remarks" id="remarks" rows="2" class="form-control form-control-sm"></textarea>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-group mb-1 w-100">
                            <button type="button" id="Btnreset" class="btn btn-secondary btn-m">Reset</button>
                            <button type="submit" name="Btnsubmit" id="Btnsubmit" class="btn btn-primary btn-m fa-pull-right <?php if($addcheck==0){echo 'disabled';} ?>">Add</button>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="recordOption" id="recordOption" value="1">
                <input type="hidden" name="recordID" id="recordID" value="">
            </form>
        </div>
      </div>

      <div class="card">
        <div class="card-body p-0 p-2">
            <div class="table scrollbar" id="style-2">
                <table id="tblexpensepayment" style="width:100%" class="table table-bordered table-striped table-sm nowrap display">
                 <thead class="thead-dark">
                     <tr>
                         <th>#</th>
                         <th>Ref No</th>
                         <th>Expenses Type</th>
                         <th>Payment Date</th>
                         <th>Amount</th>
                         <th>Method</th>
                         <th>Cheque No</th>
                         <th>Cheque Status</th>
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
        </main>
        <?php include "include/footerbar.php"; ?>
    </div>
</div>
<?php include "include/footerscripts.php"; ?>
<?php include "include/footer.php"; ?>

<script>
    var editcheck   = <?php echo (int)$editcheck; ?>;
    var statuscheck = <?php echo (int)$statuscheck; ?>;
    var deletecheck = <?php echo (int)$deletecheck; ?>;

    function methodName(m) {
        if (m == 1) { return 'Cash'; }
        else if (m == 2) { return 'Card'; }
        else if (m == 3) { return 'Cheque'; }
        else if (m == 4) { return 'Bank Transfer'; }
        return '';
    }

    function chequeStatusName(s) {
        if (s == 1) { return '<span class="badge badge-warning">Pending</span>'; }
        else if (s == 2) { return '<span class="badge badge-success">Realized</span>'; }
        else if (s == 3) { return '<span class="badge badge-danger">Returned</span>'; }
        return '';
    }

    function showMethodFields(m) {
        $('.methodbox').hide();
        if (m == 2) { $('#boxcard').show(); }
        else if (m == 3) { $('.chequebox').show(); }
    }

    $(document).ready(function () {

        showMethodFields($('#paymentmethod').val());

        $('#paymentmethod').on('change', function () {
            showMethodFields($(this).val());
        });

        var table = $('#tblexpensepayment').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [[0, "desc"]],
            "ajax": {
                "url": "scri/expensepayment.php",
                "type": "POST"
            },
            "columns": [
                { "data": "idtbl_expensepayment" },
                { "data": "refno" },
                { "data": "expencestype" },
                { "data": "paymentdate" },
                {
                    "data": "amount",
                    "className": "text-right",
                    "render": function (data) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    "data": "paymentmethod",
                    "render": function (data) { return methodName(data); }
                },
                { "data": "cheque_no" },
                {
                    "data": "cheque_status",
                    "render": function (data, type, row) {
                        if (row.paymentmethod == 3) { return chequeStatusName(data); }
                        return '';
                    }
                },
                {
                    "data": "idtbl_expensepayment",
                    "orderable": false,
                    "searchable": false,
                    "className": "text-right",
                    "render": function (data, type, row) {
                        var html = '';
                        html += '<button class="btn btn-outline-primary btn-sm btnEdit ' + (editcheck == 0 ? 'disabled' : '') + '" id="' + data + '"><i class="fas fa-edit"></i></button> ';
                        if (row.status == 1) {
                            html += '<a href="process/statusexpensepayment.php?record=' + data + '&type=2" onclick="return confirm(\'Are you sure you want to deactive this?\');" target="_self" class="btn btn-outline-success btn-sm ' + (statuscheck == 0 ? 'disabled' : '') + '"><i class="fas fa-check"></i></a> ';
                        } else {
                            html += '<a href="process/statusexpensepayment.php?record=' + data + '&type=1" onclick="return confirm(\'Are you sure you want to active this?\');" target="_self" class="btn btn-outline-warning btn-sm ' + (statuscheck == 0 ? 'disabled' : '') + '"><i class="fas fa-times"></i></a> ';
                        }
                        html += '<a href="process/statusexpensepayment.php?record=' + data + '&type=3" onclick="return confirm(\'Are you sure you want to remove this?\');" target="_self" class="btn btn-outline-danger btn-sm ' + (deletecheck == 0 ? 'disabled' : '') + '"><i class="fas fa-trash"></i></a>';
                        return html;
                    }
                }
            ]
        });

        $('#tblexpensepayment tbody').on('click', '.btnEdit', function () {
            var r = confirm("Are you sure, You want to Edit this ? ");
            if (r == true) {
                var id = $(this).attr('id');
                $.ajax({
                    type: "POST",
                    data: {
                        recordID: id
                    },
                    url: 'getprocess/getexpensepayment.php',
                    success: function (result) {
                        var obj = JSON.parse(result);
                        $('#recordID').val(obj.id);
                        $('#refno').val(obj.refno);
                        $('#expencestype').val(obj.expencestype);
                        $('#paymentdate').val(obj.paymentdate);
                        $('#amount').val(obj.amount);
                        $('#paymentmethod').val(obj.paymentmethod);
                        $('#card_last4').val(obj.card_last4);
                        $('#cheque_no').val(obj.cheque_no);
                        $('#cheque_bank_name').val(obj.cheque_bank_name);
                        $('#cheque_branch').val(obj.cheque_branch);
                        $('#cheque_date').val(obj.cheque_date);
                        $('#cheque_status').val(obj.cheque_status);
                        $('#remarks').val(obj.remarks);
                        $('#recordOption').val('2');
                        $('#Btnsubmit').html('<i class="far fa-save"></i>&nbsp;Update');
                        showMethodFields(obj.paymentmethod);
                        $('html, body').animate({ scrollTop: 0 }, 300);
                    }
                });
            }
        });

        $('#Btnreset').on('click', function () {
            $('#editform')[0].reset();
            $('#recordID').val('');
            $('#recordOption').val('1');
            $('#Btnsubmit').html('Add');
            showMethodFields($('#paymentmethod').val());
        });

    });
</script>