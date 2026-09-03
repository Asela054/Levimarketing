<?php 

require_once('../connection/db.php');
?>

            <?php
            if(isset($_POST['save'])){
                $date = $_POST['date'];
                ?>


                <div class="scrollbar pb-3" id="style-2">
                    <table class="table table-striped table-bordered table-sm nowrap" id="vehicleloaddetails" style="width:100%">
                        <thead class="thead-light">
                            <tr>
                                        <th>#</th>
                                        <th>Vehicle NO</th>
                                        <th>Driver</th>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            if(!empty($date)){
                                $sql = "SELECT * FROM tbl_vehicle_load 
                                INNER JOIN tbl_vehicle ON tbl_vehicle_load.lorryid=tbl_vehicle.idtbl_vehicle
                                INNER JOIN tbl_vehicle_load_detail ON tbl_vehicle_load.	idtbl_vehicle_load=tbl_vehicle_load_detail.tbl_vehicle_load_idtbl_vehicle_load
                                INNER JOIN tbl_employee ON tbl_vehicle_load.driverid=tbl_employee.idtbl_employee
                                INNER JOIN tbl_product ON tbl_vehicle_load_detail.tbl_product_idtbl_product=tbl_product.idtbl_product
                                WHERE tbl_vehicle_load.date = '$date' AND tbl_vehicle_load.status=1 AND tbl_vehicle_load.approvestatus =1 AND tbl_vehicle_load.	unloadstatus =0";
                                $result= mysqli_query($conn,$sql) or die( mysqli_error($conn));
                                while($row= mysqli_fetch_array($result)){ ?>
                                
                                    <tr>
                                        <td><?php echo $row['idtbl_vehicle_load']; ?></td>        
                                        <td><?php echo $row['vehicleno']; ?></td>
                                        <td><?php echo $row['name']; ?></td> 
                                        <td><?php echo $row['product_name']; ?></td> 
                                        <td><?php echo $row['qty']; ?></td> 
                                        <td class="text-center">
                                        <button  class="btn btn-outline-success btn-sm btnBill " id="<?php echo $row['idtbl_vehicle_load'] ?>" id="btnbill"><i class="fas fa-file-invoice-dollar"></i></button></td>  
                                    </tr>
                            <?php
                            }
                            } }
                            ?>
    </tbody>
 </table>
</div>

<script>
     $(document).ready(function () {
        $('#vehicleloaddetails').dataTable();
     });
</script>