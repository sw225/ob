<?php

#require_once('Mail.php');
require_once('Mail.php');

mb_language('japanese');
mb_internal_encoding('utf8');

function mailsend( $_argv ) { 
    extract( $_argv );

    if ( !empty( $from_mk ) ) { 
        $from_mk = mb_encode_mimeheader( $from_mk );
        $from_mk = addslashes( $from_mk );
        $from = sprintf( '"%s"<%s>', $from_mk, $from );
    } 
    else { 
        if ( !empty( $from_def ) ) { 
            $from_def = mb_encode_mimeheader( $from_def );
            $from_def = addslashes( $from_def );
            $from     = sprintf( '"%s"<%s>', $from_def, $from );
        }
    }

    $params =
        array(
            'host'     => $smtp_server,
            'port'     => $smtp_port,
            'auth'     => true,
            'debug'    => false,
            'username' => $user,
            'password' => $pass,
        );  
    $headers  =
        array(
            'To'      => $to,
            'From'    => $from,                   
            'Subject' => mb_encode_mimeheader( $title ),
        );
    $err  = ''; 
    $body = mb_convert_encoding( $message, 'ISO-2022-JP', 'ASCII,JIS,UTF-8,EUC-JP,SJIS' );
    $smtp = Mail::factory( 'smtp', $params );
    if ( PEAR::isError( $smtp ) ) { 
        $err = $smtp -> getMessage();
    } 
    else { 
        $res = $smtp -> send( $to, $headers, $body );
        if ( PEAR::isError( $res ) ) { 
            $err = $res -> getMessage();
        }
    }
    unset( $smtp );

    return $err;
}
