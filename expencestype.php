<?php 
include "include/header.php"; 

include "include/topnavbar.php"; 
?>

<?php
       $sqltype="SELECT * FROM `tbl_expences_type` WHERE `status` in ('1','2')";
       $resulttype=$conn->query($sqltype);
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
                                <div class="page-header-icon"><i class="fas fa-tags"></i></div>
                                    <span>&nbsp; Expenses Type</span>
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
              <div class="col-4">
                  <form action="process/expencestypeprocess.php" method="post" id="editform">
                      <div class="form-group mb-1">
                                <label class="small font-weight-bold text-dark">Expenses Type :</label>
                                <input type="text" name="expencestype" class="form-control form-control-sm" id="expencestype" maxlength="45" required>
                      </div>
                      <div class="form-group mt-2">
                            <button type="submit" name="Btnsubmit" id="Btnsubmit" class="btn btn-primary btn-m fa-pull-right <?php if($addcheck==0){echo 'disabled';} ?>">Add</button>
                       </div>

                       <input type="hidden" name="recordOption" id="recordOption" value="1">
                       <input type="hidden" name="recordID" id="recordID" value="">
                  </form>
              </div>
              <div class="col-8">
                    <div class="table scrollbar" id="style-2">
                        <table id="tblexpencestype" class="table table-bordered table-striped table-sm nowrap display">
                         <thead class="thead-dark">
                             <tr>
                                 <th>#</th>
                                 <th>Expenses Type</th>
                                 <th>Action</th>
                             </tr>
                         </thead>
                         <tbody>
                         <?php if($resulttype->num_rows > 0) {while ($row = $resulttype->fetch_assoc()) { ?>
                                            <tr>
                                                <td><?php echo $row['idtbl_expences_type'] ?></td>
                                                <td><?php echo $row['expencestype'] ?></td>
                                                <td class="text-right">

                                                    <button class="btn btn-outline-primary btn-sm btnEdit <?php if($editcheck==0){echo 'disabled';} ?>"
                                                        id="<?php echo $row['idtbl_expences_type'] ?>"><i
                                                            data-feather="edit-2"></i></button>
                                                    <?php if($row['status']==1){ ?>
                                                    <a href="process/statusexpencestype.php?record=<?php echo $row['idtbl_expences_type'] ?>&type=2"
                                                        onclick="return confirm('Are you sure you want to deactive this?');"
                                                        target="_self" class="btn btn-outline-success btn-sm <?php if($statuscheck==0){echo 'disabled';} ?>"><i
                                                            data-feather="check"></i></a>
                                                    <?php }else{ ?>
                                                    <a href="process/statusexpencestype.php?record=<?php echo $row['idtbl_expences_type'] ?>&type=1"
                                                        onclick="return confirm('Are you sure you want to active this?');"
                                                        target="_self" class="btn btn-outline-warning btn-sm <?php if($statuscheck==0){echo 'disabled';} ?>"><i
                                                            data-feather="x-square"></i></a>
                                                    <?php } ?>
                                                    <a href="process/statusexpencestype.php?record=<?php echo $row['idtbl_expences_type'] ?>&type=3"
                                                        onclick="return confirm('Are you sure you want to remove this?');"
                                                        target="_self" class="btn btn-outline-danger btn-sm <?php if($deletecheck==0){echo 'disabled';} ?>"><i
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
<?php include "include/footer.php"; ?>

<script>
    $(document).ready(function () {
        $('#tblexpencestype').DataTable();

        $('#tblexpencestype tbody').on('click', '.btnEdit', function () {
            var r = confirm("Are you sure, You want to Edit this ? ");
            if (r == true) {
                var id = $(this).attr('id');
                $.ajax({
                    type: "POST",
                    data: {
                        recordID: id
                    },
                    url: 'getprocess/getexpencestype.php',
                    success: function (result) {
                        var obj = JSON.parse(result);
                        $('#recordID').val(obj.id);
                        $('#expencestype').val(obj.expencestype);
                        $('#recordOption').val('2');
                        $('#Btnsubmit').html('<i class="far fa-save"></i>&nbsp;Update');
                    }
                });
            }
        });
    });
</script>