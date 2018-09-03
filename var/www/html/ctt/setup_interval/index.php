<?php

require_once '../ctt/session.php';

if ( count( $_POST ) ) { 

	$txt = '失敗しました。';
	if ( is_numeric( $_POST['sleep'] ) ) { 
		require_once '../mtd/setup_interval/change.php';
		if ( $res_change_setup_interval ) { 
			$txt = '変更しました。';
		}
	}

}

$res_interval_list = 
	$Inst -> 
		ListAllRecode( 
			array( 
				'tbl' => $TableInterval, 
				'clm' => 'sleep'
			)
		);
$sleep = 0;
if ( 0 < count( $res_interval_list ) ) { 
	$sleep = $res_interval_list[0]['sleep'];
}


$tmpl = new Tmpl( '../tmp/setup_interval.html' );

$tmpl -> assign( 'sleep', $sleep );

$tmpl -> flush();
