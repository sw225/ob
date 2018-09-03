<?php

require_once '../ctt/session.php';

if ( count( $_POST ) ) { 

	$ids = $_POST['chk'];
	unset( $_POST['chk'] );

	foreach ( $_POST as $k => $v ) { 
		if ( '' === $v || !is_numeric( $v ) ) { 
			unset( $_POST[ $k ] );
		}
	}

	if ( count( $_POST ) ) { 
		require_once '../mtd/setup_carrier/change.php';
		if ( $res_change_setup_carrier ) { 
			$txt = '変更しました。';

		}

	}

}



$res_carrier_list = 
	$Inst -> 
		ListAllRecode( 
			array( 
				'tbl' => $TableServer, 
				'clm' => '`id`,`domain`,`send_limit`'
			)
		);


$tmpl = new Tmpl( '../tmp/setup_carrier.html' );

$co = 0;
$tmpl -> loopset( 'loop_carrier' );
foreach ( $res_carrier_list as $ary ) { 
	$tmpl -> assign( 'carrier_num',         ++$co );
	$tmpl -> assign( 'carrier_id',          $ary['id'] );
	$tmpl -> assign( 'carrier_domain',      $ary['domain'] );
	$tmpl -> assign( 'carrier_send_limit',  $ary['send_limit'] );

	$tmpl -> loopnext( 'loop_carrier' );
}
$tmpl -> loopend( 'loop_carrier' );

$tmpl -> flush();
