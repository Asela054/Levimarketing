<?php 
include "include/header.php";  

$sql="SELECT `tbl_invoice`.`idtbl_invoice`, `tbl_invoice`.`date`, `tbl_invoice`.`total`, `tbl_invoice`.`paymentcomplete`, `tbl_customer`.`name`, `tbl_employee`.`name` AS `saleref`, `tbl_area`.`area` FROM `tbl_invoice` LEFT JOIN `tbl_customer` ON `tbl_customer`.`idtbl_customer`=`tbl_invoice`.`tbl_customer_idtbl_customer` LEFT JOIN `tbl_employee` ON `tbl_employee`.`idtbl_employee`=`tbl_invoice`.`ref_id` LEFT JOIN `tbl_area` ON `tbl_area`.`idtbl_area`=`tbl_invoice`.`tbl_area_idtbl_area` WHERE `tbl_invoice`.`status`=1";
$result =$conn-> query($sql); 

include "include/topnavbar.php"; 
?>
<style>
    .tableprint {
        table-layout: fixed;
    }
</style>
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
                            <span>Invoice View</span>
                        </h1>
                    </div>
                </div>
            </div>
            <div class="container-fluid mt-2 p-0 p-2">
                <div class="card">
                    <div class="card-body p-0 p-2">
                        <!-- NEW: Filter controls -->
                        <div class="row mb-2">
                            <div class="col-sm-6 col-md-3 mb-2 mb-md-0">
                                <label class="small font-weight-bold text-dark mb-1">Sale Type</label>
                                <select id="filtersaletype" class="form-control form-control-sm">
                                    <option value="">All Sale Types</option>
                                    <option value="1">Retail Sale</option>
                                    <option value="2">Whole Sale</option>
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-3 mb-2 mb-md-0">
                                <label class="small font-weight-bold text-dark mb-1">Payment Method</label>
                                <select id="filterpaymentmethod" class="form-control form-control-sm">
                                    <option value="">All Payment Methods</option>
                                    <option value="1">Cash</option>
                                    <option value="2">Card</option>
                                    <option value="3">Cheque</option>
                                    <option value="4">Online Transfer</option>
                                </select>
                            </div>
                        </div>
                        <!-- END NEW: Filter controls -->
                        <div class="row">
                            <div class="col-12">
                                <div class="scrollbar pb-3" id="style-2">
                                    <table class="table table-bordered table-striped table-sm nowrap" id="dataTable">
                                        <thead>
                                            <tr>
                                                <th>Invoice</th>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th>Sale Type</th>
                                                <th>Customer</th>
                                                <th class="text-right">Total</th>
                                                <th class="text-right">Discount</th>
                                                <th class="text-right">Nettotal</th>
                                                <th>Payment</th>
                                                <th>Approve User</th>
                                                <th class="text-right">Actions</th>
                                            </tr>
                                        </thead>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="5" class="text-right">Totals:</th>
                                                    <th class="text-right"></th>
                                                    <th class="text-right"></th>
                                                    <th class="text-right"></th>
                                                    <th colspan="3"></th>
                                                </tr>
                                            </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php include "include/footerbar.php"; ?>
    </div>
</div>
<!-- Modal Invoice Receipt -->
<div class="modal fade" id="modalinvoicereceipt" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="viewreceiptprint"></div>
                <input type="hidden" name="hideinvoiceid" id="hideinvoiceid" value="">
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger btn-sm fa-pull-right" id="btnreceiptprint"><i class="fas fa-print"></i>&nbsp;Print Receipt</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Invoice Pos Receipt -->
<div class="modal fade" id="modalinvoicereceiptpos" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="viewreceiptprintpos"></div>
                <input type="hidden" name="hideposinvoiceid" id="hideposinvoiceid" value="">
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger btn-sm fa-pull-right" id="btnreceiptprintpos"><i class="fas fa-print"></i>&nbsp;Print Receipt</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Warning -->
<div class="modal fade" style="z-index: 2000; " id="warningModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body bg-danger text-white text-center">
                Can't cancel this invoice, because firstly cancel payment receipt. Thank you.
            </div>
            <div class="modal-footer bg-danger rounded-0">
                <button type="button" class="btn btn-outline-light btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php include "include/footerscripts.php"; ?>
<script>
    $(document).ready(function() {
        var addcheck='<?php echo $addcheck; ?>';
        var editcheck='<?php echo $editcheck; ?>';
        var statuscheck='<?php echo $statuscheck; ?>';
        var deletecheck='<?php echo $deletecheck; ?>';

        // CHANGED: capture the DataTable instance so filters can trigger ajax.reload()
        var dataTable = $('#dataTable').DataTable( {
            "destroy": true,
            "processing": true,
            "serverSide": true,
            "info": false,
            ajax: {
                url: "scripts/invoiceviewlist.php",
                type: "POST",
                data: function(d){
                    d.filtersaletype = $('#filtersaletype').val();
                    d.filterpaymentmethod = $('#filterpaymentmethod').val();
                }
            },
            "order": [[ 0, "desc" ]],
            "columns": [
                {
                    "targets": -1,
                    "className": "",
                    "data": null,
                    "render": function(data, type, full) {
                        if (full['invtype'] == 1) {
                            return full['taxinvoice_no'];
                        } else {
                            return 'INV-' + full['id'];
                        }
                    }
                },
                {
                    "data": "date"
                },
                {
                    "targets": -1,
                    "className": '',
                    "data": null,
                    "render": function(data, type, full) {
                        if(full['invtype']==0){return 'Non Tax';}
                        else if(full['invtype']==1){return 'Tax';}
                        else{return '';}
                    }
                },
                {
                    "targets": -1,
                    "className": '',
                    "data": null,
                    "render": function(data, type, full) {
                        if(full['saletype']==1){return 'Retail Sale';}
                        else if(full['saletype']==2){return 'Whole Sale';}
                        else{return '';}
                    }
                },
                {
                    "data": "name"
                },
                {
                    "targets": -1,
                    "className": 'text-right',
                    "data": null,
                    // NEW: expose the raw numeric total via a data attribute so footerCallback can read it
                    "render": function(data, type, full) {
                        if(type === 'display'){
                            var payment=addCommas(parseFloat(full['total']).toFixed(2));
                            return payment;
                        }
                        return full['total']; // raw value for 'filter'/'sort'/'type'
                    }
                },
                {
                    "targets": -1,
                    "className": 'text-right',
                    "data": null,
                    "render": function(data, type, full) {
                        if(type === 'display'){
                            var discounttotal=addCommas(parseFloat(full['discounttotal']).toFixed(2));
                            return discounttotal;
                        }
                        return full['discounttotal'];
                    }
                },
                {
                    "targets": -1,
                    "className": 'text-right',
                    "data": null,
                    "render": function(data, type, full) {
                        if(type === 'display'){
                            var nettotal=addCommas(parseFloat(full['nettotal']).toFixed(2));
                            return nettotal;
                        }
                        return full['nettotal'];
                    }
                },
                {
                    "targets": -1,
                    "className": '',
                    "data": null,
                    "render": function(data, type, full) {
                        if(full['paymentcomplete']==1){return 'Complete';}
                        else{return 'Pending';}
                    }
                },
                {
                    "data": "approveuser"
                },
                {
                    "targets": -1,
                    "className": 'text-right',
                    "data": null,
                    "render": function(data, type, full) {
                        var button='';
                        button+='<button class="btn btn-outline-dark btn-sm '; 
                        if(full['saletype']==1){
                            if(full['paymentmethod']==1){button+='btnposview';}else{button+='btncreditview';}
                        }
                        else{
                            button+='btncreditview';
                        }                        
                        button += ' mr-1 " id="'+full['idtbl_invoice']+'" data-invtype="'+full['invtype']+'"><i class="fas fa-eye"></i></button>';
                        if(full['paymentcomplete']==0){
                            button+='<a href="process/statusinvoice.php?record='+full['idtbl_invoice']+'&type=3" onclick="return delete_confirm()" target="_self" class="btn btn-outline-danger btn-sm ';if(deletecheck==0){button+='d-none';}button+='"><i class="far fa-trash-alt"></i></a>';
                        }else{
                            button+='<button class="btn btn-outline-danger btn-sm ';if(deletecheck==0){button+='d-none';}button+='" data-toggle="modal" data-target="#warningModal"><i class="far fa-trash-alt"></i></button>';
                        }
                        return button;
                    }
                }
            ],
            "createdRow": function( row, data, dataIndex){
                if ( data['pricechangestatus']  == 1) {
                    $(row).addClass('table-info');
                }
            },
            // NEW: sum Total, Discount, and Nettotal across the current page
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api();

                var intVal = function ( i ) {
                    if (typeof i === 'string') {
                        i = i.replace(/[^0-9.-]/g, '');
                    }
                    return !isNaN(parseFloat(i)) ? parseFloat(i) : 0;
                };

                // NEW: pull the full row objects for the current page instead of column().data(),
                // since Total/Discount/Nettotal columns use "data": null with a render function —
                // api.column(n).data() would just return an array of nulls for those columns.
                var pageRows = api.rows( { page: 'current' } ).data();

                var totalSum = 0, discountSum = 0, nettotalSum = 0;

                for (var i = 0; i < pageRows.length; i++) {
                    totalSum     += intVal(pageRows[i]['total']);
                    discountSum  += intVal(pageRows[i]['discounttotal']);
                    nettotalSum  += intVal(pageRows[i]['nettotal']);
                }

                $( api.column(5).footer() ).html( addCommas(totalSum.toFixed(2)) );
                $( api.column(6).footer() ).html( addCommas(discountSum.toFixed(2)) );
                $( api.column(7).footer() ).html( addCommas(nettotalSum.toFixed(2)) );
            }
        } );

        // NEW: reload the table whenever either filter changes
        $('#filtersaletype, #filterpaymentmethod').change(function(){
            dataTable.ajax.reload();
        });

        $('#dataTable tbody').on('click', '.btncreditview, .btnposview', function() {

            var id = $(this).attr('id');
            var invtype = $(this).data('invtype');

            var url = '';

            if(invtype == 1){
                url = "printinvoice.php?id=" + id;
            } else {
                url = "getprocess/invoiceprintcredit.php?recordID=" + id;
            }

            window.open(url, "_blank");

            setTimeout(function(){
                location.reload();
            }, 3000);
        });
        $('#dataTable tbody').on('click', '.btnposview', function() {
            var id = $(this).attr('id');
            $('#hideposinvoiceid').val(id);

            $('#modalinvoicereceiptpos').modal('show');
            $('#viewreceiptprintpos').html('<div class="card border-0 shadow-none bg-transparent"><div class="card-body text-center"><img src="images/spinner.gif" alt="" srcset=""></div></div>');

            $.ajax({
                type: "POST",
                data: {
                    recordID: id
                },
                url: 'getprocess/getinvoiceview.php',
                success: function(result) { //alert(result);
                    $('#viewreceiptprintpos').html(result);
                }
            });
        });

        // $('#btnreceiptprint').click(function(){
        //     var invoiceid=$('#hidesinvoiceid').val();
        //     print(invoiceid);
        // });
        $('#btnreceiptprintpos').click(function(){
            var invoiceid=$('#hideposinvoiceid').val();
            posprintbill(invoiceid);
        });
        // document.getElementById('btnreceiptprintpos').addEventListener ("click", printpos);
    });

    // function print(invoiceid) {
        // window.open("getprocess/invoiceprintcredit.php?recordID="+invoiceid, "_blank");
        // setTimeout(location.reload(), 3000);
    //     // printJS({
    //     //     printable: 'viewreceiptprint',
    //     //     type: 'html',
    //     //     style: '@page { size: A5 portrait; margin:0.25cm; }',
    //     //     targetStyles: ['*']
    //     // })
    // }
    function posprintbill(invoiceid){
        window.open("getprocess/invoiceprintpos.php?recordID="+invoiceid, "_blank");
        setTimeout(location.reload(), 3000);
    }

    function delete_confirm() {
        return confirm("Are you sure you want to remove this?");
    }

    function addCommas(nStr){
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }
</script>
<?php include "include/footer.php"; ?>