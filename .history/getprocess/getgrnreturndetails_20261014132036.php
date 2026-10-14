<?php 
session_start();
require_once('../connection/db.php');
?>
<?php
     $recordID=intval($_POST['recordID']);
     $locationID=intval($_SESSION['location_id']);

     $sql="SELECT `r`.*, `g`.`invoicenum`, `g`.`date` AS `grndate`, `s`.`suppliername` 
           FROM `tbl_grn_return` AS `r` 
           LEFT JOIN `tbl_grn` AS `g` ON (`g`.`idtbl_grn` = `r`.`tbl_grn_idtbl_grn`) 
           LEFT JOIN `tbl_supplier` AS `s` ON (`s`.`idtbl_supplier` = `r`.`tbl_supplier_idtbl_supplier`) 
           WHERE `r`.`idtbl_grn_return`='$recordID' AND `r`.`status`=1 AND `r`.`tbl_location_idtbl_location`='$locationID'";
     $result=$conn->query($sql);

     $obj=new stdClass();

     if($result->num_rows==0){
          $obj->error='Return not found';
          echo json_encode($obj);
          exit;
     }

     $row=$result->fetch_assoc();
     $obj->id=$row['idtbl_grn_return'];
     $obj->returnnum=$row['returnnum'];
     $obj->date=$row['date'];
     $obj->grnid=$row['tbl_grn_idtbl_grn'];
     $obj->grndate=$row['grndate'];
     $obj->invoicenum=$row['invoicenum'];
     $obj->suppliername=$row['suppliername'];
     $obj->reason=$row['reason'];
     $obj->confirm_status=$row['confirm_status'];
     $obj->approvedatetime=$row['approvedatetime'];
     $obj->subtotal=number_format((float)$row['subtotal'],2);
     $obj->vatamount=number_format((float)$row['vatamount'],2);
     $obj->total=number_format((float)$row['total'],2);
     $obj->lines=array();

     $sqllines="SELECT `d`.`qty`, `d`.`unitprice`, `d`.`total`, `p`.`product_name` 
                FROM `tbl_grn_return_detail` AS `d` 
                LEFT JOIN `tbl_product` AS `p` ON (`p`.`idtbl_product` = `d`.`tbl_product_idtbl_product`) 
                WHERE `d`.`tbl_grn_return_idtbl_grn_return`='$recordID' AND `d`.`status`=1 
                ORDER BY `d`.`idtbl_grn_return_detail` ASC";
     $resultlines=$conn->query($sqllines);

     while($line=$resultlines->fetch_assoc()){
          $lineobj=new stdClass();
          $lineobj->product=htmlspecialchars($line['product_name']);
          $lineobj->qty=(float)$line['qty'];
          $lineobj->unitprice=number_format((float)$line['unitprice'],2);
          $lineobj->total=number_format((float)$line['total'],2);
          $obj->lines[]=$lineobj;
     }

     echo json_encode($obj); 
?>