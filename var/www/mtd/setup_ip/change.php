<?php

if ( !isset( $Inst ) ) { 
	exit ;
}
if ( !$Inst ) { 
	exit ;
}

$res_change_setup_ip = 
	$Inst -> 
		TempUpdate(
			array( 
				'tbl' => $TableIp, 
				'clm' => array( 
					'ip'    => 'VARCHAR(15)', 
					'memo'  => 'TEXT'
				), 
				'dat' => $_POST, 
				'ids' => $ids
			)
		);



