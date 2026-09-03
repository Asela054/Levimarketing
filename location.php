<?php 
include "include/header.php";  

$sql="SELECT * FROM `tbl_location` WHERE `status` IN (1,2)";
$result =$conn-> query($sql); 

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
                            <div class="page-header-icon"><i class="fa fa-map-marker"></i></div>
                            <span>Location</span>
                        </h1>
                    </div>
                </div>
            </div>
            <div class="container-fluid mt-2 p-0 p-2">
                <div class="card">
                    <div class="card-body p-0 p-2">
                        <div class="row">
                            <div class="col-3">
                                <form action="process/locationproccess.php" method="post" autocomplete="off">
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-dark">Location*</label>
                                        <input type="text" class="form-control form-control-sm" name="location" id="location" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-dark">Location Code*</label>
                                        <input type="text" class="form-control form-control-sm" name="location_code" id="location_code" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-dark">Location Name*</label>
                                        <input type="text" class="form-control form-control-sm" name="location_name" id="location_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-dark">Location Address*</label>
                                        <input type="text" class="form-control form-control-sm" name="location_address" id="location_address" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-dark">Location Contact 1*</label>
                                        <input type="text" class="form-control form-control-sm" name="location_contact1" id="location_contact1" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-dark">Location Contact 2</label>
                                        <input type="text" class="form-control form-control-sm" name="location_contact2" id="location_contact2">
                                    </div>
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-dark">Location Contact 3</label>
                                        <input type="text" class="form-control form-control-sm" name="location_contact3" id="location_contact3">
                                    </div>
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-dark">Email</label>
                                        <input type="text" class="form-control form-control-sm" name="location_email" id="location_email">
                                    </div>
                                    <div class="form-group mt-2">
                                        <button type="submit" id="submitBtn" class="btn btn-outline-primary btn-sm w-50 fa-pull-right" <?php if($addcheck==0){echo 'disabled';} ?>><i class="far fa-save"></i>&nbsp;Add</button>
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
                                                <th>Location</th>
                                                <th>Code</th>
                                                <th>Name</th>
                                                <th>Address</th>
                                                <th>Contact 1</th>
                                                <th>Contact 2</th>
                                                <th>Contact 3</th>
                                                <th>Email</th>
                                                <th class="text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($result->num_rows > 0) {while ($row = $result-> fetch_assoc()) { ?>
                                            <tr>
                                                <td><?php echo $row['idtbl_location'] ?></td>
                                                <td><?php echo $row['location'] ?></td>
                                                <td><?php echo $row['code']?></td>
                                                <td><?php echo $row['companyname']?></td>
                                                <td><?php echo $row['address']?></td>
                                                <td><?php echo $row['contact1']?></td>
                                                <td><?php echo $row['contact2']?></td>
                                                <td><?php echo $row['contact3']?></td>
                                                <td><?php echo $row['email']?></td>
                                                <td class="text-right">
                                                    <button
                                                        class="btn btn-outline-primary btn-sm btnEdit <?php if($editcheck==0){echo 'd-none';} ?>"
                                                        id="<?php echo $row['idtbl_location'] ?>"><i
                                                            data-feather="edit-2"></i></button>
                                                    <?php if($row['status']==1){ ?>
                                                    <a href="process/statuslocation.php?record=<?php echo $row['idtbl_location'] ?>&type=2"
                                                        onclick="return confirm('Are you sure you want to deactive this?');"
                                                        target="_self"
                                                        class="btn btn-outline-success btn-sm <?php if($statuscheck==0){echo 'd-none';} ?>"><i
                                                            data-feather="check"></i></a>
                                                    <?php }else{ ?>
                                                    <a href="process/statuslocation.php?record=<?php echo $row['idtbl_location'] ?>&type=1"
                                                        onclick="return confirm('Are you sure you want to active this?');"
                                                        target="_self"
                                                        class="btn btn-outline-warning btn-sm <?php if($statuscheck==0){echo 'd-none';} ?>"><i
                                                            data-feather="x-square"></i></a>
                                                    <?php } ?>
                                                    <a href="process/statuslocation.php?record=<?php echo $row['idtbl_location'] ?>&type=3"
                                                        onclick="return confirm('Are you sure you want to remove this?');"
                                                        target="_self"
                                                        class="btn btn-outline-danger btn-sm <?php if($deletecheck==0){echo 'd-none';} ?>"><i
                                                            data-feather="trash-2"></i></a>
                                                </td>
                                            </tr>
                                            <?php }} ?>
                                        </tbody>
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
        $('#dataTable').DataTable();
        $('#dataTable tbody').on('click', '.btnEdit', function() {
            var r = confirm("Are you sure, You want to Edit this ? ");
            if (r == true) {
                var id = $(this).attr('id');
                $.ajax({
                    type: "POST",
                    data: {
                        recordID: id
                    },
                    url: 'getprocess/getLocation.php',
                    success: function(result) { //alert(result);
                        var obj = JSON.parse(result);
                        $('#recordID').val(obj.id);
                        $('#location').val(obj.location); 
                        $('#location_code').val(obj.location_code); 
                        $('#location_name').val(obj.location_name); 
                        $('#location_address').val(obj.location_address); 
                        $('#location_contact1').val(obj.location_contact1); 
                        $('#location_contact2').val(obj.location_contact2); 
                        $('#location_contact3').val(obj.location_contact3); 
                        $('#location_email').val(obj.location_email);                      

                        $('#recordOption').val('2');
                        $('#submitBtn').html('<i class="far fa-save"></i>&nbsp;Update');
                    }
                });
            }
        });
    });

</script>
<?php include "include/footer.php"; ?>
