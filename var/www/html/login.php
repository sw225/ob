<?php

ini_set( 'display_errors', 'Off' );
ini_set( 'session.gc_maxlifetime', 1800 );
ini_set( 'session.gc_probability', 1 );
ini_set( 'session.gc_divisor', 100 );

session_start();

ini_set( 'display_errors', 'On' );

require_once '../db/connect.php';

require_once '../cls/db/index.php';
require_once '../cls/db/wrap.php';
require_once '../cls/tmp/index.php';

$Inst = new WARP( $CONNECT );

$res_login_list = 
	$Inst -> 
		ListAllRecode( 
			array( 
				'tbl' => $TableLogin, 
				'clm' => '`user`,`pass`'
			)
		);
if ( 0 === count( $res_login_list ) ) { 
	exit ;
}

if ( count( $_POST ) ) { 
	if ( isset( $_POST['login_id'] ) && isset( $_POST['login_pass'] ) ) { 
		$id   = $_POST['login_id'];
		$pass = $_POST['login_pass'];
		if ( !empty( $id ) && !empty( $pass ) ) { 
			foreach ( $res_login_list as $ary ) { 
				if ( $id === $ary['user'] && $pass === $ary['pass'] ) { 
					session_regenerate_id( TRUE );
					$_SESSION['USER'] = $ary['user'];
					$_SESSION['PASS'] = $ary['pass'];

					header('location:./');
				}
			}
		}
	}
} 
else { 
	if ( count( $_SESSION ) ) { 
		foreach ( $res_login_list as $ary ) { 
			if ( $_SESSION['USER'] === $ary['user'] && $_SESSION['PASS'] === $ary['pass'] ) { 

#				header('location:./');
			}
		}
	} 
}


$tmpl = new Tmpl( '../tmp/login.html' );


$tmpl -> flush();
