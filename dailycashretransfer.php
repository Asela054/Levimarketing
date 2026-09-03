<?php 
include "include/header.php"; 

include "include/topnavbar.php"; 
?>
<?php  $type =  $_SESSION['privatetype'];?>
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
                                    <span>&nbsp; Daily Cash Retransfer</span>
                                </h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid mt-2 p-0 p-2">
                <div class="card">
                    <div class="card-body p-0 p-2">
                        <div class="row ">
                            <div class="col-10 col-md-4">
                                <label class="small font-weight-bold text-dark"> Date*</label>
                                <input type="date" class="form-control form-control-sm" name="cashdate" id="cashdate">
                            </div>
                        </div>
                        <br>
                        <hr class="border-dark">
                        <div class="row">
                            <div class="col-12">


                                <div class="col-12">
                                    <div class="scrollbar pb-3" id="style-2">

                                        <?php
                                if($type==2){ ?>
                                        <div class="custom-control custom-checkbox ml-2 mb-2">
                                            <input type="checkbox" class="custom-control-input checkallocate"
                                                id="selectAll">
                                            <label class="custom-control-label" for="selectAll">Select All
                                                Records</label>
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
                                                    <th data-visible="false"></th>
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
                            <?php
        ?>

                        </div>
                        <?php
             if($type==2){
        ?>
                        <div class="row d-flex justify-content-center align-content-center ">
                            <div class="form-group mt-2">
                                <button type="button" name="Btnsubmit" id="Btnsubmit"
                                    class="btn btn-primary btn-m  fa-pull-right"><i
                                        class="fas fa-share"></i>&nbsp;Retransfer</button>
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
    $(document).ready(function () {

        // get bill view deatils table according to date
        $('#cashdate').on("change", function () {
            event.preventDefault();

            $('#cashdailytbl').DataTable({
                "destroy": true,
                "processing": true,
                "serverSide": true,
                ajax: {
                    url: "scripts/dailycashretransferlist.php",
                    type: "POST", // you can use GET
                    "data": function (d) {
                        return $.extend({}, d, {
                            "selectdate": $("#cashdate").val()
                        });

                    }
                },
                "lengthMenu":[100, 200, "All"],
                "order": [
                    [0, "desc"]
                ],
                "columns": [{
                        "targets": -1,
                        "className": 'text-right',
                        "data": "manuelinvno",
                        "render": function (data, type, full) {
                            var checkbox = '';

                            if(full['manuelinvno'] == null){
                              
                            }else if(full['manuelinvno'] == 0){
                            }
                            else{
                                checkbox += '<form><input type="checkbox" id="rowselect" name ="rowselect" class="form-check-input" value="2" ></form>';
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
                        "data": "saletype ",
                        "render": function (data, type, full) {
                            var label = '';

                            if (full['saletype'] == 1) {
                                label += '<label >Retail Sale</label>';
                            } else {
                                label += '<label >Whole Sale</label>';
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

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i : 0;
                };

                // Total over all pages
                total = api
                    .column(5)
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Total over this page
                pageTotal = api
                    .column(5, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function (a, b) {
                        return parseFloat(intVal(a) + intVal(b)).toFixed(2);
                    }, 0);

                // Update footer
                $(api.column(5).footer()).html(
                    // pageTotal=parseFloat(pageTotal).toFixed(2);
                    'Rs ' + pageTotal
                );
            },
            drawCallback: function (settings) {
                $('[data-toggle="tooltip"]').tooltip();
            }
            });

        });



        // select all record 
        $('#selectAll').click(function (e) {
            $('#cashdailytbl').closest('table').find('td input:checkbox').prop('checked', this.checked);
        });



        // bill data submit for process data
        $(document).on("click", "#Btnsubmit", function () {

            // get table data into array
            var tbody = $('#cashdailytbl tbody');
            if (tbody.children().length > 0) {
                jsonObj = []
                
                $("#cashdailytbl tbody tr").each(function () {
                    item = {}
                    //var tablelist = $("#cashdailytbl tbody input[type=checkbox]:checked");
                    
                    $(this).find('td').each(function (col_idx) {
                        var r='';
                        if(col_idx==0){
                            var c=$(this).find('input[type="checkbox"]');
                            //console.log($(c).is(":checked")?2:0);
                            r=$(c).is(":checked")?2:0;
                        }else{
                            r=$(this).text();
                        }
                        item["col_" + (col_idx + 1)] = r;
                    });
                    jsonObj.push(item);
                });
            }

            //console.log(jsonObj);


            $.ajax({
                type: "POST",
                data: {
                    tableData: jsonObj,
                },
                url: 'process/dailycashretransferprocess.php',
                success: function (result) {
                    // console.log(result);
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
