<?php 

require_once '../ctt/session.php';

if ( count( $_POST ) ) { 

	$ids = $_POST['ids'];
	unset( $_POST['ids'] );

	if ( count( $_POST ) ) { 
		require_once '../mtd/setup_ip/change.php';
		if ( $res_change_setup_ip ) { 
			$txt = '変更しました。';
		}
	}

}

$res_ip_list = 
	$Inst -> 
		ListAllRecode( 
			array( 
				'tbl' => $TableIp, 
				'clm' => '`id`,`ip`,`memo`'
			)
		);

$tmpl = new Tmpl( '../tmp/setup_smtp_ip.html' );

$co = 0;
$tmpl -> loopset( 'loop_ip' );
foreach ( $res_ip_list as $ary ) { 
	$tmpl -> assign( 'ip_id',    $ary['id'] );
	$tmpl -> assign( 'ip_num',   ++$co );
	$tmpl -> assign( 'ip_ip',    $ary['ip'] );
	$tmpl -> assign( 'ip_memo',  htmlentities( $ary['memo'], ENT_QUOTES, 'UTF-8' ) );

	$tmpl -> loopnext( 'loop_ip' );
}
$tmpl -> loopend( 'loop_ip' );

$tmpl -> flush();
