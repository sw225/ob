<?php

require_once '../ctt/session.php';


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
	COUNT( DISTINCT id ) AS count_domain, 
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


$tmpl = new Tmpl( '../tmp/info.html' );

$tmpl -> assign( 'max',  $AddressLimit );


if ( count( $res_list ) ) { 

	$length_regist   = 0;
	$length_activate = 0;
	$length_error    = 0;
	$total_day       = 0;

	$tmpl -> loopset( 'loop_domain' );
	foreach ( $res_list as $ary ) { 
		$length_regist    += $ary['count_domain'];
		$length_activate  += $ary['count_activate'];
		$length_error     += $ary['count_error'];
		$total_day        += $ary['count_send'];
		if ( !isset( $total_month ) ) { 
			$total_month  = $ary['total_month'];
		}

		$tmpl -> assign( 'domain',        $ary['domain'] );
		$tmpl -> assign( 'total_domain',  $ary['count_domain'] );

		$tmpl -> loopnext( 'loop_domain' );
	}
	$tmpl -> loopend( 'loop_domain' );

	$tmpl -> assign( 'length_regist',    $length_regist );
	$tmpl -> assign( 'length_activate',  $length_activate );
	$tmpl -> assign( 'length_error',     $length_error );
	$tmpl -> assign( 'total_day',        $total_day );
	$tmpl -> assign( 'total_month',      $total_month );

}

$tmpl -> flush();
