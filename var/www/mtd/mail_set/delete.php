<?php

if ( !isset( $Inst ) ) { 
	exit ;
}
if ( !$Inst ) { 
	exit ;
}

$res_register_mail_delete = 
	$Inst -> 
		DeleteOne(
			array( 
				'tbl' => $TableQueue,
				'whr' => sprintf( 'mail IN (%s)', implode( ',', $ids ) )
			)
		);
