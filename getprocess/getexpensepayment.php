<?php 

require_once('../connection/db.php');
?>
<?php
     $record=$_POST['recordID'];

     $sql="SELECT * FROM `tbl_expensepayment` WHERE `idtbl_expensepayment`='$record'";
     $result=$conn->query($sql);
     $row=$result->fetch_assoc();

     $obj=new stdClass();
     $obj->id               = $row['idtbl_expensepayment'];
     $obj->refno            = $row['refno'];
     $obj->amount           = $row['amount'];
     $obj->paymentdate      = $row['paymentdate'];
     $obj->paymentmethod    = $row['paymentmethod'];
     $obj->card_last4       = $row['card_last4'];
     $obj->cheque_no        = $row['cheque_no'];
     $obj->cheque_bank_name = $row['cheque_bank_name'];
     $obj->cheque_branch    = $row['cheque_branch'];
     $obj->cheque_date      = ($row['cheque_date']=='0000-00-00') ? '' : $row['cheque_date'];
     $obj->cheque_status    = $row['cheque_status'];
     $obj->remarks          = $row['remarks'];
     $obj->expencestype     = $row['tbl_expences_type_idtbl_expences_type'];
     echo json_encode($obj); 
?>