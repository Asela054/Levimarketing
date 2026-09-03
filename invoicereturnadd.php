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
                            <div class="page-header-icon"><i data-feather="corner-up-left"></i></div>
                            <span>New Invoice Return</span>
                        </h1>
                    </div>
                </div>
            </div>

            <div class="container-fluid mt-2 p-0 p-2 invret">

                <?php if (isset($_GET['action']) && $_GET['action'] == 5): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Could not save the return. Check the quantities and try again.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Progress stepper: this really is a 3-step sequence, not decoration -->
                <div class="invret-stepper" id="invretStepper">
                    <div class="invret-step is-active" data-step="1">
                        <span class="invret-step-dot">1</span>
                        <span class="invret-step-label">Find invoice</span>
                    </div>
                    <div class="invret-step-line" data-line="1"></div>
                    <div class="invret-step is-locked" data-step="2">
                        <span class="invret-step-dot">2</span>
                        <span class="invret-step-label">Review items</span>
                    </div>
                    <div class="invret-step-line" data-line="2"></div>
                    <div class="invret-step is-locked" data-step="3">
                        <span class="invret-step-dot">3</span>
                        <span class="invret-step-label">Confirm return</span>
                    </div>
                </div>

                <!-- Step 1: find invoice -->
                <div class="invret-card mb-3">
                    <div class="invret-card-head">
                        <i data-feather="search" class="invret-card-icon"></i>
                        <div>
                            <h2 class="invret-card-title">Find an invoice</h2>
                            <p class="invret-card-sub">Filter by type, then search by invoice number.</p>
                        </div>
                    </div>
                    <div class="invret-card-body">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="invret-label">Invoice type</label>
                                <select id="invoiceTypeFilter" class="form-select">
                                    <option value="">All invoices</option>
                                    <option value="1">Tax invoice</option>
                                    <option value="0">Non-tax invoice</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="invret-label">Invoice number</label>
                                <select id="invoiceNumber" class="form-select">
                                    <option value="">-- Select invoice --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty state, shown until an invoice is chosen -->
                <div class="invret-empty" id="invretEmpty">
                    <i data-feather="package" class="invret-empty-icon"></i>
                    <p class="invret-empty-title">No invoice selected yet</p>
                    <p class="invret-empty-sub">Search above and pick an invoice to see its returnable items.</p>
                </div>

                <!-- Loading skeleton, shown while an invoice's lines are being fetched -->
                <div class="invret-card d-none" id="invretSkeleton">
                    <div class="invret-card-body">
                        <div class="invret-skel-row"></div>
                        <div class="invret-skel-row"></div>
                        <div class="invret-skel-row"></div>
                    </div>
                </div>

                <!-- Step 2 + 3: invoice detail, lines, remarks, submit -->
                <div class="invret-card d-none" id="cardInvoiceInfo">
                    <div class="invret-card-head">
                        <i data-feather="file-text" class="invret-card-icon"></i>
                        <div>
                            <h2 class="invret-card-title">Review items</h2>
                            <p class="invret-card-sub">Set a return quantity for each line you're taking back.</p>
                        </div>
                        <span class="invret-type-badge" id="infoTypeBadge"></span>
                    </div>

                    <div class="invret-stat-strip">
                        <div class="invret-stat">
                            <span class="invret-stat-label">Invoice #</span>
                            <span class="invret-stat-value" id="infoInvoiceNo"></span>
                        </div>
                        <div class="invret-stat">
                            <span class="invret-stat-label">Date</span>
                            <span class="invret-stat-value" id="infoInvoiceDate"></span>
                        </div>
                        <div class="invret-stat">
                            <span class="invret-stat-label">Original total</span>
                            <span class="invret-stat-value" id="infoInvoiceTotal"></span>
                        </div>
                        <div class="invret-stat">
                            <span class="invret-stat-label">Line items</span>
                            <span class="invret-stat-value" id="infoLineCount"></span>
                        </div>
                    </div>

                    <div class="invret-card-body">
                        <div class="table-responsive">
                            <table class="invret-table" id="tblReturnLines">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-end">Sold qty</th>
                                        <th class="text-end">Already returned</th>
                                        <th class="text-end">Available</th>
                                        <th style="width:150px">Return qty</th>
                                        <th class="text-end">Unit price</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div class="invret-remarks">
                            <label class="invret-label" for="remarks">Remarks</label>
                            <textarea id="remarks" class="form-control" rows="2" placeholder="Optional note for whoever approves this return…"></textarea>
                        </div>
                    </div>

                    <div class="invret-summary-bar">
                        <div class="invret-summary-info">
                            <span class="invret-summary-count" id="summaryCount">0 lines selected</span>
                            <span class="invret-summary-total">Return total: <strong id="summaryTotal">Rs. 0.00</strong></span>
                        </div>
                        <button type="button" class="invret-btn-primary" id="btnSubmitReturn" disabled>
                            <i data-feather="check-circle"></i>
                            Submit return
                        </button>
                    </div>
                </div>

            </div>
        </main>
        <?php include "include/footerbar.php"; ?>
    </div>
</div>
<?php include "include/footerscripts.php"; ?>

<!-- Fonts: Sora for headings/labels, Inter for body & data -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Select2 assets (skip if already loaded globally in footerscripts.php) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

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
.invret .invret-label,
.invret h2, .invret .invret-summary-count { font-family: 'Sora', 'Inter', sans-serif; }

/* Page header icon: recolor to match the return accent */
.page-header-icon { color: var(--return, #EA6C4D); }

/* Stepper */
.invret-stepper { display: flex; align-items: center; gap: 4px; margin: 4px 0 20px; padding: 4px 2px; }
.invret-step { display: flex; align-items: center; gap: 10px; opacity: .45; transition: opacity .25s ease; }
.invret-step.is-active, .invret-step.is-done { opacity: 1; }
.invret-step-dot {
    width: 28px; height: 28px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Sora', sans-serif; font-size: 13px; font-weight: 600;
    background: var(--surface); border: 2px solid var(--border); color: var(--ink-soft);
    transition: all .25s ease; flex-shrink: 0;
}
.invret-step.is-active .invret-step-dot { border-color: var(--return); color: var(--return); background: var(--return-soft); }
.invret-step.is-done .invret-step-dot { border-color: var(--accent); background: var(--accent); color: #fff; }
.invret-step-label { font-size: 13.5px; font-weight: 600; color: var(--ink-soft); white-space: nowrap; }
.invret-step.is-active .invret-step-label { color: var(--ink); }
.invret-step-line { flex: 1; height: 2px; background: var(--border); border-radius: 1px; transition: background .25s ease; min-width: 24px; }
.invret-step-line.is-filled { background: var(--accent); }
@media (max-width: 640px) {
    .invret-step-label { display: none; }
    .invret-stepper { justify-content: space-between; }
}

/* Cards */
.invret-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow-sm); overflow: hidden; }
.invret-card-head { display: flex; align-items: flex-start; gap: 12px; padding: 18px 20px; border-bottom: 1px solid var(--border); }
.invret-card-icon { width: 20px; height: 20px; color: var(--accent); margin-top: 3px; flex-shrink: 0; }
.invret-card-title { font-size: 16px; font-weight: 600; margin: 0 0 2px; }
.invret-card-sub { font-size: 13px; color: var(--ink-soft); margin: 0; }
.invret-card-body { padding: 20px; }
.invret-type-badge { margin-left: auto; align-self: flex-start; }

.invret-label { display: block; font-size: 11.5px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-soft); margin-bottom: 6px; }
.invret-hint { font-size: 12.5px; color: var(--ink-soft); margin-top: 6px; }

/* Empty state */
.invret-empty { text-align: center; padding: 46px 20px; border: 1px dashed var(--border); border-radius: var(--radius); color: var(--ink-soft); background: var(--surface); margin-bottom: 12px; }
.invret-empty-icon { width: 30px; height: 30px; color: var(--border); margin-bottom: 10px; }
.invret-empty-title { font-weight: 600; color: var(--ink); margin: 0 0 4px; }
.invret-empty-sub { font-size: 13px; margin: 0; }

/* Skeleton loading rows */
.invret-skel-row { height: 40px; border-radius: var(--radius-sm); margin-bottom: 10px; background: linear-gradient(90deg, #EEF1F6 25%, #F7F8FB 37%, #EEF1F6 63%); background-size: 400% 100%; animation: invret-shimmer 1.4s ease infinite; }
@keyframes invret-shimmer { 0% { background-position: 100% 50%; } 100% { background-position: 0 50%; } }

/* Stat strip */
.invret-stat-strip { display: flex; flex-wrap: wrap; gap: 0; background: var(--bg); border-bottom: 1px solid var(--border); }
.invret-stat { flex: 1 1 140px; padding: 14px 20px; border-right: 1px solid var(--border); }
.invret-stat:last-child { border-right: none; }
.invret-stat-label { display: block; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: var(--ink-soft); margin-bottom: 3px; }
.invret-stat-value { font-family: 'Inter', sans-serif; font-weight: 600; font-variant-numeric: tabular-nums; font-size: 14.5px; }

/* Type badge */
.invret-type-badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
.invret-type-badge.tax { background: var(--accent-soft); color: var(--accent-dark); }
.invret-type-badge.nontax { background: #EEF1F6; color: var(--ink-soft); }

/* Table */
.invret-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.invret-table thead th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: var(--ink-soft); font-weight: 600; padding: 10px 12px; border-bottom: 1px solid var(--border); background: var(--bg); position: sticky; top: 0; }
.invret-table tbody td { padding: 10px 12px; border-bottom: 1px solid var(--border); vertical-align: middle; font-variant-numeric: tabular-nums; }
.invret-table tbody tr:last-child td { border-bottom: none; }
.invret-table tbody tr.is-selected { background: var(--return-soft); }
.invret-table tbody tr.is-selected td:first-child { box-shadow: inset 3px 0 0 var(--return); }
.invret-table tbody tr.is-exhausted { color: var(--ink-soft); background: #FAFAFB; }
.invret-table .text-end { text-align: right; }
.invret-qty-input { width: 100%; text-align: right; font-variant-numeric: tabular-nums; }
.invret-exhausted-tag { font-size: 11px; background: #EEF1F6; color: var(--ink-soft); padding: 2px 8px; border-radius: 999px; }

/* Remarks */
.invret-remarks { margin-top: 18px; }

/* Summary / submit bar */
.invret-summary-bar { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 16px 20px; background: var(--bg); border-top: 1px solid var(--border); flex-wrap: wrap; }
.invret-summary-info { display: flex; flex-direction: column; gap: 2px; }
.invret-summary-count { font-size: 13px; color: var(--ink-soft); font-weight: 600; }
.invret-summary-total { font-size: 15px; }
.invret-summary-total strong { font-variant-numeric: tabular-nums; color: var(--ink); }

.invret-btn-primary {
    display: inline-flex; align-items: center; gap: 8px;
    background: var(--accent); color: #fff; border: none; border-radius: var(--radius-sm);
    padding: 10px 18px; font-weight: 600; font-size: 14px; cursor: pointer;
    transition: background .18s ease, transform .1s ease;
}
.invret-btn-primary svg { width: 16px; height: 16px; }
.invret-btn-primary:hover:not(:disabled) { background: var(--accent-dark); }
.invret-btn-primary:active:not(:disabled) { transform: translateY(1px); }
.invret-btn-primary:disabled { background: #C9CFD8; cursor: not-allowed; }

/* Focus visibility */
.invret select:focus-visible, .invret input:focus-visible, .invret textarea:focus-visible, .invret button:focus-visible {
    outline: 2px solid var(--accent); outline-offset: 2px;
}

/* Select2 restyle to match the theme */
.invret .select2-container--bootstrap .select2-selection { border-color: var(--border); border-radius: var(--radius-sm); min-height: 38px; }
.invret .select2-container--bootstrap.select2-container--focus .select2-selection,
.invret .select2-container--bootstrap.select2-container--open .select2-selection { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-soft); }
.invret-opt-row { display: flex; align-items: center; gap: 8px; padding: 2px 0; }
.invret-opt-badge { flex-shrink: 0; padding: 1px 7px; border-radius: 999px; font-size: 10.5px; font-weight: 700; }
.invret-opt-badge.tax { background: var(--accent-soft); color: var(--accent-dark); }
.invret-opt-badge.nontax { background: #EEF1F6; color: var(--ink-soft); }
.invret-opt-no { font-weight: 600; }
.invret-opt-meta { margin-left: auto; font-size: 12px; color: var(--ink-soft); font-variant-numeric: tabular-nums; white-space: nowrap; }

/* Toasts, replacing native alert() for a calmer failure/validation experience */
.invret-toast-wrap { position: fixed; top: 18px; right: 18px; z-index: 2000; display: flex; flex-direction: column; gap: 10px; }
.invret-toast { min-width: 260px; max-width: 360px; background: var(--surface); border: 1px solid var(--border); border-left: 4px solid var(--ink-soft); border-radius: var(--radius-sm); box-shadow: var(--shadow-md); padding: 12px 14px; font-size: 13.5px; display: flex; gap: 10px; align-items: flex-start; animation: invret-toast-in .2s ease; }
.invret-toast.success { border-left-color: var(--success); }
.invret-toast.error { border-left-color: var(--danger); }
.invret-toast svg { width: 17px; height: 17px; flex-shrink: 0; margin-top: 1px; }
.invret-toast.success svg { color: var(--success); }
.invret-toast.error svg { color: var(--danger); }
@keyframes invret-toast-in { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }

@media (prefers-reduced-motion: reduce) {
    .invret-skel-row, .invret-toast, .invret-btn-primary, .invret-step, .invret-step-dot, .invret-step-line { animation: none !important; transition: none !important; }
}
</style>

<div class="invret-toast-wrap" id="invretToasts"></div>

<script>
let currentInvoiceID = null;

/* ---------- Toasts (replaces alert()) ---------- */
function showToast(message, type = 'error') {
    const wrap = document.getElementById('invretToasts');
    const el = document.createElement('div');
    el.className = 'invret-toast ' + type;
    const icon = type === 'success'
        ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>'
        : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>';
    el.innerHTML = icon + '<span>' + message + '</span>';
    wrap.appendChild(el);
    setTimeout(() => { el.remove(); }, 4500);
}

/* ---------- Fetch with timeout ---------- */
function fetchJSON(url, options = {}, timeoutMs = 12000) {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), timeoutMs);

    return fetch(url, { ...options, signal: controller.signal })
        .then(res => {
            if (!res.ok) throw new Error('Request failed (HTTP ' + res.status + ')');
            return res.json();
        })
        .finally(() => clearTimeout(timer));
}

/* ---------- Stepper ---------- */
function setStep(step) {
    document.querySelectorAll('.invret-step').forEach(el => {
        const n = Number(el.dataset.step);
        el.classList.toggle('is-active', n === step);
        el.classList.toggle('is-done', n < step);
        el.classList.toggle('is-locked', n > step);
    });
    document.querySelectorAll('.invret-step-line').forEach(el => {
        const n = Number(el.dataset.line);
        el.classList.toggle('is-filled', n < step);
    });
}

function resetInvoiceDetail() {
    currentInvoiceID = null;
    document.getElementById('cardInvoiceInfo').classList.add('d-none');
    document.getElementById('invretSkeleton').classList.add('d-none');
    document.getElementById('invretEmpty').classList.remove('d-none');
    document.getElementById('btnSubmitReturn').disabled = true;
    setStep(1);
}

function money(n) {
    return 'Rs. ' + Number(n || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

/* ---------- Live summary total across return-qty inputs ---------- */
function updateSummary() {
    let count = 0, total = 0;
    document.querySelectorAll('#tblReturnLines tbody tr').forEach(row => {
        const input = row.querySelector('.invret-qty-input');
        const qty = parseFloat(input.value) || 0;
        const price = parseFloat(row.dataset.unitprice) || 0;
        row.classList.toggle('is-selected', qty > 0);
        if (qty > 0) { count++; total += qty * price; }
    });
    document.getElementById('summaryCount').textContent = count + (count === 1 ? ' line selected' : ' lines selected');
    document.getElementById('summaryTotal').textContent = money(total);
    document.getElementById('btnSubmitReturn').disabled = count === 0;
    setStep(count > 0 ? 3 : 2);
}

/* NOTE: these must be jQuery .on('change', ...) handlers, not
   addEventListener. Select2 fires a jQuery-only "change" event on the
   underlying <select> — it never dispatches a native browser change event. */
$('#invoiceTypeFilter').on('change', function () {
    $('#invoiceNumber').val(null).trigger('change');
});

$('#invoiceNumber').on('change', function () {
    const invoiceID = this.value;
    if (!invoiceID) {
        resetInvoiceDetail();
        return;
    }

    document.getElementById('invretEmpty').classList.add('d-none');
    document.getElementById('cardInvoiceInfo').classList.add('d-none');
    document.getElementById('invretSkeleton').classList.remove('d-none');

    fetchJSON('getprocess/getInvoiceForReturn.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'invoiceID=' + encodeURIComponent(invoiceID)
    })
        .then(data => {
            if (data.error) {
                showToast(data.error, 'error');
                resetInvoiceDetail();
                return;
            }

            currentInvoiceID = data.invoice.idtbl_invoice;

            document.getElementById('infoInvoiceNo').textContent = data.invoice.taxinvoice_no || data.invoice.manuelinvno;
            document.getElementById('infoInvoiceDate').textContent = data.invoice.date;
            document.getElementById('infoInvoiceTotal').textContent = money(data.invoice.total);
            document.getElementById('infoLineCount').textContent = data.lines.length;

            const isTax = Number(data.invoice.invtype) === 1;
            const badge = document.getElementById('infoTypeBadge');
            badge.textContent = isTax ? 'Tax invoice' : 'Non-tax invoice';
            badge.className = 'invret-type-badge ' + (isTax ? 'tax' : 'nontax');

            const tbody = document.querySelector('#tblReturnLines tbody');
            tbody.innerHTML = '';

            data.lines.forEach(line => {
                const available = Number(line.available_qty);
                const row = document.createElement('tr');
                row.dataset.productId = line.tbl_product_idtbl_product;
                row.dataset.unitprice = line.unitprice;
                row.dataset.available = available;
                if (available <= 0) row.classList.add('is-exhausted');

                row.innerHTML = `
                    <td>${line.product_name ?? ''}</td>
                    <td class="text-end">${line.qty}</td>
                    <td class="text-end">${line.already_returned}</td>
                    <td class="text-end">${available <= 0 ? '<span class="invret-exhausted-tag">Fully returned</span>' : available}</td>
                    <td>
                        <input type="number" class="form-control invret-qty-input"
                               min="0" max="${available}" step="any" value="0"
                               ${available <= 0 ? 'disabled' : ''}>
                    </td>
                    <td class="text-end">${money(line.unitprice)}</td>
                `;
                tbody.appendChild(row);
            });

            tbody.querySelectorAll('.invret-qty-input').forEach(input => {
                input.addEventListener('input', updateSummary);
            });

            document.getElementById('invretSkeleton').classList.add('d-none');
            document.getElementById('cardInvoiceInfo').classList.remove('d-none');
            updateSummary();
            if (window.feather) feather.replace();
        })
        .catch(err => {
            showToast('Could not load that invoice: ' + err.message, 'error');
            resetInvoiceDetail();
        });
});

document.getElementById('btnSubmitReturn').addEventListener('click', function () {
    if (!currentInvoiceID) return;

    const rows = document.querySelectorAll('#tblReturnLines tbody tr');
    const productIDs = [];
    const qtys = [];

    for (const row of rows) {
        const input = row.querySelector('.invret-qty-input');
        const qty = parseFloat(input.value);
        const available = parseFloat(row.dataset.available);

        if (qty > 0) {
            if (qty > available) {
                showToast('Return qty cannot exceed the available qty for one of the lines.', 'error');
                return;
            }
            productIDs.push(row.dataset.productId);
            qtys.push(qty);
        }
    }

    if (productIDs.length === 0) {
        showToast('Enter a return quantity for at least one product.', 'error');
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'process/invoiceReturnCreate.php';

    const addInput = (name, value) => {
        const el = document.createElement('input');
        el.type = 'hidden';
        el.name = name;
        el.value = value;
        form.appendChild(el);
    };

    addInput('invoiceID', currentInvoiceID);
    addInput('remarks', document.getElementById('remarks').value);
    productIDs.forEach(id => addInput('productID[]', id));
    qtys.forEach(q => addInput('qty[]', q));

    document.body.appendChild(form);
    form.submit();
});

/* ---------- Rich option rendering for the invoice dropdown ---------- */
function formatInvoiceOption(item) {
    if (!item.id || !item.display_no) return item.text;
    const isTax = Number(item.invtype) === 1;
    const badgeClass = isTax ? 'tax' : 'nontax';
    const badgeLabel = isTax ? 'Tax' : 'Non-Tax';
    const amount = Number(item.total || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const $row = $(
        '<div class="invret-opt-row">' +
            '<span class="invret-opt-badge ' + badgeClass + '">' + badgeLabel + '</span>' +
            '<span class="invret-opt-no">' + item.display_no + '</span>' +
            '<span class="invret-opt-meta">' + item.date + ' · Rs. ' + amount + '</span>' +
        '</div>'
    );
    return $row;
}

$(document).ready(function () {
    if (window.feather) feather.replace();

    $('#invoiceTypeFilter').select2({
        theme: 'bootstrap',
        width: '100%',
        minimumResultsForSearch: -1
    });

    // Server-side searchable, paginated dropdown — loads ~20 invoices at a
    // time instead of dumping the whole table into the DOM.
    $('#invoiceNumber').select2({
        theme: 'bootstrap',
        width: '100%',
        placeholder: '-- Select invoice --',
        allowClear: true,
        minimumInputLength: 0,
        templateResult: formatInvoiceOption,
        templateSelection: formatInvoiceOption,
        escapeMarkup: markup => markup,
        ajax: {
            url: 'getprocess/getInvoiceListForReturn.php',
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return {
                    term: params.term || '',
                    page: params.page || 1,
                    invtype: $('#invoiceTypeFilter').val()
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: { more: data.pagination.more }
                };
            },
            cache: true
        }
    });
});
</script>
<?php include "include/footer.php"; ?>