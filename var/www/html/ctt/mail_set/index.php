<?php

require_once '../ctt/session.php';

if ( count( $_POST ) ) { 
	$txt = '失敗しました。';

	if ( isset( $_POST['address'] ) && !empty( $_POST['address'] ) ) { 
		$_POST['address'] = explode( PHP_EOL, $_POST['address'] );

		if ( isset( $_POST['title'] ) && !empty( $_POST['title'] ) ) { 
			if ( isset( $_POST['message'] ) && !empty( $_POST['message'] ) ) { 
				if ( isset( $_POST['mail'] ) && !empty( $_POST['mail'] ) ) { 
					require_once '../mtd/mail_set/register.php';
					if ( $res_register_mail_set ) { 
						$txt = 'セットしました。';
					}
				}
			}
		}
	} else 
	if ( isset( $_POST['chk'] ) && count( $_POST['chk'] ) ) { 
		$ids = $_POST['chk'];
		unset( $_POST['chk'] );

		require_once '../mtd/mail_set/delete.php';
		if ( $res_register_mail_delete ) { 
			$txt = '削除しました。';
		}
	}

}


$res_mail_list = 
	$Inst -> 
		DistinctList( 
			array( 
				'tbl' => $TableQueue, 
				'clm' => 'mail, title, message, created', 
				'whr' => 'activate = 1 AND mail > 0'
			)
		);

# mail 重複削除　　ほんとは sql で やりたかったんだけどなー
$tmp = array();
$mail_list_dis = array();
foreach( $res_mail_list as $k => $ary ){
	if( !in_array( $ary['mail'], $tmp ) ) {
	 	$tmp[] = $ary['mail'];
	 	$mail_list_dis[] = $ary;
	}
}

$mails = 
	count( $res_mail_list ) ?
		array_map(
			function( $_ary ) { 
				return $_ary['mail'];
			}, 
			$res_mail_list
		) :
		array(0) ;


$tmpl = new Tmpl( '../tmp/mail_set.html' );

$tmpl -> assign( 'mail', max( $mails ) + 1 );

if ( $res_mail_list ) { 
	$tmpl -> assign_def( 'mail_list' );

	$tmpl -> loopset( 'loop_mail' );
	foreach ( $mail_list_dis as $ary ) { 
		$tmpl -> assign( 'mail_id',      $ary['mail'] );
		$tmpl -> assign( 'mail_title',   $ary['title'] );
		$tmpl -> assign( 'mail_message', preg_replace( '/\r\n|\r|\n/', '<br />', $ary['message'] ) );
		$tmpl -> assign( 'mail_created', $ary['created'] );

		$tmpl -> loopnext( 'loop_mail' );
	}
	$tmpl -> loopend( 'loop_mail' );

}


$tmpl -> flush();




