<?php

if ( !isset( $Inst ) ) { 
	exit ;
}
if ( !$Inst ) { 
	exit ;
}

extract( $_POST );

$qry = array();
foreach ( $address as $v ) { 
	$v = str_replace( array( "\r\n", "\n", "\r" ), '', $v );
	if ( preg_match('/^[a-zA-Z0-9_\-]+([.][a-zA-Z0-9_\-]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]+$/', $v ) ) {
		$ones   = 
			array(
				/* id		*/ 'NULL', 
				/* to		*/ "'{$v}'", 
				/* fromname	*/ $Inst -> Quote( $from_name ), 
				/* title	*/ $Inst -> Quote( $title ), 
				/* message	*/ $Inst -> Quote( $message ), 
				/* activate	*/ '1', 
				/* error	*/ '0', 
				/* mail		*/ $mail, 
				/* created	*/ "'" .date('Y/m/d H:i:s'). "'"
			);
		$qry[]	= '( ' .implode( $ones, ', ' ). ' )';
	}
}


$res_register_mail_set = 
	count( $qry ) ? 
		$Inst -> 
			InsertIg(
				array(
					'tbl' => $TableQueue, 
					'dat' => implode( $qry, ', ' )
				)
			) :
		0 ;

