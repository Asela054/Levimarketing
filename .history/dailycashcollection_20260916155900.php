<?php 
include "include/header.php"; 

include "include/topnavbar.php"; 
?>
<?php  $type =  $_SESSION['privatetype'];?>
<?php
       $sqlarea="SELECT * FROM `tbl_area` WHERE `status` in ('1','2')";
       $resultarea=$conn->query($sqlarea);
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
                                    <div class="page-header-icon"><i class="fas fa-money-bill"></i></div>
                                    <span>&nbsp; Daily Cash Collection</span>
                                </h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid mt-2 p-0 p-2">
                <div class="card">
                    <div class="card-body p-0 p-2">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Date</label>
                                <input type="date" class="form-control form-control-sm" id="cashdate" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            
                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Sale Type</label>
                                <select id="filtersaletype" class="form-control form-control-sm">
                                    <option value="">All Sale Types</option>
                                    <option value="1">Retail Sale</option>
                                    <option value="2">Whole Sale</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="small font-weight-bold text-dark">Payment Method</label>
                                <select id="filterpaymentmethod" class="form-control form-control-sm">
                                    <option value="">All Payment Methods</option>
                                    <option value="1">Cash</option>
                                    <option value="2">Card</option>
                                    <option value="3">Cheque</option>
                                    <option value="4">Online Transfer</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="col-12">
                                    <hr class="border-dark">
                                    <div class="scrollbar pb-3" id="style-2">

                                    <?php
                                if($type==2){ ?>                             
                                <div class="custom-control custom-checkbox ml-2 mb-2">
                                    <input type="checkbox" class="custom-control-input checkallocate" id="selectAll">
                                    <label class="custom-control-label" for="selectAll">Select All Records</label>
                                </div>

                                    <?php 
                                }else{ 

                                 }?>
                                        <table class="table table-striped table-bordered table-sm nowrap"
                                            id="cashdailytbl" style="width:100%">
                                            <thead class="thead-light">
                                                <tr>
                                                   <?php 
                                                    if($type==2){
                                                        ?>
                                                        <th></th>
                                                    <?php
                                                }else{
                                                    ?>
                                                    <th  data-visible="false"></th>
                                            <?php
                                                }
                                                ?>
                                                    <th>ID</th>
                                                    <th>CUSTOMER</th>
                                                    <th>DATE</th>
                                                    <th>SALE TYPE</th>
                                                    <th class="text-right">TOTAL PAYMENT</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="4"></th>
                                                    <th style="text-align:right">Total:</th>
                                                    <th class="text-right"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
             if($type==2){
        ?>
                        <div class="row d-flex justify-content-center align-content-center ">
                            <div class="form-group mt-2">
                                <button type="button" name="Btnsubmit" id="Btnsubmit"
                                    class="btn btn-primary btn-m  fa-pull-right"><i
                                        class="fas fa-share"></i>&nbsp;Transfer</button>
                            </div>
                        </div>
                        <?php
             } else{

             } 
        ?>

                        <br>
                        <hr>
                    </div>
                </div>
            </div>

        </main>
        <?php include "include/footerbar.php"; ?>
    </div>
</div>
<?php include "include/footerscripts.php"; ?>
<?php include "include/footer.php"; ?>

<script>
    var cashTable;

    function loadTable() {

        if ($.fn.DataTable.isDataTable('#cashdailytbl')) {
            cashTable.ajax.reload();
            return;
        }

        cashTable = $('#cashdailytbl').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            ajax: {
                url: "scripts/dailycashlist.php",
                type: "POST",
                "data": function (d) {
                    d.selectdate = $("#cashdate").val();
                    d.filtersaletype = $("#filtersaletype").val();
                    d.filterpaymentmethod = $("#filterpaymentmethod").val();
                }
            },
            "lengthMenu": [100, 200, "All"],
            "order": [
                [0, "desc"]
            ],
            "columns": [{
                    "targets": -1,
                    "className": 'text-right',
                    "data": "manuelinvno",
                    "render": function (data, type, full) {
                        var checkbox = '';

                        if (full['manuelinvno'] == null) {
                            checkbox += '<form><input type="checkbox" name ="rowselect" class="form-check-input" value="2" ></form>';
                        } else if (full['manuelinvno'] == 0) {
                            checkbox += '<form><input type="checkbox" name ="rowselect" class="form-check-input" value="2" ></form>';
                        } else {

                        }

                        return checkbox;
                    }
                },
                {
                    "data": "id"
                },
                {
                    "data": "name"
                },
                {
                    "data": "date"
                },
                {
                    "targets": -1,
                    "className": 'text-left',
                    "data": "saletype",
                    "render": function (data, type, full) {
                        var label = '';

                        if (full['saletype'] == 1) {
                            label += '<label>Retail Sale</label>';
                        } else {
                            label += '<label>Whole Sale</label>';
                        }
                        return label;
                    }

                },
                {
                    "data": "total",
                    "className": 'text-right',
                    render: $.fn.dataTable.render.number(',', '.', 2, '')
                }

            ],
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();

                var intVal = function (i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i : 0;
                };

                var total = api
                    .column(5)
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                var pageTotal = api
                    .column(5, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function (a, b) {
                        return parseFloat(intVal(a) + intVal(b)).toFixed(2);
                    }, 0);

                $(api.column(5).footer()).html(
                    'Rs ' + pageTotal
                );
            },
            drawCallback: function (settings) {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    }

    $(document).ready(function () {

        // trigger load/reload whenever date or either filter changes
        $('#cashdate').on('change', loadTable);
        $('#filtersaletype').on('change', loadTable);
        $('#filterpaymentmethod').on('change', loadTable);

        // select all record
        $('#selectAll').click(function (e) {
            $('#cashdailytbl').closest('table').find('td input:checkbox').prop('checked', this.checked);
        });

        // bill data submit for process data
        $(document).on("click", "#Btnsubmit", function () {

            var tbody = $('#cashdailytbl tbody');
            if (tbody.children().length > 0) {
                jsonObj = []

                $("#cashdailytbl tbody tr").each(function () {
                    item = {}

                    $(this).find('td').each(function (col_idx) {
                        var r = '';
                        if (col_idx == 0) {
                            var c = $(this).find('input[type="checkbox"]');
                            r = $(c).is(":checked") ? 2 : 0;
                        } else {
                            r = $(this).text();
                        }
                        item["col_" + (col_idx + 1)] = r;
                    });
                    jsonObj.push(item);
                });
            }

            $.ajax({
                type: "POST",
                data: {
                    tableData: jsonObj,
                },
                url: 'process/dailycashtransferprocess.php',
                success: function (result) {
                    var objfirst = JSON.parse(result);
                    if (objfirst.actiontype == 1) {
                        location.reload();
                        alert("Record Added Successfully");
                    } else {
                        location.reload();
                        alert("Record Added Unsuccessfully");
                    }
                }
            });
        });
    });
</script>