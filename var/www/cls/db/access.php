<?php


CLASS ACCESS extends DB {

	var $Inst;

	function __construct( $_cnt ) {
		$this -> Inst = parent::__construct( $_cnt );
		
		return $this;
	}

	function AccessIp( $_ip ) {
		$flg	= false;
		$res	= $this -> Inst -> Sql( 'SELECT address FROM ip' );
		while ( $r = $res -> fetch( PDO::FETCH_ASSOC ) ) { 
			if ( $_ip === $r['address'] ) {
				$flg	= true;
				break ;
			}
		}
		return $flg ;
	}


	function __destruct() {
	}

}

