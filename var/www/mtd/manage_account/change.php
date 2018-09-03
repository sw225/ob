<?php

if ( !isset( $Inst ) ) { 
	exit ;
}
if ( !$Inst ) { 
	exit ;
}

$res_change_manage_account = 
	$Inst -> 
		TempUpdate(
			array( 
				'tbl' => $TableAccount, 
				'clm' => array(
					'memo'     => 'TEXT',
					'activate' => 'INT(1)'
				), 
				'dat' => $_POST, 
				'ids' => $ids
			)
		);



