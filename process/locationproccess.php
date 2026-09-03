
<?php 
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');

$userID=$_SESSION['userid'];
$updatedatetime=date('Y-m-d h:i:s');

$recordOption=$_POST['recordOption'];
$location=$_POST['location'];
$code=$_POST['location_code'];
$name=$_POST['location_name'];
$address=$_POST['location_address'];
$contact1=$_POST['location_contact1'];
$contact2=$_POST['location_contact2'];
$contact3=$_POST['location_contact3'];
$email=$_POST['location_email'];
$recordid=$_POST['recordID'];



if($recordOption==1){
   
$insertlocation = "INSERT INTO `tbl_location`(`location`, `code`, `companyname`, `address`, `contact1`, `contact2`, `contact3`, `email`, `status`, `insertdatetime`,`tbl_user_idtbl_user`)

                    VALUES ('$location','$code','$name','$address','$contact1','$contact2','$contact3','$email','1','$updatedatetime','$userID')";
    if($conn->query($insertlocation)==true){
    
         header("Location:../location.php?action=4");
 
    }else{
        
  
            header("Location:../location.php?action=5");


    }
}
else{
    $update="UPDATE `tbl_location` SET  `location`='$location',`code`='$code',`companyname`='$name',`address`='$address',`contact1`='$contact1',`contact2`='$contact2',`contact3`='$contact3',`email`='$email',`updatedatetime`='$updatedatetime',`update_user`='$userID'  WHERE `idtbl_location`='$recordid'";
    if($conn->query($update)==true){     
    
            header("Location:../location.php?action=6");
     
    }
    else{
    
            header("Location:../location.php?action=5");
    }

}

?>
