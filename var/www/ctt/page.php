<?php

$p	= 'info';
$op = 'info';
if ( 0 < count( $_GET ) ) {
	$ps	= array_keys( $_GET );

	if ( 0 < count( $_GET ) ) { 
		$p  = $ps[0];
		$op = $p;
	}

	if ( in_array( $p, $PageContentArray ) ) { 
		$p	= 'ctt/' .$p. '/index.php';
	}
	else {
		$p	= 'ctt/info/index.php';
	}

}
else {
	$p	= 'ctt/info/index.php';
}

function paging( $limit, $page, $tab, $get = '?', $disp = 5 ) { 
	$htm   = '';

	$idt   = $tab ? str_repeat( "\t", $tab ) : '';
	$idt1  = $tab ? str_repeat( "\t", $tab + 1 ) : '';

	$next  = $page + 1;
	$prev  = $page - 1;
	 
	$start = ( $page - floor( $disp / 2 ) > 0 ) ? ( $page - floor( $disp / 2) ) : 1;
	$end   = ( $start > 1 )                     ? ( $page + floor( $disp / 2) ) : $disp;
	$start = ( $limit < $end )                  ? $start - ( $end - $limit )    : $start;
	 
	if ( $page != 1 ) { 
		$htm = sprintf( "\n%s<li class=\"prev\"><a href=\"%spage=%s\">&laquo; 前へ</a></li>", $idt1, $get, $prev );
	}
	 
	if ( $start >= floor( $disp / 2 ) ) { 
		$htm = sprintf( "%s\n%s<li><a href=\"%spage=1\">1</a></li>", $htm, $idt1, $get );
		if ( $start > floor( $disp / 2 ) ) { 
			$htm = sprintf( "%s\n%s<li>...</li>", $htm, $idt1 );
		}
	}
	 
	for ( $i = $start; $i <= $end; $i++ ) { 
		$cls = ( $page == $i ) ? ' class="p"' : '' ;
		if ( $i <= $limit && $i > 0 ) { 
			$htm = sprintf( "%s\n%s<li><a href=\"%spage=%s\"%s>%s</a></li>", $htm, $idt1, $get, $i, $cls, $i );
		}
	}

	if ( $limit > $end ) { 
		if ( $limit - 1 > $end ) { 
			$htm = sprintf( "%s\n%s<li>...</li>", $htm, $idt1 );
		}
		$htm = sprintf( "%s\n%s<li><a href=\"%spage=%s\">%s</a></li>", $htm, $idt1, $get, $limit, $limit );
	}

	if ( $page < $limit ) { 
		$htm = sprintf( "%s\n%s<li class=\"next\"><a href=\"%spage=%s\">次へ &raquo;</a></li>", $htm, $idt1, $get, $next );
	}

	return sprintf( "%s<ul>%s\n%s</ul>", $idt, $htm, $idt );
}

