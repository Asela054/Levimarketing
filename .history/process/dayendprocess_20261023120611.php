<?php
// Blocks the system until yesterday's day end is done
if (!isset($_SESSION['userid'])) { return; }

date_default_timezone_set('Asia/Colombo');
$yesterday = date('Y-m-d', strtotime('-1 day'));
$yesterdayNice = date('l, d M Y', strtotime($yesterday));

$needDayEnd = false;
$stmtde = $conn->prepare("SELECT `idtbl_dayend` FROM `tbl_dayend` WHERE `dayenddate` = ? LIMIT 1");
$stmtde->bind_param('s', $yesterday);
$stmtde->execute();
$stmtde->store_result();
if ($stmtde->num_rows == 0) { $needDayEnd = true; }
$stmtde->close();

if ($needDayEnd) { ?>
<style>
#dayendAlertModal .modal-dialog { max-width: 460px; }
#dayendAlertModal .modal-content {
    border: 0; border-radius: 18px; overflow: hidden;
    box-shadow: 0 25px 60px rgba(15, 23, 42, .35);
}
#dayendAlertModal .de-head {
    background: linear-gradient(135deg, #1e293b 0%, #334155 55%, #dc2626 140%);
    color: #fff; text-align: center; padding: 32px 24px 26px; position: relative;
}
#dayendAlertModal .de-icon {
    width: 72px; height: 72px; margin: 0 auto 14px; border-radius: 50%;
    background: rgba(255,255,255,.12); border: 2px solid rgba(255,255,255,.25);
    display: flex; align-items: center; justify-content: center; font-size: 30px;
    animation: dePulse 2.2s ease-in-out infinite;
}
@keyframes dePulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(255,255,255,.35); }
    50%      { box-shadow: 0 0 0 14px rgba(255,255,255,0); }
}
#dayendAlertModal .de-title { font-size: 1.35rem; font-weight: 700; margin: 0 0 4px; letter-spacing: .2px; }
#dayendAlertModal .de-sub   { font-size: .85rem; opacity: .8; margin: 0; }

#dayendAlertModal .modal-body { padding: 24px 28px 8px; background: #fff; }
#dayendAlertModal .de-pending {
    display: flex; align-items: center; background: #fef2f2; border: 1px solid #fecaca;
    border-radius: 12px; padding: 12px 14px; margin-bottom: 20px;
}
#dayendAlertModal .de-pending .de-pi {
    width: 40px; height: 40px; border-radius: 10px; background: #dc2626; color: #fff;
    display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0;
}
#dayendAlertModal .de-pending small { display: block; color: #991b1b; font-weight: 600; text-transform: uppercase; font-size: .68rem; letter-spacing: .6px; }
#dayendAlertModal .de-pending strong { color: #7f1d1d; font-size: .98rem; }

#dayendAlertModal .de-label { font-size: .72rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: .6px; margin-bottom: 6px; }
#dayendAlertModal .de-input {
    height: 46px; border-radius: 10px; border: 1.5px solid #e2e8f0; padding: 0 14px;
    font-size: .95rem; color: #0f172a; transition: all .2s;
}
#dayendAlertModal .de-input:focus { border-color: #dc2626; box-shadow: 0 0 0 4px rgba(220,38,38,.12); outline: 0; }

#dayendAlertModal .de-note { font-size: .8rem; color: #64748b; margin: 14px 0 6px; line-height: 1.5; }
#dayendAlertModal .de-note i { color: #94a3b8; margin-right: 4px; }

#dayendAlertModal .modal-footer { border: 0; padding: 16px 28px 26px; background: #fff; display: flex; }
#dayendAlertModal .de-btn {
    flex: 1; height: 46px; border: 0; border-radius: 10px; font-weight: 600; font-size: .92rem;
    color: #fff; background: linear-gradient(135deg, #ef4444, #b91c1c);
    box-shadow: 0 8px 18px rgba(220,38,38,.3); transition: transform .15s, box-shadow .15s;
}
#dayendAlertModal .de-btn:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 12px 22px rgba(220,38,38,.38); }
#dayendAlertModal .de-btn:disabled { opacity: .7; cursor: not-allowed; }
#dayendAlertModal .de-btn-ghost {
    flex: 0 0 38%; margin-right: 10px; background: #f1f5f9; color: #475569; box-shadow: none;
}
#dayendAlertModal .de-btn-ghost:hover:not(:disabled) { background: #e2e8f0; box-shadow: none; }

#dayendAlertModal .de-confirm { text-align: center; padding: 4px 0 10px; }
#dayendAlertModal .de-confirm .de-q {
    width: 56px; height: 56px; border-radius: 50%; background: #fff7ed; color: #ea580c;
    display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 10px;
}
#dayendAlertModal .de-confirm h6 { font-weight: 700; color: #0f172a; margin-bottom: 6px; }
#dayendAlertModal .de-confirm p  { color: #64748b; font-size: .85rem; margin: 0; }

#dayendAlertModal .de-success { text-align: center; padding: 10px 0 24px; }
#dayendAlertModal .de-success .de-ok {
    width: 72px; height: 72px; border-radius: 50%; background: #dcfce7; color: #16a34a;
    display: inline-flex; align-items: center; justify-content: center; font-size: 32px;
    margin-bottom: 12px; animation: dePop .4s cubic-bezier(.2,1.4,.4,1);
}
@keyframes dePop { from { transform: scale(0); } to { transform: scale(1); } }
#dayendAlertModal .de-success h6 { font-weight: 700; color: #0f172a; }
#dayendAlertModal .de-success p  { color: #64748b; font-size: .85rem; margin: 0; }

#dayendAlertModal .de-error {
    background: #fef2f2; color: #991b1b; border: 1px solid #fecaca;
    border-radius: 10px; padding: 10px 14px; font-size: .84rem; margin-top: 12px;
}
</style>

<div class="modal fade" id="dayendAlertModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="de-head">
                <div class="de-icon"><i class="fas fa-moon"></i></div>
                <h5 class="de-title">Day End Required</h5>
                <p class="de-sub">Complete the pending day end to continue</p>
            </div>

            <!-- Step 1: select date -->
            <div id="deStepForm">
                <div class="modal-body">
                    <div class="de-pending">
                        <div class="de-pi"><i class="fas fa-calendar-times"></i></div>
                        <div>
                            <small>Pending day end</small>
                            <strong><?php echo $yesterdayNice; ?></strong>
                        </div>
                    </div>

                    <div class="de-label">Day End Date</div>
                    <input type="date" class="form-control de-input" id="alertDayEndDate"
                           value="<?php echo $yesterday; ?>" max="<?php echo $yesterday; ?>">

                    <p class="de-note"><i class="fas fa-info-circle"></i>
                        All current stock quantities will be saved under this date. The system stays locked until the day end is completed.</p>
                    <div id="alertDayEndMsg"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="de-btn" id="btnAlertDayEnd"><i class="fas fa-lock"></i>&nbsp; End Day</button>
                </div>
            </div>

            <!-- Step 2: confirm -->
            <div id="deStepConfirm" style="display:none;">
                <div class="modal-body">
                    <div class="de-confirm">
                        <div class="de-q"><i class="fas fa-question"></i></div>
                        <h6>Run day end for <span id="deConfirmDate"></span>?</h6>
                        <p>Stock will be saved for this date and this cannot be undone.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="de-btn de-btn-ghost" id="btnDeBack">Back</button>
                    <button type="button" class="de-btn" id="btnDeConfirm"><i class="fas fa-check"></i>&nbsp; Yes, End Day</button>
                </div>
            </div>

            <!-- Step 3: success -->
            <div id="deStepSuccess" style="display:none;">
                <div class="modal-body">
                    <div class="de-success">
                        <div class="de-ok"><i class="fas fa-check"></i></div>
                        <h6>Day End Completed</h6>
                        <p id="deSuccessMsg"></p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#dayendAlertModal').modal({ backdrop: 'static', keyboard: false, show: true });

    function showStep(id) {
        $('#deStepForm, #deStepConfirm, #deStepSuccess').hide();
        $(id).show();
    }
    function showError(text) {
        $('#alertDayEndMsg').html('<div class="de-error"><i class="fas fa-exclamation-circle"></i>&nbsp; ' + $('<div>').text(text).html() + '</div>');
    }

    $('#btnAlertDayEnd').click(function () {
        var d = $('#alertDayEndDate').val();
        if (!d) { showError('Please select a date'); return; }
        $('#alertDayEndMsg').empty();
        $('#deConfirmDate').text(d);
        showStep('#deStepConfirm');
    });

    $('#btnDeBack').click(function () { showStep('#deStepForm'); });

    $('#btnDeConfirm').click(function () {
        var d = $('#alertDayEndDate').val();
        var $btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>&nbsp; Processing...');
        $('#btnDeBack').prop('disabled', true);

        function reset() {
            $btn.prop('disabled', false).html('<i class="fas fa-check"></i>&nbsp; Yes, End Day');
            $('#btnDeBack').prop('disabled', false);
        }

        $.post('process/dayendprocess.php', { dayenddate: d }, function (res) {
            if (res.type === 'success') {
                $('#deSuccessMsg').text(res.message);
                showStep('#deStepSuccess');
                setTimeout(function () { location.reload(); }, 1600);
            } else {
                reset();
                showStep('#deStepForm');
                showError(res.message);
            }
}, 'json').fail(function (xhr) {
    reset();
    showStep('#deStepForm');
    showError('Request failed (' + xhr.status + '): ' + xhr.responseText.substring(0, 300));
});
    });
});
</script>
<?php } ?>