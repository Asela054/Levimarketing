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

$idtbl_stock=isset($_GET['idtbl_stock']) ? intval($_GET['idtbl_stock']) : 0;

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

$sqlGetStock="SELECT s.idtbl_stock, s.qty,
        s.tbl_product_idtbl_product, s.tbl_location_idtbl_location,
        p.product_name, l.location
    FROM tbl_stock s
    LEFT JOIN tbl_product p ON p.idtbl_product = s.tbl_product_idtbl_product
    LEFT JOIN tbl_location l ON l.idtbl_location = s.tbl_location_idtbl_location
    WHERE s.idtbl_stock='$idtbl_stock' AND s.tbl_location_idtbl_location='$locationID' AND s.status IN (1, 2)
    LIMIT 1";

$resultGetStock=$conn->query($sqlGetStock);

if(!$resultGetStock || $resultGetStock->num_rows==0){
    $actionObj=new stdClass();
    $actionObj->icon='fas fa-exclamation-triangle';
    $actionObj->title='';
    $actionObj->message='Stock record not found';
    $actionObj->url='';
    $actionObj->target='_blank';
    $actionObj->type='danger';
    echo json_encode($actionObj);
    exit;
}

$row=$resultGetStock->fetch_assoc();

$actionObj=new stdClass();
$actionObj->icon='';
$actionObj->title='';
$actionObj->message='';
$actionObj->url='';
$actionObj->target='_blank';
$actionObj->type='success';
$actionObj->data=array(
    'idtbl_stock'  => $row['idtbl_stock'],
    'qty'          => $row['qty'],
    'product_id'   => $row['tbl_product_idtbl_product'],
    'location_id'  => $row['tbl_location_idtbl_location'],
    'product_name' => $row['product_name'],
    'location'     => $row['location'],
);

echo json_encode($actionObj);