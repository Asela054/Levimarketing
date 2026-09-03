<?php 
require_once('../connection/db.php');

$record=$_POST['recordID'];

$obj = new stdClass();
$sql = "SELECT * FROM tbl_vehicle_load  WHERE idtbl_vehicle_load ='$record'";
$result=$conn->query($sql);
if($result == true){
      $row=$result->fetch_assoc();
      $date = $row['date'];
      $lorry = $row['lorryid'];
      $driver = $row['driverid'];

      // product list
      $productlist=array();
      $sqlproduct="SELECT `idtbl_product`, `product_name` FROM `tbl_product` WHERE `status`=1 AND `idtbl_product` IN (SELECT DISTINCT(`tbl_product_idtbl_product`) FROM `tbl_vehicle_load_detail` WHERE `status`=1 AND `tbl_vehicle_load_idtbl_vehicle_load`='$record')";
      $resultproduct=$conn->query($sqlproduct);
      while ($rowproduct = $resultproduct-> fetch_assoc()) {
            $objproduct=new stdClass();
            $objproduct->idtbl_product=$rowproduct['idtbl_product'];
            $objproduct->product_name=$rowproduct['product_name'];

            array_push($productlist, $objproduct);
      }
      $obj->loaddate=$date;
      $obj->lorry=$lorry;
      $obj->driver=$driver;
      $obj->productlist=$productlist;
}

echo json_encode($obj);

?>

      