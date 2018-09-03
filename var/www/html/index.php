<?php

ini_set( 'display_errors', 0 );

require_once '../db/connect.php';

require_once '../cls/db/index.php';
require_once '../cls/db/wrap.php';
require_once '../cls/tmp/index.php';

require_once '../ctt/config.php';
require_once '../ctt/page.php';

$Inst = new WARP( $CONNECT );

require_once '../ctt/session.php';

?><!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<title>RMK</title>
<link rel="stylesheet" type="text/css" href="cmn/css/lib/reset.css">
<link rel="stylesheet" type="text/css" href="cmn/css/lib/default.css?<?php echo time();?>">
<link rel="stylesheet" type="text/css" href="cmn/css/lib/icon.css?<?php echo time();?>">
<link rel="stylesheet" type="text/css" href="cmn/css/index.css?<?php echo time();?>">
<link rel="stylesheet" type="text/css" href="cmn/css/ctt.css?<?php echo time();?>">
<link rel="shortcut icon" href="img/favicon.ico" type="image/vnd.microsoft.icon" />
<script type="text/javascript" src="cmn/js/my.js?<?php echo time();?>"></script>
</head>
<body>

<div id="" class="wrap hmax">

	<header class="rel">
		<p class="abs logo">
			<span><img src="img/rmk_logo.png" /></span>
		</p>
		<p class="abs home"><a href="./">ホーム</a></p>
		<p class="abs contact"><a href="?contact" class="clear">問い合わせ</a></p>
		<p class="abs logout"><a href="logout.php" class="clear">ログアウト</a></p>
	</header>

	<div class="wrap_ctt clear">

		<div class="menu left">
			<dl>
				<dt><strong>状況管理</strong></dt>
					<dd<?php echo ('manage_account'===$op?$OpenCss:'');?>><a href="?manage_account">アカウント管理</a></dd>
					<dd<?php echo ('manage_queue'===$op?$OpenCss:'');?>><a href="?manage_queue">キュー管理</a></dd>
				<dt><strong>レポート</strong></dt>
					<dd<?php echo ('report_smtp_daily'===$op?$OpenCss:'');?>><a href="?report_smtp_daily">時間別配信レポート</a></dd>
					<dd<?php echo ('report_smtp_month'===$op?$OpenCss:'');?>><a href="?report_smtp_month">日別配信レポート</a></dd>
					<dd<?php echo ('report_smtp_send'===$op?$OpenCss:'');?>><a href="?report_smtp_send">配信レポート詳細</a></dd>
				<dt><strong>システム設定</strong></dt>
					<dd<?php echo ('setup_carrier'===$op?$OpenCss:'');?>><a href="?setup_carrier">キャリア別送信設定</a></dd>
					<dd<?php echo ('setup_account'===$op?$OpenCss:'');?>><a href="?setup_account">アカウント設定</a></dd>
					<dd<?php echo ('setup_retry'===$op?$OpenCss:'');?>><a href="?setup_retry">リトライ設定</a></dd>
					<dd<?php echo ('setup_interval'===$op?$OpenCss:'');?>><a href="?setup_interval">インターバル設定</a></dd>
				<dt><strong>配信管理</strong></dt>
					<dd<?php echo ('mail_set'===$op?$OpenCss:'');?>><a href="?mail_set">一斉送信</a></dd>
				<dt><strong>IP管理</strong></dt>
					<dd<?php echo ('setup_smtp_ip'===$op?$OpenCss:'');?>><a href="?setup_smtp_ip">送信許可IP設定</a></dd>
			</dl>
		</div>

<?php require_once $p; ?>

	</div>

	<footer></footer>
</div>

</body>
</html>