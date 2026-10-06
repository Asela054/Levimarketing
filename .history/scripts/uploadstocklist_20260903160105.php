<?php

/*
 * DataTables server-side processing script for tbl_stock.
 * Adds an Action column (Edit / Delete buttons), disabled per the
 * logged-in user's privilege on the "manage stock" menu item (id 34),
 * same as the disabled-button pattern used on area.php.
 */

session_start();

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Privilege check
 * This script runs as a standalone AJAX endpoint, so it can't rely on
 * $menuprivilegearray being set by include/header.php on managestock.php's
 * own request — that only happens on that page's HTML render. header.php
 * never stores this in $_SESSION either; it rebuilds it from
 * tbl_user_privilege on every request. We do the same thing here.
 */

// Adjust this path if connection/db.php lives somewhere else relative to scripts/
require_once('../connection/db.php');

$menuprivilegearray = array();

if (isset($_SESSION['userid'])) {
    $userSessionID = $_SESSION['userid'];

    $sqlmenucheck = "SELECT `idtbl_menu_list`, `menu` FROM `tbl_menu_list` WHERE `status`=1";
    $resultmenucheck = $conn->query($sqlmenucheck);

    while ($rowmenucheck = $resultmenucheck->fetch_assoc()) {
        $menucheckID = $rowmenucheck['idtbl_menu_list'];

        $sqlprivilegecheck = "SELECT `add`, `edit`, `statuschange`, `remove`, `access_status`, `tbl_menu_list_idtbl_menu_list`
                               FROM `tbl_user_privilege`
                               WHERE `tbl_user_idtbl_user`='$userSessionID'
                               AND `tbl_menu_list_idtbl_menu_list`='$menucheckID'
                               AND `status`=1";
        $resultprivilegecheck = $conn->query($sqlprivilegecheck);
        $rowprivilegecheck = $resultprivilegecheck->fetch_assoc();

        $objmenu = new stdClass();
        $objmenu->add = $rowprivilegecheck['add'];
        $objmenu->edit = $rowprivilegecheck['edit'];
        $objmenu->statuschange = $rowprivilegecheck['statuschange'];
        $objmenu->remove = $rowprivilegecheck['remove'];
        $objmenu->access_status = $rowprivilegecheck['access_status'];
        $objmenu->menuid = $rowprivilegecheck['tbl_menu_list_idtbl_menu_list'];
        array_push($menuprivilegearray, $objmenu);
    }
}

if (!function_exists('checkprivilege')) {
    function checkprivilege($arraymenu, $menuID, $type) {
        foreach ($arraymenu as $array) {
            if ($array->menuid == $menuID) {
                if ($type == 1) { return $array->add; }
                else if ($type == 2) { return $array->edit; }
                else if ($type == 3) { return $array->statuschange; }
                else if ($type == 4) { return $array->remove; }
            }
        }
        return 0;
    }
}

// Menu id 34 = "manage stock" per tbl_menu_list
$editcheck   = checkprivilege($menuprivilegearray, 34, 2); // edit
$deletecheck = checkprivilege($menuprivilegearray, 34, 4); // remove

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'tbl_stock';

// Table's primary key
$primaryKey = 'idtbl_stock';

// Array of database columns which should be read and sent back to DataTables.
$columns = array(
    array( 'db' => '`u`.`idtbl_stock`', 'dt' => 'idtbl_stock', 'field' => 'idtbl_stock' ),
    array( 'db' => '`u`.`qty`', 'dt' => 'qty', 'field' => 'qty' ),
    array( 'db' => '`ud`.`product_name`', 'dt' => 'product_name', 'field' => 'product_name' ),
    array( 'db' => '`uc`.`location`', 'dt' => 'location', 'field' => 'location' ),
    array(
        'db' => '`u`.`updatedatetime`',
        'dt' => 'date',
        'field' => 'updatedatetime',
        'formatter' => function($d, $row) {
            return date('Y-m-d', strtotime($d));
        }
    ),
    array(
            'db' => '`u`.`idtbl_stock`',
            'dt' => 'action',
            'field' => 'idtbl_stock',
            'formatter' => function($d, $row) use ($editcheck, $deletecheck) {
                $editDisabled   = ($editcheck == 0)   ? ' disabled' : '';
                $deleteDisabled = ($deletecheck == 0) ? ' disabled' : '';
                return '<div class="btn-group" role="group">'
                    . '<button type="button" class="btn btn-sm btn-outline-primary mr-2 btn-edit-stock' . $editDisabled . '" data-id="' . intval($d) . '">'
                    . '<i class="fas fa-edit"></i></button>'
                    . '<button type="button" class="btn btn-sm btn-outline-danger mr-2 btn-delete-stock' . $deleteDisabled . '" data-id="' . intval($d) . '">'
                    . '<i class="fas fa-trash"></i></button>'
                    . '</div>';
            }
        ),
);

// SQL server connection information
require('config.php');
$sql_details = array(
	'user' => $db_username,
	'pass' => $db_password,
	'db'   => $db_name,
	'host' => $db_host
);

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require('ssp.customized.class.php');

// Location comes from the logged-in user's session, not a UI selector.
$locationID = isset($_SESSION['location_id']) ? intval($_SESSION['location_id']) : 0;

$joinQuery = "FROM (SELECT * FROM `tbl_stock` WHERE `tbl_location_idtbl_location` = '$locationID' AND `qty` > 0) AS `u`
LEFT JOIN `tbl_product` AS `ud` ON (`ud`.`idtbl_product` = `u`.`tbl_product_idtbl_product`)
LEFT JOIN `tbl_location` AS `uc` ON (`uc`.`idtbl_location` = `u`.`tbl_location_idtbl_location`)";

// status = 1 or 2 => visible/active. status = 3 => soft-deleted, excluded here.
$extraWhere = "`u`.`status` IN (1, 2)";
echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);