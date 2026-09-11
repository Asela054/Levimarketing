<?php 

session_start(); 
$locationId = $_SESSION['location_id']; 

$table = 'tbl_stock'; 
$primaryKey = 'idtbl_stock'; 

$columns = array( 
	array( 'db' => '`u`.`idtbl_stock`',   'dt' => 'idtbl_stock',   'field' => 'idtbl_stock' ), 
	array( 'db' => '`u`.`qty`',           'dt' => 'qty',           'field' => 'qty' ), 
	array( 'db' => '`u`.`update`',        'dt' => 'update',        'field' => 'update' ), 
	array( 'db' => '`u`.`status`',        'dt' => 'status',        'field' => 'status' ), 
	array( 'db' => '`ud`.`product_name`', 'dt' => 'product_name',  'field' => 'product_name' ), 
	array( 'db' => '`uc`.`location`',     'dt' => 'location',      'field' => 'location' ),
	array( 'db' => '`ud`.`unitprice`',    'dt' => 'unitprice',     'field' => 'unitprice' )
); 

// SQL server connection information 
require('config.php'); 
$sql_details = array( 
	'user' => $db_username, 
	'pass' => $db_password, 
	'db'   => $db_name, 
	'host' => $db_host 
); 

// Local mysqli connection just for safely escaping the keyword search below 
$escConn = new mysqli($db_host, $db_username, $db_password, $db_name); 

require('ssp.customized.class.php'); 

// u  = tbl_stock 
// ud = tbl_product   (product name / category / unitprice) 
// uc = tbl_location  (location name) 
$joinQuery = "FROM `tbl_stock` AS `u` 
    LEFT JOIN `tbl_product` AS `ud` ON (`ud`.`idtbl_product` = `u`.`tbl_product_idtbl_product`) 
    LEFT JOIN `tbl_location` AS `uc` ON (`uc`.`idtbl_location` = `u`.`tbl_location_idtbl_location`)"; 

// Base clause - all products/stock rows show by default 
$extraWhere = "`u`.`status` IN (0,1)"; 

// Category filter 
if (!empty($_POST['search_category'])) { 
    $search_category = intval($_POST['search_category']); 
    $extraWhere .= " AND `ud`.`tbl_product_category_idtbl_product_category` = " . $search_category; 
} 

// Product filter 
if (!empty($_POST['search_product'])) { 
    $search_product = intval($_POST['search_product']); 
    $extraWhere .= " AND `u`.`tbl_product_idtbl_product` = " . $search_product; 
} 

// Keyword search - match product name anywhere (LIKE %keyword%) 
if (!empty($_POST['search_keyword'])) { 
    $keyword = trim($_POST['search_keyword']); 
    if ($keyword !== '' && !$escConn->connect_error) { 
        $keyword_esc = $escConn->real_escape_string($keyword); 
        $extraWhere .= " AND `ud`.`product_name` LIKE '%" . $keyword_esc . "%'"; 
    } 
} 

// Location filter - restrict every query to the logged-in user's location 
if (!empty($locationId)) { 
    $extraWhere .= " AND `u`.`tbl_location_idtbl_location` = " . intval($locationId); 
} 

echo json_encode( 
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere ) 
); 

$escConn->close();