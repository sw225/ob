<?php

if ( !isset( $Inst ) ) { 
	exit ;
}
if ( !$Inst ) { 
	exit ;
}

$res_queue_domain_delete = 
	$Inst -> 
		DeleteOne(
			array( 
				'tbl' => $TableQueue, 
				'whr' => sprintf( 'id IN (%s)', implode( ',', $ids ) )
			)
		);

