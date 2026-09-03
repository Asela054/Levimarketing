
<?php 
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');

$userID=$_SESSION['userid'];
$updatedatetime=date('Y-m-d h:i:s');

$recordOption=$_POST['recordOption'];
$type=$_POST['vehicletype'];
$vehicaleno=$_POST['vehicalno'];

$recordID=$_POST['recordID'];



if($recordOption==1){
   
$insertarea = "INSERT INTO `tbl_vehicle`(`type`, `vehicleno`, `status`, `updatedatetime`, `tbl_user_idtbl_user`)
                VALUES ('$type','$vehicaleno','1','$updatedatetime','$userID')";
    if($conn->query($insertarea)==true){
    
         header("Location:../vehical.php?action=4");
 
    }else{
        
  
            header("Location:../vehical.php?action=5");


    }
}
else{
    $update="UPDATE `tbl_vehicle` SET `type`='$type',`vehicleno`='$vehicaleno',`updatedatetime`='$updatedatetime' ,`tbl_user_idtbl_user`='$userID' WHERE `idtbl_vehicle`='$recordID'";
    if($conn->query($update)==true){     
    
            header("Location:../vehical.php?action=6");
     
    }
    else{
    
            header("Location:../vehical.php?action=5");
    }

}

?>
