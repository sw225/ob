<?php

if ( !isset( $Inst ) ) { 
	exit ;
}
if ( !$Inst ) { 
	exit ;
}

$res_queue_all_delete = 
	$Inst -> 
		DeleteAll(
			array( 
				'tbl' => $TableQueue
			)
		);

