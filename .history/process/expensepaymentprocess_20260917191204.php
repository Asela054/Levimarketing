<?php 
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');

$userID=$_SESSION['userid'];
$updatedatetime=date('Y-m-d h:i:s');

$recordOption      = $_POST['recordOption'];
$recordID          = $_POST['recordID'];

$refno             = $_POST['refno'];
$expencestype      = $_POST['expencestype'];
$paymentdate       = $_POST['paymentdate'];
$amount            = $_POST['amount'];
$paymentmethod     = $_POST['paymentmethod'];

$card_last4        = isset($_POST['card_last4']) ? $_POST['card_last4'] : '';
$cheque_no         = isset($_POST['cheque_no']) ? $_POST['cheque_no'] : '';
$cheque_bank_name  = isset($_POST['cheque_bank_name']) ? $_POST['cheque_bank_name'] : '';
$cheque_branch     = isset($_POST['cheque_branch']) ? $_POST['cheque_branch'] : '';
$cheque_date       = isset($_POST['cheque_date']) ? $_POST['cheque_date'] : '';
$cheque_status     = isset($_POST['cheque_status']) ? $_POST['cheque_status'] : 0;
$remarks           = isset($_POST['remarks']) ? $_POST['remarks'] : '';

/* clear the fields that do not belong to the selected method
   1=Cash  2=Card  3=Cheque  4=Bank Transfer */
if($paymentmethod!=2){ $card_last4=''; }
if($paymentmethod!=3){
    $cheque_no='';
    $cheque_bank_name='';
    $cheque_branch='';
    $cheque_date='';
    $cheque_status=0;
}

/* cheque_date is a DATE column and cannot be empty */
if($cheque_date==''){ $chequedatevalue="'0000-00-00'"; } else { $chequedatevalue="'$cheque_date'"; }


if($recordOption==1){

    $insert = "INSERT INTO `tbl_expensepayment`(`refno`, `amount`, `paymentdate`, `paymentmethod`, `card_last4`,
                    `cheque_no`, `cheque_bank_name`, `cheque_branch`, `cheque_date`, `cheque_status`, `remarks`,
                    `status`, `updatedatetime`, `tbl_expences_type_idtbl_expences_type`, `tbl_user_idtbl_user`)
                VALUES ('$refno','$amount','$paymentdate','$paymentmethod','$card_last4',
                    '$cheque_no','$cheque_bank_name','$cheque_branch',$chequedatevalue,'$cheque_status','$remarks',
                    '1','$updatedatetime','$expencestype','$userID')";

    if($conn->query($insert)==true){

        header("Location:../expensepayment.php?action=4");

    }else{

        header("Location:../expensepayment.php?action=5");

    }
}
else{

    $update = "UPDATE `tbl_expensepayment` SET
                    `refno`='$refno',
                    `amount`='$amount',
                    `paymentdate`='$paymentdate',
                    `paymentmethod`='$paymentmethod',
                    `card_last4`='$card_last4',
                    `cheque_no`='$cheque_no',
                    `cheque_bank_name`='$cheque_bank_name',
                    `cheque_branch`='$cheque_branch',
                    `cheque_date`=$chequedatevalue,
                    `cheque_status`='$cheque_status',
                    `remarks`='$remarks',
                    `updatedatetime`='$updatedatetime',
                    `tbl_expences_type_idtbl_expences_type`='$expencestype',
                    `tbl_user_idtbl_user`='$userID'
               WHERE `idtbl_expensepayment`='$recordID'";

    if($conn->query($update)==true){

        header("Location:../expensepayment.php?action=6");

    }
    else{

        header("Location:../expensepayment.php?action=5");
    }

}

?>