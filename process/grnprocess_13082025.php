<?php 
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');

$userID=$_SESSION['userid'];

$grnnum=$_POST['grnnum'];
$porderID=$_POST['ponumber'];
$grndate=$_POST['grndate'];
$grninvoice=$_POST['grninvoice'];
$grndispatch=$_POST['grndispatch'];
$grnnettotal=$_POST['grnnettotal'];
$tableData=$_POST['tableData'];

$updatedatetime=date('Y-m-d h:i:s');

$insertgrn="INSERT INTO `tbl_grn`(`date`, `total`, `invoicenum`, `dispatchnum`, `status`, `updatedatetime`, `tbl_user_idtbl_user`) VALUES ('$grndate','$grnnettotal','$grninvoice','$grndispatch','1','$updatedatetime','$userID')";
if($conn->query($insertgrn)==true){
    $grnid=$conn->insert_id;

    $insertpordergrn="INSERT INTO `tbl_porder_grn`(`tbl_grn_idtbl_grn`, `tbl_porder_idtbl_porder`) VALUES ('$grnid','$porderID')";

    if($conn->query($insertpordergrn)==true){

        foreach($tableData as $rowtabledata){
            $product=$rowtabledata['col_2'];
            $unitprice=$rowtabledata['col_3'];
            $newqty=$rowtabledata['col_5'];
            $total=$rowtabledata['col_6'];

            $insretgrndetail="INSERT INTO `tbl_grndetail`(`date`, `type`, `qty`, `unitprice`, `total`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_grn_idtbl_grn`, `tbl_product_idtbl_product`) VALUES ('$grndate','0','$newqty','$unitprice','$total','1','$updatedatetime','$userID','$grnid','$product')";
            $conn->query($insretgrndetail);

            $totqty=($newqty);
            // update in stock
            // if($totqty>0){
            //     $updatestock="UPDATE `tbl_stock` SET `qty`=(`qty`+'$totqty') WHERE `tbl_product_idtbl_product`='$product'";
            //     $conn->query($updatestock);
            // }   
            

        }
        
        $actionObj=new stdClass();
        $actionObj->icon='fas fa-check-circle';
        $actionObj->title='';
        $actionObj->message='Add Successfully';
        $actionObj->url='';
        $actionObj->target='_blank';
        $actionObj->type='success';

        echo $actionJSON=json_encode($actionObj);
    }
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