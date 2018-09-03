<?php

if ( !isset( $Inst ) ) { 
	exit ;
}
if ( !$Inst ) { 
	exit ;
}


$res_change_setup_carrier = 
	$Inst -> 
		TempUpdate(
			array( 
				'tbl' => $TableServer, 
				'clm' => array(
					'send_limit' => 'INT'
				), 
				'dat' => $_POST, 
				'ids' => $ids
			)
		);



