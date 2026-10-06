<?php 
include "include/header.php"; 

/* ------------------------------------------------------------------
 * Dashboard data
 * Same conventions as the reports: invoices with status IN (0,1),
 * stock rows with status IN (0,1) and qty > 0, restricted to the
 * logged-in user's location.
 * ------------------------------------------------------------------ */
$locationId = isset($_SESSION['location_id']) ? intval($_SESSION['location_id']) : 0;
$locInv   = $locationId ? " AND `u`.`tbl_location_idtbl_location` = $locationId" : "";
$locStock = $locationId ? " AND `s`.`tbl_location_idtbl_location` = $locationId" : "";

function dash_rows($conn, $sql) {
    $out = array();
    $r = $conn->query($sql);
    if ($r) { while ($row = $r->fetch_assoc()) { $out[] = $row; } }
    return $out;
}
function dash_val($conn, $sql) {
    $rows = dash_rows($conn, $sql);
    return $rows ? floatval($rows[0]['v']) : 0;
}
function dash_money($n, $dec = 0) { return number_format((float)$n, $dec); }

$today          = date('Y-m-d');
$monthStart     = date('Y-m-01');
$nextMonthStart = date('Y-m-01', strtotime('first day of next month'));
$lastMonthStart = date('Y-m-01', strtotime('first day of last month'));
$lastMonthEnd   = min(
    date('Y-m-d', strtotime($lastMonthStart . ' +' . (date('j') - 1) . ' days')),
    date('Y-m-t', strtotime($lastMonthStart))
);

/* ---- KPI values ---- */
$salesToday = dash_val($conn, "SELECT COALESCE(SUM(`u`.`total`),0) AS v FROM `tbl_invoice` AS `u` WHERE `u`.`status` IN (0,1) AND `u`.`date` = '$today' $locInv");
$salesMonth = dash_val($conn, "SELECT COALESCE(SUM(`u`.`total`),0) AS v FROM `tbl_invoice` AS `u` WHERE `u`.`status` IN (0,1) AND `u`.`date` >= '$monthStart' AND `u`.`date` < '$nextMonthStart' $locInv");
$salesLast  = dash_val($conn, "SELECT COALESCE(SUM(`u`.`total`),0) AS v FROM `tbl_invoice` AS `u` WHERE `u`.`status` IN (0,1) AND `u`.`date` >= '$lastMonthStart' AND `u`.`date` <= '$lastMonthEnd' $locInv");
$invCount   = dash_val($conn, "SELECT COUNT(*) AS v FROM `tbl_invoice` AS `u` WHERE `u`.`status` IN (0,1) AND `u`.`date` >= '$monthStart' AND `u`.`date` < '$nextMonthStart' $locInv");
$stockValue = dash_val($conn, "SELECT COALESCE(SUM(`s`.`qty` * `p`.`unitprice`),0) AS v FROM `tbl_stock` AS `s` LEFT JOIN `tbl_product` AS `p` ON (`p`.`idtbl_product` = `s`.`tbl_product_idtbl_product`) WHERE `s`.`status` IN (0,1) AND `s`.`qty` > 0 $locStock");
$unpaid     = dash_rows($conn, "SELECT COUNT(*) AS c, COALESCE(SUM(`u`.`total`),0) AS v FROM `tbl_invoice` AS `u` WHERE `u`.`status` IN (0,1) AND `u`.`paymentcomplete` = 0 $locInv");
$unpaidCnt  = $unpaid ? intval($unpaid[0]['c']) : 0;
$unpaidVal  = $unpaid ? floatval($unpaid[0]['v']) : 0;

$deltaPct = ($salesLast > 0) ? (($salesMonth - $salesLast) / $salesLast * 100) : null;

/* ---- Daily sales, last 30 days ---- */
$dailyRaw = array();
foreach (dash_rows($conn, "SELECT `u`.`date` AS d, SUM(`u`.`total`) AS v FROM `tbl_invoice` AS `u` WHERE `u`.`status` IN (0,1) AND `u`.`date` >= DATE_SUB('$today', INTERVAL 29 DAY) AND `u`.`date` <= '$today' $locInv GROUP BY `u`.`date`") as $r) {
    $dailyRaw[$r['d']] = floatval($r['v']);
}
$dailyLabels = array(); $dailyData = array();
for ($i = 29; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $dailyLabels[] = date('d M', strtotime($d));
    $dailyData[]   = isset($dailyRaw[$d]) ? $dailyRaw[$d] : 0;
}

/* ---- Monthly sales, last 12 months ---- */
$firstMonth = date('Y-m-01', strtotime('first day of -11 months'));
$monthRaw = array();
foreach (dash_rows($conn, "SELECT DATE_FORMAT(`u`.`date`, '%Y-%m') AS ym, SUM(`u`.`total`) AS v FROM `tbl_invoice` AS `u` WHERE `u`.`status` IN (0,1) AND `u`.`date` >= '$firstMonth' AND `u`.`date` < '$nextMonthStart' $locInv GROUP BY ym") as $r) {
    $monthRaw[$r['ym']] = floatval($r['v']);
}
$monthLabels = array(); $monthData = array();
for ($i = 11; $i >= 0; $i--) {
    $ts = strtotime("first day of -$i months");
    $monthLabels[] = date('M y', $ts);
    $monthData[]   = isset($monthRaw[date('Y-m', $ts)]) ? $monthRaw[date('Y-m', $ts)] : 0;
}

/* ---- Line value = qty x saleprice (assumes saleprice is the per-unit selling price) ---- */
$lineJoin = "FROM `tbl_invoice_detail` AS `d`
    INNER JOIN `tbl_invoice` AS `u` ON (`u`.`idtbl_invoice` = `d`.`tbl_invoice_idtbl_invoice`)
    INNER JOIN `tbl_product` AS `p` ON (`p`.`idtbl_product` = `d`.`tbl_product_idtbl_product`)";
$lineWhere = "WHERE `d`.`status` = 1 AND `u`.`status` IN (0,1) AND `u`.`date` >= '$monthStart' AND `u`.`date` < '$nextMonthStart' $locInv";

$catRows = dash_rows($conn, "SELECT `c`.`category` AS label, SUM(`d`.`qty` * `d`.`saleprice`) AS v $lineJoin
    INNER JOIN `tbl_product_category` AS `c` ON (`c`.`idtbl_product_category` = `p`.`tbl_product_category_idtbl_product_category`)
    $lineWhere GROUP BY `c`.`idtbl_product_category` ORDER BY v DESC LIMIT 6");
$catLabels = array(); $catData = array();
foreach ($catRows as $r) { $catLabels[] = $r['label']; $catData[] = round(floatval($r['v']), 2); }

$topProducts = dash_rows($conn, "SELECT `p`.`product_name` AS name, SUM(`d`.`qty`) AS qty, SUM(`d`.`qty` * `d`.`saleprice`) AS v $lineJoin
    $lineWhere GROUP BY `p`.`idtbl_product` ORDER BY v DESC LIMIT 8");
$topMax = 0;
foreach ($topProducts as $r) { if (floatval($r['v']) > $topMax) { $topMax = floatval($r['v']); } }

/* ---- Low stock: total stock at location at or below the product's reorder level (rol) ---- */
$lowAll = dash_rows($conn, "SELECT `p`.`product_name` AS name, `p`.`rol` AS rol, COALESCE(SUM(`s`.`qty`),0) AS qty
    FROM `tbl_product` AS `p`
    LEFT JOIN `tbl_stock` AS `s` ON (`s`.`tbl_product_idtbl_product` = `p`.`idtbl_product` AND `s`.`status` IN (0,1) $locStock)
    WHERE `p`.`status` = 1 AND `p`.`rol` > 0
    GROUP BY `p`.`idtbl_product` HAVING qty <= `p`.`rol`
    ORDER BY (qty / `p`.`rol`) ASC LIMIT 500");
$lowCount = count($lowAll);
$lowStock = array_slice($lowAll, 0, 8);

/* ---- Recent invoices ---- */
$recent = dash_rows($conn, "SELECT COALESCE(NULLIF(`u`.`taxinvoice_no`, ''), `u`.`manuelinvno`, `u`.`idtbl_invoice`) AS no,
        `ud`.`name` AS customer, `u`.`date` AS d, `u`.`saletype` AS saletype, `u`.`paymentcomplete` AS paid, `u`.`total` AS total
    FROM `tbl_invoice` AS `u`
    LEFT JOIN `tbl_customer` AS `ud` ON (`ud`.`idtbl_customer` = `u`.`customerid`)
    WHERE `u`.`status` IN (0,1) $locInv
    ORDER BY `u`.`idtbl_invoice` DESC LIMIT 8");

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
                            <div class="col-sm-12">
                                <h1 class="page-header-title">
                                    <div class="page-header-icon"><i data-feather="activity"></i></div>
                                    <span>Dashboard</span>
                                </h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap');
.lv-dash { --ink:#12282b; --petrol:#0f3d3e; --amber:#e8a317; --rust:#c2410c; --moss:#2f7d5b; --fog:#f2f5f4; --line:#e3e9e7; --muted:#6b7b7a;
    font-family:'Manrope', 'Segoe UI', system-ui, sans-serif; color:var(--ink); }
.lv-dash .lv-hero { background:var(--petrol); color:#fff; border-radius:14px; padding:1.5rem 1.75rem; display:flex; flex-wrap:wrap; gap:1.5rem; align-items:center; }
.lv-dash .lv-hero-main { flex:1 1 280px; }
.lv-dash .lv-hero-label { font-size:.9rem; color:#b8d0cd; font-weight:600; }
.lv-dash .lv-hero-value { font-size:2.6rem; font-weight:800; line-height:1.1; letter-spacing:-.02em; margin:.25rem 0 .6rem; }
.lv-dash .lv-hero-value small { font-size:1.1rem; font-weight:600; color:var(--amber); margin-right:.35rem; }
.lv-dash .lv-pill { display:inline-block; padding:.2rem .65rem; border-radius:999px; font-size:.8rem; font-weight:700; }
.lv-dash .lv-pill.up { background:rgba(120,220,170,.18); color:#8de6b8; }
.lv-dash .lv-pill.down { background:rgba(255,150,110,.18); color:#ffb089; }
.lv-dash .lv-pill.flat { background:rgba(255,255,255,.12); color:#d6e4e2; }
.lv-dash .lv-hero-note { font-size:.82rem; color:#9fbdb9; margin-top:.6rem; }
.lv-dash .lv-hero-chart { flex:2 1 420px; height:170px; position:relative; }
.lv-dash .lv-kpi { background:#fff; border:1px solid var(--line); border-radius:10px; padding:1rem 1.1rem; display:flex; gap:.9rem; align-items:center; height:100%; }
.lv-dash .lv-chip { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex:0 0 44px; }
.lv-dash .lv-chip.amber { background:#fdf1d3; color:#a8730a; }
.lv-dash .lv-chip.teal { background:#d8ece8; color:var(--petrol); }
.lv-dash .lv-chip.moss { background:#dcefe5; color:var(--moss); }
.lv-dash .lv-chip.rust { background:#fbe2d6; color:var(--rust); }
.lv-dash .lv-chip.slate { background:#e6ebee; color:#41525c; }
.lv-dash .lv-k-label { font-size:.8rem; color:var(--muted); font-weight:600; }
.lv-dash .lv-k-value { font-size:1.35rem; font-weight:800; line-height:1.2; }
.lv-dash .lv-k-sub { font-size:.76rem; color:var(--muted); }
.lv-dash .lv-panel { background:#fff; border:1px solid var(--line); border-radius:10px; padding:1.1rem 1.25rem; height:100%; }
.lv-dash .lv-panel-title { font-size:1rem; font-weight:800; margin:0; }
.lv-dash .lv-panel-sub { font-size:.78rem; color:var(--muted); margin-bottom:.75rem; }
.lv-dash .lv-chart-box { position:relative; height:270px; }
.lv-dash .lv-table { width:100%; font-size:.86rem; }
.lv-dash .lv-table th { color:var(--muted); font-weight:700; font-size:.76rem; border-bottom:1px solid var(--line); padding:.45rem .5rem; }
.lv-dash .lv-table td { padding:.5rem; border-bottom:1px solid var(--fog); vertical-align:middle; }
.lv-dash .lv-table tr:last-child td { border-bottom:0; }
.lv-dash .lv-bar { height:6px; border-radius:3px; background:var(--fog); margin-top:.3rem; }
.lv-dash .lv-bar > span { display:block; height:100%; border-radius:3px; background:var(--amber); }
.lv-dash .lv-tag { font-size:.72rem; font-weight:700; padding:.15rem .5rem; border-radius:999px; }
.lv-dash .lv-tag.paid { background:#dcefe5; color:var(--moss); }
.lv-dash .lv-tag.due { background:#fbe2d6; color:var(--rust); }
.lv-dash .lv-empty { color:var(--muted); font-size:.86rem; padding:1rem 0; }
.lv-dash .text-r { text-align:right; }
</style>

            <div class="container-fluid mt-2 p-2 lv-dash">

                <!-- Hero: this month's sales + 30-day trend -->
                <div class="lv-hero mb-3">
                    <div class="lv-hero-main">
                        <div class="lv-hero-label">Sales this month (<?php echo date('F Y'); ?>)</div>
                        <div class="lv-hero-value"><small>Rs</small><?php echo dash_money($salesMonth); ?></div>
                        <?php if ($deltaPct === null): ?>
                            <span class="lv-pill flat">No sales last month to compare</span>
                        <?php else: ?>
                            <span class="lv-pill <?php echo $deltaPct > 0 ? 'up' : ($deltaPct < 0 ? 'down' : 'flat'); ?>">
                                <?php echo ($deltaPct > 0 ? '+' : '') . number_format($deltaPct, 1); ?>% vs same days last month
                            </span>
                        <?php endif; ?>
                        <div class="lv-hero-note">Last month so far: Rs <?php echo dash_money($salesLast); ?></div>
                    </div>
                    <div class="lv-hero-chart"><canvas id="chartDaily"></canvas></div>
                </div>

                <!-- KPI cards -->
                <div class="row mb-3">
                    <div class="col-xl col-md-4 col-sm-6 mb-3 mb-xl-0">
                        <div class="lv-kpi"><span class="lv-chip amber"><i class="fas fa-sun"></i></span>
                            <div><div class="lv-k-label">Today's sales</div><div class="lv-k-value">Rs <?php echo dash_money($salesToday); ?></div><div class="lv-k-sub"><?php echo date('D, d M'); ?></div></div></div>
                    </div>
                    <div class="col-xl col-md-4 col-sm-6 mb-3 mb-xl-0">
                        <div class="lv-kpi"><span class="lv-chip teal"><i class="fas fa-file-invoice"></i></span>
                            <div><div class="lv-k-label">Invoices this month</div><div class="lv-k-value"><?php echo dash_money($invCount); ?></div><div class="lv-k-sub">Avg Rs <?php echo dash_money($invCount > 0 ? $salesMonth / $invCount : 0); ?> each</div></div></div>
                    </div>
                    <div class="col-xl col-md-4 col-sm-6 mb-3 mb-xl-0">
                        <div class="lv-kpi"><span class="lv-chip moss"><i class="fas fa-boxes"></i></span>
                            <div><div class="lv-k-label">Stock value</div><div class="lv-k-value">Rs <?php echo dash_money($stockValue); ?></div><div class="lv-k-sub">Qty x unit price</div></div></div>
                    </div>
                    <div class="col-xl col-md-6 col-sm-6 mb-3 mb-xl-0">
                        <div class="lv-kpi"><span class="lv-chip rust"><i class="fas fa-exclamation-triangle"></i></span>
                            <div><div class="lv-k-label">Low stock items</div><div class="lv-k-value"><?php echo dash_money($lowCount); ?></div><div class="lv-k-sub">At or below reorder level</div></div></div>
                    </div>
                    <div class="col-xl col-md-6 col-sm-12">
                        <div class="lv-kpi"><span class="lv-chip slate"><i class="fas fa-hourglass-half"></i></span>
                            <div><div class="lv-k-label">Unpaid invoices</div><div class="lv-k-value"><?php echo dash_money($unpaidCnt); ?></div><div class="lv-k-sub">Rs <?php echo dash_money($unpaidVal); ?> outstanding</div></div></div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row mb-3">
                    <div class="col-lg-8 mb-3 mb-lg-0">
                        <div class="lv-panel">
                            <h5 class="lv-panel-title">Monthly sales</h5>
                            <div class="lv-panel-sub">Last 12 months</div>
                            <div class="lv-chart-box"><canvas id="chartMonthly"></canvas></div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="lv-panel">
                            <h5 class="lv-panel-title">Sales by category</h5>
                            <div class="lv-panel-sub">This month, top 6</div>
                            <?php if (empty($catData)): ?>
                                <div class="lv-empty">No category sales recorded this month yet.</div>
                            <?php else: ?>
                                <div class="lv-chart-box"><canvas id="chartCategory"></canvas></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Tables -->
                <div class="row mb-3">
                    <div class="col-lg-7 mb-3 mb-lg-0">
                        <div class="lv-panel">
                            <h5 class="lv-panel-title">Top selling products</h5>
                            <div class="lv-panel-sub">This month, by sales value</div>
                            <?php if (empty($topProducts)): ?>
                                <div class="lv-empty">No product sales recorded this month yet.</div>
                            <?php else: ?>
                            <table class="lv-table">
                                <thead><tr><th>Product</th><th class="text-r">Qty</th><th class="text-r">Sales (Rs)</th></tr></thead>
                                <tbody>
                                <?php foreach ($topProducts as $r): $w = $topMax > 0 ? round(floatval($r['v']) / $topMax * 100) : 0; ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($r['name']); ?>
                                            <div class="lv-bar"><span style="width:<?php echo $w; ?>%"></span></div></td>
                                        <td class="text-r"><?php echo dash_money($r['qty']); ?></td>
                                        <td class="text-r"><?php echo dash_money($r['v'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="lv-panel">
                            <h5 class="lv-panel-title">Running low</h5>
                            <div class="lv-panel-sub">Lowest stock against reorder level</div>
                            <?php if (empty($lowStock)): ?>
                                <div class="lv-empty">Nothing is below its reorder level.</div>
                            <?php else: ?>
                            <table class="lv-table">
                                <thead><tr><th>Product</th><th class="text-r">In stock</th><th class="text-r">Reorder at</th></tr></thead>
                                <tbody>
                                <?php foreach ($lowStock as $r): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($r['name']); ?></td>
                                        <td class="text-r"><strong style="color:<?php echo floatval($r['qty']) <= 0 ? 'var(--rust)' : 'inherit'; ?>"><?php echo dash_money($r['qty']); ?></strong></td>
                                        <td class="text-r"><?php echo dash_money($r['rol']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="lv-panel">
                            <h5 class="lv-panel-title">Recent invoices</h5>
                            <div class="lv-panel-sub">Latest 8</div>
                            <?php if (empty($recent)): ?>
                                <div class="lv-empty">No invoices yet.</div>
                            <?php else: ?>
                            <div class="table-responsive">
                            <table class="lv-table">
                                <thead><tr><th>Invoice</th><th>Customer</th><th>Date</th><th>Sale type</th><th>Payment</th><th class="text-r">Total (Rs)</th></tr></thead>
                                <tbody>
                                <?php foreach ($recent as $r): ?>
                                    <tr>
                                        <td><strong>INV-<?php echo htmlspecialchars($r['no']); ?></strong></td>
                                        <td><?php echo $r['customer'] ? htmlspecialchars($r['customer']) : '-'; ?></td>
                                        <td><?php echo htmlspecialchars($r['d']); ?></td>
                                        <td><?php echo $r['saletype'] == 1 ? 'Retail' : 'Wholesale'; ?></td>
                                        <td><?php echo $r['paid'] == 1 ? '<span class="lv-tag paid">Paid</span>' : '<span class="lv-tag due">Unpaid</span>'; ?></td>
                                        <td class="text-r"><?php echo dash_money($r['total'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </main>
        <?php include "include/footerbar.php"; ?>
    </div>
</div>
<?php include "include/footerscripts.php"; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var petrol = '#0f3d3e', amber = '#e8a317';
        var fmtShort = function (v) {
            if (v >= 1000000) return (v / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
            if (v >= 1000) return (v / 1000).toFixed(0) + 'K';
            return v;
        };
        var fmtFull = function (v) { return 'Rs ' + Number(v).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); };

        Chart.defaults.font.family = "'Manrope', 'Segoe UI', system-ui, sans-serif";
        Chart.defaults.color = '#6b7b7a';

        // Hero: daily sales, last 30 days
        new Chart(document.getElementById('chartDaily'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dailyLabels); ?>,
                datasets: [{
                    data: <?php echo json_encode($dailyData); ?>,
                    borderColor: amber, backgroundColor: 'rgba(232,163,23,.16)',
                    fill: true, tension: .35, borderWidth: 2.5, pointRadius: 0, pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: function (c) { return fmtFull(c.parsed.y); } } } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#9fbdb9', maxTicksLimit: 6 }, border: { display: false } },
                    y: { grid: { color: 'rgba(255,255,255,.08)' }, ticks: { color: '#9fbdb9', callback: fmtShort, maxTicksLimit: 4 }, border: { display: false }, beginAtZero: true }
                }
            }
        });

        // Monthly sales, last 12 months (current month highlighted)
        var monthData = <?php echo json_encode($monthData); ?>;
        new Chart(document.getElementById('chartMonthly'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($monthLabels); ?>,
                datasets: [{
                    data: monthData,
                    backgroundColor: monthData.map(function (v, i) { return i === monthData.length - 1 ? amber : petrol; }),
                    borderRadius: 5, maxBarThickness: 34
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: function (c) { return fmtFull(c.parsed.y); } } } },
                scales: {
                    x: { grid: { display: false }, border: { display: false } },
                    y: { grid: { color: '#edf1f0' }, ticks: { callback: fmtShort }, border: { display: false }, beginAtZero: true }
                }
            }
        });

        // Sales by category (this month)
        var catEl = document.getElementById('chartCategory');
        if (catEl) {
            new Chart(catEl, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode($catLabels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($catData); ?>,
                        backgroundColor: ['#0f3d3e', '#e8a317', '#2f7d5b', '#c2410c', '#5b8a99', '#b9c6c4'],
                        borderWidth: 2, borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '62%',
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, boxHeight: 10, usePointStyle: true, padding: 12 } },
                        tooltip: { callbacks: { label: function (c) { return ' ' + c.label + ': ' + fmtFull(c.parsed); } } }
                    }
                }
            });
        }

        if (window.feather) { feather.replace(); }
    });
</script>

<?php include "include/footer.php"; ?>