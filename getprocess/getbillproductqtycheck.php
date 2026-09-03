<?php 

require_once('../connection/db.php');
?>

<?php
if(isset($_POST['save'])){

    $product = $_POST['product'];
    $inserted_qty = $_POST['qty'];
    $recordDate = $_POST['recordDate'];
    $recordDriver = $_POST['driver'];
    $recordLorry = $_POST['lorry'];

    $sqlproduct="SELECT * FROM tbl_vehicle_load 
    INNER JOIN tbl_vehicle_load_detail ON tbl_vehicle_load.idtbl_vehicle_load=tbl_vehicle_load_detail.tbl_vehicle_load_idtbl_vehicle_load
    WHERE date = '$recordDate' AND lorryid ='$recordLorry' AND driverid= '$recordDriver'AND tbl_product_idtbl_product = '$product'";
    
    $result= mysqli_query($conn,$sqlproduct);
    $row=$result->fetch_assoc();
    $qty = $row['qty'];

    $obj=new stdClass();
    if($inserted_qty >= $qty){
        $qtyresult = '1';
    }
    else{
        $qtyresult = '0';
    }
    $obj->checkqty = $qtyresult;
    $obj->inserted_qty = $qty;
    //  $obj->checkqty = $inserted_qty;

    echo json_encode($obj);
  }?> 