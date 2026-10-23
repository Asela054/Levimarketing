<?php
if (!isset($_SESSION['userid'])) { return; }

date_default_timezone_set('Asia/Colombo');
$yesterday = date('Y-m-d', strtotime('-1 day'));

$needDayEnd = false;
$stmtde = $conn->prepare("SELECT `idtbl_dayend` FROM `tbl_dayend` WHERE `dayenddate` = ? LIMIT 1");
$stmtde->bind_param('s', $yesterday);
$stmtde->execute();
$stmtde->store_result();
if ($stmtde->num_rows == 0) { $needDayEnd = true; }
$stmtde->close();

if ($needDayEnd) { ?>
<div class="modal fade" id="dayendAlertModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i>&nbsp; Day End Required</h5>
            </div>
            <div class="modal-body">
                <p class="mb-3">Yesterday (<strong><?php echo $yesterday; ?></strong>) was not day ended. You must complete the day end before using the system.</p>
                <label class="small font-weight-bold text-dark mb-1">Day End Date</label>
                <input type="date" class="form-control form-control-sm" id="alertDayEndDate" value="<?php echo $yesterday; ?>" max="<?php echo $yesterday; ?>">
                <div id="alertDayEndMsg" class="mt-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" id="btnAlertDayEnd"><i class="fas fa-lock"></i>&nbsp;End Day</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#dayendAlertModal').modal({ backdrop: 'static', keyboard: false, show: true });

    $('#btnAlertDayEnd').click(function () {
        var d = $('#alertDayEndDate').val();
        if (!d) { alert('Select a date'); return; }
        if (!confirm('Run day end for ' + d + '?\nAll current stock rows will be saved. This cannot be undone.')) { return; }

        var $btn = $(this).prop('disabled', true);
        $.post('process/dayendprocess.php', { dayenddate: d }, function (res) {
            var ok = (res.type === 'success');
            var cls = ok ? 'alert-success' : 'alert-danger';
            $('#alertDayEndMsg').html('<div class="alert ' + cls + ' py-2 mb-0">' + $('<div>').text(res.message).html() + '</div>');
            if (ok) { setTimeout(function () { location.reload(); }, 1200); }
            else { $btn.prop('disabled', false); }
        }, 'json').fail(function () {
            $('#alertDayEndMsg').html('<div class="alert alert-danger py-2 mb-0">Request failed</div>');
            $btn.prop('disabled', false);
        });
    });
});
</script>
<?php } ?>