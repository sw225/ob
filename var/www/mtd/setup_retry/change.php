<?php

if ( !isset( $Inst ) ) { 
	exit ;
}
if ( !$Inst ) { 
	exit ;
}

$res_change_setup_retry = 
	$Inst -> 
		TempUpdate(
			array( 
				'tbl' => $TableServer, 
				'clm' => array(
					'retry_count' => 'INT', 
					'retry_time'  => 'INT'
				), 
				'dat' => $_POST, 
				'ids' => $ids
			)
		);



