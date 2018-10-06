<?php

/********************************
 * メール送信 cron
 * １分に一回
 *
*********************************/
#ini_set('display_errors', 'On');
/**/
$proc = array();
if ( !exec( 'ps ax | grep -e main.php | grep -v grep', $proc, $result ) ) { 
#echo 1;
    error_log("すでに実行中のプロセスが存在するため、強制終了します。");
    exit;
}

$dir = '/var/www/';	#	'../';	#	
require_once $dir.'db/connect.php';
require_once $dir.'cls/db/index.php';
require_once $dir.'cls/db/wrap.php';
require_once $dir.'cron/send.php';

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
	account.domain like concat('%', server.domain)
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

		for ( $i = 0, $l = count( $res_queue_list ), $co = 0, $limit = 0; $i < $l; $i++ ) { 
			if ( ++$limit >= 1000 ) { 
				# 無限ループ防止
				break;
			}

			if ( $co++ === $l ) { 
				# 送るのがない状態で キュー回りきった
				break ;
			}

			$flg  = false;
			$smtp = $res_account_list[ $c ];
			$send = $res_queue_list[ $i ];

			$smtp_id  = $smtp['id'];
			$queue_id = $send['id'];
                        
			if(!empty($smtp) && count($smtp)) extract( $smtp );
			if(!empty($send) && count($send)) extract( $send );
			
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

/*
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
*/
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
                        // ログ出力用パラメータ
                        $log_param = "to=$to, from=$address, from_mk=$from_mk, from_def=$from_def, smtp_server=$smtp_server, smtp_port=$smtp_port, user=$user, title=$title";

                        $err = mailsend( $prm );
                        $ttl = $Inst -> Quote( $title );
                        $msg = $Inst -> Quote( $message );
			if ( !empty( $err ) ) { 
                            error_log("type:error メールサーバとのコネクションが確立できませんでした。 $log_param");
                            ##### 失敗 #####

                            ## retry数 確認 ##
                            if ( $retry_count < ++$res_account_list[ $c ]['error_count'] ) { 
                                # retry 上限に達した # 
                                error_log("type:error リトライ数の上限に達したため、配信処理を停止します。 $log_param");
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
                                error_log("type:success 送信に成功しました。 $log_param");
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
                                    error_log("type:error 送信数の上限に達したため、配信処理を停止します。 $log_param");
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
			} else { 
                                error_log("type:warning updateの処理に失敗したため、リトライします。sleep=1, sql=" . $qry);
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
                        
			if ( $slp ) { 
                            error_log("type:info " . $slp . "秒間、スリープを行います。");
                            sleep( $slp );
                            error_log("type:info スリープが完了しました。");
			}
			
			if ( $flg ) { 
				# 送信可能アドレスがない #
				break;
			}

			if ( $len <= ++$c ) { 
				$c = 0;
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




/*
# counter + 1 #
$res_update = 
	$Inst -> 
		UpdateOne( 
			array( 
				'tbl' => $TableAccount, 
				'dat' => 
					array(
						'send_length' => 'send_length + 1'
					), 
				'whr' => 'id = '.$smtp['id']
			) 
		);

# queue 送信完了 #
$res_update = 
	$Inst -> 
		UpdateOne( 
			array( 
				'tbl' => $TableQueue, 
				'dat' => 
					array(
						'activate' => '0'
					), 
				'whr' => 'id = '.$send['id']
			) 
		);
*/

