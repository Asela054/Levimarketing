<?php 
include "include/header.php"; 

include "include/topnavbar.php"; 
?>

<?php 
        // product list
         $sqlproduct="SELECT * FROM `tbl_product` WHERE `status` in ('1')";
         $resultproduct=$conn->query($sqlproduct);

         // area list
         $sqlarea="SELECT * FROM `tbl_area` WHERE `status` in ('1')";
         $resultarea=$conn->query($sqlarea);

         // vehicle list
         $sqlvehicle="SELECT * FROM `tbl_vehicle` WHERE `status` in ('1')";
         $resultvehicle=$conn->query($sqlvehicle);

         // employee list
         $sqlemployee="SELECT * FROM `tbl_employee` WHERE `status` in ('1')";
         $resultemployee=$conn->query($sqlemployee);
         $resultdriver=$conn->query($sqlemployee);
         $resulthelper=$conn->query($sqlemployee);
         $resulthelper2=$conn->query($sqlemployee);
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
                                <div class="page-header-icon"><i class="fas fa-truck"></i></div>
                                    <span>&nbsp; Vehicle Transfer</span>
                                </h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid mt-2 p-0 p-2">
                <div class="card">
                    <div class="card-body p-0 p-2">
                        <!-- <div class="row">
                            <div class="col">
                                <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right" id="btnordercreate"><i class="fas fa-plus"></i>&nbsp;Create Vehicle Load</button>
                            </div>
                        </div> -->
                        <div class="row mt-2">
                        <div class="col-12">
                            <div class="table scrollbar" id="style-2">
                                <table id="tblvehicletransfer" style="width:100%"  class="table table-bordered table-striped table-sm nowrap display" >
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Current Vehicle NO</th>
                                        <th>Transfer Vehicle NO</th>
                                        <th>Area</th>
                                        <th>Driver</th>
                                        <th>Officer</th>
                                        <th>Helper 1</th>
                                        <th>Helper 2</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
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
<!-- Modal Load -->
<div class="modal fade" id="modaldispatchdetail" data-backdrop="static" data-keyboard="false" tabindex="-1"
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
                <div id="viewdispatchprint"></div>
            </div>
        </div>
    </div>
</div>
<!--Create Vehicle Dispatch Modal-->
<div class="modal fade" id="modalcreatedispatch" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="staticBackdropLabel">Create Vehicle Dispatch</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                <div class="col-12">
                        <form method="post" id="vehiceDispatchForm" enctype="multipart/form-data">
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Date :</label>
                                    <input  type="date" name="date" class="form-control form-control-sm" id="date" required>
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Vehicle No:</label>
                                    <select class="form-control form-control-sm" name="vehicleno" id="vehicleno"required>
                                        <option value="">Select</option>
                                        <?php if($resultvehicle->num_rows > 0) {while ($rowvehicle = $resultvehicle-> fetch_assoc()) { ?>
                                        <option value="<?php echo $rowvehicle['idtbl_vehicle'] ?>">
                                            <?php echo $rowvehicle['vehicleno'] ?></option>
                                        <?php }} ?>
                                    </select>
                                </div>          
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Area:</label>
                                    <select class="form-control form-control-sm" name="area" id="area"required>
                                            <option value="">Select</option>
                                            <?php if($resultarea->num_rows > 0) {while ($rowarea = $resultarea-> fetch_assoc()) { ?>
                                            <option value="<?php echo $rowarea['idtbl_area'] ?>">
                                                <?php echo $rowarea['area'] ?></option>
                                            <?php }} ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row mb-1">  
                                <div class="col-6">
                                    <label class="small font-weight-bold text-dark">Product:</label>
                                    <select class="form-control form-control-sm" name="product" id="product">
                                            <option value="">Select</option>
                                            <?php if($resultproduct->num_rows > 0) {while ($rowproduct = $resultproduct-> fetch_assoc()) { ?>
                                            <option value="<?php echo $rowproduct['idtbl_product'] ?>">
                                                <?php echo $rowproduct['product_name'] ?></option>
                                            <?php }} ?>
                                    </select> 
                                </div>
                                <div class="col-4">
                                    <label class="small font-weight-bold text-dark">QTY :</label>
                                    <input  type="number" name="qty" class="form-control form-control-sm" id="qty">
                                </div>  
                                <div class="col-2">
                                    <button type="button" name ="addproducts" id="addproducts" class="btn btn-primary btn-sm" style="margin-top:31px; width:100%;"><i class="fas fa-plus"></i>&nbsp;Add</button>
                                </div>                                                            
                            </div>
                            <div class="form-row mt-2 mb-1">
                                <div class="col">
                                    <table class="table table-striped table-bordered table-sm small" id="addProductTable">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th class="d-none">Product ID</th>
                                                <th class="text-right">QTY</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Officer :</label>
                                    <select class="form-control form-control-sm" name="officer" id="officer"required>
                                            <option value="">Select</option>
                                            <?php if($resultemployee->num_rows > 0) {while ($rowemployee = $resultemployee-> fetch_assoc()) { ?>
                                            <option value="<?php echo $rowemployee['idtbl_employee'] ?>">
                                                <?php echo $rowemployee['name'] ?></option>
                                            <?php }} ?>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Driver :</label>
                                    <select class="form-control form-control-sm" name="driver" id="driver"required>
                                            <option value="">Select</option>
                                            <?php if($resultdriver->num_rows > 0) {while ($rowdriver = $resultdriver-> fetch_assoc()) { ?>
                                            <option value="<?php echo $rowdriver['idtbl_employee'] ?>">
                                                <?php echo $rowdriver['name'] ?></option>
                                            <?php }} ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Helper :</label>
                                    <select class="form-control form-control-sm" name="helper1" id="helper1"required>
                                            <option value="">Select</option>
                                            <?php if($resulthelper->num_rows > 0) {while ($rowemployee = $resulthelper-> fetch_assoc()) { ?>
                                            <option value="<?php echo $rowemployee['idtbl_employee'] ?>">
                                                <?php echo $rowemployee['name'] ?></option>
                                            <?php }} ?>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Helper :</label>
                                    <select class="form-control form-control-sm" name="helper2" id="helper2">
                                            <option value="">Select</option>
                                            <?php if($resulthelper2->num_rows > 0) {while ($rowemployee = $resulthelper2-> fetch_assoc()) { ?>
                                            <option value="<?php echo $rowemployee['idtbl_employee'] ?>">
                                                <?php echo $rowemployee['name'] ?></option>
                                            <?php }} ?>
                                    </select>
                                </div>
                            </div>         
                            <div class="form-group mt-2">
                                <button type="submit" name ="btnsubmit" id="btnsubmit" class="btn btn-primary btn-m  fa-pull-right <?php if($addcheck==0){echo 'disabled';} ?>" >Create Dispatch</button>
                            </div>
                            <input type="hidden" name="recordOption" id="recordOption" value="1">
                            <input type="hidden" name="recordID" id="recordID" value="">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "include/footerscripts.php"; ?>
<?php include "include/footer.php"; ?>

<script>
    $(document).ready(function () {
        var addcheck
        var editcheck
        var statuscheck
        var deletecheck

        $('#tblvehicletransfer').DataTable( {
            "destroy": true,
            "processing": true,
            "serverSide": true,
            ajax: {
                url: "scripts/vehicletransferlist.php",
                type: "POST",
            },
            "order": [[ 0, "desc" ]],
            "columns": [
                {
                    "data": "idtbl_vehicle_transfer"
                },
                {
                    "data": "date"
                },
                {
                    "data": "currentvehicleno"
                },
                {
                    "data": "transfervehicleno"
                },
                {
                    "data": "area"
                },
                {
                    "data": "driver"
                },
                {
                    "data": "officer"
                },
                {
                    "data": "helper1"
                },
                {
                    "data": "helper2"
                },
                {
                    "targets": -1,
                    "className": 'text-right',
                    "data": "status,approvestatus",
                    "render": function(data, type,full) {
                        var button='';
                        button+='<button class="btn btn-outline-dark btn-sm mr-1 btnloadview" data-toggle="tooltip" data-placement="bottom" title="View" id="'+full['idtbl_vehicle_transfer']+'" ><i class="far fa-eye"></i></button>';
                        
                        if(full['approvestatus']==0){
                            button+='<button data-toggle="tooltip" data-placement="bottom" title="Approve" class="btn btn-outline-warning btn-sm btnApprove" id="'+full['idtbl_vehicle_transfer']+'"><i class="fa fa-fw fa-thumbs-up"></i></button>&nbsp;';
                        }else {
                        }
                        if(full['status']==1){
                        button+='<a href="process/statusvehicletransfer.php?recordID='+full['idtbl_vehicle_transfer']+'&type=2" onclick="return ConfirmDeactivate()" target="_self" data-toggle="tooltip" data-placement="bottom" title="Deactivate" class="btn btn-outline-success btn-sm mr-1 ';if(statuscheck==0){button+='d-none';}button+='"><i class="fas fa-check"></i></a>';
                        }else {
                        button+='<a href="process/statusvehicletransfer.php?recordID='+full['idtbl_vehicle_transfer']+'&type=1" onclick="return ConfirmActivate()" target="_self" data-toggle="tooltip" data-placement="bottom" title="Active" class="btn btn-outline-warning btn-sm mr-1 ';if(statuscheck==0){button+='d-none';}button+='"><i class="fas fa-times"></i></a>';
                        }
                        
                        button+='<a href="process/statusvehicletransfer.php?recordID='+full['idtbl_vehicle_transfer']+'&type=3" onclick="return  ConfirmDelete()" target="_self" data-toggle="tooltip" data-placement="bottom" title="Delete" class="btn btn-outline-danger btn-sm mr-1 ';if(deletecheck==0){button+='d-none';}button+='"><i class="fas fa-trash"></i></a>&nbsp;';
                        return button;
                    }
                }
            ]
        });

        $('#tblvehicletransfer tbody').on('click', '.btnloadview', function() {
            var loadID=$(this).attr('id');
            $.ajax({
                type: "POST",
                data: {
                    loadID : loadID
                },
                url: 'getprocess/gettransferdetail.php',
                success: function(result) {//alert(result);
                    $('#viewdispatchprint').html(result);
                    $('#modaldispatchdetail').modal('show');
                }
            }); 
        });

        $('#tblvehicletransfer tbody').on('click', '.btnApprove', function() {
            var r = confirm("Are you sure, You want to Approve this ? ");
            if (r == true) {
                var vehicletransferid=$(this).attr('id');
                $.ajax({
                    type: "POST",
                    data: {
                        recordID : vehicletransferid
                    },
                    url: 'process/vehicletransferapproveprocess.php',
                    success: function(result) {//alert(result);
                        action(result);
                        // Convert the object to a JSON-formatted string
                        // Optionally reload the page after a delay or user interaction
                        setTimeout(function() { location.reload(); }, 2000); // Reload after 2 seconds
                        // location.reload();
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

function ConfirmDelete()
{
  return confirm("Are you sure, You want to Delete this ?");
}
function ConfirmActivate()
{
  return confirm("Are you sure, You want to Activate this ?");
}
function ConfirmDeactivate()
{
  return confirm("Are you sure, You want to Deactivate this ?");
}
</script>
