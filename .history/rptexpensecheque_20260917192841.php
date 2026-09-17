<?php
include "include/header.php";

// Expenses type list for the filter dropdown
$sqlexpencestype = "SELECT `idtbl_expences_type`, `expencestype` FROM `tbl_expences_type` WHERE `status` = 1 ORDER BY `expencestype` ASC";
$resultexpencestype = $conn->query($sqlexpencestype);

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
                            <span>Expenses Cheque Payment Report</span>
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

                                        <div class="col-auto">
                                            <label class="small font-weight-bold text-dark mb-1">Filter By*</label><br>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="filterdate" name="filtertype" class="custom-control-input" value="date" checked>
                                                <label class="custom-control-label" for="filterdate">Date</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="filterweek" name="filtertype" class="custom-control-input" value="week">
                                                <label class="custom-control-label" for="filterweek">Week</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="filtermonth" name="filtertype" class="custom-control-input" value="month">
                                                <label class="custom-control-label" for="filtermonth">Month</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="filterrange" name="filtertype" class="custom-control-input" value="range">
                                                <label class="custom-control-label" for="filterrange">Date Range</label>
                                            </div>
                                        </div>

                                        <div class="col-auto" id="divsearchdate">
                                            <label class="small font-weight-bold text-dark mb-1">Cheque Date</label>
                                            <input type="date" class="form-control form-control-sm" id="search_date" value="<?php echo date('Y-m-d'); ?>">
                                        </div>

                                        <div class="col-auto d-none" id="divsearchweek">
                                            <label class="small font-weight-bold text-dark mb-1">Week</label>
                                            <input type="week" class="form-control form-control-sm" id="search_week">
                                        </div>

                                        <div class="col-auto d-none" id="divsearchmonth">
                                            <label class="small font-weight-bold text-dark mb-1">Month</label>
                                            <input type="month" class="form-control form-control-sm" id="search_month">
                                        </div>

                                        <div class="col-auto d-none" id="divsearchfromdate">
                                            <label class="small font-weight-bold text-dark mb-1">From Date</label>
                                            <input type="date" class="form-control form-control-sm" id="search_from_date">
                                        </div>
                                        <div class="col-auto d-none" id="divsearchtodate">
                                            <label class="small font-weight-bold text-dark mb-1">To Date</label>
                                            <input type="date" class="form-control form-control-sm" id="search_to_date">
                                        </div>

                                        <div class="col-auto" style="min-width: 220px;">
                                            <label class="small font-weight-bold text-dark mb-1">Expenses Type</label>
                                            <select class="form-control form-control-sm selecter2 px-0" id="search_expencestype">
                                                <option value="">All Types</option>
                                                <?php if ($resultexpencestype->num_rows > 0) { while ($rowtype = $resultexpencestype->fetch_assoc()) { ?>
                                                <option value="<?php echo $rowtype['idtbl_expences_type']; ?>"><?php echo $rowtype['expencestype']; ?></option>
                                                <?php }} ?>
                                            </select>
                                        </div>

                                        <div class="col-auto" style="min-width: 170px;">
                                            <label class="small font-weight-bold text-dark mb-1">Cheque Status</label>
                                            <select class="form-control form-control-sm" id="search_cheque_status">
                                                <option value="">All</option>
                                                <option value="1">Pending</option>
                                                <option value="2">Realized</option>
                                                <option value="3">Returned</option>
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

                        <table class="table table-bordered table-striped table-sm nowrap" id="dataTableExpenseCheque">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Payment Date</th>
                                    <th>Ref No</th>
                                    <th>Expenses Type</th>
                                    <th>Bank</th>
                                    <th>Branch</th>
                                    <th>Cheque No</th>
                                    <th>Cheque Date</th>
                                    <th class="text-right">Amount</th>
                                    <th class="text-center">Cheque Status</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th colspan="8" class="text-right">Total :</th>
                                    <th class="text-right" id="footChequeTotal">0.00</th>
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

        $('#search_expencestype').select2({
            width: '100%',
            placeholder: 'All Types',
            allowClear: true
        });

        var expenseChequeTable = $('#dataTableExpenseCheque').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "pageLength": 25,
            "stateSave": true,
            ajax: {
                url: "scripts/rptexpensechequelist.php",
                type: "POST",
                cache: true,
                data: function (d) {
                    var filtertype = $('input[name="filtertype"]:checked').val();

                    if (filtertype === 'date') {
                        d.search_date = $('#search_date').val();
                    } else if (filtertype === 'week') {
                        d.search_week = $('#search_week').val();
                    } else if (filtertype === 'month') {
                        d.search_month = $('#search_month').val();
                    } else if (filtertype === 'range') {
                        d.search_from_date = $('#search_from_date').val();
                        d.search_to_date = $('#search_to_date').val();
                    }

                    d.search_expencestype   = $('#search_expencestype').val();
                    d.search_cheque_status  = $('#search_cheque_status').val();
                },
                dataSrc: function (json) {
                    var chequeSum = 0;
                    if (json.data) {
                        json.data.forEach(function (row) {
                            chequeSum += parseFloat(row.amount) || 0;
                        });
                    }
                    $('#footChequeTotal').text(addCommas(chequeSum.toFixed(2)));
                    return json.data;
                }
            },
            "order": [[7, "desc"]],
            "columns": [
                { "data": "idtbl_expensepayment" },
                { "data": "paymentdate" },
                {
                    "targets": -1, "className": '', "data": null,
                    "render": function (data, type, full) {
                        return full['refno'] ? full['refno'] : '-';
                    }
                },
                {
                    "targets": -1, "className": '', "data": null,
                    "render": function (data, type, full) {
                        return full['expencestype'] ? full['expencestype'] : '-';
                    }
                },
                {
                    "targets": -1, "className": '', "data": null,
                    "render": function (data, type, full) {
                        return full['cheque_bank_name'] ? full['cheque_bank_name'] : '-';
                    }
                },
                {
                    "targets": -1, "className": '', "data": null,
                    "render": function (data, type, full) {
                        return full['cheque_branch'] ? full['cheque_branch'] : '-';
                    }
                },
                { "data": "cheque_no" },
                { "data": "cheque_date" },
                {
                    "targets": -1, "className": 'text-right', "data": null,
                    "render": function (data, type, full) {
                        return addCommas(parseFloat(full['amount']).toFixed(2));
                    }
                },
                {
                    "targets": -1, "className": 'text-center', "data": null,
                    "render": function (data, type, full) {
                        if (full['cheque_status'] == 1) {
                            return '<span class="badge badge-warning">Pending</span>';
                        } else if (full['cheque_status'] == 2) {
                            return '<span class="badge badge-success">Realized</span>';
                        } else if (full['cheque_status'] == 3) {
                            return '<span class="badge badge-danger">Returned</span>';
                        }
                        return '-';
                    }
                },
                {
                    "targets": -1, "className": '', "data": null,
                    "render": function (data, type, full) {
                        return full['remarks'] ? full['remarks'] : '-';
                    }
                }
            ]
        });

        $('body').tooltip({ selector: '[data-toggle="tooltip"]' });

        // Toggle filter-type inputs
        $('input[name="filtertype"]').change(function() {
            $('#divsearchdate, #divsearchweek, #divsearchmonth, #divsearchfromdate, #divsearchtodate').addClass('d-none');

            var filtertype = $(this).val();
            if (filtertype === 'date') {
                $('#divsearchdate').removeClass('d-none');
            } else if (filtertype === 'week') {
                $('#divsearchweek').removeClass('d-none');
            } else if (filtertype === 'month') {
                $('#divsearchmonth').removeClass('d-none');
            } else if (filtertype === 'range') {
                $('#divsearchfromdate, #divsearchtodate').removeClass('d-none');
            }
        });

        $('#btnSearch').click(function() {
            expenseChequeTable.ajax.reload();
        });

        $('#btnResetFilter').click(function() {
            $('#filterForm')[0].reset();
            $('#filterdate').prop('checked', true).trigger('change');
            $('#search_date').val('<?php echo date("Y-m-d"); ?>');
            $('#search_expencestype').val(null).trigger('change');
            $('#search_cheque_status').val('');
            expenseChequeTable.ajax.reload();
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