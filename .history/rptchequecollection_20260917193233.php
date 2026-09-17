<?php
include "include/header.php";

$locationid = $_SESSION['location_id'];

// Customer list for the filter dropdown
$sqlcustomer = "SELECT `idtbl_customer`, `type`, `name`, `nic`, `phone`, `email`, `address`, `vat_num`, `s_vat`, `creditlimit`, `credittype`, `creditperiod`, `emergencydate`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_area_idtbl_area` FROM `tbl_customer` WHERE `status` = 1 ORDER BY `name` ASC";
$resultcustomer = $conn->query($sqlcustomer);

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
                            <div class="page-header-icon"><i data-feather="file-text"></i></div>
                            <span>Cheque Collection Report</span>
                        </h1>
                    </div>
                </div>
            </div>
            <div class="container-fluid mt-2 p-0 p-2">
                <div class="card">
                    <div class="card-body p-0 p-2">

                        <!-- Filters -->
                        <div class="row mb-2">
                            <div class="col-12">
                                <form action="#" method="post" autocomplete="off" id="filterForm">
                                    <div class="form-row align-items-end">

                                        <div class="col-auto" style="min-width: 220px;">
                                            <label class="small font-weight-bold text-dark mb-1">Customer</label>
                                            <select class="form-control form-control-sm selecter2 px-0" id="search_customer">
                                                <option value="">All Customers</option>
                                                <?php if ($resultcustomer->num_rows > 0) { while ($rowcustomer = $resultcustomer->fetch_assoc()) { ?>
                                                <option value="<?php echo $rowcustomer['idtbl_customer']; ?>"><?php echo $rowcustomer['name']; ?></option>
                                                <?php }} ?>
                                            </select>
                                        </div>

                                        <div class="col-auto">
                                            <button type="button" class="btn btn-primary btn-sm" id="btnSearch"><i class="fas fa-search"></i>&nbsp;Search</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnResetFilter"><i class="fas fa-redo"></i>&nbsp;Reset</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <hr>

                        <table class="table table-bordered table-striped table-sm nowrap" id="dataTableChequeCollection">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Payment Date</th>
                                    <th>Invoice No</th>
                                    <th>Invoice Date</th>
                                    <th>Customer</th>
                                    <th>Location</th>
                                    <th>Bank</th>
                                    <th>Cheque No</th>
                                    <th>Cheque Date</th>
                                    <th class="text-right">Cheque Amount</th>
                                    <th class="text-right">Applied to Invoice</th>
                                    <th class="text-center">Added to A/C</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th colspan="9" class="text-right">Total :</th>
                                    <th class="text-right" id="footChequeTotal">0.00</th>
                                    <th class="text-right" id="footInvoiceTotal">0.00</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
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

        $('#search_customer').select2({
            width: '100%',
            placeholder: 'All Customers',
            allowClear: true
        });

        var chequeTable = $('#dataTableChequeCollection').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "pageLength": 25,
            "stateSave": true,
            ajax: {
                url: "scripts/rptchequecollection.php",
                type: "POST",
                cache: true,
                data: function (d) {
                    d.search_customer = $('#search_customer').val();
                },
                dataSrc: function (json) {
                    var chequeSum = 0;
                    var invoiceSum = 0;
                    if (json.data) {
                        json.data.forEach(function (row) {
                            chequeSum += parseFloat(row.chequeamount) || 0;
                            invoiceSum += parseFloat(row.invoiceamount) || 0;
                        });
                    }
                    $('#footChequeTotal').text(addCommas(chequeSum.toFixed(2)));
                    $('#footInvoiceTotal').text(addCommas(invoiceSum.toFixed(2)));
                    return json.data;
                }
            },
            "order": [[1, "desc"]],
            "columns": [
                { "data": "idtbl_invoice_payment_detail" },
                { "data": "paymentdate" },
                {
                    "targets": -1, "className": '', "data": null,
                    "render": function (data, type, full) {
                        return full['manuelinvno'] ? ('INV-' + full['manuelinvno']) : ('INV-' + full['idtbl_invoice']);
                    }
                },
                { "data": "invoicedate" },
                {
                    "targets": -1, "className": '', "data": null,
                    "render": function (data, type, full) {
                        return full['name'] ? full['name'] : '-';
                    }
                },
                { "data": "location" },
                {
                    "targets": -1, "className": '', "data": null,
                    "render": function (data, type, full) {
                        return full['bank'] ? full['bank'] : '-';
                    }
                },
                { "data": "chequeno" },
                { "data": "chequedate" },
                {
                    "targets": -1, "className": 'text-right', "data": null,
                    "render": function (data, type, full) {
                        return addCommas(parseFloat(full['chequeamount']).toFixed(2));
                    }
                },
                {
                    "targets": -1, "className": 'text-right', "data": null,
                    "render": function (data, type, full) {
                        return addCommas(parseFloat(full['invoiceamount']).toFixed(2));
                    }
                },
                {
                    "targets": -1, "className": 'text-center', "data": null,
                    "render": function (data, type, full) {
                        if (full['addaccountstatus'] == 1) {
                            return '<i class="fas fa-check text-success"></i>&nbsp;Added';
                        }
                        return '<i class="fas fa-times text-danger"></i>&nbsp;Pending';
                    }
                },
                {
                    "targets": -1, "className": 'text-center', "data": null,
                    "render": function (data, type, full) {
                        if (full['paymentdetailstatus'] == 1) {
                            return '<span class="badge badge-success">Active</span>';
                        }
                        return '<span class="badge badge-danger">Cancelled</span>';
                    }
                }
            ]
        });

        $('body').tooltip({ selector: '[data-toggle="tooltip"]' });

        $('#btnSearch').click(function() {
            chequeTable.ajax.reload();
        });

        $('#btnResetFilter').click(function() {
            $('#filterForm')[0].reset();
            $('#search_customer').val(null).trigger('change');
            chequeTable.ajax.reload();
        });
    });

    function addCommas(nStr) {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }
</script>
<?php include "include/footer.php"; ?>