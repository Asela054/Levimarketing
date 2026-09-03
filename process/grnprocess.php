<?php
session_start();
if(!isset($_SESSION['userid'])){header ("Location:../index.php"); exit;}
require_once('../connection/db.php');

$userID=$_SESSION['userid'];
$locationID=$_SESSION['location_id'];

$withPO=isset($_POST['withpo']) ? intval($_POST['withpo']) : 1;
$porderID=isset($_POST['ponumber']) && $_POST['ponumber'] !== '' ? intval($_POST['ponumber']) : 0;
$grnsupplier=isset($_POST['grnsupplier']) && $_POST['grnsupplier'] !== '' ? intval($_POST['grnsupplier']) : 0;
$grndate=$conn->real_escape_string($_POST['grndate']);
$grninvoice=$conn->real_escape_string($_POST['grninvoice']);
$grndispatch=$conn->real_escape_string($_POST['grndispatch']);
$grnnettotal=$conn->real_escape_string($_POST['grnnettotal']);
$tableData=$_POST['tableData'];

$updatedatetime=date('Y-m-d h:i:s');

if ($withPO && !$porderID) {
    $actionObj=new stdClass();
    $actionObj->icon='fas fa-exclamation-triangle';
    $actionObj->title='';
    $actionObj->message='Purchase Order is required';
    $actionObj->url='';
    $actionObj->target='_blank';
    $actionObj->type='danger';
    echo json_encode($actionObj);
    exit;
}
if (!$withPO && !$grnsupplier) {
    $actionObj=new stdClass();
    $actionObj->icon='fas fa-exclamation-triangle';
    $actionObj->title='';
    $actionObj->message='Supplier is required';
    $actionObj->url='';
    $actionObj->target='_blank';
    $actionObj->type='danger';
    echo json_encode($actionObj);
    exit;
}

$porderIdVal = $withPO ? $porderID : 0;

if ($withPO) {
    $sqlGetSupplier = "SELECT `tbl_supplier_idtbl_supplier` FROM `tbl_porder` WHERE `idtbl_porder`='$porderID'";
    $resultGetSupplier = $conn->query($sqlGetSupplier);

    if (!$resultGetSupplier || $resultGetSupplier->num_rows == 0) {
        $actionObj=new stdClass();
        $actionObj->icon='fas fa-exclamation-triangle';
        $actionObj->title='';
        $actionObj->message='Selected Purchase Order was not found';
        $actionObj->url='';
        $actionObj->target='_blank';
        $actionObj->type='danger';
        echo json_encode($actionObj);
        exit;
    }

    $rowGetSupplier = $resultGetSupplier->fetch_assoc();
    $supplierIdVal = intval($rowGetSupplier['tbl_supplier_idtbl_supplier']);
} else {
    $supplierIdVal = $grnsupplier;
}

$insertgrn="INSERT INTO `tbl_grn`(`date`, `total`, `invoicenum`, `dispatchnum`, `porder_id`, `status`, `confirm_status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_location_idtbl_location`, `tbl_supplier_idtbl_supplier`)
    VALUES ('$grndate','$grnnettotal','$grninvoice','$grndispatch','$porderIdVal','1','0','$updatedatetime','$userID','$locationID','$supplierIdVal')";

if($conn->query($insertgrn)==true){
    $grnid=$conn->insert_id;

    foreach($tableData as $rowtabledata){
        $product=intval($rowtabledata['col_2']);
        $unitprice=$conn->real_escape_string($rowtabledata['col_3']);
        $newqty=$conn->real_escape_string($rowtabledata['col_5']);
        $total=$conn->real_escape_string($rowtabledata['col_6']);

        $insretgrndetail="INSERT INTO `tbl_grndetail`(`date`, `type`, `qty`, `unitprice`, `total`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_product_idtbl_product`, `tbl_grn_idtbl_grn`)
            VALUES ('$grndate','0','$newqty','$unitprice','$total','1','$updatedatetime','$userID','$product','$grnid')";
        $conn->query($insretgrndetail);
    }

    $actionObj=new stdClass();
    $actionObj->icon='fas fa-check-circle';
    $actionObj->title='';
    $actionObj->message='GRN Created Successfully';
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