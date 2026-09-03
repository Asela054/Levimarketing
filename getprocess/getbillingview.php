<?php 

require_once('../connection/db.php');
?>
<?php
     $record=$_POST['recordID'];
    $sql ="SELECT * FROM tbl_invoice  INNER JOIN `tbl_customer` ON tbl_invoice.customerid=tbl_customer.idtbl_customer
    WHERE idtbl_invoice='$record'";


    $result=$conn->query($sql);
    $row=$result->fetch_assoc();

    $type =  $row['saletype']; 
    $date =  $row['date'];
    $total =  $row['total'];
    $customer =  $row['name'];

    $saletype;
    if($type ==1){
        $saletype ="Retail Sale";
    }else{
        $saletype ="Whole Sale";
    }
?>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group mb-1">
                                <label class="small font-weight-bold text-dark">Sale Type :</label>
                                <input  type="text" class="form-control form-control-md" value="<?php echo $saletype;?>" readonly>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group mb-1">
                                <label class="small font-weight-bold text-dark">Date :</label>
                                <input  type="text" class="form-control form-control-md"  value="<?php echo $date;?>" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-12">
                        <div class="form-group mb-1">
                                <label class="small font-weight-bold text-dark">Customer :</label>
                                <input  type="text" class="form-control form-control-md" value="<?php echo $customer;?>" readonly>
                            </div>
                        </div>
                    </div>
                    <br>

                        <div class="col-12 col-md-12">
                            <div class="row">
                                <div class="col-12 col-md-12">
                                <div class="table scrollbar" id="style-2">
                                    <table  class="table table-bordered table-striped  nowrap display" id="tblinvoice">
                                        <thead>
                                        <th>Product Name</th>
                                        <th>Qty</th>
                                        <th>Sale Price</th>
                                        </thead>
                                        <tbody>
<?php 
                                $sql2 ="SELECT `ua`.*,`ub`.*, `ua`.`saleprice` AS `invoicesaleprice`,`ub`.`saleprice` AS `productsaleprice`,`ua`.`qty` AS `qty`,`ub`.`product_name` AS `productname` FROM `tbl_invoice_detail` AS `ua` 
                                        INNER JOIN `tbl_product` AS `ub` ON `ua`.`tbl_product_idtbl_product` = `ub`.`idtbl_product` 
                                        WHERE `ua`.`tbl_invoice_idtbl_invoice` = '$record';";

                                    $result= mysqli_query($conn,$sql2) or die( mysqli_error($conn));
                                    while($row= mysqli_fetch_array($result)){
                                        ?>
                                        <tr>
                                        <td><?php echo $row['productname']; ?></td> 
                                        <td><?php echo $row['qty']; ?></td> 
                                        <td class="text-right"><?php echo $row['invoicesaleprice']; ?></td> 
                                         </tr>
                        <?php }?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td class="text-right" colspan="2">Total:</td>
                                                <td class="text-right"> 
                                                    <?php echo $total;?>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                </div>
                                </div>
                        </div>