<?php
session_start();
if(!isset($_SESSION['userid'])){
    echo json_encode(array('draw'=>0,'recordsTotal'=>0,'recordsFiltered'=>0,'data'=>array()));
    exit;
}
require_once('../connection/db.php');

$draw=isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start=isset($_POST['start']) ? intval($_POST['start']) : 0;
$length=isset($_POST['length']) ? intval($_POST['length']) : 25;
if($length<1 || $length>500){ $length=25; }
if($start<0){ $start=0; }

$search=isset($_POST['search']['value']) ? $conn->real_escape_string($_POST['search']['value']) : '';

// DataTable column index => DB column (must match the column order in supplierpayment.php)
$columns=array(
    'p.idtbl_supplier_payment', // 0 #
    'p.date',                   // 1 Date
    'p.idtbl_supplier_payment', // 2 Payment No
    's.suppliername',           // 3 Supplier
    'p.paymethod',              // 4 Method
    'p.chequeno',               // 5 Reference
    'p.totalpayment',           // 6 Total
    'p.status',                 // 7 Status
    'p.idtbl_supplier_payment'  // 8 Actions
);
$orderCol=isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir=(isset($_POST['order'][0]['dir']) && $_POST['order'][0]['dir']==='asc') ? 'ASC' : 'DESC';
$orderBy=isset($columns[$orderCol]) ? $columns[$orderCol] : 'p.idtbl_supplier_payment';

$from="FROM `tbl_supplier_payment` p JOIN `tbl_supplier` s ON s.`idtbl_supplier`=p.`tbl_supplier_idtbl_supplier`";

$where='';
if($search!==''){
    $where="WHERE (p.`idtbl_supplier_payment` LIKE '%$search%' OR p.`date` LIKE '%$search%' OR s.`suppliername` LIKE '%$search%' OR p.`chequeno` LIKE '%$search%' OR p.`bankref` LIKE '%$search%' OR p.`totalpayment` LIKE '%$search%')";
}

$rowTotal=$conn->query("SELECT COUNT(*) AS cnt FROM `tbl_supplier_payment`")->fetch_assoc();
$rowFiltered=$conn->query("SELECT COUNT(*) AS cnt $from $where")->fetch_assoc();

$sql="SELECT p.`idtbl_supplier_payment`, p.`date`, p.`paymethod`, p.`chequeno`, p.`chequedate`, p.`bankref`, p.`totalpayment`, p.`status`, p.`confirm_status`, s.`suppliername`
      $from $where ORDER BY $orderBy $orderDir LIMIT $start, $length";
$result=$conn->query($sql);

$data=array();
while($row=$result->fetch_assoc()){
    $data[]=$row;
}

echo json_encode(array(
    'draw'=>$draw,
    'recordsTotal'=>intval($rowTotal['cnt']),
    'recordsFiltered'=>intval($rowFiltered['cnt']),
    'data'=>$data
));