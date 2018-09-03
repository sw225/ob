<?php
/*    local 	*/
$CONNECT = 
	array( 
		'host' => 'localhost', 
		'user' => 'sawa', 
		'pass' => '', 
		'db'   => 'relay' 
	);

/*    dti 	
$CONNECT = 
	array( 
		'host' => 'localhost', 
		'user' => 'root', 
		'pass' => 'poiuytrewq12345zxcvb000', 
		'db'   => 'relay'
	);
*/

/*    sakura 	
$CONNECT = 
	array( 
		'host' => 'localhost', 
		'user' => 'root', 
		'pass' => 'poiuytrewq1234567890mmm', 
		'db'   => 'relay'
	);
*/

/*    sakura2 	
$CONNECT = 
	array( 
		'host' => 'localhost', 
		'user' => 'root', 
		'pass' => 'Fdmz]e[f:R@t0fEJz#$lrD4', 
		'db'   => 'relay'
	);
*/





$DB = $CONNECT['db'];

# ------ テーブル一覧 ----- #
$TableAccount  = "`{$DB}`.`account`";
$TableServer   = "`{$DB}`.`server`";
$TableQueue    = "`{$DB}`.`queue`";
$TableIp       = "`{$DB}`.`ip`";
$TableInterval = "`{$DB}`.`interval`";
$TableCounter  = "`{$DB}`.`counter`";

$TableOk       = "`{$DB}`.`log_ok`";
$TableNg       = "`{$DB}`.`log_ng`";

$TableLogin    = "`{$DB}`.`login`";

$TableSent     = "`{$DB}`.`sent`";
$TableContact  = "`{$DB}`.`contact`";

