<?php

if ( !isset( $Inst ) ) { 
	exit ;
}
if ( !$Inst ) { 
	exit ;
}

ini_set( 'display_errors', 'On' );
ini_set( 'session.gc_maxlifetime', 1800 );
ini_set( 'session.gc_probability', 1 );
ini_set( 'session.gc_divisor', 100 );

session_start();

$flg = true;
if ( count( $_SESSION ) ) { 
	if ( isset( $_SESSION['USER'] ) || isset( $_SESSION['PASS'] ) ) { 
		if ( !isset( $res_login_list ) ) { 
			$res_login_list = 
				$Inst -> 
					ListAllRecode( 
						array( 
							'tbl' => $TableLogin, 
							'clm' => '`user`,`pass`'
						)
					);
		}
		if ( count( $res_login_list ) ) { 
			foreach ( $res_login_list as $ary ) { 
				if ( $_SESSION['USER'] === $ary['user'] && $_SESSION['PASS'] === $ary['pass'] ) { 
					$flg = false;
					break;
				}
			}
		}
	}
}
if ( $flg ) { 
	header('location:./login.php');
}


