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
	die("{'limit':0,'regist':0,'active':0,'error':0,'day':0,'month':0,'domainn':[],'domainc':[]}");
}


require_once '../../../db/connect.php';

require_once '../../../cls/db/index.php';
require_once '../../../cls/db/wrap.php';

require_once '../../../ctt/config.php';

$Inst = new WARP( $CONNECT );

require_once '../../../ctt/session.php';


$start = date("Y-m-01");
$end   = date( "Y-m-t", strtotime( $start ) );

$ary = range( 0, 23 );

$clm_ok = array_map(
	function( $_v, $_tbl ) { 
		return sprintf( '%s.`%d`', $_tbl, $_v, $_v );
	}, 
	$ary, 
	array_fill( 0, 24, $TableOk )
);
$clm_ok = implode( $clm_ok, '+' );

$qry = <<<SQL
SELECT 
	domain, 
	COUNT( address <> '' OR NULL ) AS count_domain, 
	SUM( send_length ) AS count_send, 
	COUNT( activate > 0 OR NULL ) AS count_activate, 
	COUNT( error_count > 0 OR NULL ) AS count_error, 
	( 
		SELECT 
			SUM( {$clm_ok} ) AS ok
		FROM 
			log_ok 
		WHERE 
			day BETWEEN '{$start}' AND '{$end}' 
	) AS total_month
FROM 
	account 
GROUP BY 
	domain
SQL;
$res_list = $Inst -> SelectAll( $qry );


$length_regist   = 0;
$length_activate = 0;
$length_error    = 0;
$total_day       = 0;
$domain_name     = array();
$domain_count    = array();

if ( count( $res_list ) ) { 
	// --- 成形 ＆ 計算 --- //
	foreach ( $res_list as $ary ) { 
		$length_regist    += $ary['count_domain'];
		$length_activate  += $ary['count_activate'];
		$length_error     += $ary['count_error'];
		$total_day        += $ary['count_send'];
		if ( !isset( $total_month ) ) { 
			$total_month  = $ary['total_month'];
		}
		$domain_name[]  = $ary['domain'];
		$domain_count[] = $ary['count_domain'];
	}
} 


// ===== Ajax result ===== //
die( 
	sprintf( 
		"{'limit':%s,'regist':%s,'active':%s,'error':%s,'day':%s,'month':%s,'domainn':[%s],'domainc':[%s]}", 
		$AddressLimit, 
		$length_regist, 
		$length_activate, 
		$length_error, 
		$total_day, 
		isset( $total_month )  ? $total_month : 0,  
		count( $domain_name )  ? sprintf( "'%s'", implode( $domain_name, "','" ) ) : '', 
		count( $domain_count ) ? implode( $domain_count, "," ) : ''
	)
);
