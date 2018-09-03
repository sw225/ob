<?php

if ( !isset( $Inst ) ) { 
	exit ;
}
if ( !$Inst ) { 
	exit ;
}

$clm = array(
	'address'   => 'VARCHAR(256)',
	'user'      => 'VARCHAR(256)',
	'pass'      => 'VARCHAR(256)',
	'from_name' => 'TEXT',
	'memo'      => 'TEXT'
);
if ( isset( $cng ) ) { 
	$clm['error_count'] = 'INT(11)';
	$clm['error_txt']   = 'TEXT';
	$clm['error_time']  = 'DATETIME';
	$clm['domain']      = 'VARCHAR(256)';
	$clm['created']     = 'DATETIME';
}

$res_change_setup_account = 
	$Inst -> 
		TempUpdate(
			array( 
				'tbl' => $TableAccount, 
				'clm' => $clm, 
				'dat' => $_POST, 
				'ids' => $ids
			)
		);

