<?php 
include "include/header.php";  

$sql="SELECT * FROM `tbl_invoice_payment` WHERE `status`=1";
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
                            <span>Payment Receipt</span>
                        </h1>
                    </div>
                </div>
            </div>
            <div class="container-fluid mt-2 p-0 p-2">
                <div class="card">
                    <div class="card-body p-0 p-2">
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-bordered table-striped table-sm nowrap" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>Receipt No</th>
                                            <th>Invoice Number</th>
                                            <th>Date</th>
                                            <th class="text-right">Payment</th>
                                            <th class="text-right">Balance</th>
                                            <th class="text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php include "include/footerbar.php"; ?>
    </div>
</div>
<!-- Modal Payment Receipt -->
<div class="modal fade" id="modalpaymentreceipt" data-backdrop="static" data-keyboard="false" tabindex="-1"
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
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger btn-sm fa-pull-right" id="btnreceiptprint"><i class="fas fa-print"></i>&nbsp;Print Receipt</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Cheque Return -->
<div class="modal fade" id="modalchequereturn" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="chequereturnlabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chequereturnlabel">Return Cheque</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="chequereturndetails"></div>
                <hr>
                <input type="hidden" id="chequereturnid" value="">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label class="small font-weight-bold text-dark">Return Date*</label>
                        <input type="date" class="form-control form-control-sm" id="chequereturndate">
                    </div>
                    <div class="form-group col-md-8">
                        <label class="small font-weight-bold text-dark">Return Reason*</label>
                        <textarea class="form-control form-control-sm" id="chequereturnreason" rows="2" maxlength="255"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning btn-sm" id="btnconfirmchequereturn" disabled><i class="fas fa-undo"></i>&nbsp;Return Cheque</button>
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
            ajax: {
                url: "scripts/paymentreceiptviewlist.php",
                type: "POST", // you can use GET
            },
            "order": [[ 0, "desc" ]],
            "columns": [
                {
                    "targets": -1,
                    "className": '',
                    "data": null,
                    "render": function(data, type, full) {
                        return 'PR-'+full['idtbl_invoice_payment'];
                    }
                },
                {
                    "targets": -1,
                    "className": "",
                    "data": null,
                    "render": function(data, type, full) {
                        if (full['invtype'] == 1) {
                            return full['taxinvoice_no'];
                        } else {
                            return 'INV-' + full['manuelinvno'];
                        }
                    }
                },
                {
                    "data": "date"
                },
                {
                    "targets": -1,
                    "className": 'text-right',
                    "data": null,
                    "render": function(data, type, full) {
                        var payment=addCommas(parseFloat(full['payment']).toFixed(2));
                        return payment;
                    }
                },
                {
                    "targets": -1,
                    "className": 'text-right',
                    "data": null,
                    "render": function(data, type, full) {
                        var payment=addCommas(parseFloat(full['balance']).toFixed(2));
                        return payment;
                    }
                },
                {
                    "targets": -1,
                    "className": 'text-right',
                    "data": null,
                    "render": function(data, type, full) {
                        var button='';
                        button+='<button class="btn btn-outline-dark btn-sm btnview mr-1" id="'+full['idtbl_invoice_payment']+'"><i class="fas fa-eye"></i></button>';

                        // Cheque return (method = 3 only; row has a cheque, not yet returned, receipt still active)
                        if (full['chequeid'] && full['chequereturnstatus'] == 0 && full['status'] == 1) {
                            button+='<button class="btn btn-outline-warning btn-sm btnchequereturn mr-1" data-id="'+full['chequeid']+'" data-chequedate="'+full['chequedate']+'" title="Return Cheque"><i class="fas fa-undo"></i></button>';
                        } else if (full['chequereturnstatus'] == 1) {
                            button+='<span class="badge badge-danger mr-1">Cheque Returned</span>';
                        }

                        button+='<a href="process/statuspaymentreceipt.php?record='+full['idtbl_invoice_payment']+'&type=3" onclick="return delete_confirm()" target="_self" class="btn btn-outline-danger btn-sm ';if(deletecheck==0){button+='d-none';}button+='"><i class="far fa-trash-alt"></i></a>';
                        
                        return button;
                    }
                }
            ]
        } );
        $('#dataTable tbody').on('click', '.btnview', function() {
            var paymentinoiceID =$(this).attr('id');
            
            $('#modalpaymentreceipt').modal('show');
            $('#viewreceiptprint').html('<div class="card border-0 shadow-none bg-transparent"><div class="card-body text-center"><img src="images/spinner.gif" alt="" srcset=""></div></div>');

            $.ajax({
                type: "POST",
                data: {
                    paymentinoiceID: paymentinoiceID
                },
                url: 'getprocess/getpaymentreceipt.php',
                success: function(result) { //alert(result);
                    $('#viewreceiptprint').html(result);
                }
            });
        });

        // Return cheque: open modal with invoice / payment details
        $('#dataTable tbody').on('click', '.btnchequereturn', function() {
            var chequeid = $(this).data('id');
            var chequedate = $(this).data('chequedate');
            var today = '<?php echo date("Y-m-d"); ?>';

            // Cheque can only be returned AFTER the cheque date
            if (today <= chequedate) {
                alert('Cheque can only be returned after the cheque date (' + chequedate + ').');
                return false;
            }

            $('#chequereturnid').val(chequeid);
            $('#chequereturndate').val('').removeAttr('min').removeAttr('max');
            $('#chequereturnreason').val('');
            $('#btnconfirmchequereturn').prop('disabled', true);
            $('#modalchequereturn').modal('show');
            $('#chequereturndetails').html('<div class="card border-0 shadow-none bg-transparent"><div class="card-body text-center"><img src="images/spinner.gif" alt="" srcset=""></div></div>');

            $.ajax({
                type: "POST",
                url: "getprocess/getchequereturndetails.php",
                data: { chequeid: chequeid },
                success: function(result) {
                    $('#chequereturndetails').html(result);
                    var info = $('#chequereturninfo');
                    if (info.length) {
                        $('#chequereturndate')
                            .attr('min', info.attr('data-minreturn'))
                            .attr('max', info.attr('data-maxreturn'))
                            .val(info.attr('data-maxreturn'));
                        $('#btnconfirmchequereturn').prop('disabled', false);
                    }
                },
                error: function() {
                    $('#chequereturndetails').html('<div class="alert alert-danger mb-0">Failed to load details. Please try again.</div>');
                }
            });
        });

        // Return cheque: submit
        $('#btnconfirmchequereturn').on('click', function() {
            var btn = $(this);
            var info = $('#chequereturninfo');
            var chequeid = $('#chequereturnid').val();
            var returndate = $('#chequereturndate').val();
            var reason = $.trim($('#chequereturnreason').val());
            var chequedate = info.attr('data-chequedate');
            var minreturn = info.attr('data-minreturn');
            var maxreturn = info.attr('data-maxreturn');

            if (returndate == '') {
                alert('Please select the return date.');
                return false;
            }
            if (returndate < minreturn) {
                alert('Return date must be after the cheque date (' + chequedate + ').');
                return false;
            }
            if (returndate > maxreturn) {
                alert('Return date cannot be in the future.');
                return false;
            }
            if (reason == '') {
                alert('Please enter the return reason.');
                return false;
            }
            if (!confirm('Return this cheque? The related invoice will be unlocked for payment again.')) {
                return false;
            }

            btn.prop('disabled', true);

            $.ajax({
                type: "POST",
                url: "process/chequereturn.php",
                dataType: "json",
                data: { chequeid: chequeid, returndate: returndate, reason: reason },
                success: function(res) {
                    alert(res.msg);
                    if (res.status == 1) {
                        $('#modalchequereturn').modal('hide');
                        $('#dataTable').DataTable().ajax.reload(null, false);
                    } else {
                        btn.prop('disabled', false);
                    }
                },
                error: function() {
                    alert('Something went wrong. Please try again.');
                    btn.prop('disabled', false);
                }
            });
        });

        document.getElementById('btnreceiptprint').addEventListener ("click", print);
    });

    function print() {
        printJS({
            printable: 'viewreceiptprint',
            type: 'html',
            style: '@page { size: A5 portrait; margin:0.25cm; }',
            targetStyles: ['*']
        })
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