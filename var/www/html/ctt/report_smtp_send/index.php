<?php

require_once '../ctt/session.php';

$num  = 0;
$page = '1';
$ymd1 = date('Y-m-d');
$ymd2 = date('Y-m-d');
$whr  = sprintf( "`created` BETWEEN '%s 00:00:00' AND '%s 23:59:59'", $ymd1, $ymd2 );
$lmt  = sprintf( "0,%s", $ListLimit );
$from = '';
$to   = '';
$err  = '';
if ( 1 < count( $_GET ) ) { 
	extract( $_GET );

	if ( !empty( $report_smtp_send ) || !empty( $date ) ) { 
		if ( $report_smtp_send === date( 'Y-m-d', strtotime( $report_smtp_send ) ) && 
			$date === date( 'Y-m-d', strtotime( $date ) ) ) { 

			$ymd1 = $report_smtp_send;
			$ymd2 = $date;
			$whr  = sprintf( "`created` BETWEEN '%s 00:00:00' AND '%s 23:59:59'", $ymd1, $ymd2 );
		} 
		else { 
			if ( $report_smtp_send === date( 'Y-m-d', strtotime( $report_smtp_send ) ) ) { 
				$ymd1 = $report_smtp_send;
				$whr  = sprintf( "`created` BETWEEN '%s 00:00:00' AND '%s 23:59:59'", $ymd1, $ymd2 );
			} else 
			if ( $date === date( 'Y-m-d', strtotime( $date ) ) ) { 
				$ymd1 = '';
				$ymd2 = $date;
				$whr  = sprintf( "`created` BETWEEN '1970/01/01 00:00:00' AND '%s 23:59:59'", $ymd2 );
			}
		}
	}

	if ( is_numeric( $err ) || 'unknown' === $err ) { 
		if ( '0' !== $err && 'unknown' !== $err ) { 
			$whr  = sprintf( "%s%serror LIKE '%%%s%%'", $whr, ( empty( $whr ) ? '' : ' AND ' ), $err );
		} else 
		if ( '0' === $err ) { 
			$whr  = sprintf( "%s%serror = '%s'", $whr, ( empty( $whr ) ? '' : ' AND ' ), $err );
		} else 
		if ( 'unknown' === $err ) { 
			$whr  = sprintf( "%s%serror <> '0' AND NOT error REGEXP 'code ?:? ?-?[0-9]+'", $whr, ( empty( $whr ) ? '' : ' AND ' ), $err );
		}
	}
 
	if ( !empty( $from ) ) { 
		$whr  = sprintf( "%s%s`from` = '%s'", $whr, ( empty( $whr ) ? '' : ' AND ' ), $from );
	}
	if ( !empty( $to ) ) { 
		$whr  = sprintf( "%s%s`to` = '%s'", $whr, ( empty( $whr ) ? '' : ' AND ' ), $to );
	}

	if ( 1 < (int)$page ) { 
		$stt = ( (int)$page - 1 ) * $ListLimit;
		$num = $stt;
		$lmt = sprintf( "%s,%s", $stt, $ListLimit );
	}

} 


$res_error_list = 
	$Inst -> 
		ListAllRecode( 
			array( 
				'tbl' => $TableSent, 
				'clm' => '`error`'
			)
		);
$res_sent_list = 
	$Inst -> 
		ListAllRecode( 
			array( 
				'tbl' => $TableSent, 
				'clm' => '`from`,`to`,`title`,`message`,`error`,`created`', 
				'whr' => $whr, 
				'lmt' => $lmt
			)
		);
$res_sent_count = 
	$Inst -> 
		CountRecord( 
			array( 
				'tbl' => $TableSent, 
				'whr' => $whr
			)
		);

$tmpl = new Tmpl( '../tmp/report_smtp_send.html' );

if ( count( $res_error_list ) ) { 

	$error = array();
	$error = 
		array_map( 
			function( $_ary ) { 
				$err =  $_ary['error'];
				$res = false;
				if ( !empty( $err ) ) { 
					preg_match( '/.*code ?:? ?(-?\d+).*/i', $err, $mac );
					$res = ( count( $mac ) ) ? $mac[1] : 'unknown';
				}
				return $res;
			}, 
			$res_error_list 
		);

	$error = array_filter( $error );
	array_unshift( $error, '', '0' );
	if ( count( $error ) ) { 
		$error = array_unique( $error );

		$tmpl -> assign_def( 'error_list' );

		$tmpl -> loopset( 'loop_error' );
		foreach ( $error as $v ) { 
			$val = $v;
			$txt = $v;
			$sel = '';
			if ( '' === $v || '0' === $v ) { 
				$txt = ( '' === $v ) ? '指定なし' : '正常' ;
			}
			$tmpl -> assign( 'error_val',   $val );
			$tmpl -> assign( 'error_txt',   $txt );
			$tmpl -> assign( 'selected',    ( $err === $v ) ? 'selected' : '' );

			$tmpl -> loopnext( 'loop_error' );
		}
		$tmpl -> loopend( 'loop_error' );
	}
}

$tmpl -> assign( 'date1',  $ymd1 );
$tmpl -> assign( 'date2',  $ymd2 );
$tmpl -> assign( 'from',   $from );
$tmpl -> assign( 'to',     $to );

if ( count( $res_sent_list ) ) { 
	$tmpl -> assign_def( 'send_list' );

	$tmpl -> loopset( 'loop_sent' );
	foreach ( $res_sent_list as $ary ) { 
		$tmpl -> assign( 'sent_num',    ++$num );
		$tmpl -> assign( 'sent_from',   $ary['from'] );
		$tmpl -> assign( 'sent_to',     $ary['to'] );
		$tmpl -> assign( 'sent_title',  $ary['title'] );
		$tmpl -> assign( 'sent_message',  preg_replace( '/\r\n|\r|\n/', '<br />', $ary['message'] ) );
		$err = '';
		if ( !empty( $ary['error'] ) ) { 
			preg_match( '/.*code ?:? ?(-?\d+).*/i', $ary['error'], $mac );
			$err = ( count( $mac ) ) ? $mac[1] : 'unknown';
		}
		$tmpl -> assign( 'sent_error',  $err );
		$tmpl -> assign( 'sent_time',   $ary['created'] );

		$tmpl -> loopnext( 'loop_sent' );
	}
	$tmpl -> loopend( 'loop_sent' );

	$qry = '';
	if ( $ListLimit < $res_sent_count ) { 
		$qry = preg_replace( '/&page=\d+/', '', $_SERVER['QUERY_STRING'] );
		$qry = sprintf( '?%s&', $qry );
		$tmpl -> assign( 'paging',   paging( ceil( $res_sent_count / $ListLimit ), $page, 3, $qry ) );
	} 
	else { 
		$tmpl -> assign( 'paging',   '' );
	}
}

$tmpl -> flush();
