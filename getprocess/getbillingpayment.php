<?php 

require_once('../connection/db.php');
?>
<?php
            if(isset($_POST['save'])){
                $customerID = $_POST['customerID'];
                ?>

<div class="scrollbar pb-3" id="style-2">
    <table class="table table-striped table-bordered table-sm nowrap" id="vehicleloaddetails" style="width:100%">
        <thead class="thead-light">
            <tr>
                <th>Invoice No</th>
                <th>Date</th>
                <th>Sale Type</th>
                <th>Amount</th>
                <th>Paid Amount</th>
                <th>Balance</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if(!empty($customerID)){
                    $sql = "SELECT * FROM tbl_invoice 
                    WHERE  tbl_invoice.customerid= '$customerID' AND tbl_invoice.paymentcomplete = 0 AND tbl_invoice.status = 1";
                    $result= mysqli_query($conn,$sql) or die( mysqli_error($conn));
                    while($row= mysqli_fetch_array($result)){ ?>

                    <tr>
                        <td>INV-<?php $id = $row['idtbl_invoice']; 
                                                echo $id; ?></td>
                        <td><?php echo $row['date']; ?></td>
                        <td><?php if($row['saletype'] == 1 ){
                                                    echo "Retail Sale";
                                                }else{ echo "Whole Sale";}  ?> </td>
                        <td><?php echo $row['total']; ?></td>
                        <td>
                            <?php  $existqurry = "SELECT EXISTS(SELECT * FROM tbl_invoice_payment_has_tbl_invoice WHERE tbl_invoice_idtbl_invoice = '$id')";
                                                    
                                                    
                        if($conn->query($existqurry)==true){
                            $paymentquery ="SELECT payment FROM tbl_invoice_payment_has_tbl_invoice 
                            INNER JOIN tbl_invoice_payment ON tbl_invoice_payment_has_tbl_invoice.tbl_invoice_payment_idtbl_invoice_payment=tbl_invoice_payment.idtbl_invoice_payment 
                            WHERE tbl_invoice_idtbl_invoice = '$id' AND tbl_invoice_payment_idtbl_invoice_payment IN (SELECT  MAX(idtbl_invoice_payment) FROM tbl_invoice_payment)";
                                $resultpayments= mysqli_query($conn,$paymentquery) or die( mysqli_error($conn));
                            while($row2= mysqli_fetch_array($resultpayments)){ ?>

                        <?php echo $row2['payment'];?>

                            <?php
                            } 
                        }
                        else{
                        echo "0" ;
                        }
                        ?>
                        </td>
                        <td>
                        <?php $existqurry = "SELECT EXISTS(SELECT * FROM tbl_invoice_payment_has_tbl_invoice WHERE tbl_invoice_idtbl_invoice = '$id')";
                                            
                                            
                     if($conn->query($existqurry)==true){
                        $paymentquery2 ="SELECT balance FROM tbl_invoice_payment_has_tbl_invoice 
                        INNER JOIN tbl_invoice_payment ON tbl_invoice_payment_has_tbl_invoice.tbl_invoice_payment_idtbl_invoice_payment=tbl_invoice_payment.idtbl_invoice_payment 
                        WHERE tbl_invoice_idtbl_invoice = '$id' AND tbl_invoice_payment_idtbl_invoice_payment IN (SELECT  MAX(idtbl_invoice_payment) FROM tbl_invoice_payment)";
                            $resultpayments= mysqli_query($conn,$paymentquery2) or die( mysqli_error($conn));
                        while($row2= mysqli_fetch_array($resultpayments)){ ?>

                        <?php echo $row2['balance'];?>

                        <?php
                        } 
                        }
                        else{
                            echo "0" ;
                        }
                        ?>
                        </td>
                        <td class="text-right">
                            <button class="btn btn-outline-dark btn-sm btnView " id="<?php echo $row['idtbl_invoice'] ?>"
                                id="btnView"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-outline-info btn-sm btnPayment " id="<?php echo $row['idtbl_invoice'] ?>"
                                id="btnPayment"><i class="fas fa-file-invoice-dollar"></i></button>
                        </td>
                    </tr>
                    <?php
                    }
                } 
            }
        ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    $('#vehicleloaddetails').dataTable();
});
</script>