<?php

$flg = 0;
if ( !isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) || !( strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) === 'xmlhttprequest' ) ) {  
	$flg = 1;
} 
if ( !$flg && 3 !== count( $_POST ) ) { 
	$flg = 1;
}
if ( !$flg && ( !isset( $_POST['yyyy'] ) || !isset( $_POST['mm'] ) || !isset( $_POST['dd'] ) ) ) { 
	$flg = 1;
}
if ( !$flg && !checkdate( $_POST['mm'], $_POST['dd'], $_POST['yyyy'] ) ) { 
	$flg = 1;
}
if ( $flg ) { 
	$ary  = array_fill( 0, 24, 0 );
	$zero = implode( $ary, ',' );
	die("{'dat':'Error','sends':[{$zero}],'errors':[{$zero}]}");
}




require_once '../../../db/connect.php';

require_once '../../../cls/db/index.php';
require_once '../../../cls/db/wrap.php';

$Inst = new WARP( $CONNECT );

require_once '../../../ctt/session.php';


$data  = sprintf( '%d-%02d-%02d', $_POST['yyyy'], $_POST['mm'], $_POST['dd'] );
$range = range( 0, 23 );


// ===== Query 生成 ===== //
$clm_ok = array_map(
	function( $_v, $_tbl ) { 
		return sprintf( '%s.`%d` AS ok%d', $_tbl, $_v, $_v );
	}, 
	$range, 
	array_fill( 0, 24, $TableOk )
);
$clm_ok = implode( $clm_ok, ', ' );

$clm_ng = array_map(
	function( $_v, $_tbl ) { 
		return sprintf( '%s.`%d` AS ng%d', $_tbl, $_v, $_v );
	}, 
	$range, 
	array_fill( 0, 24, $TableNg )
);
$clm_ng = implode( $clm_ng, ', ' );

$qry = <<<SQL
SELECT 
	{$clm_ok}, 
	{$clm_ng}
FROM 
	log_ok 
	INNER JOIN log_ng 
ON 
	log_ok.day = log_ng.day
WHERE 
	log_ok.day = '{$data}'
SQL;
#echo $qry;


// ===== Sql result ===== //
$oks = array_fill( 0, 24, 0 );
$ngs = array_fill( 0, 24, 0 );
$res_ok_list = $Inst -> SelectAll( $qry );
if ( $res_ok_list ) {
	$ary = array_chunk( $res_ok_list[0], 24 );
	$oks = $ary[0];
	$ngs = $ary[1];
} 
#var_dump($oks);
#var_dump($ngs);


// ===== Ajax result ===== //
die( sprintf( "{'dat':'%s','sends':[%s],'errors':[%s]}", $data, implode( $oks, ',' ), implode( $ngs, ',' ) ) );
