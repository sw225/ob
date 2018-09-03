<?php

require_once '../ctt/session.php';

$dt = time();
if ( count( $_GET ) ) { 
	$dt = strtotime( $_GET['report_smtp_month'] . '-01' );
}
$days = date( 't', $dt );

// ===== TH ===== //
$range = range( 1, $days );
array_unshift( $range, '-' );
$range[] = '合計';

// ===== OK + 合計 ===== //
$ok  = array_fill( 0, $days + 1, 0 );
array_unshift( $ok, 'ＯＫ' );

// ===== NG + 合計 ===== //
$ng  = array_fill( 0, $days + 1, 0 );
array_unshift( $ng, 'ＮＧ' );

// ===== 比率 + 合計 ===== //
$pct = array_fill( 0, $days + 1, 0 );
array_unshift( $pct, '比率' );



$tmpl = new Tmpl( '../tmp/report_smtp_month.html' );

$tmpl -> loopset( 'loop_th' );
foreach ( $range as $v ) { 
	$tmpl -> assign( 'th', $v );

	$tmpl -> loopnext( 'loop_th' );
}
$tmpl -> loopend( 'loop_th' );


$tmpl -> loopset( 'loop_ok' );
foreach ( $ok as $v ) { 
	$tmpl -> assign( 'ok', $v );

	$tmpl -> loopnext( 'loop_ok' );
}
$tmpl -> loopend( 'loop_ok' );


$tmpl -> loopset( 'loop_ng' );
foreach ( $ng as $v ) { 
	$tmpl -> assign( 'ng', $v );

	$tmpl -> loopnext( 'loop_ng' );
}
$tmpl -> loopend( 'loop_ng' );


$tmpl -> loopset( 'loop_pc' );
foreach ( $pct as $v ) { 
	$tmpl -> assign( 'pc', $v );

	$tmpl -> loopnext( 'loop_pc' );
}
$tmpl -> loopend( 'loop_pc' );


$tmpl -> flush();
