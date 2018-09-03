<?php

require_once '../ctt/session.php';

if ( count( $_POST ) ) { 

	if ( isset( $_POST['chk'] ) && count( $_POST['chk'] ) ) { 
		$ids = $_POST['chk'];
		unset( $_POST['chk'] );

		if ( isset( $_POST['change'] ) ) { 
			### 変更 ###
			unset( $_POST['change'] );

			$clm_def = explode( ',', 'activate,memo' );

			$ary = array( 'act' => array(), 'del' => array() ) ;
			foreach ( $_POST as $k => $v ) { 
				$n = 0;
				$n = str_replace( 'memo', '', $k );
				if ( is_numeric( $n ) ) { 
					if ( !in_array( $n, $ids ) ) { 
						$ary['del'][] = $n;
					} 
					else { 
						$ary['act'][] = $n;
					}
				}
			}

			foreach ( $ary as $k => $a ) {
				if ( 'act' === $k ) { 
					foreach ( $a as $v ) { 
						if ( !isset( $_POST["activate{$v}"] ) ) { 
							$_POST["activate{$v}"] = '0';
						}
					}
				} else 
				if ( 'del' === $k ) { 
					foreach ( $a as $v ) { 
						if ( !in_array( $v, $ids ) ) { 
							foreach ( $clm_def as $nam ) { 
								unset( $_POST[ $nam.$v ] );
							}
						}
					}
				}
			}

			$txt = '失敗しました。';
			require_once '../mtd/manage_account/change.php';
			if ( $res_change_manage_account ) { 
				$txt = '変更しました。';
			}

		} else 
		if ( isset( $_POST['delete'] ) ) { 
			### 削除 ###
			unset( $_POST['delete'] );
			
			require_once '../mtd/manage_account/delete.php';
			if ( $res_delete_manage_account ) { 
				$txt = '削除しました。';
			}

		}

	}

}


$res_account_list = 
	$Inst -> 
		ListAllRecodeJoin( 
			array( 
				'tbl' => $TableAccount, 
				'clm' => 
					$Inst -> JoinClm(
						array(
							$TableAccount => explode( '|', 'id|address|user|pass|from_name|send_length|error_count|error_txt|error_time|memo|activate' ), 
							$TableServer  => explode( '|', 'send_limit|retry_count' )
						)
					), 
				'jin' => $TableServer, 
				'on'  => "{$TableAccount}.domain = {$TableServer}.domain", 
				'oby' => "{$TableAccount}.created ASC"
			)
		);

$tmpl = new Tmpl( '../tmp/manage_account.html' );

$tmpl -> assign_def( 'account_list' );

$co = 0;
$tmpl -> loopset( 'loop_account' );
foreach ( $res_account_list as $ary ) { 
	$tmpl -> assign( 'account_id',          $ary['id'] );
	$tmpl -> assign( 'account_num',         ++$co );
	$tmpl -> assign( 'account_checked',     $ary['activate'] ? 'checked' : '' );
	$tmpl -> assign( 'account_state',       $ary['activate'] ? '使用中' : '未使用' );
	$tmpl -> assign( 'account_address',     $ary['address'] );
	$tmpl -> assign( 'account_user',        $ary['user'] );
	$tmpl -> assign( 'account_pass',        $ary['pass'] );
	$tmpl -> assign( 'account_from_name',   $ary['from_name'] );
	$tmpl -> assign( 'account_memo',        htmlentities( $ary['memo'], ENT_QUOTES, 'UTF-8' ) );
	$tmpl -> assign( 'account_send_length', $ary['send_length'] );
	$tmpl -> assign( 'account_send_limit',  $ary['send_limit'] );
	$tmpl -> assign( 'account_error_count', $ary['error_count'] );
	$err = '';
	$clr  = ( $ary['error_count'] > $ary['retry_count'] ) ? 'error' : 'normal';
	if ( !empty( $ary['error_txt'] ) ) { 
		preg_match( '/.*code ?:? ?(-?\d+).*/i', $ary['error_txt'], $res );
		$err = ( count( $res ) ) ? trim( $res[1] ) : 'unknown';
	}
	if ( '521' === $err ) { 
		if ( strstr( $ary['error_txt'], 'Your SMTP service is temporarily stopped' ) ) { 
			$clr = 'auth';
		}
	}
	$tmpl -> assign( 'account_tr_color',    $clr );
	$tmpl -> assign( 'account_error_code',  $err );
	$tmpl -> assign( 'account_error_time',  ( '0000-00-00 00:00:00' === $ary['error_time'] ) ? '' : date( 'y/m/d H:i', strtotime( $ary['error_time'] ) ) );

	$tmpl -> loopnext( 'loop_account' );
}
$tmpl -> loopend( 'loop_account' );

$tmpl -> flush();
