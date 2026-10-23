<?php
session_start();
require_once 'connection.php';   // <-- adjust: must create $conn = new mysqli(...)
if (!isset($_SESSION['userid'])) { header('Location: index.php'); exit; }   // <-- adjust to your login check
$sup = $conn->query("SELECT idtbl_supplier, suppliername FROM tbl_supplier WHERE status = 1 ORDER BY suppliername");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Supplier Payment</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- If you use your own template, move the content to it and drop these CDN tags -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-bs4@1.13.6/css/dataTables.bootstrap4.min.css">
<style>.num{text-align:right}.pay{width:130px;text-align:right}</style>
</head>
<body class="bg-light">
<div class="container-fluid py-3">

  <div class="card mb-3">
    <div class="card-header"><b>Supplier Payment (GRN)</b></div>
    <div class="card-body">
      <div class="form-row">
        <div class="form-group col-md-4">
          <label>Supplier</label>
          <select id="supplier" class="form-control" style="width:100%">
            <option value="">Select supplier</option>
            <?php while ($s = $sup->fetch_assoc()) { ?>
              <option value="<?php echo (int)$s['idtbl_supplier']; ?>"><?php echo htmlspecialchars($s['suppliername']); ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="form-group col-md-2">
          <label>Payment Date</label>
          <input type="date" id="paydate" class="form-control" value="<?php echo date('Y-m-d'); ?>">
        </div>
        <div class="form-group col-md-2">
          <label>Method</label>
          <select id="paymethod" class="form-control">
            <option value="1">Cash</option>
            <option value="2">Cheque</option>
            <option value="3">Bank Transfer</option>
          </select>
        </div>
        <div class="form-group col-md-2 m-cheque d-none">
          <label>Cheque No</label>
          <input type="text" id="chequeno" class="form-control">
        </div>
        <div class="form-group col-md-2 m-cheque d-none">
          <label>Cheque Date</label>
          <input type="date" id="chequedate" class="form-control">
        </div>
        <div class="form-group col-md-4 m-bank d-none">
          <label>Bank Reference</label>
          <input type="text" id="bankref" class="form-control">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-6">
          <label>Remark</label>
          <input type="text" id="remark" class="form-control" maxlength="400">
        </div>
        <div class="form-group col-md-3">
          <label>Lump sum to allocate (oldest first)</label>
          <div class="input-group">
            <input type="number" step="0.01" min="0" id="lump" class="form-control">
            <div class="input-group-append"><button type="button" id="btnAlloc" class="btn btn-outline-secondary">Allocate</button></div>
          </div>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-sm table-bordered table-hover bg-white">
          <thead class="thead-light">
            <tr>
              <th></th><th>GRN Date</th><th>Invoice No</th><th>VAT Type</th>
              <th class="num">Subtotal</th><th class="num">VAT %</th><th class="num">VAT Amount</th>
              <th class="num">Total</th><th class="num">Paid</th><th class="num">Balance</th><th class="num">Pay Now</th>
            </tr>
          </thead>
          <tbody id="grnbody"><tr><td colspan="11" class="text-center text-muted">Select a supplier</td></tr></tbody>
          <tfoot>
            <tr><th colspan="10" class="num">Total Payment</th><th class="num" id="totalpay">0.00</th></tr>
          </tfoot>
        </table>
      </div>
      <div class="text-right"><button type="button" id="btnSave" class="btn btn-primary">Save Payment</button></div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div class="form-inline">
        <b class="mr-3">Payment History</b>
        <input type="date" id="hfrom" class="form-control form-control-sm mr-2" value="<?php echo date('Y-m-01'); ?>">
        <input type="date" id="hto" class="form-control form-control-sm mr-2" value="<?php echo date('Y-m-d'); ?>">
        <button type="button" id="btnHist" class="btn btn-sm btn-secondary">Search</button>
      </div>
    </div>
    <div class="card-body">
      <table id="histtable" class="table table-sm table-bordered w-100">
        <thead><tr><th>Payment No</th><th>Date</th><th>Supplier</th><th>Method</th><th>Ref</th><th class="num">Amount</th><th>Status</th><th></th></tr></thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net@1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net-bs4@1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
var AJAX = 'Supplier_payment_ajax.php', rows = [], hist;

function n(v){ return (parseFloat(v) || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}); }
function esc(s){ return $('<div>').text(s == null ? '' : s).html(); }
function msg(r){ return Swal.fire({icon: r.icon, title: r.title, text: r.message}); }

$('#supplier').select2();
$('#supplier').on('change', loadGrn);
$('#paymethod').on('change', function(){
  var m = $(this).val();
  $('.m-cheque').toggleClass('d-none', m !== '2');
  $('.m-bank').toggleClass('d-none', m !== '3');
});

function loadGrn(){
  var sid = $('#supplier').val();
  if (!sid) { rows = []; render(); return; }
  $.post(AJAX, {action: 'getgrn', supplier: sid}, function(r){ rows = r.data || []; render(); }, 'json');
}

function render(){
  var h = '';
  if (!rows.length) {
    h = '<tr><td colspan="11" class="text-center text-muted">No outstanding GRNs</td></tr>';
  }
  $.each(rows, function(i, g){
    var vp = parseFloat(g.vatpercentage) || 0;
    var std = (g.stdvat !== null && vp === 0 && g.vattype != 0) ? ' <small class="text-muted" title="Standard VAT on GRN date">(std ' + n(g.stdvat) + '%)</small>' : '';
    h += '<tr data-i="' + i + '">' +
      '<td><input type="checkbox" class="chk"></td>' +
      '<td>' + esc(g.date) + '</td><td>' + esc(g.invoicenum) + '</td>' +
      '<td>' + esc(g.vatlabel) + '</td>' +
      '<td class="num">' + n(g.subtotal) + '</td><td class="num">' + n(vp) + std + '</td><td class="num">' + n(g.vatamount) + '</td>' +
      '<td class="num">' + n(g.total) + '</td><td class="num">' + n(g.paidamount) + '</td><td class="num">' + n(g.balance) + '</td>' +
      '<td class="num"><input type="number" step="0.01" min="0" class="form-control form-control-sm pay" value="0.00" disabled></td></tr>';
  });
  $('#grnbody').html(h);
  calc();
}

function calc(){
  var t = 0;
  $('#grnbody tr').each(function(){
    if ($(this).find('.chk').is(':checked')) { t += parseFloat($(this).find('.pay').val()) || 0; }
  });
  $('#totalpay').text(n(t));
}

$('#grnbody').on('change', '.chk', function(){
  var $r = $(this).closest('tr'), g = rows[$r.data('i')], on = $(this).is(':checked');
  $r.find('.pay').prop('disabled', !on).val(on ? g.balance.toFixed(2) : '0.00');
  calc();
});
$('#grnbody').on('input', '.pay', function(){
  var g = rows[$(this).closest('tr').data('i')], v = parseFloat($(this).val()) || 0;
  if (v > g.balance) { $(this).val(g.balance.toFixed(2)); }
  if (v < 0) { $(this).val('0.00'); }
  calc();
});

$('#btnAlloc').click(function(){
  var left = parseFloat($('#lump').val()) || 0;
  $('#grnbody tr').each(function(){
    var $r = $(this), g = rows[$r.data('i')]; if (!g) { return; }
    var p = Math.min(g.balance, left);
    $r.find('.chk').prop('checked', p > 0);
    $r.find('.pay').prop('disabled', p <= 0).val(p.toFixed(2));
    left = Math.round((left - p) * 100) / 100;
  });
  calc();
});

$('#btnSave').click(function(){
  var items = [];
  $('#grnbody tr').each(function(){
    var $r = $(this);
    if ($r.find('.chk').is(':checked')) {
      var a = parseFloat($r.find('.pay').val()) || 0;
      if (a > 0) { items.push({grn: rows[$r.data('i')].idtbl_grn, amount: a}); }
    }
  });
  if (!$('#supplier').val() || !items.length) {
    Swal.fire({icon: 'warning', title: 'Nothing to pay', text: 'Select a supplier and at least one GRN.'}); return;
  }
  var $b = $(this).prop('disabled', true);
  $.post(AJAX, {
    action: 'save', supplier: $('#supplier').val(), date: $('#paydate').val(), paymethod: $('#paymethod').val(),
    chequeno: $('#chequeno').val(), chequedate: $('#chequedate').val(), bankref: $('#bankref').val(),
    remark: $('#remark').val(), items: JSON.stringify(items)
  }, function(r){
    msg(r).then(function(){
      if (r.icon === 'success') { $('#chequeno,#chequedate,#bankref,#remark,#lump').val(''); loadGrn(); loadHist(); }
    });
  }, 'json').fail(function(){ Swal.fire({icon: 'error', title: 'Error', text: 'Request failed.'}); })
    .always(function(){ $b.prop('disabled', false); });
});

/* ---------- history ---------- */
hist = $('#histtable').DataTable({
  order: [[1, 'desc']],
  columns: [
    {data: 'paymentno'}, {data: 'date'}, {data: 'suppliername'}, {data: 'methodlabel'},
    {data: null, render: function(d){
      if (d.paymethod == 2) { return esc(d.chequeno) + ' (' + esc(d.chequedate) + ')'; }
      return d.paymethod == 3 ? esc(d.bankref) : '';
    }},
    {data: 'totalpayment', className: 'num', render: function(v){ return n(v); }},
    {data: 'status', render: function(v){ return v == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Cancelled</span>'; }},
    {data: null, orderable: false, render: function(d){
      return '<button class="btn btn-sm btn-info bview" data-id="' + d.idtbl_supplier_payment + '" data-no="' + esc(d.paymentno) + '">View</button> ' +
        (d.status == 1 ? '<button class="btn btn-sm btn-danger bcancel" data-id="' + d.idtbl_supplier_payment + '">Cancel</button>' : '');
    }}
  ]
});

function loadHist(){
  $.post(AJAX, {action: 'list', from: $('#hfrom').val(), to: $('#hto').val()}, function(r){
    hist.clear().rows.add(r.data || []).draw();
  }, 'json');
}
$('#btnHist').click(loadHist);

$('#histtable').on('click', '.bview', function(){
  var no = $(this).data('no');
  $.post(AJAX, {action: 'view', id: $(this).data('id')}, function(r){
    var h = '<table class="table table-sm"><thead><tr><th>Invoice</th><th>GRN Date</th><th class="num">GRN Total</th><th class="num">Paid</th></tr></thead><tbody>';
    $.each(r.data || [], function(i, d){
      h += '<tr><td>' + esc(d.invoicenum) + '</td><td>' + esc(d.date) + '</td><td class="num">' + n(d.total) + '</td><td class="num">' + n(d.amount) + '</td></tr>';
    });
    Swal.fire({title: no, html: h + '</tbody></table>', width: 700});
  }, 'json');
});

$('#histtable').on('click', '.bcancel', function(){
  var id = $(this).data('id');
  Swal.fire({icon: 'warning', title: 'Cancel this payment?', text: 'GRN balances will be restored.', showCancelButton: true, confirmButtonText: 'Yes, cancel'})
    .then(function(x){
      if (!x.isConfirmed) { return; }
      $.post(AJAX, {action: 'cancel', id: id}, function(r){ msg(r).then(function(){ loadHist(); loadGrn(); }); }, 'json');
    });
});

loadHist();
</script>
</body>
</html>