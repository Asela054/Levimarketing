<?php
session_start();
if(!isset($_SESSION['userid'])){header ("Location:../index.php"); exit;}
require_once('../connection/db.php');

$userID=$_SESSION['userid'];
$dayend=isset($_POST['dayenddate']) ? $_POST['dayenddate'] : '';

function sendAction($conn, $type, $message){
    $actionObj=new stdClass();
    $actionObj->icon=($type=='success') ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
    $actionObj->title='';
    $actionObj->message=$message;
    $actionObj->url='';
    $actionObj->target='_blank';
    $actionObj->type=$type;
    echo json_encode($actionObj);
    exit;
}

// validate date
if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $dayend, $m) || !checkdate((int)$m[2], (int)$m[3], (int)$m[1])) {
    sendAction($conn, 'danger', 'Invalid date');
}
if ($dayend > date('Y-m-d')) {
    sendAction($conn, 'danger', 'Day end date cannot be in the future');
}

$conn->begin_transaction();

// already done for this date?
$stmt=$conn->prepare("SELECT `idtbl_dayend` FROM `tbl_dayend` WHERE `dayenddate`=? FOR UPDATE");
$stmt->bind_param('s', $dayend);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    $conn->rollback();
    sendAction($conn, 'danger', "Day end for $dayend is already done");
}
$stmt->close();

// header row
$stmt=$conn->prepare("INSERT INTO `tbl_dayend` (`dayenddate`, `totalrows`, `totalqty`, `doneby`, `donedatetime`)
                      SELECT ?, COUNT(*), IFNULL(SUM(`qty`), 0), ?, NOW() FROM `tbl_stock`");
$stmt->bind_param('si', $dayend, $userID);
if (!$stmt->execute()) {
    $err=$stmt->errno;
    $stmt->close();
    $conn->rollback();
    sendAction($conn, 'danger', $err==1062 ? "Day end for $dayend is already done" : 'Record Error');
}
$dayendID=$conn->insert_id;
$stmt->close();

// snapshot of all stock rows
$stmt=$conn->prepare("INSERT INTO `tbl_stock_dayend`
        (`tbl_dayend_idtbl_dayend`, `dayenddate`, `idtbl_stock`, `qty`, `update`, `status`, `updatedatetime`,
         `tbl_user_idtbl_user`, `tbl_product_idtbl_product`, `tbl_location_idtbl_location`)
    SELECT ?, ?, `idtbl_stock`, `qty`, `update`, `status`, `updatedatetime`,
           `tbl_user_idtbl_user`, `tbl_product_idtbl_product`, `tbl_location_idtbl_location`
    FROM `tbl_stock`");
$stmt->bind_param('is', $dayendID, $dayend);
if (!$stmt->execute()) {
    $stmt->close();
    $conn->rollback();
    sendAction($conn, 'danger', 'Failed to copy stock details');
}
$copied=$stmt->affected_rows;
$stmt->close();

$conn->commit();

sendAction($conn, 'success', "Day end completed for $dayend ($copied stock rows saved)");