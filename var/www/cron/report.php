<?php

/********************************
 * ログ記録 cron
 * １時間に一回   n時05分
 *
*********************************/
#ini_set('display_errors', 'On');

$proc = array();
if ( !exec( 'ps ax | grep -e report.php | grep -v grep', $proc, $result ) ) {
    exit;
}


$dir = '/var/www/';	#	'../';	#	
require_once $dir.'db/connect.php';
require_once $dir.'cls/db/index.php';
require_once $dir.'cls/db/wrap.php';

$qry  = 'SELECT COUNT( `activate` = 0 OR NULL ) AS act, COUNT( `error` = 1 OR NULL ) AS err FROM queue;';
$Inst = new WARP( $CONNECT );

$res_count = $Inst -> SelectOne( $qry );
if ( 0 < count( $res_count ) ) { 
	extract( $res_count );

	if ( $act || $err ) { 
		$h   = date( 'G', strtotime('-1 hour') );
		$dat = (int)date('G') ? date('Y-m-d') : date( 'Y-m-d', strtotime('-1 day') ) ;

		$res_count = 
			$Inst -> 
				CountRecord( 
					array(
						'tbl' => $TableOk, 
						'whr' => "`day` = '{$dat}'"
					)
				);
		if ( 0 === $res_count ) { 
			##### 当日 新規 #####
			$def_act = array_fill( 0, 24, 0 );
			if ( $act ) { 
				$def_act[ $h ] = $act;
			}
			$def_err = array_fill( 0, 24, 0 );
			if ( $err ) { 
				$def_err[ $h ] = $err;
			}
			$upd = 
				array( 
					sprintf( "INSERT INTO %s VALUES (NULL,'%s',%s);", $TableOk, $dat, implode( ',', $def_act ) ), 
					sprintf( "INSERT INTO %s VALUES (NULL,'%s',%s);", $TableNg, $dat, implode( ',', $def_err ) )
				);
		} 
		else { 
			##### 更新 #####
			$upd = array();
			if ( $act ) { 
				$upd[] = "UPDATE {$TableOk} SET `{$h}` = {$act} WHERE `day` = '{$dat}';";
			}
			if ( $err ) { 
				$upd[] = "UPDATE {$TableNg} SET `{$h}` = {$err} WHERE `day` = '{$dat}';";
			}
		}
		$upd[] = "DELETE FROM {$TableQueue} WHERE `activate` = 0;";
		
		$qry = $Inst -> Sql( implode( "\n", $upd ) );
		if ( $qry ) { 
			$qry -> closeCursor();
		} else { 
			exit ;
		}

	}

}

