<?php

/********************************
 * 送信数 リセット
 * １日一回
 *
 * 20160301
 * 	　　ログ削除追加
 * 
*********************************/

$dir = '/var/www/';	#	'../';	#	

require_once $dir.'db/connect.php';
require_once $dir.'cls/db/index.php';
require_once $dir.'cls/db/wrap.php';

$Inst = new WARP( $CONNECT );
$qry = $Inst -> Sql( "UPDATE {$TableAccount} SET `send_length` = 0;" );
if ( $qry ) { 
	$qry -> closeCursor();
}

$befw = date('Y-m-d',strtotime('-1 week'));
$befm = date('Y-m-d',strtotime('-1 month'));
$sql = <<<SQL
DELETE FROM {$TableOk} WHERE `day` < '{$befm}';
DELETE FROM {$TableNg} WHERE `day` < '{$befm}';
DELETE FROM {$TableSent} WHERE `created` < '{$befw}';
SQL;
$qry = $Inst -> Sql( $sql );
if ( $qry ) { 
	$qry -> closeCursor();
}

exit ;
