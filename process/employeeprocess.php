
<?php 
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');

$userID=$_SESSION['userid'];
$usertype=$_SESSION['type'];
$updatedatetime=date('Y-m-d h:i:s');

$recordOption=$_POST['recordOption'];
$name=$_POST['employeeName'];
$epf=$_POST['epf'];
$nic=$_POST['nic'];
$phone=$_POST['Contact'];
$address=$_POST['address'];

$recordID=$_POST['recordID'];



if($recordOption==1){
   
$insertarea = "INSERT INTO `tbl_employee`( `name`, `epfno`, `nic`, `phone`, `address`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_user_type_idtbl_user_type`)
                VALUES ('$name','$epf','$nic','$phone','$address','1','$updatedatetime','$userID','$usertype')";
    if($conn->query($insertarea)==true){
    
         header("Location:../employeedetails.php?action=4");
 
    }else{
        
  
            header("Location:../employeedetails.php?action=5");


    }
}
else{
    $update="UPDATE `tbl_employee` SET `name`='$name',`epfno`='$epf',`nic`='$nic',`phone`='$phone',`address`='$address',`updatedatetime`='$updatedatetime' ,`tbl_user_idtbl_user`='$userID',`tbl_user_type_idtbl_user_type`='$usertype'   WHERE `idtbl_employee`='$recordID'";
    if($conn->query($update)==true){     
    
            header("Location:../employeedetails.php?action=6");
     
    }
    else{
    
            header("Location:../employeedetails.php?action=5");
    }

}

?>
