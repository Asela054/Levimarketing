<?php 
session_start();
require_once('../connection/db.php');
?>
<?php
     $grnID=intval($_POST['grnID']);
     $locationID=intval($_SESSION['location_id']);

     $sql="SELECT `g`.`idtbl_grn`, `g`.`invoicenum`, `s`.`suppliername` 
           FROM `tbl_grn` AS `g` 
           LEFT JOIN `tbl_supplier` AS `s` ON (`s`.`idtbl_supplier` = `g`.`tbl_supplier_idtbl_supplier`) 
           WHERE `g`.`idtbl_grn`='$grnID' AND `g`.`status`=1 AND `g`.`confirm_status`=1 AND `g`.`tbl_location_idtbl_location`='$locationID'";
     $result=$conn->query($sql);

     $obj=new stdClass();

     if($result->num_rows==0){
          $obj->error='This GRN is not available for return';
          echo json_encode($obj);
          exit;
     }

     $row=$result->fetch_assoc();
     $obj->id=$row['idtbl_grn'];
     $obj->invoicenum=$row['invoicenum'];
     $obj->suppliername=$row['suppliername'];
     $obj->lines=array();

     // balance = GRN qty - qty already on active returns (pending + approved)
     $sqllines="SELECT `d`.`idtbl_grndetail`, `d`.`qty`, `d`.`unitprice`, `p`.`product_name`,
                COALESCE((
                    SELECT SUM(`rd`.`qty`) FROM `tbl_grn_return_detail` AS `rd` 
                    JOIN `tbl_grn_return` AS `r` ON (`r`.`idtbl_grn_return` = `rd`.`tbl_grn_return_idtbl_grn_return`) 
                    WHERE `rd`.`tbl_grndetail_idtbl_grndetail`=`d`.`idtbl_grndetail` AND `rd`.`status`=1 AND `r`.`status`=1
                ),0) AS `returned` 
                FROM `tbl_grndetail` AS `d` 
                LEFT JOIN `tbl_product` AS `p` ON (`p`.`idtbl_product` = `d`.`tbl_product_idtbl_product`) 
                WHERE `d`.`tbl_grn_idtbl_grn`='$grnID' AND `d`.`status`=1 
                ORDER BY `d`.`idtbl_grndetail` ASC";
     $resultlines=$conn->query($sqllines);

     while($line=$resultlines->fetch_assoc()){
          $balance=$line['qty']-$line['returned'];
          if($balance<0){$balance=0;}

          $lineobj=new stdClass();
          $lineobj->id=$line['idtbl_grndetail'];
          $lineobj->product=htmlspecialchars($line['product_name']);
          $lineobj->qty=(float)$line['qty'];
          $lineobj->returned=(float)$line['returned'];
          $lineobj->balance=(float)$balance;
          $lineobj->unitprice=number_format((float)$line['unitprice'],2,'.','');
          $obj->lines[]=$lineobj;
     }

     echo json_encode($obj); 
?>