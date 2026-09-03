<?php 

require_once('../connection/db.php');
?>
<?php
     $record=$_POST['recordID'];

     $sql="SELECT * FROM `tbl_vehicle_load` WHERE `idtbl_vehicle_load`='$record'";
     $result=$conn->query($sql);
     $row=$result->fetch_assoc();
     
     $obj=new stdClass();
     $obj->id=$row['idtbl_vehicle_load'];
     $obj->date=$row['date'];
     $obj->lorry=$row['lorryid'];
     $obj->driver=$row['driverid'];
     $obj->officer=$row['officerid'];
     $obj->helper=$row['helperid'];
     $obj->helper2=$row['helperid2'];
     $obj->area=$row['tbl_area_idtbl_area'];

     $sql2="SELECT `u`.`tbl_product_idtbl_product`,`u`.`qty`,`ua`.`product_name` FROM `tbl_vehicle_load_detail` AS `u` LEFT JOIN `tbl_product` AS `ua` ON(`ua`.`idtbl_product` = `u`.`tbl_product_idtbl_product`) WHERE `u`.`tbl_vehicle_load_idtbl_vehicle_load`='$record'";
     $result2=$conn->query($sql2);

     $html='';
     $html.='<label class="small font-weight-bold text-dark">Product:</label>
                    <select class="form-control form-control-sm" name="productT" id="productT">
                         <option value="">Select</option>';

     while ($rowdata = $result2->fetch_assoc()) {
          $html .= '<option value="' . $rowdata['tbl_product_idtbl_product'] . '" data-qty="' . $rowdata['qty'] . '">' . $rowdata['product_name'] . '</option>';      }
      $html.='</select>';
      
     $obj->productdetails=$html;
    
     echo json_encode($obj); 
?>