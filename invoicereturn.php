<?php
include "include/header.php";

$sql = "SELECT * FROM `tbl_location` WHERE `status` IN (1,2)";
$result = $conn->query($sql);

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
                            <div class="page-header-icon"><i data-feather="file"></i></div>
                            <span>Invoice Return</span>
                        </h1>
                    </div>
                </div>
            </div>

            <div class="container-fluid mt-2 p-0 p-2 invret">

                <!-- Quick counts -->
                <div class="invret-card mb-3">
                    <div class="invret-stat-strip">
                        <div class="invret-stat">
                            <span class="invret-stat-label">Total returns</span>
                            <span class="invret-stat-value" id="statTotal">—</span>
                        </div>
                        <div class="invret-stat">
                            <span class="invret-stat-label">Pending approval</span>
                            <span class="invret-stat-value invret-stat-warn" id="statPending">—</span>
                        </div>
                        <div class="invret-stat">
                            <span class="invret-stat-label">Approved</span>
                            <span class="invret-stat-value invret-stat-good" id="statApproved">—</span>
                        </div>
                    </div>
                </div>

                <div class="invret-card">
                    <div class="invret-card-head">
                        <i data-feather="file-text" class="invret-card-icon"></i>
                        <div>
                            <h2 class="invret-card-title">All invoice returns</h2>
                            <p class="invret-card-sub">Review pending returns and approve them to restock quantities.</p>
                        </div>
                    </div>
                    <div class="invret-card-body">
                        <div class="table-responsive">
                            <table class="invret-table" id="tblInvoiceReturn" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Location</th>
                                        <th class="text-end">Total</th>
                                        <th>Remarks</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </main>
        <?php include "include/footerbar.php"; ?>
    </div>
</div>

<!-- Detail / Approve modal -->
<div class="modal fade" id="modalReturnDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg invret">
        <div class="modal-content invret-modal">
            <div class="modal-header invret-modal-head">
                <div class="d-flex align-items-start gap-2">
                    <i data-feather="corner-up-left" class="invret-card-icon" style="margin-top:2px"></i>
                    <div>
                        <h5 class="modal-title mb-0">Invoice return detail</h5>
                        <p class="invret-card-sub mb-0" id="modalReturnSub"></p>
                    </div>
                </div>
            </div>

            <div class="invret-stat-strip">
                <div class="invret-stat">
                    <span class="invret-stat-label">Return #</span>
                    <span class="invret-stat-value" id="modalReturnNo"></span>
                </div>
                <div class="invret-stat">
                    <span class="invret-stat-label">Date</span>
                    <span class="invret-stat-value" id="modalReturnDate"></span>
                </div>
                <div class="invret-stat">
                    <span class="invret-stat-label">Location</span>
                    <span class="invret-stat-value" id="modalReturnLocation"></span>
                </div>
                <div class="invret-stat">
                    <span class="invret-stat-label">Status</span>
                    <span id="modalReturnStatus"></span>
                </div>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table class="invret-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Unit price</th>
                                <th class="text-end">Net total</th>
                                <th>Comment</th>
                            </tr>
                        </thead>
                        <tbody id="returnDetailBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer invret-modal-foot">
                <button type="button" class="invret-btn-ghost" data-bs-dismiss="modal" onclick="closeReturnModal()">Close</button>
                <button type="button" class="invret-btn-primary" id="btnApproveConfirm">
                    <i data-feather="check-circle"></i>
                    Approve &amp; update stock
                </button>
            </div>
        </div>
    </div>
</div>

<?php include "include/footerscripts.php"; ?>

<!-- Fonts: Sora for headings/labels, Inter for body & data (same system as the return-add page) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- DataTables assets (skip if already loaded globally in footerscripts.php) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.13.11/css/jquery.dataTables.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.13.11/js/jquery.dataTables.min.js"></script>

<style>
.invret {
    --ink: #12161C;
    --ink-soft: #5B6472;
    --bg: #F3F5F9;
    --surface: #FFFFFF;
    --border: #E2E6ED;
    --accent: #0F766E;
    --accent-dark: #0B5C56;
    --accent-soft: #E4F5F3;
    --return: #EA6C4D;
    --return-soft: #FDEAE3;
    --warn: #D97706;
    --warn-soft: #FEF3E2;
    --danger: #DC2626;
    --danger-soft: #FDEBEA;
    --success: #15803D;
    --success-soft: #E8F6EC;
    --radius: 12px;
    --radius-sm: 8px;
    --shadow-sm: 0 1px 2px rgba(18,22,28,.06);
    --shadow-md: 0 10px 28px rgba(18,22,28,.08);
    font-family: 'Inter', -apple-system, sans-serif;
    color: var(--ink);
}
.invret h2, .invret h5.modal-title, .invret .invret-label { font-family: 'Sora', 'Inter', sans-serif; }
.page-header-icon { color: var(--accent, #0F766E); }

/* Cards */
.invret-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow-sm); overflow: hidden; }
.invret-card-head { display: flex; align-items: flex-start; gap: 12px; padding: 18px 20px; border-bottom: 1px solid var(--border); }
.invret-card-icon { width: 20px; height: 20px; color: var(--accent); margin-top: 3px; flex-shrink: 0; }
.invret-card-title { font-size: 16px; font-weight: 600; margin: 0 0 2px; }
.invret-card-sub { font-size: 13px; color: var(--ink-soft); margin: 0; }
.invret-card-body { padding: 20px; }

/* Stat strip (reused for page header counts and modal header facts) */
.invret-stat-strip { display: flex; flex-wrap: wrap; background: var(--bg); border-bottom: 1px solid var(--border); }
.invret-stat { flex: 1 1 140px; padding: 14px 20px; border-right: 1px solid var(--border); }
.invret-stat:last-child { border-right: none; }
.invret-stat-label { display: block; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: var(--ink-soft); margin-bottom: 3px; }
.invret-stat-value { font-weight: 600; font-variant-numeric: tabular-nums; font-size: 16px; }
.invret-stat-warn { color: var(--warn); }
.invret-stat-good { color: var(--success); }

/* Table (shared look with return-add page) */
.invret-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.invret-table thead th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: var(--ink-soft); font-weight: 600; padding: 10px 12px; border-bottom: 1px solid var(--border); background: var(--bg); }
.invret-table tbody td { padding: 10px 12px; border-bottom: 1px solid var(--border); vertical-align: middle; font-variant-numeric: tabular-nums; }
.invret-table tbody tr:last-child td { border-bottom: none; }
.invret-table tbody tr:hover td { background: var(--bg); }
.invret-table .text-end { text-align: right; }

/* Status pills */
.invret-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
.invret-badge.approved { background: var(--success-soft); color: var(--success); }
.invret-badge.pending { background: var(--warn-soft); color: var(--warn); }
.invret-badge.dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

/* Row action buttons */
.invret-row-btn { display: inline-flex; align-items: center; gap: 6px; border: none; border-radius: 999px; padding: 6px 12px; font-size: 12.5px; font-weight: 600; cursor: pointer; transition: background .15s ease, color .15s ease; }
.invret-row-btn svg { width: 14px; height: 14px; }
.invret-row-btn.view { background: #EEF1F6; color: var(--ink-soft); }
.invret-row-btn.view:hover { background: #E2E6ED; color: var(--ink); }
.invret-row-btn.review { background: var(--accent-soft); color: var(--accent-dark); }
.invret-row-btn.review:hover { background: var(--accent); color: #fff; }

/* Buttons */
.invret-btn-primary {
    display: inline-flex; align-items: center; gap: 8px;
    background: var(--accent); color: #fff; border: none; border-radius: var(--radius-sm);
    padding: 10px 18px; font-weight: 600; font-size: 14px; cursor: pointer;
    transition: background .18s ease, transform .1s ease;
}
.invret-btn-primary svg { width: 16px; height: 16px; }
.invret-btn-primary:hover { background: var(--accent-dark); }
.invret-btn-primary:active { transform: translateY(1px); }
.invret-btn-ghost { background: transparent; border: 1px solid var(--border); color: var(--ink-soft); border-radius: var(--radius-sm); padding: 10px 16px; font-weight: 600; font-size: 14px; cursor: pointer; transition: background .15s ease; }
.invret-btn-ghost:hover { background: var(--bg); }

/* Modal chrome */
.invret-modal { border-radius: var(--radius); border: 1px solid var(--border); overflow: hidden; }
.invret-modal-head { border-bottom: 1px solid var(--border); padding: 18px 20px; }
.invret-modal-foot { border-top: 1px solid var(--border); padding: 14px 20px; gap: 10px; }

/* DataTables chrome, restyled to match the card system */
.invret .dataTables_wrapper { font-size: 13.5px; }
.invret .dataTables_filter { margin-bottom: 14px; }
.invret .dataTables_filter input {
    border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 7px 12px; margin-left: 8px;
    font-family: 'Inter', sans-serif; min-width: 220px;
}
.invret .dataTables_filter input:focus-visible { outline: 2px solid var(--accent); outline-offset: 1px; }
.invret .dataTables_length select {
    border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 5px 8px; margin: 0 6px;
}
.invret .dataTables_info { color: var(--ink-soft); font-size: 12.5px; padding-top: 12px; }
.invret .dataTables_paginate .paginate_button {
    border-radius: var(--radius-sm) !important; margin-left: 4px; padding: 5px 11px !important;
    border: 1px solid transparent !important; color: var(--ink-soft) !important;
}
.invret .dataTables_paginate .paginate_button.current {
    background: var(--accent) !important; color: #fff !important; border-color: var(--accent) !important;
}
.invret .dataTables_paginate .paginate_button:hover:not(.current) { background: var(--bg) !important; border-color: var(--border) !important; }
.invret table.dataTable { border-collapse: collapse !important; }
.invret div.dataTables_wrapper div.dataTables_processing {
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm);
    box-shadow: var(--shadow-md); color: var(--ink-soft); font-weight: 600;
}

@media (prefers-reduced-motion: reduce) {
    .invret-row-btn, .invret-btn-primary, .invret-btn-ghost { transition: none !important; }
}
</style>

<script>
let currentReturnID = null;
let returnTable = null;
let returnsById = {};
let returnModalInstance = null;

// Reuse a single Modal instance instead of calling `new bootstrap.Modal(...)`
// on every open. Creating a fresh instance each time a row is viewed stacks
// extra backdrops/instances on top of each other, and after the first
// open-close cycle the close button (and backdrop click) stop registering
// because a stale instance is intercepting the click.
function getReturnModal() {
    if (!returnModalInstance) {
        returnModalInstance = new bootstrap.Modal(document.getElementById('modalReturnDetail'));
    }
    return returnModalInstance;
}

function closeReturnModal() {
    getReturnModal().hide();
}

function statusBadgeHtml(row) {
    const isApproved = row.approvestatus == 1;
    return isApproved
        ? '<span class="invret-badge approved"><span class="invret-badge dot"></span>Approved</span>'
        : '<span class="invret-badge pending"><span class="invret-badge dot"></span>Pending</span>';
}

function actionBtnHtml(row) {
    const isApproved = row.approvestatus == 1;
    return isApproved
        ? '<button class="invret-row-btn view" onclick="viewReturn(' + row.idtbl_invoice_return + ', true)"><i data-feather="eye"></i>View</button>'
        : '<button class="invret-row-btn review" onclick="viewReturn(' + row.idtbl_invoice_return + ', false)"><i data-feather="check-circle"></i>Review</button>';
}

function updateStats(rows) {
    const total = rows.length;
    const approved = rows.filter(r => r.approvestatus == 1).length;
    document.getElementById('statTotal').textContent = total;
    document.getElementById('statApproved').textContent = approved;
    document.getElementById('statPending').textContent = total - approved;
}

function initReturnTable() {
    returnTable = $('#tblInvoiceReturn').DataTable({
        dom: '<"d-flex justify-content-between align-items-center flex-wrap"f>rt<"d-flex justify-content-between align-items-center flex-wrap"ip>',
        processing: true,
        language: {
            processing: 'Loading returns…',
            search: '',
            searchPlaceholder: 'Search returns…',
            emptyTable: 'No invoice returns yet.'
        },
        ajax: {
            url: 'getprocess/getInvoiceReturnList.php',
            dataSrc: function (json) {
                // Endpoint returns a plain JSON array, same as before — build
                // a lookup map here so the modal can show header context
                // (date/location/status) without a second request.
                returnsById = {};
                json.forEach(r => { returnsById[r.idtbl_invoice_return] = r; });
                updateStats(json);
                return json;
            }
        },
        columns: [
            {
                data: 'idtbl_invoice_return',
                render: (data) => 'INVRET-' + data
            },
            { data: 'date' },
            { data: 'location_name', defaultContent: '' },
            { data: 'total', className: 'text-end', render: (data) => Number(data).toFixed(2) },
            { data: 'remarks', defaultContent: '<span class="text-muted">—</span>' },
            { data: null, render: (data, type, row) => statusBadgeHtml(row) },
            { data: null, orderable: false, searchable: false, render: (data, type, row) => actionBtnHtml(row) }
        ],
        order: [[0, 'desc']],
        drawCallback: function () {
            if (window.feather) feather.replace();
        }
    });
}

function viewReturn(returnID, isApproved) {
    currentReturnID = returnID;

    const header = returnsById[returnID];
    if (header) {
        document.getElementById('modalReturnNo').textContent = 'INVRET-' + header.idtbl_invoice_return;
        document.getElementById('modalReturnDate').textContent = header.date;
        document.getElementById('modalReturnLocation').textContent = header.location_name || '—';
        document.getElementById('modalReturnStatus').innerHTML = statusBadgeHtml(header);
        document.getElementById('modalReturnSub').textContent = (header.remarks && header.remarks.trim()) || 'No remarks provided.';
    }

    fetch('getprocess/getInvoiceReturnDetail.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'recordID=' + encodeURIComponent(returnID)
    })
        .then(res => res.json())
        .then(data => {
            const body = document.getElementById('returnDetailBody');
            body.innerHTML = '';
            data.forEach(line => {
                body.insertAdjacentHTML('beforeend', `
                    <tr>
                        <td>${line.product_name ?? ''}</td>
                        <td class="text-end">${line.qty}</td>
                        <td class="text-end">${Number(line.unitprice).toFixed(2)}</td>
                        <td class="text-end">${Number(line.nettotal).toFixed(2)}</td>
                        <td>${line.comment ?? ''}</td>
                    </tr>
                `);
            });

            document.getElementById('btnApproveConfirm').style.display = isApproved ? 'none' : 'inline-flex';

            getReturnModal().show();
            if (window.feather) feather.replace();
        });
}

document.getElementById('btnApproveConfirm').addEventListener('click', function () {
    if (!currentReturnID) return;

    if (!confirm('Approve this return? This will add the returned quantities back into stock for the return\'s location.')) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'process/invoiceReturnApprove.php';

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'recordID';
    input.value = currentReturnID;

    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
});

$(document).ready(function () {
    if (window.feather) feather.replace();
    initReturnTable();
});
</script>
<?php include "include/footer.php"; ?>