<?php 
session_start();
if(!isset($_SESSION['userid'])){header ("Location:index.php");}
require_once('../connection/db.php');

$userID=$_SESSION['userid'];
$locationID=intval($_SESSION['location']);
$updatedatetime=date('Y-m-d H:i:s');

$grnID=intval($_POST['grnID']);
$returndate=$conn->real_escape_string($_POST['returndate']);
$reason=$conn->real_escape_string($_POST['reason']);
$detailids=isset($_POST['detailid'])?$_POST['detailid']:array();
$returnqtys=isset($_POST['returnqty'])?$_POST['returnqty']:array();

// runs a query and throws if it fails so the transaction can roll back
function run($conn, $sql){
    $result=$conn->query($sql);
    if($result===false){ throw new Exception('Database error: '.$conn->error); }
    return $result;
}

$conn->begin_transaction();

try{
    // lock the GRN so two people cannot return the same quantity at once
    $resultgrn=run($conn, "SELECT * FROM `tbl_grn` WHERE `idtbl_grn`='$grnID' AND `status`=1 AND `confirm_status`=1 AND `tbl_location_idtbl_location`='$locationID' FOR UPDATE");
    if($resultgrn->num_rows==0){ throw new Exception('This GRN is not available for return'); }
    $grn=$resultgrn->fetch_assoc();

    $lines=array();
    $subtotal=0;

    foreach($detailids as $i=>$detailid){
        $detailid=intval($detailid);
        $qty=isset($returnqtys[$i])?floatval($returnqtys[$i]):0;
        if($qty<=0){ continue; }

        $resultline=run($conn, "SELECT `d`.`unitprice`, `d`.`qty`, `d`.`tbl_product_idtbl_product`,
            COALESCE((
                SELECT SUM(`rd`.`qty`) FROM `tbl_grn_return_detail` AS `rd` 
                JOIN `tbl_grn_return` AS `r` ON (`r`.`idtbl_grn_return` = `rd`.`tbl_grn_return_idtbl_grn_return`) 
                WHERE `rd`.`tbl_grndetail_idtbl_grndetail`=`d`.`idtbl_grndetail` AND `rd`.`status`=1 AND `r`.`status`=1
            ),0) AS `returned` 
            FROM `tbl_grndetail` AS `d` 
            WHERE `d`.`idtbl_grndetail`='$detailid' AND `d`.`tbl_grn_idtbl_grn`='$grnID' AND `d`.`status`=1");
        if($resultline->num_rows==0){ throw new Exception('Invalid GRN item'); }
        $line=$resultline->fetch_assoc();

        $balance=$line['qty']-$line['returned'];
        if($qty>$balance+0.0001){ throw new Exception('Return quantity is more than the returnable balance'); }

        // price always comes from the GRN, never from the form
        $linetotal=round($qty*$line['unitprice'],2);
        $subtotal+=$linetotal;

        $lines[]=array(
            'detailid'=>$detailid,
            'productid'=>$line['tbl_product_idtbl_product'],
            'qty'=>$qty,
            'unitprice'=>$line['unitprice'],
            'total'=>$linetotal
        );
    }

    if(count($lines)==0){ throw new Exception('Enter a return quantity for at least one item'); }

    // VAT follows the GRN proportionally
    $vatamount=0;
    if($grn['subtotal']>0){ $vatamount=round($grn['vatamount']*$subtotal/$grn['subtotal'],2); }
    $total=$subtotal+$vatamount;

    $vattype=$conn->real_escape_string($grn['vattype']);
    $vatpercentage=floatval($grn['vatpercentage']);
    $supplierID=intval($grn['tbl_supplier_idtbl_supplier']);

    run($conn, "INSERT INTO `tbl_grn_return`(`date`, `subtotal`, `vattype`, `vatpercentage`, `vatamount`, `total`, `reason`, `status`, `confirm_status`, `insertdatetime`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_grn_idtbl_grn`, `tbl_supplier_idtbl_supplier`, `tbl_location_idtbl_location`) 
        VALUES ('$returndate','$subtotal','$vattype','$vatpercentage','$vatamount','$total','$reason','1','0','$updatedatetime','$updatedatetime','$userID','$grnID','$supplierID','$locationID')");
    $returnID=$conn->insert_id;

    $returnnum='GRR'.str_pad($returnID, 5, '0', STR_PAD_LEFT);
    run($conn, "UPDATE `tbl_grn_return` SET `returnnum`='$returnnum' WHERE `idtbl_grn_return`='$returnID'");

    foreach($lines as $l){
        run($conn, "INSERT INTO `tbl_grn_return_detail`(`qty`, `unitprice`, `total`, `status`, `updatedatetime`, `tbl_user_idtbl_user`, `tbl_product_idtbl_product`, `tbl_grn_return_idtbl_grn_return`, `tbl_grndetail_idtbl_grndetail`) 
            VALUES ('".$l['qty']."','".$l['unitprice']."','".$l['total']."','1','$updatedatetime','$userID','".$l['productid']."','$returnID','".$l['detailid']."')");
    }

    $conn->commit();
    header("Location:../grnreturn.php?action=4");
}
catch(Exception $e){
    $conn->rollback();
    $_SESSION['grnreturn_msg']=$e->getMessage();
    header("Location:../grnreturn.php?action=5");
}
?>