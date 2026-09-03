<?php
require_once('../connection/db.php');

$arrayresult=array();
$approveusername=$_POST['approveusername'];
$approvepassword=md5($_POST['approvepassword']);

$sql="SELECT COUNT(*) AS `count`, `idtbl_user` FROM `tbl_user` WHERE `status`=1 AND `username`='$approveusername' AND `password`='$approvepassword' AND `tbl_user_type_idtbl_user_type` IN (1, 2)";
$result=$conn->query($sql);
$row=$result->fetch_assoc();

if($row['count']>0){
    $obj=new stdClass();
    $obj->status='1';
    $obj->userid=$row['idtbl_user'];
}
else{
    $obj=new stdClass();
    $obj->status='0';
    $obj->userid='0';
}

array_push($arrayresult, $obj);

echo json_encode($arrayresult);