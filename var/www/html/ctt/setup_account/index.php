<?php

require_once '../ctt/session.php';

if ( count( $_POST ) ) { 

	$type = $_POST['type'];
	unset( $_POST['type'] );

	if ( 'setup' === $type ) { 
		if ( isset( $_POST['chk'] ) && count( $_POST['chk'] ) ) { 
			$ids = $_POST['chk'];
			unset( $_POST['chk'] );

			if ( isset( $_POST['change'] ) ) { 
				### 変更 ###
				unset( $_POST['change'] );

				$clm_def = explode( ',', 'address,def,user,pass,from_name,memo' );

				for ( $i = 1; $i <= $AddressLimit; $i++ ) { 
					if ( !in_array( $i, $ids ) ) { 
						foreach ( $clm_def as $v ) { 
							unset( $_POST[ $v.$i ] );
						}
					}
				}

				$res_account_list = 
					$Inst -> 
						ListAllRecode( 
							array( 
								'tbl' => $TableAccount, 
								'clm' => '`id`,`domain`,`error_count`,`error_txt`,`error_time`,`created`', 
								'whr' => sprintf( 'id IN (%s)', implode( ',', $ids ) )
							)
						);


				# アドレス変更時 エラー初期化 #
				foreach ( $ids as $id ) { 
					if ( $_POST["address{$id}"] != $_POST["def{$id}"] ) { 
						$_POST["error_count{$id}"] = '0';
						$_POST["error_txt{$id}"]   = '';
						$_POST["error_time{$id}"]  = '0000-00-00 00:00:00';

						# domain も変更 #
						$domain = explode( '@', $_POST["address{$id}"] );
						$domain = $domain[1];
						$_POST["domain{$id}"]  = $domain;

						$_POST["created{$id}"] = date('Y-m-d H:i:s');

						$cng = 1;
					} 
					else { 
						foreach ( $res_account_list as $ary ) { 
							if ( $ary['id'] === $id ) { 
								$_POST["error_count{$id}"] = $ary['error_count'];
								$_POST["error_txt{$id}"]   = $ary['error_txt'];
								$_POST["error_time{$id}"]  = $ary['error_time'];
								$_POST["domain{$id}"]      = $ary['domain'];
								$_POST["created{$id}"]     = $ary['created'];
							}
						}
					}
				}

				require_once '../mtd/setup_account/change.php';
				if ( $res_change_setup_account ) { 
					$txt = '変更しました。';
				}

			} else 
			if ( isset( $_POST['delete'] ) ) { 
				### 削除 ###
				unset( $_POST['delete'] );
				
				require_once '../mtd/setup_account/delete.php';
				if ( $res_delete_setup_account ) { 
					$txt = '削除しました。';
				}

			}

		}

	} else
	if ( 'import' === $type ) { 
		if ( isset( $_POST['dat'] ) && !empty( $_POST['dat'] ) ) { 
			$res_blank_list = 
				$Inst -> 
					ListAllRecode( 
						array( 
							'tbl' => $TableAccount, 
							'clm' => '`id`', 
							'whr' => "`address` = ''"
						)
					);
			if ( count( $res_blank_list ) ) { 
				$blank_id = 
					array_map(
						function( $_ary ) { 
							return $_ary['id'];
						}, 
						$res_blank_list
					);
				require_once '../mtd/setup_account/import.php';
				if ( $res_import ) { 
					$txt = '追加しました。';
				}

			}

		}

	}

}




$res_account_list = 
	$Inst -> 
		ListAllRecode( 
			array( 
				'tbl' => $TableAccount, 
				'clm' => '`id`,`address`,`user`,`pass`,`from_name`,`error_count`,`error_txt`,`memo`,`activate`', 
				'oby' => '`created` ASC'
			)
		);

$tmpl = new Tmpl( '../tmp/setup_account.html' );

$tmpl -> assign_def( 'account_list' );

$tmpl -> assign( 'max', $AddressLimit );

$ary_blank = 
	array_filter( 
		$res_account_list, 
		function ( $_ary ) { 
			return ( '' === $_ary['address'] ) ? true : false ;
		}
	);
$ary_busy = 
	array_filter( 
		$res_account_list, 
		function ( $_ary ) { 
			return ( '' !== $_ary['address'] ) ? true : false ;
		}
	);
$res_account_list = array_merge( $ary_busy, $ary_blank );

$co = 0;
$tmpl -> loopset( 'loop_account' );
foreach ( $res_account_list as $ary ) { 
	$tmpl -> assign( 'account_id',          $ary['id'] );
	$tmpl -> assign( 'account_num',         ++$co );
	$tmpl -> assign( 'account_address',     $ary['address'] );
	$tmpl -> assign( 'account_user',        $ary['user'] );
	$tmpl -> assign( 'account_pass',        $ary['pass'] );
	$tmpl -> assign( 'account_from_name',   htmlentities( $ary['from_name'], ENT_QUOTES, 'UTF-8' ) );
	$tmpl -> assign( 'account_memo',        htmlentities( $ary['memo'], ENT_QUOTES, 'UTF-8' ) );
	$err  = ''; 
	$clr  = $ary['error_count'] ? 'error' : 'normal';
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
	$tmpl -> loopnext( 'loop_account' );
}
$tmpl -> loopend( 'loop_account' );

$tmpl -> flush();


/*
$clm_def = explode( ',', 'id,address,user,pass,from_name,memo' );
$ary_def = array_combine( $clm_def, array_fill( 0, count( $clm_def ), '' ) );

$tmpl -> loopset( 'loop_account' );
for ( $i = 0; $i < $AddressLimit; $i++ ) { 
	$ary = isset( $res_account_list[ $i ] ) ? $res_account_list[ $i ] : $ary_def ;

	$tmpl -> assign( 'account_id',          $ary['id'] );
	$tmpl -> assign( 'account_num',         $i + 1 );
	$tmpl -> assign( 'account_address',     $ary['address'] );
	$tmpl -> assign( 'account_user',        $ary['user'] );
	$tmpl -> assign( 'account_pass',        $ary['pass'] );
	$tmpl -> assign( 'account_from_name',   $ary['from_name'] );
	$tmpl -> assign( 'account_memo',        $ary['memo'] );

	$tmpl -> loopnext( 'loop_account' );
}
$tmpl -> loopend( 'loop_account' );
*/
