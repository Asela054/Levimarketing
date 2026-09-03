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
                                        <th>Vehicle NO</th>
                                        <th>Area</th>
                                        <th>Driver</th>
                                        <th>Product</th>
                                        <th>Qty</th>
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
                        <form action="process/vehicleloadprocess.php" method="post" id="vehiceDispatchForm" enctype="multipart/form-data">
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
                    "data": "vehicleno"
                },
                {
                    "data": "area"
                },
                {
                    "data": "name"
                },
                {
                    "data": "product_name"
                },
                {
                    "data": "qty"
                },
                {
                    "targets": -1,
                    "className": 'text-right',
                    "data": "status , unloadstatus , approvestatus,veiwallcustomerstatus ",
                    "render": function(data, type,full) {
                        var button='';
                        button+='<button class="btn btn-outline-primary btn-sm btnEdit" id="'+full['idtbl_vehicle_load']+'"><i class="fas fa-edit"></i></button>&nbsp;';
                        
                        
                        if(full['status']==1){
                        button+='<a href="process/statusvehicleload.php?recordID='+full['idtbl_vehicle_load']+'&type=2" onclick="return ConfirmDeactivate()" target="_self" class="btn btn-outline-success btn-sm mr-1 ';if(statuscheck==0){button+='d-none';}button+='"><i class="fas fa-check"></i></a>';
                        }else {
                        button+='<a href="process/statusvehicleload.php?recordID='+full['idtbl_vehicle_load']+'&type=1" onclick="return ConfirmActivate()" target="_self" class="btn btn-outline-warning btn-sm mr-1 ';if(statuscheck==0){button+='d-none';}button+='"><i class="fas fa-times"></i></a>';
                        }
                        if(full['unloadstatus']==0){
                            button+='<button class="btn btn-outline-info btn-sm btnUnload" id="'+full['idtbl_vehicle_load']+'"><i class="fas fa-file-export"></i></button>&nbsp;';
                        }else {
                        }
                        
                        if(full['approvestatus']==0){
                            button+='<button class="btn btn-outline-danger btn-sm btnApprove" id="'+full['idtbl_vehicle_load']+'"><i class="fas fa-exclamation-triangle"></i></button>&nbsp;';
                        }else {
                        }

                        if(full['veiwallcustomerstatus'] ==0){
                        button+='<a href="process/viewallcustomerprocess.php?recordID='+full['idtbl_vehicle_load']+'&type=1" onclick="return Confirmviewallcustomer()" target="_self" class="btn btn-outline-warning btn-sm mr-1 ';if(viewallcustomer==0){button+='d-none';}button+='"><i class="fas fa-user-friends"></i></a>';
                        }else {
                        button+='<a href="process/viewallcustomerprocess.php?recordID='+full['idtbl_vehicle_load']+'&type=2" onclick="return ConfirmviewallcustomerDeactivate()" target="_self" class="btn btn-outline-success btn-sm mr-1 ';if(viewallcustomer==0){button+='d-none';}button+='"><i class="fas fa-user-friends"></i></a>';
                        }
                        button+='<a href="process/statusvehicleload.php?recordID='+full['idtbl_vehicle_load']+'&type=3" onclick="return  ConfirmDelete()" target="_self" class="btn btn-outline-danger btn-sm mr-1 ';if(deletecheck==0){button+='d-none';}button+='"><i class="fas fa-trash"></i></a>&nbsp;';
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
			$('#addProductTable > tbody:last').append('<tr><td>' + productName + '</td><td class="d-none">' + productID + '</td><td class="text-right">' + qty + '</td><td class="text-right"><button class="btn btn-danger btn-sm btnRowRemove mr-1"><i class="fas fa-times"></i></button></td></tr>');
            $('#product').val('');
            $('#qty').val('');
        });

        $('#addProductTable').on('click', '.btnRowRemove', function() {
			var r = confirm("Are you sure, You want to remove this ? ");
			if (r == true) {
				$(this).closest('tr').remove();
			}
		});
        
        var count
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

        var addcheck
        var editcheck
        var statuscheck
        var deletecheck
        var viewallcustomer
        // edit function
        $('#tblvehicleload tbody').on('click', '.btnEdit', function () {
            var r = confirm("Are you sure, You want to Edit this ? ");
            if (r == true) {
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
                        $('#qty').val(obj.qty);
                        $('#product').val(obj.product);
                        $('#recordOption').val('2');
                        $('#Btnsubmit').html('<i class="far fa-save"></i>&nbsp;Update');

                    }
                });
            }
        });

        // get data to unload function 
        $('#tblvehicleload tbody').on('click', '.btnUnload', function () {
            var r = confirm("Are you sure, You want to Unload this ? ");
            if (r == true) {
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
                        $('#qty').val(obj.qty);
                        $('#product').val(obj.product);
                        $('#recordOption').val('3');
                        $('#Btnsubmit').html('<i class="fas fa-file-export"></i>&nbsp;Unload');

                    }
                });
            }
        });


     // Modal
        $('#btnordercreate').click(function () {
        $('#modalcreatedispatch').modal('show');
        $('#modalcreatedispatch').on('shown.bs.modal', function () {
            // $.ajax({
            //     url: 'getprocess/get_products.php',
            //     type: 'GET',
            //     dataType: 'json',
            //     success: function (data) {
            //         var tableBody = $('#tableBody');
            //         tableBody.empty();

            //         tableBody.find('tr').each(function () {
            //             updateTotalForRow($(this));
            //         });

            //         $.each(data, function (index, product) {
            //             if (product.idtbl_product !== undefined) {

            //                 var categoryClass = parseInt(product.tbl_product_category_idtbl_product_category) === 2 ? 'accessory-row' : '';

            //                 var row = $('<tr class="' + categoryClass + '">' +
            //                     '<td>' + product.product_name + '</td>' +
            //                     '<td class="d-none">' + product.idtbl_product + '</td>' +
            //                     '<td class="text-center"><input type="number" class="form-control form-control-sm custom-width stock-input" id ="new_quantity" name="new_quantity[]" value="0" data-product-id="' + product.idtbl_product + '"></td>' +
            //                     '</tr>');

            //                 $('.stock-input').on('blur', function () {
            //                     if ($(this).val().trim() === '') {
            //                         $(this).val('0');
            //                     }
            //                 });

            //                 tableBody.append(row);

            //                 var stockInput = row.find('.stock-input');

            //                 stockInput.on('keyup', function () {
            //                     var enteredQty = $(this).val();
            //                     var productId = product.idtbl_product;

            //                     $.ajax({
            //                         url: 'getprocess/get_available_qty.php',
            //                         type: 'GET',
            //                         dataType: 'json',
            //                         data: {
            //                             productId: productId
            //                         },
            //                         success: function (response) {
            //                             if (response.hasOwnProperty('avaqty')) {
            //                                 var availableQty = response.avaqty;

            //                                 if (parseInt(enteredQty) > parseInt(availableQty)) {
            //                                     stockInput.removeClass('is-valid');
            //                                     stockInput.addClass('is-invalid');

            //                                     $('#btncreatedispatch').prop('disabled', true);
            //                                 } else {
            //                                     stockInput.removeClass('is-invalid');
            //                                     stockInput.addClass('is-valid');
            //                                     $('#btncreatedispatch').prop('disabled', false);
            //                                 }
            //                             } else {
            //                                 console.log('Error: Unexpected response format');
            //                             }
            //                         },
            //                         error: function (error) {
            //                             console.log('Error fetching available quantity:', error);
            //                         }
            //                     });
            //                 });
            //             }
            //         });

            //         $('.accessory-row').hide();

            //             $(document).on('change', '.show-accessories-checkbox', function() {
            //                 var isChecked = $(this).prop('checked');
            //                 if (isChecked) {
            //                     $('.accessory-row').show();
            //                 } else {
            //                     $('.accessory-row').hide();
            //                 }
            //             });
            //     },
            //     error: function (error) {
            //         console.log('Error fetching products:', error);
            //     }
            // });
        });


        // get data to Approve function 
        $('#tblvehicleload tbody').on('click', '.btnApprove', function () {
            var r = confirm("Are you sure, You want to Approve this ? ");
            if (r == true) {
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
                        $('#qty').val(obj.qty);
                        $('#product').val(obj.product);
                        $('#recordOption').val('4');
                        $('#Btnsubmit').html('<i class="fas fa-check-square"></i>&nbsp;Approve');

                    }
                });
            }
        });
       });
    });

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
function Confirmviewallcustomer()
{
  return confirm("Are you sure, You want to Activate View all Customer this ?");
}
function ConfirmviewallcustomerDeactivate()
{
  return confirm("Are you sure, You want to Deactivate View all Customer this ?");
}
</script>
