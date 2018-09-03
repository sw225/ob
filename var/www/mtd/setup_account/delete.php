<?php

if ( !isset( $Inst ) ) { 
	exit ;
}
if ( !$Inst ) { 
	exit ;
}


$updt = array();
foreach ( $ids as $id ) {
	$updt[ "domain{$id}" ]      = '';
	$updt[ "address{$id}" ]     = '';
	$updt[ "user{$id}" ]        = '';
	$updt[ "pass{$id}" ]        = '';
	$updt[ "from_name{$id}" ]   = '';
	$updt[ "send_length{$id}" ] = 0;
	$updt[ "error_count{$id}" ] = 0;
	$updt[ "error_txt{$id}" ]   = '';
	$updt[ "error_time{$id}" ]  = '0000-00-00 00:00:00';
	$updt[ "memo{$id}" ]        = '';
	$updt[ "activate{$id}" ]    = 0;
	$updt[ "created{$id}" ]     = '0000-00-00 00:00:00';
}


$clm = array( 
	'domain'      => 'VARCHAR(256)',
	'address'     => 'VARCHAR(256)',
	'user'        => 'VARCHAR(256)',
	'pass'        => 'VARCHAR(256)', 
	'from_name'   => 'TEXT', 
	'send_length' => 'INT(11)', 
	'error_count' => 'INT(11)', 
	'error_txt'   => 'TEXT', 
	'error_time'  => 'DATETIME', 
	'memo'        => 'TEXT', 
	'activate'    => 'INT(1)', 
	'created'     => 'DATETIME'
);

$res_delete_setup_account = 
	$Inst -> 
		TempUpdate(
			array( 
				'tbl' => $TableAccount, 
				'clm' => $clm, 
				'dat' => $updt, 
				'ids' => $ids
			)
		);

