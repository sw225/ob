<?php

require_once('Mail.php');

mb_language('japanese');
mb_internal_encoding('utf8');

function mailsend( $_argv ) { 
    extract( $_argv );

    if ( !empty( $from_name ) ) { 
        $from = sprintf( '%s<%s>', mb_encode_mimeheader( $from_name ), $from );
    }

    $params =
        array(
            'host'     => $smtp_server,
            'port'     => $smtp_port,
            'auth'     => true,
            'debug'    => true,
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
        echo "\n---------------------------------------smtp------------------------------------\n";
        var_dump($smtp);
        $err = $smtp -> getMessage();
    } 
    else { 
        $res = $smtp -> send( $to, $headers, $body );
        if ( PEAR::isError( $res ) ) { 
            echo "\n---------------------------------------send------------------------------------\n";
            var_dump($smtp);
            $err = $res -> getMessage();
        }
    }
    unset( $smtp );

    return $err;
}


$ad = explode( '|', 'dsfdyyi7@yahoo.co.jp|a5tc1ze9@yahoo.co.jp|apgybfum@yahoo.co.jp|yjzwfwwm@yahoo.co.jp|pbuurwzh@yahoo.co.jp|scgnjyzk@yahoo.co.jp|ytzhjnrd@yahoo.co.jp|yicwbaxw@yahoo.co.jp|fddxinsm@yahoo.co.jp|ngsnstru@yahoo.co.jp|buhiuagb@yahoo.co.jp|andijnbx@yahoo.co.jp|admwrfmy@yahoo.co.jp' );
$us = explode( '|', 'dsfdyyi7|a5tc1ze9|apgybfum|yjzwfwwm|pbuurwzh|scgnjyzk|ytzhjnrd|yicwbaxw|fddxinsm|ngsnstru|buhiuagb|andijnbx|admwrfmy' ); 
$ps = explode( '|', 'uk0d4rgJ|zHQJIlve|9995n58y|b9bbaghs|tnghdmdm|3drjga3g|rzwpygre|s3si8rub|rjb9mwhr|dnr9fz6c|rgjs34re|xgsgwp9w|x7wu7axi' );

$to = 'liyuutgrfa8rfgha76trg2q3r@gmail.com';
$ad = array('fff4cf7ajhyju29@yahoo.co.jp');
$us = array('fff4cf7ajhyju29');
$ps = array('fr2f8cbcblo29');

#for ( $j = 3; $j--; ) { 
    for ( $i = count($ad); $i--; ) { 
        $prm = array( 
            'to'          => $to, 
            'from'        => $ad[$i], 
            'from_name'   => 'test', 
            'smtp_server' => 'smtp.mail.yahoo.co.jp', 
            'smtp_port'   => 587, 
            'user'        => $us[$i], 
            'pass'        => $ps[$i], 
            'title'       => 'ほげほげ', 
            'message'     => 'ふがHTTP://y47ikaihin-2015.or.jp/rd/kuykj/________/________/_____/____/dWdyZXVqb2JmZA==/73e99e259ac027d6987bd061b1bfdf896597a8f04fe8aeb86fbf00a11f57b825
貴方へ二千万(斉藤)様から..,の.,.,メ ー ル_を,読,む
HTTP://y47ikaihin-2015.or.jp/rd/nrgrs/___/2138055/m4776803506/73e99e259ac027d6987bd061b1bfdf896597a8f04fe8aeb86fbf00a11f57b825ふが'
        );
        $err = mailsend( $prm );
    echo "\n";
    echo $ad[$i]." : ".$err;
    echo "\n";

    sleep(1);

    }
#}











