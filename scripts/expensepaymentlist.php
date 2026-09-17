<?php

// DB table to use
$table = 'tbl_expensepayment';

// Table's primary key
$primaryKey = 'idtbl_expensepayment';

// Array of database columns which should be read and sent back to DataTables.
$columns = array(
	array( 'db' => '`u`.`idtbl_expensepayment`', 'dt' => 'idtbl_expensepayment', 'field' => 'idtbl_expensepayment' ),
	array( 'db' => '`u`.`refno`',                'dt' => 'refno',                'field' => 'refno' ),
	array( 'db' => '`u`.`amount`',               'dt' => 'amount',               'field' => 'amount' ),
	array( 'db' => '`u`.`paymentdate`',          'dt' => 'paymentdate',          'field' => 'paymentdate' ),
	array( 'db' => '`u`.`paymentmethod`',        'dt' => 'paymentmethod',        'field' => 'paymentmethod' ),
	array( 'db' => '`u`.`card_last4`',           'dt' => 'card_last4',           'field' => 'card_last4' ),
	array( 'db' => '`u`.`cheque_no`',            'dt' => 'cheque_no',            'field' => 'cheque_no' ),
	array( 'db' => '`u`.`cheque_bank_name`',     'dt' => 'cheque_bank_name',     'field' => 'cheque_bank_name' ),
	array( 'db' => '`u`.`cheque_branch`',        'dt' => 'cheque_branch',        'field' => 'cheque_branch' ),
	array( 'db' => '`u`.`cheque_date`',          'dt' => 'cheque_date',          'field' => 'cheque_date' ),
	array( 'db' => '`u`.`cheque_status`',        'dt' => 'cheque_status',        'field' => 'cheque_status' ),
	array( 'db' => '`u`.`remarks`',              'dt' => 'remarks',              'field' => 'remarks' ),
	array( 'db' => '`u`.`status`',               'dt' => 'status',               'field' => 'status' ),
	array( 'db' => '`et`.`expencestype`',        'dt' => 'expencestype',         'field' => 'expencestype' )
);

// SQL server connection information
require('config.php');
$sql_details = array(
	'user' => $db_username,
	'pass' => $db_password,
	'db'   => $db_name,
	'host' => $db_host
);

require('ssp.customized.class.php' );

$joinQuery = "FROM `tbl_expensepayment` AS `u` LEFT JOIN `tbl_expences_type` AS `et` ON (`et`.`idtbl_expences_type` = `u`.`tbl_expences_type_idtbl_expences_type`)";

$extraWhere = "`u`.`status` IN (1,2)";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);