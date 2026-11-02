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
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <h1 class="page-header-title">
                                    <div class="page-header-icon"><i data-feather="activity"></i></div>
                                    <span>Dashboard</span>
                                </h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid mt-2 p-0 p-2">

                <!-- Summary cards -->
                <div class="row">
                    <div class="col-12 col-md-6 col-xl-3 mb-3">
                        <div class="card bg-info text-white h-100">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="font-weight-bold">Month Sales</div>
                                        <div class="h5 font-weight-bold mb-0" id="monthSales">0.00</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="font-weight-bold">Month Purchase</div>
                                        <div class="h5 font-weight-bold mb-0" id="monthPurchase">0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3 mb-3">
                        <div class="card bg-success text-white h-100">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="font-weight-bold">Month Collection</div>
                                        <div class="h5 font-weight-bold mb-0" id="monthCollection">0.00</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="font-weight-bold">Month Payment</div>
                                        <div class="h5 font-weight-bold mb-0" id="monthPayment">0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3 mb-3">
                        <div class="card bg-warning text-dark h-100">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="font-weight-bold">Today Sales</div>
                                        <div class="h5 font-weight-bold mb-0" id="todaySales">0.00</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="font-weight-bold">Today Purchase</div>
                                        <div class="h5 font-weight-bold mb-0" id="todayPurchase">0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3 mb-3">
                        <div class="card bg-danger text-white h-100">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="font-weight-bold">Today Collection</div>
                                        <div class="h5 font-weight-bold mb-0" id="todayCollection">0.00</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="font-weight-bold">Today Payment</div>
                                        <div class="h5 font-weight-bold mb-0" id="todayPayment">0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i data-feather="pie-chart" class="mr-1"></i> Sales</span>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-primary" id="tabSummary">Summary</button>
                            <button type="button" class="btn btn-sm btn-light" id="tabProfit">Profit</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="form-inline mb-3" onsubmit="return false;">
                            <label class="mr-2" for="fromDate">From</label>
                            <input type="date" class="form-control form-control-sm mr-3" id="fromDate" value="<?php echo date('Y-m-01'); ?>">
                            <label class="mr-2" for="toDate">To</label>
                            <input type="date" class="form-control form-control-sm mr-3" id="toDate" value="<?php echo date('Y-m-t'); ?>">
                            <button type="button" class="btn btn-sm btn-primary" id="btnApply">Apply</button>
                        </form>
                        <div style="position:relative; height:420px;">
                            <canvas id="dashChart"></canvas>
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
        var chart = null;
        var feed  = null;       // last JSON from the server
        var mode  = 'summary';  // 'summary' | 'profit'

        function fmt(n) {
            return Number(n || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        var SETS = {
            summary: [
                {key: 'sales',            label: 'Sales',             color: '#17a2b8'},
                {key: 'returns',          label: 'Returns',           color: '#dc3545'},
                {key: 'purchases',        label: 'Purchases',         color: '#28a745'},
                {key: 'purchase_returns', label: 'Purchase Returns',  color: '#fd7e14'},
                {key: 'collection',       label: 'Collections',       color: '#ffc107'},
                {key: 'payment',          label: 'Supplier Payments', color: '#6f42c1'},
                {key: 'expenses',         label: 'Expenses',          color: '#343a40'}
            ],
            profit: [
                {key: 'sales',    label: 'Sales',        color: '#17a2b8'},
                {key: 'cost',     label: 'Cost of Sales', color: '#fd7e14'},
                {key: 'gross',    label: 'Gross Profit', color: '#28a745'},
                {key: 'expenses', label: 'Expenses',     color: '#343a40'},
                {key: 'net',      label: 'Net Profit',   color: '#6f42c1'}
            ]
        };

        function drawChart() {
            if (!feed) { return; }
            var datasets = SETS[mode].map(function (s) {
                return {
                    label: s.label,
                    data: feed.series[s.key],
                    backgroundColor: s.color
                };
            });
            if (chart) { chart.destroy(); }
            chart = new Chart(document.getElementById('dashChart'), {
                type: 'bar',
                data: {labels: feed.labels, datasets: datasets},
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {mode: 'index', intersect: false},
                    plugins: {
                        tooltip: {callbacks: {label: function (c) { return c.dataset.label + ': ' + fmt(c.parsed.y); }}}
                    },
                    scales: {
                        y: {title: {display: true, text: 'Amount'}, ticks: {callback: function (v) { return v.toLocaleString('en-US'); }}},
                        x: {ticks: {maxRotation: 45, minRotation: 45}}
                    }
                }
            });
        }

        function fillCards(c) {
            $('#monthSales').text(fmt(c.month.sales));
            $('#monthPurchase').text(fmt(c.month.purchase));
            $('#monthCollection').text(fmt(c.month.collection));
            $('#monthPayment').text(fmt(c.month.payment));
            $('#todaySales').text(fmt(c.today.sales));
            $('#todayPurchase').text(fmt(c.today.purchase));
            $('#todayCollection').text(fmt(c.today.collection));
            $('#todayPayment').text(fmt(c.today.payment));
        }

        function load() {
            $.ajax({
                url: 'scripts/dashboarddata.php',
                type: 'GET',
                dataType: 'json',
                data: {from: $('#fromDate').val(), to: $('#toDate').val()},
                success: function (res) {
                    feed = res;
                    fillCards(res.cards);
                    drawChart();
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                    var msg = 'Could not load dashboard data.';
                    try { msg = JSON.parse(xhr.responseText).error || msg; } catch (e) {}
                    alert(msg);
                }
            });
        }

        $('#btnApply').on('click', load);
        $('#tabSummary').on('click', function () {
            mode = 'summary';
            $('#tabSummary').removeClass('btn-light').addClass('btn-primary');
            $('#tabProfit').removeClass('btn-primary').addClass('btn-light');
            drawChart();
        });
        $('#tabProfit').on('click', function () {
            mode = 'profit';
            $('#tabProfit').removeClass('btn-light').addClass('btn-primary');
            $('#tabSummary').removeClass('btn-primary').addClass('btn-light');
            drawChart();
        });

        load();
    });
</script>

<?php include "include/footer.php"; ?>