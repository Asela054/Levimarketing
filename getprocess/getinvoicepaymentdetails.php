<?php 

require_once('../connection/db.php');
?>
<?php
     $record=$_POST['recordID'];

     $sql = "SELECT * FROM tbl_invoice WHERE idtbl_invoice ='$record'";
     $result=$conn->query($sql);
     $row=$result->fetch_assoc();
     
     $obj=new stdClass();
     
     $obj->id=$row['idtbl_invoice'];
     $obj->total=$row['total'];


     $existqurry = "SELECT EXISTS(SELECT * FROM tbl_invoice_payment_has_tbl_invoice WHERE tbl_invoice_idtbl_invoice = '$record')";

     if($conn->query($existqurry)==true){
        $paymentquery ="SELECT * FROM tbl_invoice_payment_has_tbl_invoice 
        INNER JOIN tbl_invoice_payment ON tbl_invoice_payment_has_tbl_invoice.tbl_invoice_payment_idtbl_invoice_payment=tbl_invoice_payment.idtbl_invoice_payment 
        WHERE tbl_invoice_idtbl_invoice = '$record'AND tbl_invoice_payment_idtbl_invoice_payment IN (SELECT  MAX(idtbl_invoice_payment) FROM tbl_invoice_payment)";
        
        $result2=$conn->query($paymentquery);
        $row2=$result2->fetch_assoc();
        $obj->paymentid=$row2['idtbl_invoice_payment'];
        $obj->balance=$row2['balance'];
        
      }

     echo json_encode($obj); 
?>