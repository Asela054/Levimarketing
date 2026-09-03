<?php
session_start();
if(!isset($_SESSION['userid'])){
    $actionObj=new stdClass();
    $actionObj->icon='fas fa-exclamation-triangle';
    $actionObj->title='';
    $actionObj->message='Session expired. Please log in again.';
    $actionObj->url='';
    $actionObj->target='_blank';
    $actionObj->type='danger';
    echo json_encode($actionObj);
    exit;
}
require_once('../connection/db.php');

$userID=$_SESSION['userid'];
$locationID=$_SESSION['location_id'];

$idtbl_stock=isset($_POST['idtbl_stock']) ? intval($_POST['idtbl_stock']) : 0;
$reason=isset($_POST['reason']) ? trim($_POST['reason']) : '';

if(!$idtbl_stock){
    $actionObj=new stdClass();
    $actionObj->icon='fas fa-exclamation-triangle';
    $actionObj->title='';
    $actionObj->message='Invalid stock record';
    $actionObj->url='';
    $actionObj->target='_blank';
    $actionObj->type='danger';
    echo json_encode($actionObj);
    exit;
}
if($reason===''){
    $actionObj=new stdClass();
    $actionObj->icon='fas fa-exclamation-triangle';
    $actionObj->title='';
    $actionObj->message='A reason is required to delete stock';
    $actionObj->url='';
    $actionObj->target='_blank';
    $actionObj->type='danger';
    echo json_encode($actionObj);
    exit;
}

$reason=$conn->real_escape_string($reason);

// ---- Fetch current row + snapshot names (scoped to this user's location) ----
$sqlGetStock="SELECT s.qty, s.tbl_product_idtbl_product, s.tbl_location_idtbl_location,
        p.product_name, l.location, u.name AS username
    FROM tbl_stock s
    LEFT JOIN tbl_product p ON p.idtbl_product = s.tbl_product_idtbl_product
    LEFT JOIN tbl_location l ON l.idtbl_location = s.tbl_location_idtbl_location
    LEFT JOIN tbl_user u ON u.idtbl_user='$userID'
    WHERE s.idtbl_stock='$idtbl_stock' AND s.tbl_location_idtbl_location='$locationID' AND s.status IN (1, 2)
    LIMIT 1";

$resultGetStock=$conn->query($sqlGetStock);

if(!$resultGetStock || $resultGetStock->num_rows==0){
    $actionObj=new stdClass();
    $actionObj->icon='fas fa-exclamation-triangle';
    $actionObj->title='';
    $actionObj->message='Stock record not found or already deleted';
    $actionObj->url='';
    $actionObj->target='_blank';
    $actionObj->type='danger';
    echo json_encode($actionObj);
    exit;
}

$row=$resultGetStock->fetch_assoc();

$old_qty=floatval($row['qty']);
$productID=intval($row['tbl_product_idtbl_product']);
$stockLocationID=intval($row['tbl_location_idtbl_location']);
$productName=$conn->real_escape_string($row['product_name']!==null ? $row['product_name'] : '');
$locationName=$conn->real_escape_string($row['location']!==null ? $row['location'] : '');
$username=$conn->real_escape_string($row['username']!==null ? $row['username'] : 'Unknown');

$qty_change=-$old_qty;
$updatedatetime=date('Y-m-d h:i:s');
$ip=$conn->real_escape_string(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
$userAgent=$conn->real_escape_string(isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'],0,255) : '');

// ---- Soft delete + audit log together, as one transaction ----
// If the log insert fails for any reason, the stock change is rolled back too.
// A delete that isn't logged must never happen.
$conn->autocommit(FALSE);

$updateStock="UPDATE `tbl_stock` SET `status`='3', `updatedatetime`='$updatedatetime', `tbl_user_idtbl_user`='$userID'
    WHERE `idtbl_stock`='$idtbl_stock'";

$updateOk=$conn->query($updateStock);

$insertOk=false;
if($updateOk){
    $insertLog="INSERT INTO `tbl_stock_activity_log`
            (`action_type`, `tbl_stock_idtbl_stock`, `tbl_product_idtbl_product`, `product_name_snapshot`,
             `tbl_location_idtbl_location`, `location_name_snapshot`,
             `old_qty`, `new_qty`, `qty_change`, `reason`,
             `tbl_user_idtbl_user`, `username_snapshot`, `ip_address`, `user_agent`, `action_datetime`)
        VALUES
            ('DELETE','$idtbl_stock','$productID','$productName','$stockLocationID','$locationName',
             '$old_qty','0','$qty_change','$reason',
             '$userID','$username','$ip','$userAgent','$updatedatetime')";
    $insertOk=$conn->query($insertLog);
}

if($updateOk && $insertOk){
    $conn->commit();
    $conn->autocommit(TRUE);

    $actionObj=new stdClass();
    $actionObj->icon='fas fa-check-circle';
    $actionObj->title='';
    $actionObj->message='Stock record deleted';
    $actionObj->url='';
    $actionObj->target='_blank';
    $actionObj->type='success';

    echo $actionJSON=json_encode($actionObj);
}
else{
    // Capture the real error BEFORE rollback/autocommit, since those calls
    // reset the connection's error state.
    $dbError=$conn->error;

    $conn->rollback();
    $conn->autocommit(TRUE);

    $actionObj=new stdClass();
    $actionObj->icon='fas fa-exclamation-triangle';
    $actionObj->title='';
    $actionObj->message='Record Error - nothing was deleted (log write failed): '.$dbError;
    $actionObj->url='';
    $actionObj->target='_blank';
    $actionObj->type='danger';

    echo $actionJSON=json_encode($actionObj);
}