<?php

require_once('Mail.php');
$dir = '/var/www/';
require_once $dir.'cron/send.php';

mailsend(array(
    'to'          => "sawa@f-i-d.jp", 
    'from'        => "ysn.kg.jpn@gmail.com",
    'from_mk'     => "test",
    'from_def'    => "", 
    'smtp_server' => "smtp.gmail.com",
    'smtp_port'   => "587",
    'user'        => "ysn.kg.jpn@gmail.com",
    'pass'        => "__Localhost2525",
    'title'       => "プログラムテスト送信",
    'message'     => "アイウエオ"
));