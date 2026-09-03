<?php 

require_once('../connection/db.php');
?>

<?php
if(isset($_POST['save'])){

    $product = $_POST['product'];
    $inserted_qty = $_POST['qty'];

    $sql = "SELECT qty FROM tbl_stock WHERE tbl_product_idtbl_product ='$product'";
    $result= mysqli_query($conn,$sql);
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

    echo json_encode($obj);
  }?> 