<?php 

require_once('../connection/db.php');
?>
<?php
     $record=$_POST['recordID'];

     $sql="SELECT * FROM `tbl_employee` WHERE `idtbl_employee`='$record'";
     $result=$conn->query($sql);
     $row=$result->fetch_assoc();
     
     $obj=new stdClass();
     $obj->id=$row['idtbl_employee'];
     $obj->name=$row['name'];
     $obj->epf=$row['epfno'];
     $obj->nic=$row['nic'];
     $obj->phoneno=$row['phone'];
     $obj->address=$row['address'];
     echo json_encode($obj); 
?>