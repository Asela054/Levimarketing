<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['userid'])) {
    http_response_code(403);
    echo json_encode(array('ok' => false, 'msg' => 'Not logged in'));
    exit;
}

require('config.php');
mysqli_report(MYSQLI_REPORT_OFF);
$mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);
if ($mysqli->connect_errno) {
    echo json_encode(array('ok' => false, 'msg' => 'Database connection failed'));
    exit;
}

$userID = (int) $_SESSION['userid'];
$dayend = isset($_POST['dayenddate']) ? $_POST['dayenddate'] : '';

// validate date
if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $dayend, $m) || !checkdate((int)$m[2], (int)$m[3], (int)$m[1])) {
    echo json_encode(array('ok' => false, 'msg' => 'Invalid date'));
    exit;
}
if ($dayend > date('Y-m-d')) {
    echo json_encode(array('ok' => false, 'msg' => 'Day end date cannot be in the future'));
    exit;
}

$mysqli->begin_transaction();

// already done for this date?
$stmt = $mysqli->prepare("SELECT `idtbl_dayend` FROM `tbl_dayend` WHERE `dayenddate` = ? FOR UPDATE");
$stmt->bind_param('s', $dayend);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    $mysqli->rollback();
    echo json_encode(array('ok' => false, 'msg' => "Day end for $dayend is already done"));
    exit;
}
$stmt->close();

// header row
$stmt = $mysqli->prepare("INSERT INTO `tbl_dayend` (`dayenddate`, `totalrows`, `totalqty`, `doneby`, `donedatetime`)
                          SELECT ?, COUNT(*), IFNULL(SUM(`qty`), 0), ?, NOW() FROM `tbl_stock`");
$stmt->bind_param('si', $dayend, $userID);
if (!$stmt->execute()) {
    $err = $stmt->errno;
    $stmt->close();
    $mysqli->rollback();
    echo json_encode(array('ok' => false, 'msg' => $err == 1062 ? "Day end for $dayend is already done" : 'Failed to create day end'));
    exit;
}
$dayendID = $mysqli->insert_id;
$stmt->close();

// snapshot of all stock rows
$stmt = $mysqli->prepare("INSERT INTO `tbl_stock_dayend`
        (`tbl_dayend_idtbl_dayend`, `dayenddate`, `idtbl_stock`, `qty`, `update`, `status`, `updatedatetime`,
         `tbl_user_idtbl_user`, `tbl_product_idtbl_product`, `tbl_location_idtbl_location`)
    SELECT ?, ?, `idtbl_stock`, `qty`, `update`, `status`, `updatedatetime`,
           `tbl_user_idtbl_user`, `tbl_product_idtbl_product`, `tbl_location_idtbl_location`
    FROM `tbl_stock`");
$stmt->bind_param('is', $dayendID, $dayend);
if (!$stmt->execute()) {
    $stmt->close();
    $mysqli->rollback();
    echo json_encode(array('ok' => false, 'msg' => 'Failed to copy stock details'));
    exit;
}
$copied = $stmt->affected_rows;
$stmt->close();

$mysqli->commit();
$mysqli->close();

echo json_encode(array('ok' => true, 'msg' => "Day end completed for $dayend ($copied stock rows saved)"));