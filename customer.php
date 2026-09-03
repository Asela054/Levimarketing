<?php 
include "include/header.php";  

$productarray=array();
$sqlproduct="SELECT `idtbl_product`, `product_name` FROM `tbl_product` WHERE `status`=1";
$resultproduct =$conn-> query($sqlproduct); 
while ($rowproduct = $resultproduct-> fetch_assoc()) {
    $obj=new stdClass();
    $obj->productID=$rowproduct['idtbl_product'];
    $obj->product=$rowproduct['product_name'];

    array_push($productarray, $obj);
}

$sqlarea="SELECT `idtbl_area`, `area` FROM `tbl_area` WHERE `status`=1";
$resultarea =$conn-> query($sqlarea); 

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
                            <div class="page-header-icon"><i data-feather="users"></i></div>
                            <span>Customer</span>
                        </h1>
                    </div>
                </div>
            </div>
            <div class="container-fluid mt-2 p-0 p-2">
                <div class="card">
                    <div class="card-body p-0 p-2">
                        <div class="row">
                            <div class="col-3">
                                <form action="process/customerprocess.php" method="post" autocomplete="off">
                                    <div class="form-group mb-1">
                                        <label class="small font-weight-bold text-dark">Customer Name*</label>
                                        <input type="text" class="form-control form-control-sm" id="cusName" name="cusName" required>
                                    </div>
                                    <div class="form-row mb-1">
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">Customer Type*</label>
                                            <select name="cusType" id="cusType" class="form-control form-control-sm" required>
                                                <option value="">Select</option>
                                                <option value="1">Hole Sale</option>
                                                <option value="2">Retail</option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">Area*</label>
                                            <select name="area" id="area" class="form-control form-control-sm" required>
                                                <option value="">Select</option>
                                                <?php while ($rowarea = $resultarea-> fetch_assoc()) { ?>
                                                <option value="<?php echo $rowarea['idtbl_area'] ?>"><?php echo $rowarea['area'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-row mb-1">
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">NIC</label>
                                            <input type="text" class="form-control form-control-sm" id="cusNic" name="cusNic" placeholder="">
                                        </div>
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">Mobile*</label>
                                            <input type="text" class="form-control form-control-sm" id="cusMobile" name="cusMobile" required>
                                        </div>
                                    </div>
                                    <div class="form-group mb-1">
                                        <label class="small font-weight-bold text-dark">Address</label>
                                        <textarea class="form-control form-control-sm" id="address" name="address"></textarea>
                                    </div>
                                    <div class="form-row mb-1">
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">Vat Num</label>
                                            <input type="text" class="form-control form-control-sm" id="cusVatNum" name="cusVatNum" placeholder="">
                                        </div>
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">S-Vat</label>
                                            <input type="text" class="form-control form-control-sm" id="cusSVat" name="cusSVat" placeholder="">
                                        </div>
                                    </div>
                                    <div class="form-group mb-1">
                                        <label class="small font-weight-bold text-dark">Email</label>
                                        <input type="email" class="form-control form-control-sm" id="cusEmail" name="cusEmail">
                                    </div>
                                    <div class="form-row mb-1">
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">Credit Type</label>
                                            <div class="input-group input-group-sm">
                                                <select class="form-control" id="cuscredittype" name="cuscredittype">
                                                    <option value="">Select</option>
                                                    <option value="1">Bill To Bill</option>
                                                    <option value="2">Credit Days</option>
                                                    <option value="3">Cash</option>
                                                </select>
                                                <input type="text" class="form-control" id="cuscreditdays" name="cuscreditdays" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row mb-1">
                                        <div class="col">
                                            <label class="small font-weight-bold text-dark">Credit Limit</label>
                                            <input type="text" class="form-control form-control-sm" id="cusCreditlimit" name="cusCreditlimit" placeholder="">
                                        </div>
                                    </div>
                                    <div class="form-group mt-2">
                                        <button type="submit" id="submitBtn" class="btn btn-outline-primary btn-sm px-4 fa-pull-right" <?php if($addcheck==0){echo 'disabled';} ?>><i class="far fa-save"></i>&nbsp;Add</button>
                                    </div>
                                    <input type="hidden" name="recordOption" id="recordOption" value="1">
                                    <input type="hidden" name="recordID" id="recordID" value="">
                                </form>
                            </div>
                            <div class="col-9">
                                <div class="scrollbar pb-3" id="style-2">
                                    <table class="table table-bordered table-striped table-sm nowrap" id="dataTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Area</th>
                                                <th>Name</th>
                                                <th>Type</th>
                                                <th>NIC</th>
                                                <th>Contact</th>
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
<?php include "include/footerscripts.php"; ?>
<script>
    $(document).ready(function() {
        var addcheck='<?php echo $addcheck; ?>';
        var editcheck='<?php echo $editcheck; ?>';
        var statuscheck='<?php echo $statuscheck; ?>';
        var deletecheck='<?php echo $deletecheck; ?>';

        $("#cusVisitDays").select2();

        $('#cuscredittype').change(function(){
            var type = $(this).val();
            if(type==2){
                $('#cuscreditdays').prop('readonly', false);
            }
            else{
                $('#cuscreditdays').prop('readonly', true);
            }
        });

        $('#dataTable').DataTable( {
            "destroy": true,
            "processing": true,
            "serverSide": true,
            ajax: {
                url: "scripts/customerlist.php",
                type: "POST", // you can use GET
            },
            "order": [[ 0, "desc" ]],
            "columns": [
                {
                    "data": "idtbl_customer"
                },
                {
                    "data": "area"
                },
                {
                    "data": "name"
                },
                {
                    "targets": -1,
                    "className": 'text-center',
                    "data": null,
                    "render": function(data, type, full) {
                        var html = '';
                        if(full['type']==1){
                            html+='Hole Sale';
                        }
                        else if(full['type']==2){
                            html+='Retail';
                        }

                        return html;     
                    }
                },
                {
                    "data": "nic"
                },
                {
                    "data": "phone"
                },
                {
                    "targets": -1,
                    "className": 'text-right',
                    "data": null,
                    "render": function(data, type, full) {
                        var button='';
                        button+='<button class="btn btn-outline-primary btn-sm btnEdit mr-1 ';if(editcheck==0){button+='d-none';}button+='" id="'+full['idtbl_customer']+'"><i class="fas fa-pen"></i></button>';
                        if(full['status']==1){
                        button+='<a href="process/statuscustomer.php?record='+full['idtbl_customer']+'&type=2" onclick="return deactive_confirm()" target="_self" class="btn btn-outline-success btn-sm mr-1 ';if(statuscheck==0){button+='d-none';}button+='"><i class="fas fa-check"></i></a>';
                        }else if(full['status']!=5){
                        button+='<a href="process/statuscustomer.php?record='+full['idtbl_customer']+'&type=1" onclick="return active_confirm()" target="_self" class="btn btn-outline-warning btn-sm mr-1 ';if(statuscheck==0){button+='d-none';}button+='"><i class="fas fa-times"></i></a>';
                        }
                        if(full['status']==1){
                        button+='<a href="process/statuscustomer.php?record='+full['idtbl_customer']+'&type=4" onclick="return emergancyactive_confirm()" target="_self" class="btn btn-outline-dark btn-sm mr-1 ';if(statuscheck==0){button+='d-none';}button+='"><i class="far fa-calendar-check"></i></a>';
                        }
                        if(full['status']!=5){
                        button+='<a href="process/statuscustomer.php?record='+full['idtbl_customer']+'&type=3" onclick="return delete_confirm()" target="_self" class="btn btn-outline-danger btn-sm ';if(deletecheck==0){button+='d-none';}button+='"><i class="far fa-trash-alt"></i></a>';
                        }
                        
                        return button;
                    }
                }
            ]
        } );
        $('#dataTable tbody').on('click', '.btnEdit', function() {
            var r = confirm("Are you sure, You want to Edit this ? ");
            if (r == true) {
                var id = $(this).attr('id');
                $.ajax({
                    type: "POST",
                    data: {
                        recordID: id
                    },
                    url: 'getprocess/getcustomer.php',
                    success: function(result) { //alert(result);
                        var obj = JSON.parse(result);
                        $('#recordID').val(obj.id);
                        $('#cusName').val(obj.name);
                        $('#cusType').val(obj.type);                 
                        $('#cusNic').val(obj.nic);
                        $('#cusMobile').val(obj.phone);
                        $('#address').val(obj.address);
                        $('#cusVatNum').val(obj.vat_num);
                        $('#cusSVat').val(obj.svat);
                        $('#cusEmail').val(obj.email);
                        $('#cusCreditlimit').val(obj.credit);
                        $('#cuscredittype').val(obj.credittype);
                        $('#cuscreditdays').val(obj.creditperiod);
                        $('#area').val(obj.area);

                        if(obj.credittype==2){
                            $('#cuscreditdays').prop('readonly', false);
                        }
                        else{
                            $('#cuscreditdays').prop('readonly', true);
                        }

                        $('#recordOption').val('2');
                        $('#submitBtn').html('<i class="far fa-save"></i>&nbsp;Update');
                    }
                });
            }
        });
    });
    function action(data) { //alert(data);
        var obj = JSON.parse(data);
        $.notify({
            // options
            icon: obj.icon,
            title: obj.title,
            message: obj.message,
            url: obj.url,
            target: obj.target
        }, {
            // settings
            element: 'body',
            position: null,
            type: obj.type,
            allow_dismiss: true,
            newest_on_top: false,
            showProgressbar: false,
            placement: {
                from: "top",
                align: "center"
            },
            offset: 100,
            spacing: 10,
            z_index: 1031,
            delay: 5000,
            timer: 1000,
            url_target: '_blank',
            mouse_over: null,
            animate: {
                enter: 'animated fadeInDown',
                exit: 'animated fadeOutUp'
            },
            onShow: null,
            onShown: null,
            onClose: null,
            onClosed: null,
            icon_type: 'class',
            template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0}" role="alert">' +
                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                '<span data-notify="icon"></span> ' +
                '<span data-notify="title">{1}</span> ' +
                '<span data-notify="message">{2}</span>' +
                '<div class="progress" data-notify="progressbar">' +
                '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
                '</div>' +
                '<a href="{3}" target="{4}" data-notify="url"></a>' +
                '</div>'
        });
    }

    function loadproductstocklist(cusID){ 
        var deletecheck = '<?php echo $deletecheck; ?>';
        $.ajax({
            type: "POST",
            data: {
                cusID: cusID,
                deletecheck: deletecheck
            },
            url: 'getprocess/getproductstockaccocustomer.php',
            success: function(result) { //alert(result);
                $('#viewenterstocklist').html(result);
                loadstocklistoption(cusID);
            }
        });
    }
    function loadstocklistoption(cusID){
        $('#tablestockproductlist tbody').on('click', '.btnremovestockproduct', function() {
            var r = confirm("Are you sure, You want to Remove this ? ");
            if (r == true) {
                var id = $(this).attr('id'); 
                $.ajax({
                    type: "POST",
                    data: {
                        cusproductID: id
                    },
                    url: 'process/statuscustomerproductstock.php',
                    success: function(result) { //alert(result);
                        action(result);
                        loadproductstocklist(cusID)
                    }
                });
            }
        });
    }

    function deactive_confirm() {
        return confirm("Are you sure you want to deactive this?");
    }

    function active_confirm() {
        return confirm("Are you sure you want to active this?");
    }

    function delete_confirm() {
        return confirm("Are you sure you want to remove this?");
    }

    function emergancyactive_confirm(){
        return confirm("Are you sure you want to emergency this customer?");
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
