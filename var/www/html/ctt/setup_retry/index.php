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
		require_once '../mtd/setup_retry/change.php';
		if ( $res_change_setup_retry ) { 
			$txt = '変更しました。';

		}

	}

}


$res_retry_list = 
	$Inst -> 
		ListAllRecode( 
			array( 
				'tbl' => $TableServer, 
				'clm' => '`id`,`domain`,`retry_count`,`retry_time`'
			)
		);

$tmpl = new Tmpl( '../tmp/setup_retry.html' );

$co = 0;
$tmpl -> loopset( 'loop_retry' );
foreach ( $res_retry_list as $ary ) { 
	$tmpl -> assign( 'retry_num',     ++$co );
	$tmpl -> assign( 'retry_id',      $ary['id'] );
	$tmpl -> assign( 'retry_domain',  $ary['domain'] );
	$tmpl -> assign( 'retry_count',   $ary['retry_count'] );
	$tmpl -> assign( 'retry_time',    $ary['retry_time'] );

	$tmpl -> loopnext( 'loop_retry' );
}
$tmpl -> loopend( 'loop_retry' );

$tmpl -> flush();
