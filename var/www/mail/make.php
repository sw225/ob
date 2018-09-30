<?php

require_once('Mail/mimeDecode.php');

$prm['include_bodies'] = true; 
$prm['decode_bodies']  = true; 
$prm['decode_headers'] = true; 
$prm['input'] = $header;
$prm['crlf'] = "\n";

$stt = Mail_mimeDecode::decode( $prm );

$addr = 
	array( 
		isset( $stt -> headers['from'] ) ? $stt -> headers['from'] : false, 
		isset( $stt -> headers['to'] )   ? $stt -> headers['to']   : false, 
		''
	);
if ( !$addr[1] ) { 
	$addr[1] = isset( $stt -> headers['delivered-to'] ) ? $stt -> headers['delivered-to'] : false ;
	if ( !$addr[1] ) { 
		exit(0);
	}
}

if ( is_array( $addr[1] ) ) { 
	$addr[1] = $addr[1][0];
}

if ( preg_match( "/(.*)<(.*?)>/", $addr[0], $ary ) ) { 
	$addr[0] = $ary[2];
	$addr[2] = $ary[1];
} 
$addr = 
	array_map(
		function( $_v ) { 
			$_v = trim( $_v, "\x22 \x27" );
			preg_match( "/<.*>/", $_v, $str );
			if ( !empty( $str[0] ) ) {
				$_v = substr( $str[0], 1, strlen( $str[0] ) - 2 );
			}
			$_v = stripcslashes( $_v );
			return $_v;
		}, 
		$addr
	);
if ( '' !== $addr[2] ) { 
	$addr[2] = mb_convert_encoding( $addr[2], 'UTF-8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS' );
}
#var_dump($addr);
list( $from, $to, $from_name ) = $addr;

$mail = 
	array( 
		isset( $stt -> headers['subject'] ) ? $stt -> headers['subject'] : '', 
		isset( $stt -> body )               ? $stt -> body               : false
	);
$mail = 
	array_map(
		function( $_v ) { 
			return mb_convert_encoding( $_v, 'UTF-8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS' );
		}, 
		$mail
	);
#var_dump($mail);
list( $title, $message ) = $mail;
