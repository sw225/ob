<?php

if ( !isset( $Inst ) ) { 
	exit ;
}
if ( !$Inst ) { 
	exit ;
}

$res_change_setup_interval = 
	$Inst -> 
		UpdateOne(
			array( 
				'tbl' => $TableInterval, 
				'dat' => $_POST, 
				'whr' => 'id = 1'
			)
		);



