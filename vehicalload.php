<?php 
include "include/header.php"; 

include "include/topnavbar.php"; 
?>

<?php 
        // product list
         $sqlproduct="SELECT * FROM `tbl_product` WHERE `status` in ('1')";
         $resultproduct=$conn->query($sqlproduct);
         $resultproductT=$conn->query($sqlproduct);

         // area list
         $sqlarea="SELECT * FROM `tbl_area` WHERE `status` in ('1')";
         $resultarea=$conn->query($sqlarea);
         $resultareaT=$conn->query($sqlarea);

         // vehicle list
         $sqlvehicle="SELECT * FROM `tbl_vehicle` WHERE `status` in ('1')";
         $resultvehicle=$conn->query($sqlvehicle);
         $resultvehicleT=$conn->query($sqlvehicle);
         $resulttransfervehicleT=$conn->query($sqlvehicle);

         // employee list
         $sqlemployee="SELECT * FROM `tbl_employee` WHERE `status` in ('1')";
         $resultemployee=$conn->query($sqlemployee);
         $resultemployeeT=$conn->query($sqlemployee);
         $resultdriver=$conn->query($sqlemployee);
         $resultdriverT=$conn->query($sqlemployee);
         $resulthelper=$conn->query($sqlemployee);
         $resulthelperT=$conn->query($sqlemployee);
         $resulthelper2=$conn->query($sqlemployee);
         $resulthelper2T=$conn->query($sqlemployee);
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
                                    <span>&nbsp; Vehicle Load</span>
                                </h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="container-fluid mt-2 p-0 p-2">
                <div class="card">
                    <div class="card-body p-0 p-2">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn btn-outline-primary btn-sm fa-pull-right" id="btnordercreate"><i class="fas fa-plus"></i>&nbsp;Create Vehicle Load</button>
                            </div>
                        </div>
                        <div class="row mt-2">
                        <div class="col-12">
                            <div class="table scrollbar" id="style-2">
                                <table id="tblvehicleload" style="width:100%"  class="table table-bordered table-striped table-sm nowrap display" >
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Vehicle NO</th>
                                        <th>Area</th>
                                        <th>Driver</th>
                                        <!-- <th>Product</th>
                                        <th>Qty</th> -->
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
<!-- Modal Product Price List For Co-operate Customer -->
<div class="modal fade" id="modaladdproductprice" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="staticBackdropLabel">Add Product Price</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <form id="addproductform" autocomplete="off">
                            <div class="form-row">
                                <div class="col-5">
                                    <label class="small font-weight-bold text-dark">Product*</label>
                                    <select name="productlist" id="productlist" class="form-control form-control-sm" required>
                                        <option value="">Select</option>
                                        <?php foreach($productarray as $rowprocutlist) { ?>
                                        <option value="<?php echo $rowprocutlist->productID ?>"><?php echo $rowprocutlist->product ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">New Sale Price*</label>
                                    <input type="text" class="form-control form-control-sm" id="newsaleprice" name="newsaleprice" required>
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Refill Sale Price*</label>
                                    <input type="text" class="form-control form-control-sm" id="refillsaleprice" name="refillsaleprice" required>
                                </div>
                            </div>
                            <div class="form-group mt-2">
                                <button type="button" id="submitmodalBtn" class="btn btn-outline-primary btn-sm px-4 fa-pull-right" <?php if($addcheck==0){echo 'disabled';} ?>><i class="far fa-save"></i>&nbsp;Add</button>
                                <input type="submit" class="d-none" id="hidesubmit" value="">
                            </div>
                            <input type="hidden" name="hidecusid" id="hidecusid" value="">
                        </form>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col">
                        <div id="viewenterlist"></div>
                    </div>
                </div>
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
                                    <label class="small font-weight-bold text-dark">Date</label>
                                    <input  type="date" name="date" class="form-control form-control-sm" id="date" required>
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Vehicle No</label>
                                    <select class="form-control form-control-sm" name="vehicleno" id="vehicleno"required>
                                        <option value="">Select</option>
                                        <?php if($resultvehicle->num_rows > 0) {while ($rowvehicle = $resultvehicle-> fetch_assoc()) { ?>
                                        <option value="<?php echo $rowvehicle['idtbl_vehicle'] ?>">
                                            <?php echo $rowvehicle['vehicleno'] ?></option>
                                        <?php }} ?>
                                    </select>
                                </div>          
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Area</label>
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
                                    <label class="small font-weight-bold text-dark">Product</label><br>
                                    <select class="form-control form-control-sm" style="width: 100%;" name="product" id="product">
                                            <option value="">Select</option>
                                            <?php if($resultproduct->num_rows > 0) {while ($rowproduct = $resultproduct-> fetch_assoc()) { ?>
                                            <option value="<?php echo $rowproduct['idtbl_product'] ?>">
                                                <?php echo $rowproduct['product_name'] ?></option>
                                            <?php }} ?>
                                    </select> 
                                </div>
                                <div class="col-4">
                                    <label class="small font-weight-bold text-dark">QTY</label>
                                    <input  type="number" name="qty" class="form-control form-control-sm" id="qty">
                                </div>  
                                <div class="col-2 text-right">
                                    <label class="small font-weight-bold text-dark">&nbsp;</label><br>
                                    <button type="button" name ="addproducts" id="addproducts" class="btn btn-primary btn-sm px-3"><i class="fas fa-plus mr-2"></i>Add to list</button>
                                </div>                                                            
                            </div>
                            <hr>
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
                                    <label class="small font-weight-bold text-dark">Officer</label>
                                    <select class="form-control form-control-sm" name="officer" id="officer">
                                            <option value="">Select</option>
                                            <?php if($resultemployee->num_rows > 0) {while ($rowemployee = $resultemployee-> fetch_assoc()) { ?>
                                            <option value="<?php echo $rowemployee['idtbl_employee'] ?>">
                                                <?php echo $rowemployee['name'] ?></option>
                                            <?php }} ?>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Driver*</label>
                                    <select class="form-control form-control-sm" name="driver" id="driver" required>
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
                                    <label class="small font-weight-bold text-dark">Helper*</label>
                                    <select class="form-control form-control-sm" name="helper1" id="helper1" required>
                                            <option value="">Select</option>
                                            <?php if($resulthelper->num_rows > 0) {while ($rowemployee = $resulthelper-> fetch_assoc()) { ?>
                                            <option value="<?php echo $rowemployee['idtbl_employee'] ?>">
                                                <?php echo $rowemployee['name'] ?></option>
                                            <?php }} ?>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Helper</label>
                                    <select class="form-control form-control-sm" name="helper2" id="helper2">
                                            <option value="">Select</option>
                                            <?php if($resulthelper2->num_rows > 0) {while ($rowemployee = $resulthelper2-> fetch_assoc()) { ?>
                                            <option value="<?php echo $rowemployee['idtbl_employee'] ?>">
                                                <?php echo $rowemployee['name'] ?></option>
                                            <?php }} ?>
                                    </select>
                                </div>
                            </div>         
                            <div class="form-group mt-3">
                                <button type="submit" name ="btnsubmit" id="btnsubmit" class="btn btn-primary btn-sm px-3 fa-pull-right <?php if($addcheck==0){echo 'disabled';} ?>" >Create Dispatch</button>
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
<!--Create Vehicle Transfer Modal-->
<div class="modal fade" id="modalcreatetransfer" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="staticBackdropLabel">Create Vehicle Transfer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                <div class="col-12">
                        <form method="post" id="vehiceTransferForm" enctype="multipart/form-data">
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Date</label>
                                    <input  type="date" name="dateT" class="form-control form-control-sm" id="dateT" required>
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Vehicle No</label>
                                    <select class="form-control form-control-sm" name="vehiclenoT" id="vehiclenoT"required>
                                        <option value="">Select</option>
                                        <?php if($resultvehicleT->num_rows > 0) {while ($rowvehicle = $resultvehicleT-> fetch_assoc()) { ?>
                                        <option value="<?php echo $rowvehicle['idtbl_vehicle'] ?>">
                                            <?php echo $rowvehicle['vehicleno'] ?></option>
                                        <?php }} ?>
                                    </select>
                                </div>          
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Area</label>
                                    <select class="form-control form-control-sm" name="areaT" id="areaT"required>
                                            <option value="">Select</option>
                                            <?php if($resultareaT->num_rows > 0) {while ($rowarea = $resultareaT-> fetch_assoc()) { ?>
                                            <option value="<?php echo $rowarea['idtbl_area'] ?>">
                                                <?php echo $rowarea['area'] ?></option>
                                            <?php }} ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row mb-1">  
                                <div class="col-4">
                                    <label class="small font-weight-bold text-dark">Transfer Vehicle No</label>
                                    <select class="form-control form-control-sm" name="transfervehiclenoT" id="transfervehiclenoT" required>
                                        <option value="">Select</option>
                                        <?php if($resulttransfervehicleT->num_rows > 0) {while ($rowvehicle = $resulttransfervehicleT-> fetch_assoc()) { ?>
                                        <option value="<?php echo $rowvehicle['idtbl_vehicle'] ?>">
                                            <?php echo $rowvehicle['vehicleno'] ?></option>
                                        <?php }} ?>
                                    </select>
                                </div>
                                <div class="col-6" id="productview"></div>  
                                <div class="col-2">
                                    <label class="small font-weight-bold text-dark">&nbsp;</label><br>
                                    <button type="button" name ="addproductsT" id="addproductsT" class="btn btn-primary btn-sm px-3"><i class="fas fa-plus mr-2"></i>Add to list</button>
                                </div>                                                            
                            </div>
                            <hr>
                            <div class="form-row mt-2 mb-1">
                                <div class="col">
                                    <table class="table table-striped table-bordered table-sm small" id="addProductTableT">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th class="d-none">Product ID</th>
                                                <th class="text-right">QTY</th>
                                                <th class="text-right">Transfer QTY</th>
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
                                    <label class="small font-weight-bold text-dark">Officer</label>
                                    <select class="form-control form-control-sm" name="officerT" id="officerT">
                                            <option value="">Select</option>
                                            <?php if($resultemployeeT->num_rows > 0) {while ($rowemployee = $resultemployeeT-> fetch_assoc()) { ?>
                                            <option value="<?php echo $rowemployee['idtbl_employee'] ?>">
                                                <?php echo $rowemployee['name'] ?></option>
                                            <?php }} ?>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Driver*</label>
                                    <select class="form-control form-control-sm" name="driverT" id="driverT" required>
                                            <option value="">Select</option>
                                            <?php if($resultdriverT->num_rows > 0) {while ($rowdriver = $resultdriverT-> fetch_assoc()) { ?>
                                            <option value="<?php echo $rowdriver['idtbl_employee'] ?>">
                                                <?php echo $rowdriver['name'] ?></option>
                                            <?php }} ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row mb-1">
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Helper*</label>
                                    <select class="form-control form-control-sm" name="helper1T" id="helper1T" required>
                                            <option value="">Select</option>
                                            <?php if($resulthelperT->num_rows > 0) {while ($rowemployee = $resulthelperT-> fetch_assoc()) { ?>
                                            <option value="<?php echo $rowemployee['idtbl_employee'] ?>">
                                                <?php echo $rowemployee['name'] ?></option>
                                            <?php }} ?>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="small font-weight-bold text-dark">Helper</label>
                                    <select class="form-control form-control-sm" name="helper2T" id="helper2T">
                                            <option value="">Select</option>
                                            <?php if($resulthelper2T->num_rows > 0) {while ($rowemployee = $resulthelper2T-> fetch_assoc()) { ?>
                                            <option value="<?php echo $rowemployee['idtbl_employee'] ?>">
                                                <?php echo $rowemployee['name'] ?></option>
                                            <?php }} ?>
                                    </select>
                                </div>
                            </div>         
                            <div class="form-group mt-3">
                                <button type="submit" name ="btnsubmitT" id="btnsubmitT" class="btn btn-primary btn-sm fa-pull-right px-3 <?php if($addcheck==0){echo 'disabled';} ?>" >Create Dispatch</button>
                            </div>
                            <input type="hidden" name="recordOptionT" id="recordOptionT" value="1">
                            <input type="hidden" name="recordIDT" id="recordIDT" value="">
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
        var addcheck='<?php echo $addcheck; ?>';
        var editcheck='<?php echo $editcheck; ?>';
        var statuscheck='<?php echo $statuscheck; ?>';
        var deletecheck='<?php echo $deletecheck; ?>';
        var viewallcustomer
        var count

        $('#product').select2({dropdownParent: $('#modalcreatedispatch')});

        $('#tblvehicleload').DataTable( {
            "destroy": true,
            "processing": true,
            "serverSide": true,
            ajax: {
                url: "scripts/vehicleloadlist.php",
                type: "POST",
            },
            "order": [[ 0, "desc" ]],
            "columns": [
                {
                    "data": "idtbl_vehicle_load"
                },
                {
                    "data": "date"
                },
                {
                    "targets": -1,
					"className": 'text-left',
					"data": null,
					"render": function(data, type, full) {
						if (full['type'] == 0) {
							return 'Dispatch';
						} else {
							return 'Transfer';
						}
					}
                },
                {
                    "data": "vehicleno"
                },
                {
                    "data": "area"
                },
                {
                    "data": "name"
                },
                {
                    "targets": -1,
                    "className": 'text-right',
                    "data": "status , unloadstatus , transferstatus",
                    "render": function(data, type,full) {
                        var button='';
                        button+='<button class="btn btn-outline-dark btn-sm mr-1 btnloadview" data-toggle="tooltip" data-placement="bottom" title="View" id="'+full['idtbl_vehicle_load']+'" ><i class="far fa-eye"></i></button>';
                        if(full['approvestatus']==0){
                            button+='<button data-toggle="tooltip" data-placement="bottom" title="Approve" class="btn btn-outline-warning btn-sm btnApprove mr-2" id="'+full['idtbl_vehicle_load']+'"><i class="fa fa-fw fa-thumbs-up"></i></button>';
                        }else {
                            button+='<button data-toggle="tooltip" class="btn btn-outline-success btn-sm mr-2"><i class="fa fa-fw fa-thumbs-up"></i></button>';
                        }
                        if(full['veiwallcustomerstatus']==0){
                            button+='<a href="process/statusvehicleload.php?record='+full['idtbl_vehicle_load']+'&type=2" onclick="return allcustomer_confirm()" target="_self" class="btn btn-outline-orange btn-sm mr-1 ';if(statuscheck==0){button+='d-none';}button+='"><i class="fas fa-users"></i></a>';
                        }else {
                            button+='<button class="btn btn-success btn-sm mr-1"><i class="fas fa-users"></i></button>';
                        }
                        // if(full['status']==1){
                        //     button+='<a href="process/statusvehicleload.php?recordID='+full['idtbl_vehicle_load']+'&type=2" onclick="return ConfirmDeactivate()" target="_self" data-toggle="tooltip" data-placement="bottom" title="Deactivate" class="btn btn-outline-success btn-sm mr-1 ';if(statuscheck==0){button+='d-none';}button+='"><i class="fas fa-check"></i></a>';
                        // }else {
                        //     button+='<a href="process/statusvehicleload.php?recordID='+full['idtbl_vehicle_load']+'&type=1" onclick="return ConfirmActivate()" target="_self" data-toggle="tooltip" data-placement="bottom" title="Active" class="btn btn-outline-warning btn-sm mr-1 ';if(statuscheck==0){button+='d-none';}button+='"><i class="fas fa-times"></i></a>';
                        // }
                        
                        if(full['unloadstatus']==0){
                            button+='<button data-toggle="tooltip" data-placement="bottom" title="Unload" class="btn btn-outline-info btn-sm btnUnload" id="'+full['idtbl_vehicle_load']+'"><i class="fas fa-file-export"></i></button>&nbsp;';
                        }
                        
                        if(full['transferstatus']==0){
                            button+='<button data-toggle="tooltip" data-placement="bottom" title="Transfer" class="btn btn-outline-warning btn-sm btnTransfer" id="'+full['idtbl_vehicle_load']+'"><i class="fas fa-exchange-alt"></i></button>&nbsp;';
                        }
                        // button+='<a href="process/statusvehicleload.php?recordID='+full['idtbl_vehicle_load']+'&type=3" onclick="return  ConfirmDelete()" target="_self" data-toggle="tooltip" data-placement="bottom" title="Delete" class="btn btn-outline-danger btn-sm mr-1 ';if(deletecheck==0){button+='d-none';}button+='"><i class="fas fa-trash"></i></a>&nbsp;';
                        return button;
                    }
                }
            ]
        });

        $('#addproducts').click(function(){
            var productID = $('#product').val();
            var productName =  $('#product').find(':selected').text();
            var qty = $('#qty').val();

            if (productID === "" || qty === "") {
				alert("Select Product And QTY fields.");
				return;
			}
			$('#addProductTable > tbody:last').append('<tr><td>' + productName + '</td><td class="d-none">' + productID + '</td><td class="text-right">' + qty + '</td><td class="text-right"><button class="btn btn-danger btn-sm btnRowRemoveT mr-1"><i class="fas fa-times"></i></button></td></tr>');
            $('#product').val('').trigger('change');
            $('#qty').val('');
        });

        $('#addProductTable').on('click', '.btnRowRemove', function() {
			var r = confirm("Are you sure, You want to remove this ? ");
			if (r == true) {
				$(this).closest('tr').remove();
			}
		});

        $('#addproductsT').click(function(){
            var productID = $('#productT').val();
            var productName =  $('#productT').find(':selected').text();
            var qty = $('#productT').find(':selected').data('qty');

            if (productID === "" || productName === "" || qty === "") {
				alert("Select Product field.");
				return;
			}
			$('#addProductTableT > tbody:last').append('<tr><td>' + productName + '</td><td class="d-none">' + productID + '</td><td class="text-right">' + qty + '</td><td class="text-right"><input type="number" class="transferqty" style="border-radius: 4px; border-color: #007bff;" id ="transferqty" name="transferqty[]" value="0"></td><td class="text-right"><button class="btn btn-danger btn-sm btnRowRemoveT mr-1"><i class="fas fa-times"></i></button></td></tr>');
            $('#productT').val('');
        });

        $('#addProductTableT').on('click', '.btnRowRemoveT', function() {
			var r = confirm("Are you sure, You want to remove this ? ");
			if (r == true) {
				$(this).closest('tr').remove();
			}
		});

        $('#addProductTableT').on('change', '.transferqty', function(){
            $('#addProductTableT tbody tr').each(function () {
                var qty = $(this).find('td:eq(2)').text();
                var transferqty = $(this).find('input[name^="transferqty"]').val();
                // console.log('Available qty:', qty);
                // console.log('Transfer qty:', transferqty);
                if (transferqty > qty) {
                    alert('Transfer qty cannot be greater than available qty.');
                    $(this).find('input[name^="transferqty"]').val('0');
                }
                if(transferqty < 0)
                {
                    alert('Transfer quantity cannot be negative.');
                    $(this).find('input[name^="transferqty"]').val('0');
                }
            });
        });

        $('#vehiceDispatchForm').submit(function(event){
            event.preventDefault();
            var date = $('#date').val();
            var vehicleNo = $('#vehicleno').val();
            var area = $('#area').val();
            var officer = $('#officer').val();
            var driver = $('#driver').val();
            var helper1 = $('#helper1').val();
            var helper2 = $('#helper2').val();
            var recordID = $('#recordID').val();
            var recordOption = $('#recordOption').val();

            var tbody = $('#addProductTable tbody');
            if (tbody.children().length > 0) {
                var jsonObjAddProduct = []
                $("#addProductTable tbody tr").each(function() {
                    item = {}
                    $(this).find('td').each(function(col_idx) {
                        item["col_" + (col_idx + 1)] = $(this).text();
                    });
                    jsonObjAddProduct.push(item);
                });
            }
            console.log(jsonObjAddProduct);
            $.ajax({
                url: 'process/vehicleloadprocess.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    date: date,
                    vehicleno: vehicleNo,
                    area: area,
                    officer: officer,
                    driver: driver,
                    helper1: helper1,
                    helper2: helper2,
                    tableData: jsonObjAddProduct,
                    recordID: recordID,
                    recordOption: recordOption
                },
                success: function(result) {
                    $('#modalcreatedispatch').modal('hide');
                    action(JSON.stringify(result));
                     // Convert the object to a JSON-formatted string
                    // Optionally reload the page after a delay or user interaction
                    // setTimeout(function() { location.reload(); }, 2000); // Reload after 2 seconds
                    location.reload();
                }
            });
        });

        $('#vehiceTransferForm').submit(function(event){
            event.preventDefault();
            var date = $('#dateT').val();
            var vehicleNo = $('#vehiclenoT').val();
            var transfervehicleNo = $('#transfervehiclenoT').val();
            var area = $('#areaT').val();
            var officer = $('#officerT').val();
            var driver = $('#driverT').val();
            var helper1 = $('#helper1T').val();
            var helper2 = $('#helper2T').val();
            var recordID = $('#recordIDT').val();
            var recordOption = $('#recordOptionT').val();

            var productDetails = [];
            $('#addProductTableT tbody tr').each(function () {
                var productid = $(this).find('td:eq(1)').text();
                var qty = $(this).find('td:eq(2)').text();
                var transferqty = $(this).find('input[name^="transferqty"]').val();

                productDetails.push({
                    productid: productid,
                    qty: qty,
                    transferqty: transferqty
                });
            });
            console.log(productDetails);
            $.ajax({
                url: 'process/vehicaletransferprocess.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    date: date,
                    vehicleno: vehicleNo,
                    transfervehicleNo: transfervehicleNo,
                    area: area,
                    officer: officer,
                    driver: driver,
                    helper1: helper1,
                    helper2: helper2,
                    tableData: productDetails,
                    recordID: recordID,
                    recordOption: recordOption
                },
                success: function(result) {
                    $('#modalcreatetransfer').modal('hide');
                    action(JSON.stringify(result));
                     // Convert the object to a JSON-formatted string
                    // Optionally reload the page after a delay or user interaction
                    // setTimeout(function() { location.reload(); }, 2000); // Reload after 2 seconds
                    location.reload();
                }
            });
        });

        $('#modalcreatetransfer').on('hidden.bs.modal', function(event) {
			window.location.reload();
		});
        
        $("#qty").change(function (event) {
            event.preventDefault();
            var productid = $('#product').val();
            var enterqty = $('#qty').val();
            $.ajax({
                type: "POST",
                data: {
                    save : '1',
                    product:productid,
                    qty:enterqty
                  },
                url: "getprocess/getproductqty.php",
                success: function (rest) { 
                    var obj = JSON.parse(rest);
                  count = obj.checkqty;

                    if(count == '1'){
                        /*if the quantity is greater than the stock*/
                        alert('Warning !! The Quantity you Entered is not Available in stock !!');
                        $('#Btnsubmit').prop('disabled', true);
                    }else{
                        $('#Btnsubmit').prop('disabled', false);
                    }
                }
            });
        });

        // edit function
        $('#tblvehicleload tbody').on('click', '.btnEdit', function () {
            var r = confirm("Are you sure, You want to Edit this ? ");
            if (r == true) {
                $('#modalcreatedispatch').modal('show');
                var id = $(this).attr('id');
                $.ajax({
                    type: "POST",
                    data: {
                        recordID: id
                    },
                    url: 'getprocess/getvehicleload.php',
                    success: function (result) {
                        var obj = JSON.parse(result);
                        $('#recordID').val(obj.id);
                        $('#date').val(obj.date);
                        $('#vehicleno').val(obj.lorry);
                        $('#driver').val(obj.driver);
                        $('#officer').val(obj.officer);
                        $('#helper1').val(obj.helper);
                        $('#helper2').val(obj.helper2);
                        $('#area').val(obj.area);
						$('#addProductTable tbody').empty().html(obj.productdetails);
                        $('#recordOption').val('2');
                        $('#Btnsubmit').html('<i class="far fa-save"></i>&nbsp;Update');

                    }
                });
            }
        });

        $('#tblvehicleload tbody').on('click', '.btnloadview', function() {
            var loadID=$(this).attr('id');
            $.ajax({
                type: "POST",
                data: {
                    loadID : loadID
                },
                url: 'getprocess/getloaddetail.php',
                success: function(result) {//alert(result);
                    $('#viewdispatchprint').html(result);
                    $('#modaldispatchdetail').modal('show');
                }
            }); 
        });

        //unload 
        $('#tblvehicleload tbody').on('click', '.btnUnload', function () {
            var r = confirm("Are you sure, You want to Unload this ? ");
            if (r == true) {
                var id = $(this).attr('id');
                $.ajax({
                    type: "POST",
                    data: {
                        recordID: id
                    },
                    url: 'process/vehicleunloadprocess.php',
                    success: function (result) {
                        $('#modalcreatedispatch').modal('hide');
                        action(JSON.stringify(result));
                        // Convert the object to a JSON-formatted string
                        // Optionally reload the page after a delay or user interaction
                        // setTimeout(function() { location.reload(); }, 2000); // Reload after 2 seconds
                        location.reload();
                    }
                });
            }
        });

        $('#btnordercreate').click(function () {
            $('#modalcreatedispatch').modal('show');
        });

        // Transfer
        $('#tblvehicleload tbody').on('click', '.btnTransfer', function () {
            var r = confirm("Are you sure, You want to Transfer this ? ");
            if (r == true) {
                $('#modalcreatetransfer').modal('show');
                var id = $(this).attr('id');
                $.ajax({
                    type: "POST",
                    data: {
                        recordID: id
                    },
                    url: 'getprocess/getvehicleload.php',
                    success: function (result) {
                        var obj = JSON.parse(result);
                        $('#recordIDT').val(obj.id);
                        $('#dateT').val(obj.date);
                        $('#vehiclenoT').val(obj.lorry);
                        $('#vehiclenoT').prop('disabled', true);
                        $('#driverT').val(obj.driver);
                        $('#officerT').val(obj.officer);
                        $('#helper1T').val(obj.helper);
                        $('#helper2T').val(obj.helper2);
                        $('#areaT').val(obj.area);
                        // $('#qtyT').val(obj.qty);
                        // $('#productT').val(obj.product);
                        $('#productview').html(obj.productdetails);
                        // $('#recordOptionT').val('1');
                        $('#btnsubmitT').html('<i class="fas fa-check-square"></i>&nbsp;Transfer');
                    }
                });
            }
        });

        //approve
        $('#tblvehicleload tbody').on('click', '.btnApprove', function() {
            var r = confirm("Are you sure, You want to Approve this ? ");
            if (r == true) {
                var id=$(this).attr('id');
                $.ajax({
                    type: "POST",
                    data: {
                        recordID : id
                    },
                    url: 'process/vehicleloadapproveprocess.php',
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
function allcustomer_confirm(){
  return confirm("Are you sure, You want to View all Customer this ?");
}
</script>
