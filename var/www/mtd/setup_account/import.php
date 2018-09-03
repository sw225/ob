<?php

if ( !isset( $Inst ) ) { 
	exit ;
}
if ( !$Inst ) { 
	exit ;
}

$tmp = explode( PHP_EOL, $_POST['dat'] );
$dts = array();

$r = 0;
foreach ( $tmp as $line ) { 
	$data = fgetCsvReg( $line );

	$to  = mb_internal_encoding();
	$frm = mb_detect_order();
	mb_convert_variables( $to, $frm, $data );

	$dts[ $r ]  = array();
	for ( $c = 0, $num = count( $data ); $c < $num; $c++ ) {
		$dts[ $r ][] = nl2br( mb_convert_encoding( $data[ $c ], 'UTF-8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS' ) ) ;
	}
	++$r;
}

$res_import = false;
if ( count( $dts ) ) { 
	$res_import	= 
		$Inst -> 
			CsvImport( 
				array( 
					'tbl'	=> $TableAccount, 
					'dat'	=> $dts, 
					'ids'   => $blank_id
				)
			);

}





function fgetCsvReg( $line, $d = ',', $e = '"' ) { 
	$d = preg_quote( $d );
	$e = preg_quote( $e );
	$l = $line;

	$lin = preg_replace( '/(?:\\r\\n|[\\r\\n])?$/', $d, trim( $l ) );
	$ptn = ( '/('.$e.'[^'.$e.']*(?:'.$e.$e.'[^'.$e.']*)*'.$e.'|[^'.$d.']*)'.$d.'/' );

	preg_match_all( $ptn, $lin, $mac );
	$dat = $mac[1];
	for( $i = 0; $i < count( $dat ); $i++ ) {
		$dat[ $i ] = preg_replace( ( '/^'.$e.'(.*)'.$e.'$/s' ), '$1', $dat[ $i ] );
		$dat[ $i ] = str_replace( $e.$e, $e, $dat[ $i ] );
	}
	return empty( $l ) ? false : $dat ;
}
