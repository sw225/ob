<?php

# ページ コンテンツ #
$pages	= <<<FILE
info
contact
manage_account
manage_queue
report_smtp_daily
report_smtp_month
report_smtp_send
setup_carrier
setup_account
setup_retry
setup_smtp_ip
setup_interval
mail_set
FILE;
$PageContentArray	= preg_split( "/\R/", $pages );

$AddressLimit = 500;

$OpenCss = ' class="op"'; 

$ListLimit = 50;
