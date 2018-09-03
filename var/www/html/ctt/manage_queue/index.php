<?php

require_once '../ctt/session.php';

$lmt  = sprintf( "0,%s", $ListLimit );
$page = '1';
if ( 0 === count( $_POST ) && isset( $_GET['page'] ) ) { 
	if ( is_numeric( $_GET['page'] ) ) { 
		$page = $_GET['page'];
		if ( 1 < (int)$page ) { 
			$stt = ( (int)$page - 1 ) * $ListLimit;
			$num = $stt;
			$lmt = sprintf( "%s,%s", $stt, $ListLimit );
		}
		$_POST['manage_queue'] = 'キュー詳細';
	}
}


$type = '';
if ( count( $_POST ) ) { 
	if ( isset( $_POST['manage_queue'] ) ) { 
		$type = $_POST['manage_queue'];
		unset( $_POST['manage_queue'] );

		if ( 'ドメイン別表示' === $type ) { 
			if ( isset( $_POST['mtd'] ) ) { 
				if ( 'delete' === $_POST['mtd'] ) {
					unset( $_POST['mtd'] ); 

					if ( isset( $_POST['chk'] ) && count( $_POST['chk'] ) ) { 
						$ids = array();
						foreach ( $_POST['chk'] as $v ) { 
							$ids = array_merge( $ids, explode( ',', $v ) );
						}
						unset( $_POST['chk'] ); 

						require_once '../mtd/manage_queue/domain_delete.php';
						if ( $res_queue_domain_delete ) { 
							$txt = '削除しました。';
						}

					}

				} else { 
				}
			} 

		} else 
		if ( 'キュー詳細' === $type ) { 
			if ( isset( $_POST['mtd'] ) ) { 
				if ( 'delete' === $_POST['mtd'] ) {
					unset( $_POST['mtd'] ); 

					if ( isset( $_POST['chk'] ) && count( $_POST['chk'] ) ) { 
						$ids = $_POST['chk'];
						unset( $_POST['chk'] ); 

						require_once '../mtd/manage_queue/detail_delete.php';
						if ( $res_queue_detail_delete ) { 
							$txt = '削除しました。';
						}

					}

				} else { 
				}
			} 
		} else 
		if ( '全件削除' === $type ) { 
			if ( isset( $_POST['mtd'] ) ) { 
				if ( 'delete' === $_POST['mtd'] ) {
					unset( $_POST['mtd'] ); 
					
					require_once '../mtd/manage_queue/all_delete.php';
					if ( $res_queue_all_delete ) { 
						$txt = '削除しました。';
					}

				} else { 
				}
			} 

		}
	}


}

$res_queue_count = 
	$Inst -> 
		CountRecord( 
			array( 
				'tbl' => $TableQueue, 
				'whr' => 'activate = 1'
			)
		);

$tmpl = new Tmpl( '../tmp/manage_queue.html' );

$tmpl -> assign( 'queue_length', $res_queue_count );

if ( !empty( $type ) ) { 

	$res_queue_list = 
		$Inst -> 
			ListAllRecode( 
				array( 
					'tbl' => $TableQueue, 
					'clm' => '`id`,`to`,`title`,`message`,`mail`,`created`', 
					'whr' => 'activate = 1', 
					'lmt' => $lmt
				)
			);

	$res_queue_domain_list = 
		$Inst -> 
			ListAllRecode( 
				array( 
					'tbl' => $TableQueue, 
					'clm' => '`id`,`to`', 
					'whr' => 'activate = 1'
				)
			);

	if ( 'ドメイン別表示' === $type ) { 
		$tmpl -> assign_def( 'queue_domain' );

		$list = array();
		foreach ( $res_queue_domain_list as $ary ) { 
			$d = explode( '@', $ary['to'] );
			if ( 2 === count( $d ) ) { 
				$domain = $d[1];
				if ( array_key_exists( $domain, $list ) ) {
					$list[ $domain ][] = $ary['id'];
				} 
				else { 
					$list[ $domain ] = array( $ary['id'] );
				}
			}
		}

		if ( count( $list ) ) { 
			$tmpl -> loopset( 'loop_queue_domain' );
			$c = 0;
			foreach ( $list as $k => $ary ) { 
				$tmpl -> assign( 'queue_domain_id',      ++$c );
				$tmpl -> assign( 'queue_domain_ids',     implode( ',', $ary ) );
				$tmpl -> assign( 'queue_domain_name',    $k );
				$tmpl -> assign( 'queue_domain_length',  count( $ary ) );

				$tmpl -> loopnext( 'loop_queue_domain' );
			}
			$tmpl -> loopend( 'loop_queue_domain' );
		}

	} else 
	if ( 'キュー詳細' === $type ) { 
		$tmpl -> assign_def( 'queue_detail' );

		if ( $res_queue_list ) { 
			$tmpl -> loopset( 'loop_queue_detail' );
			foreach ( $res_queue_list as $ary ) { 
				$tmpl -> assign( 'queue_detail_id',       $ary['id'] );
				$tmpl -> assign( 'queue_detail_to',       $ary['to'] );
				$tmpl -> assign( 'queue_detail_title',    $ary['title'] );
				$tmpl -> assign( 'queue_detail_message',  preg_replace( '/\r\n|\r|\n/', '<br />', $ary['message'] ) );
				$tmpl -> assign( 'queue_detail_created',  $ary['created'] );

				$tmpl -> loopnext( 'loop_queue_detail' );
			}
			$tmpl -> loopend( 'loop_queue_detail' );

			$qry = '';
			if ( $ListLimit < $res_queue_count ) { 
				$qry = preg_replace( '/&page=\d+/', '', $_SERVER['QUERY_STRING'] );
				$qry = sprintf( '?%s&', $qry );
				$tmpl -> assign( 'paging',   paging( ceil( $res_queue_count / $ListLimit ), $page, 3, $qry ) );
			} 
			else { 
				$tmpl -> assign( 'paging',   '' );
			}

		} 
		else { 
			$tmpl -> assign( 'paging',   '' );
		}

	} else 
	if ( '全件削除' === $type ) { 
		$tmpl -> assign_def( 'queue_delete' );
	}


}

$tmpl -> flush();
