<?php

require_once '../ctt/session.php';

$lmt = '';

if ( count( $_POST ) ) { 
	if ( !empty( $_POST['message'] ) ) { 

		require_once '../mtd/contact/register.php';
		if ( $res_contact_regist ) { 
			$txt = '送信しました。';
		}

	}
}


$res_contact_list = 
	$Inst -> 
		ListAllRecode( 
			array( 
				'tbl' => $TableContact, 
				'clm' => '`id`,`message`,`type`,`flag`,`created`', 
				'lmt' => $lmt
			)
		);

$tmpl = new Tmpl( '../tmp/contact.html' );

if ( count( $res_contact_list ) ) { 
	$tmpl -> assign_def( 'contact_list' );

	$tmpl -> loopset( 'loop_contact' );
	foreach ( $res_contact_list as $ary ) { 
		$tmpl -> assign( 'contact_class',    $ary['type'] ? 'a'   : 'q'   );
		$tmpl -> assign( 'contact_icon',     $ary['type'] ? 'hdr' : 'mig' );
		$tmpl -> assign( 'contact_time',     $ary['created'] );
		$tmpl -> assign( 'contact_message',  $ary['message'] );

		$tmpl -> loopnext( 'loop_contact' );
	}
	$tmpl -> loopend( 'loop_contact' );

}

$tmpl -> flush();
