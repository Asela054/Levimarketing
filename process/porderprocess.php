<?php 
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');//die('bc');
$userID=$_SESSION['userid'];
$locationID=$_SESSION['location_id'];


$orderdate=$_POST['orderdate'];
$supplier=$_POST['supplier'];
$remark=$_POST['remark'];
$total=$_POST['total'];
$tableData=$_POST['tableData'];

$updatedatetime=date('Y-m-d h:i:s');

$insretorder="INSERT INTO `tbl_porder`(`potype`, `orderdate`, `subtotal`, `disamount`, `discount`, `nettotal`, `payfullhalf`, `remark`, `confirmstatus`, `dispatchissue`, `grnissuestatus`, `paystatus`, `shipstatus`, `deliverystatus`, `trackingno`, `trackingwebsite`, `callstatus`, `narration`, `cancelreason`, `returnstatus`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_location_idtbl_location`, `tbl_supplier_idtbl_supplier`) VALUES ('0','$orderdate','$total','0','0','$total','0','$remark','0','0','0','0','0','0','','-','0','-','-','0','1','$updatedatetime','$userID',$locationID,$supplier)";
if($conn->query($insretorder)==true){
    $orderID=$conn->insert_id;

    foreach($tableData as $rowtabledata){
        $product=$rowtabledata['col_2'];
        $unitprice=$rowtabledata['col_3'];
        $saleprice=$rowtabledata['col_4'];
        $newqty=$rowtabledata['col_5'];
        $total=$rowtabledata['col_6'];

        $insertorderdetail="INSERT INTO `tbl_porder_detail`(`type`, `qty`, `freeqty`, `unitprice`, `saleprice`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_porder_idtbl_porder`, `tbl_product_idtbl_product`) VALUES ('0','$newqty','0','$unitprice','$saleprice','1','$updatedatetime','$userID','$orderID','$product')";
        $conn->query($insertorderdetail);
    }

    $insertpayment = "INSERT INTO `tbl_porder_payment`(`date`, `ordertotal`, `previousbill`, `balancetotal`, `accountstatus`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_porder_idtbl_porder`) VALUES ('$orderdate','$total','0','0','0','1','$updatedatetime','$userID','$orderID')";
    $conn->query($insertpayment);

    $actionObj=new stdClass();
    $actionObj->icon='fas fa-check-circle';
    $actionObj->title='';
    $actionObj->message='Add Successfully';
    $actionObj->url='';
    $actionObj->target='_blank';
    $actionObj->type='success';

    echo $actionJSON=json_encode($actionObj);
}
else{
    $actionObj=new stdClass();
    $actionObj->icon='fas fa-exclamation-triangle';
    $actionObj->title='';
    $actionObj->message='Record Error';
    $actionObj->url='';
    $actionObj->target='_blank';
    $actionObj->type='danger';

    echo $actionJSON=json_encode($actionObj);
}