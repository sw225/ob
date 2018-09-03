<?php

if ( !isset( $Inst ) ) { 
	exit ;
}
if ( !$Inst ) { 
	exit ;
}

$_POST['type'] = 0;
$_POST['flag'] = 0;

$res_contact_regist = 
	$Inst -> 
		InsertOne( 
			array( 
				'tbl' => $TableContact, 
				'dat' => $_POST
			)
		);

