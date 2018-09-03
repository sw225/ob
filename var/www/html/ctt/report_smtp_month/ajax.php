<?php

$flg = 0;
if ( !isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) || !( strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) === 'xmlhttprequest' ) ) {  
	$flg = 1;
} 
if ( !$flg && 2 !== count( $_POST ) ) { 
	$flg = 1;
}
if ( !$flg && ( !isset( $_POST['yyyy'] ) || !isset( $_POST['mm'] ) ) ) { 
	$flg = 1;
}
if ( !$flg && !checkdate( $_POST['mm'], '01', $_POST['yyyy'] ) ) { 
	$flg = 1;
}
if ( $flg ) { 
	$dt = strtotime( sprintf( '%s-%s-01', $_POST['yyyy'], $_POST['mm'] ) );
	$days = date( 't', $dt );
	$ary  = array_fill( 0, $days, 0 );
	$zero = implode( $ary, ',' );
	die("{'dat':'Error','lng':'0','sends':[{$zero}],'errors':[{$zero}]}");
}




require_once '../../../db/connect.php';

require_once '../../../cls/db/index.php';
require_once '../../../cls/db/wrap.php';

$Inst = new WARP( $CONNECT );

require_once '../../../ctt/session.php';


$ym = sprintf( '%s-%s', $_POST['yyyy'], $_POST['mm'] );

$start = date("{$ym}-01");
$today = date( "{$ym}-t", strtotime( "{$ym}-01" ) );
$end   = date( "{$ym}-t", strtotime( "{$ym}-01" ) );

$ary = range( 0, 23 );

$clm_ok = array_map(
	function( $_v, $_tbl ) { 
		return sprintf( '%s.`%d`', $_tbl, $_v, $_v );
	}, 
	$ary, 
	array_fill( 0, 24, $TableOk )
);
$clm_ok = implode( $clm_ok, '+' );

$clm_ng = array_map(
	function( $_v, $_tbl ) { 
		return sprintf( '%s.`%d`', $_tbl, $_v, $_v );
	}, 
	$ary, 
	array_fill( 0, 24, $TableNg )
);
$clm_ng = implode( $clm_ng, '+' );


$qry = <<<SQL
SELECT 
	DISTINCT log_ok.day, 
	( {$clm_ok} ) AS ok, 
	( {$clm_ng} ) AS ng
FROM 
	log_ok 
	INNER JOIN log_ng 
ON 
	log_ok.day = log_ng.day
WHERE 
	log_ok.day BETWEEN '{$start}' AND '{$today}' 

SQL;

#echo $qry;

$mk = array();
$co = 0;

// ===== Sql result ===== //
$res_ok_list = $Inst -> SelectAll( $qry );
if ( $res_ok_list ) { 
	foreach ( $res_ok_list as $ary ) { 
		$dat = $ary['day'];
		$mk[ $dat ] = array();
		$mk[ $dat ]['ok'] = $ary['ok'];
		$mk[ $dat ]['ng'] = $ary['ng'];
	}
}

// ===== 足りないとこ 0 埋め ===== //
$start = strtotime( $start );
$end   = strtotime( $end );
while ( $start <= $end ) { 
	$ymd = date( 'Y-m-d', $start );
	if ( !isset( $mk[ $ymd ] ) ) { 
		$mk[ $ymd ] = array();
		$mk[ $ymd ]['ok'] = '0';
		$mk[ $ymd ]['ng'] = '0';
	}
	$start = strtotime( '+1 day', $start );
	++$co;
}
ksort( $mk );

// ===== Ajax result ===== //
$oks  = array();
$ngs  = array();
foreach ( $mk as $k => $ary ) { 
	$oks[] = $ary['ok'];
	$ngs[] = $ary['ng'];
}
#var_dump($mk);

// ===== Ajax result ===== //
die( sprintf( "{'dat':'%s','lng':'%d','sends':[%s],'errors':[%s]}", $ym, $co, implode( $oks, ',' ), implode( $ngs, ',' ) ) );
