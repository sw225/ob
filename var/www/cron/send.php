<?php

#require_once('Mail.php');
require_once('Mail.php');

mb_language('japanese');
mb_internal_encoding('utf8');

/**
 * 
 * @param type $_argv<br />
 *     	'to'          => 宛先, 
 *      'from'        => FROMアドレス,
 *      'from_mk'     => 優先される差出人名,
 *      'from_def'    => from_mkがない場合に適用される差出人名, 
 *      'smtp_server' => SMTPサーバ host,
 *      'smtp_port'   => SMTPサーバ port,
 *      'user'        => ユーザ名,
 *      'pass'        => パスワード,
 *      'title'       => メールSubject,
 *      'message'     => メールBody
 * @return type
 */
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
