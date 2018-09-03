<?php

/********************************
 * 受信 aliases
 * 色々取得して DB 追加
 *
*********************************/
#ini_set('display_errors', 'On');

$stdin = '';  
while ( ! feof( STDIN ) ) { 
	$stdin .= fgets( STDIN );
}

if ( empty( $stdin ) ) { 
	exit(0);
}
$header = $stdin;


$dir = '/var/www/';	#'../'
require_once $dir.'db/connect.php';
require_once $dir.'cls/db/index.php';
require_once $dir.'cls/db/wrap.php';


$Inst = new WARP( $CONNECT );

$res_ip_list = 
	$Inst -> 
		ListAllRecode( 
			array( 
				'tbl' => $TableIp, 
				'clm' => '`ip`',
				'whr' => "`ip` <> ''"
			)
		);
if ( 0 === count( $res_ip_list ) ) { 
	exit ;
}

$flg = false;
foreach ( $res_ip_list as $ary ) { 
	$ip = $ary['ip'];
	if ( preg_match( "/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $ip ) ) { 
		if ( preg_match( "/[\[|\D]+{$ip}[\]|\D]+/", $header ) ) { 
			$flg = true;
			break;
		}
	}
}

if ( !$flg ) { 
	exit ;
}



# アドチェン繋げた状態で こいつ ONにして header 確認してみたいな
$sss  = $Inst -> Quote( $header );
$chng = $Inst -> Sql( "INSERT INTO `relay`.`test` (`id`, `str`, `created`) VALUES (NULL, {$sss}, CURRENT_TIMESTAMP);" );




require_once('make.php');

require_once('regist.php');

