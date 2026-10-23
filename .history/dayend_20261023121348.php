<?php 
include "include/header.php"; 

$sqlhistory = "SELECT d.`idtbl_dayend`, d.`dayenddate`, d.`totalrows`, d.`totalqty`, d.`donedatetime`, u.`name` AS doneby
               FROM `tbl_dayend` d
               LEFT JOIN `tbl_user` u ON u.`idtbl_user` = d.`doneby`
               ORDER BY d.`dayenddate` DESC";
$resulthistory = $conn->query($sqlhistory);

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
                            <div class="col-12">
                                <h1 class="page-header-title">
                                    <span class="bi bi-calendar-check">&nbsp; Day End</span>
                                </h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid mt-2 p-0 p-2">
                <div class="card">
                    <div class="card-body">

                        <div class="form-row align-items-end">
                            <div class="col-auto">
                                <label class="small font-weight-bold text-dark mb-1">Day End Date</label>
                                <input type="date" class="form-control form-control-sm" id="dayenddate" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-danger btn-sm" id="btnDayEnd"><i class="fas fa-lock"></i>&nbsp;Run Day End</button>
                            </div>
                        </div>
                        <div id="dayendMsg" class="mt-2"></div>
                        <hr class="border-dark">

                        <div class="scrollbar pb-3">
                            <table class="table table-striped table-bordered table-sm nowrap" id="dayendHistory" style="width:100%">
                                <thead class="thead-light">
                                <tr>
                                    <th>DAY END DATE</th>
                                    <th>STOCK ROWS</th>
                                    <th>TOTAL QTY</th>
                                    <th>DONE BY</th>
                                    <th>DONE ON</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if ($resulthistory && $resulthistory->num_rows > 0) { while ($r = $resulthistory->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $r['dayenddate']; ?></td>
                                    <td class="text-right"><?php echo $r['totalrows']; ?></td>
                                    <td class="text-right"><?php echo number_format($r['totalqty'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($r['doneby']); ?></td>
                                    <td><?php echo $r['donedatetime']; ?></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm btnView"
                                            data-id="<?php echo $r['idtbl_dayend']; ?>"
                                            data-date="<?php echo $r['dayenddate']; ?>"><i class="fas fa-eye"></i></button>
                                    </td>
                                </tr>
                                <?php }} ?>
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

<!-- Snapshot modal -->
<div class="modal fade" id="snapshotModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Stock on <span id="snapDate"></span></h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered table-sm nowrap" id="snapshotTable" style="width:100%">
                    <thead class="thead-light">
                    <tr>
                        <th>STOCK ID</th>
                        <th>PRODUCT</th>
                        <th>LOCATION</th>
                        <th>QTY</th>
                        <th>LAST UPDATE</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include "include/footerscripts.php"; ?>

<script>
var snapshotTable;

$(document).ready(function () {

    $('#dayendHistory').DataTable({
        "order": [[0, "desc"]],
        "responsive": true
    });

    // Run day end
    $('#btnDayEnd').click(function () {
        var d = $('#dayenddate').val();
        if (!d) { alert('Select a date'); return; }
        if (!confirm('Run day end for ' + d + '?\nAll current stock rows will be saved. This cannot be undone.')) { return; }

        var $btn = $(this).prop('disabled', true);
        $.post('process/dayendprocess.php', { dayenddate: d }, function (res) {
            var ok = (res.type === 'success');
            var cls = ok ? 'alert-success' : 'alert-danger';
            $('#dayendMsg').html('<div class="alert ' + cls + ' py-2 mb-0">' + $('<div>').text(res.message).html() + '</div>');
            if (ok) { setTimeout(function () { location.reload(); }, 1500); }
            else { $btn.prop('disabled', false); }
        }, 'json').fail(function () {
            $('#dayendMsg').html('<div class="alert alert-danger py-2 mb-0">Request failed</div>');
            $btn.prop('disabled', false);
        });
    });

    // View snapshot
    $(document).on('click', '.btnView', function () {
        var id = $(this).data('id');
        $('#snapDate').text($(this).data('date'));
        $('#snapshotModal').modal('show');

        if (snapshotTable) { snapshotTable.destroy(); }
        snapshotTable = $('#snapshotTable').DataTable({
            ajax: { url: 'scripts/dayendstocklist.php', type: 'POST', data: { dayendid: id } },
            columns: [
                { data: 'idtbl_stock' },
                { data: 'productname', render: function (d) { return d ? d : '-'; } },
                { data: 'locationname', render: function (d) { return d ? d : '-'; } },
                { data: 'qty', className: 'text-right', render: $.fn.dataTable.render.number(',', '.', 2, '') },
                { data: 'update', render: function (d) { return d ? d : '-'; } }
            ],
            pageLength: 25
        });
    });
});
</script>

<?php include "include/footer.php"; ?>