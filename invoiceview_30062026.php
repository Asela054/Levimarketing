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
                        <div class="row">
                            <div class="col-12">
                                <div class="scrollbar pb-3" id="style-2">
                                    <table class="table table-bordered table-striped table-sm nowrap" id="dataTable">
                                        <thead>
                                            <tr>
                                                <th>Invoice</th>
                                                <th>Date</th>
                                                <th>Inv Type</th>
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

        $('#dataTable').DataTable( {
            "destroy": true,
            "processing": true,
            "serverSide": true,
            "info": false,
            ajax: {
                url: "scripts/invoiceviewlist.php",
                type: "POST", // you can use GET
            },
            "order": [[ 1, "desc" ]],
            "columns": [
                {
                    "targets": -1,
                    "className": '',
                    "data": null,
                    "render": function(data, type, full) {
                        return 'INV-'+full['id'];
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
                        if(full['paymentmethod']==1){return 'Cash';}
                        else if(full['paymentmethod']==2){return 'Credit';}
                        else if(full['paymentmethod']==3){return 'Quatation';}
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
                    "render": function(data, type, full) {
                        var payment=addCommas(parseFloat(full['total']).toFixed(2));
                        return payment;
                    }
                },
                {
                    "targets": -1,
                    "className": 'text-right',
                    "data": null,
                    "render": function(data, type, full) {
                        var discounttotal=addCommas(parseFloat(full['discounttotal']).toFixed(2));
                        return discounttotal;
                    }
                },
                {
                    "targets": -1,
                    "className": 'text-right',
                    "data": null,
                    "render": function(data, type, full) {
                        var nettotal=addCommas(parseFloat(full['nettotal']).toFixed(2));
                        return nettotal;
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
                        button+=' mr-1 ';if(editcheck==0){button+='d-none';}button+='" id="'+full['idtbl_invoice']+'"><i class="fas fa-eye"></i></button>';
                        // if(full['paymentcomplete']==0){
                        // button+='<a href="process/statusinvoice.php?record='+full['idtbl_invoice']+'&type=3" onclick="return delete_confirm()" target="_self" class="btn btn-outline-danger btn-sm ';if(deletecheck==0){button+='d-none';}button+='"><i class="far fa-trash-alt"></i></a>';
                        // }else{
                        // button+='<button class="btn btn-outline-danger btn-sm ';if(deletecheck==0){button+='d-none';}button+='" data-toggle="modal" data-target="#warningModal"><i class="far fa-trash-alt"></i></button>';
                        // }
                        
                        
                        return button;
                    }
                }
            ],
            "createdRow": function( row, data, dataIndex){
                if ( data['pricechangestatus']  == 1) {
                    $(row).addClass('table-info');
                }
            }
        } );
        $('#dataTable tbody').on('click', '.btncreditview', function() {
            var id = $(this).attr('id');
            window.open("getprocess/invoiceprintcredit.php?recordID="+id, "_blank");
            setTimeout(location.reload(), 3000);
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
