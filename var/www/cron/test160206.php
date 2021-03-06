<?php


$dir = '/var/www/';	#	'../';	#	
require_once $dir.'db/connect.php';
require_once $dir.'cls/db/index.php';
require_once $dir.'cls/db/wrap.php';
require_once 'send.php';

$Inst = new WARP( $CONNECT );

$res_queue_list = 
	$Inst -> 
		ListAllRecode( 
			array( 
				'tbl' => $TableQueue, 
				'clm' => '`id`,`to`,`from_name` AS from_def, `title`,`message`', 
				'whr' => 'activate = 1', 
				'oby' => 'id ASC'
			)
		);
if ( count( $res_queue_list ) ) { 
	$qry = <<<SQL
SELECT 
	account.id, account.address, account.user, account.pass, account.from_name AS from_mk, account.send_length, account.error_count, account.error_time, account.activate, 
	server.smtp_server, server.smtp_port, server.send_limit, server.retry_count, server.retry_time, 
	`interval`.sleep, 
	counter.n
FROM 
	account 
	INNER JOIN server 
	INNER JOIN `interval` 
	INNER JOIN counter
ON 
	account.domain = server.domain
;
SQL;
	$res_account_list = $Inst -> SelectAll( $qry );

	if ( count( $res_account_list ) ) { 
		$len = count( $res_account_list );
		$slp = $res_account_list[0]['sleep'] ? (int)$res_account_list[0]['sleep'] : 0 ;
		$c   = $res_account_list[0]['n'];
		if ( $len <= $c ) { 
			$c = 0;
		}

		for ( $i = 0, $l = count( $res_queue_list ), $co = 0; $i < $l; $i++ ) { 
#		foreach ( $res_queue_list as $send ) { 
			if ( $co++ === $l ) { 
				# 送るのがない状態で キュー回りきった
				exit ;
			}

			$flg  = false;
			$smtp = $res_account_list[ $c ];
			$send = $res_queue_list[ $i ];

			$smtp_id  = $smtp['id'];
			$queue_id = $send['id'];

			extract( $smtp );
			extract( $send );

			if ( 1 != $smtp['activate'] ) { 
				if ( $len <= ++$c ) { 
					$c = 0;
				}
				--$i;
				continue;
			}
			if ( 0 != $smtp['error_count'] && $smtp['retry_count'] <= $smtp['error_count'] ) {  
				if ( $len <= ++$c ) { 
					$c = 0;
				}
				--$i;
				continue;
			}

			if ( 0 < $res_account_list[ $c ]['error_count'] ) { 
				$tim = strtotime( "{$error_time} +{$retry_time} hour");
				$tim = strtotime( date( 'Y-m-d H:i:s', $tim ) );
				$now = strtotime( date('Y-m-d H:i:s') );
				if ( $now < $tim ) { 
					if ( $len <= ++$c ) { 
						$c = 0;
					}
					--$i;
					continue;
				}
			}

/**/
echo $c."\n";
echo $smtp['id']."\n";
echo $smtp['address']."\n";
echo $smtp['user']."\n";
echo $smtp['pass']."\n";
echo $smtp['from_mk']."\n";
echo $smtp['send_length']."\n";
echo $smtp['error_count']."\n";
echo $smtp['error_time']."\n";

echo $smtp['smtp_server']."\n";
echo $smtp['smtp_port']."\n";
echo $smtp['send_limit']."\n";
echo $smtp['retry_count']."\n";
echo $smtp['retry_time']."\n";

echo $smtp['sleep']."\n";

echo $send['id']."\n";
echo $send['to']."\n";
echo $send['from_def']."\n";
echo $send['title']."\n";
echo $send['message']."\n";

echo "---------------------------------------------------------------------------\n";


			##### メール送信 #####
			$prm = array( 
				'to'          => $to, 
				'from'        => $address, 
				'from_mk'     => $from_mk, 
				'from_def'    => $from_def, 
				'smtp_server' => $smtp_server, 
				'smtp_port'   => $smtp_port, 
				'user'        => $user, 
				'pass'        => $pass, 
				'title'       => $title, 
				'message'     => $message
			);
			$err = mailsend( $prm );

			$ttl = $Inst -> Quote( $title );
			$msg = $Inst -> Quote( $message );
			if ( !empty( $err ) ) { 
				##### 失敗 #####

				## retry数 確認 ##
				if ( $retry_count < ++$res_account_list[ $c ]['error_count'] ) { 
					# retry 上限に達した # 
					unset( $res_account_list[ $c ] );
					$len = count( $res_account_list );
					if ( 0 === $len ) { 
						$flg = true;
					}
				}
				## error time 更新 ##
				$dt  = date('Y-m-d H:i:s');
				if ( preg_match( '/.*code ?:? ?(-?\d+).*/i', $err, $res_error ) ) { 
					$err_num = ( count( $res_error ) ) ? trim( $res_error[1] ) : '';
					# 画像認証 エラー 判定 #
					if ( '521' === $err_num ) { 
						if ( strstr( $err, 'Your SMTP service is temporarily stopped' ) ) { 
							$dt  = '9999-12-31 23:59:59';
						}
					}
				}
				$res_account_list[ $c ]['error_time'] = $dt;

				$err = $Inst -> Quote( $err );
				$upd = array( 
					"UPDATE {$TableAccount} SET send_length = send_length + 1, error_count = error_count + 1, error_txt = {$err}, error_time = '{$dt}' WHERE id = {$smtp_id};", 
					"UPDATE {$TableQueue} SET activate = 0, error = 1 WHERE id = {$queue_id};", 
					sprintf(
						"INSERT INTO %s VALUES ( NULL, '%s', '%s', %s, %s, %s, CURRENT_TIMESTAMP );", 
						$TableSent, 
						$smtp['address'], 
						$send['to'], 
						$ttl, 
						$msg, 
						$err
					)
				);
			} 
			else { 
				##### 成功 #####

				## error time 更新 ##
				$dt  = '0000-00-00 00:00:00';
				$res_account_list[ $c ]['error_time'] = $dt;

				$upd = array( 
					"UPDATE {$TableAccount} SET send_length = send_length + 1, error_count = 0, error_txt = '', error_time = '{$dt}' WHERE id = {$smtp_id};", 
					"UPDATE {$TableQueue} SET activate = 0 WHERE id = {$queue_id};", 
					sprintf(
						"INSERT INTO %s VALUES ( NULL, '%s', '%s', %s, %s, '%s', CURRENT_TIMESTAMP );", 
						$TableSent, 
						$smtp['address'], 
						$send['to'], 
						$ttl, 
						$msg, 
						'0'
					)
				);
			}

			## 送信上限 確認 ##
			if ( isset( $res_account_list[ $c ] ) && $res_account_list[ $c ]['send_length'] ) { 
				if ( $send_limit < ++$res_account_list[ $c ]['send_length'] ) { 
					# limit までいった # 
					unset( $res_account_list[ $c ] );
					$len = count( $res_account_list );
					if ( 0 === $len ) { 
						$flg = true;
					}
				}
			}

			$qry = $Inst -> Sql( implode( "\n", $upd ) );
			if ( $qry ) { 
				$qry -> closeCursor();
			} 
			else { 
				sleep(1);

				# 失敗 やり直し #
				$Inst = new WARP( $CONNECT );
				$qry = $Inst -> Sql( implode( "\n", $upd ) );
				if ( $qry ) { 
					$qry -> closeCursor();
				} 
				else { 
					# 2回 失敗したら諦め #
					exit ;
				}				
			}
			
			if ( $flg ) { 
				# 送信可能アドレスがない #
				break;
			}

			if ( $len <= ++$c ) { 
				$c = 0;
			}
			if ( $slp ) { 
				echo $c;
				sleep( $slp );
			}

			$co = 0;
		}
	}
}

if ( isset( $c ) ) { 
	$res_update = 
		$Inst -> 
			UpdateOne( 
				array( 
					'tbl' => $TableCounter, 
					'dat' => array( 'n' => $c ), 
					'whr' => 'id = 1'
				) 
			);
}



