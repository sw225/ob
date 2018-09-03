<?php

//header('Content-type: text/plain; charset=UTF-8');

CLASS WARP extends DB {

	var $Inst;

	function __construct( $_cnt ) {
		$this -> Inst = parent::__construct( $_cnt );
	}


	function Quote( $_str ) {
		return $this -> Pdo -> quote( $_str );
	}

	function DbConf( $_argv ) {
		$qry = $this -> Inst -> Sql( "SHOW DATABASES LIKE '{$_argv}'" );
		$row = $qry -> fetch( PDO::FETCH_ASSOC ); 
		$qry -> closeCursor();
		return $row ? true : false ;
	}

	function TableConf( $_argv ) {
		$db	 = $_argv['db'];
		$tbl = $_argv['tbl'];

		$qry = $this -> Inst -> Sql( "SHOW TABLES FROM {$db} LIKE '{$tbl}';" );
		$row = $qry -> fetch( PDO::FETCH_ASSOC );
		$qry -> closeCursor();
		return $row ? true : false ;
	}

	function TableList( $_argv ) { 
		$db		= $_argv['db'];
		$tbls	= $this -> Inst -> SelectAll( "SHOW TABLES FROM {$db};" ) ;
		$ary	= array() ;
		if ( $tbls ) {
			$ary = 
				array_map( 
					function( $_ary ) { 
						$v = array_values( $_ary ) ;
						return $v[0] ;

					}, 
				$tbls ) ;
		}
		return $ary;
	}

	function TableListMatch( $_argv ) {
		$db		= $_argv['db'];
		$ptn	= $_argv['ptn'];
		$dat	= array();

		$res	= $this -> Inst -> SelectAll( "SHOW TABLES FROM `{$db}` LIKE '{$ptn}';" );
		foreach ( $res as $ary ) { 
			foreach ( $ary as $v ) { 
				$dat[] = $v ;
			}
		}
		return $dat ;
	}


	function DbName( $_argv ) {
		$sql	= implode(' ', array( 'SELECT', $_argv['clm'], 'FROM', $_argv['tbl'] ) );

		if ( isset( $_argv['whr'] ) && !empty( $_argv['whr'] ) ) {
			$sql	= implode(' ', array( $sql, 'WHERE', $_argv['whr'] ) );
		}

		return $this -> Inst -> SelectOne( $sql );
	}

	function InsertOne( $_argv ) {
		$dat	= $_argv['dat'];

		$k		= array_keys( $dat );
		$k[]	= 'created';
		array_unshift( $k, 'id' );
		$clm	= implode( ' ', array( '(', implode(',', $k ), ')' ) );

		$v		= array_values( $dat );
		foreach ( $v as &$s ) { 
			$s	=  
				( '' !== $s ) ? 
					( is_numeric( $s ) ? $s : $this -> Quote( $s ) ) : 
					"''" ;
		}
		$v[]	= 'CURRENT_TIMESTAMP';
		array_unshift( $v, "NULL" );
		$val	= implode( ' ', array( '(', implode(',', $v ), ')' ) );

		$prm	= 
			array(
				'tbl'	=> $_argv['tbl'], 
				'clm'	=> $clm, 
				'val'	=> $val
			);

		return $this -> Inst -> Insert( $prm );
	}




	function UpdateOne( $_argv ) {
		$dat	= $_argv['dat'];
		$res	= 
			array_map(
			 	function( $_k, $_v ) { 
					$dt = $_v;
					if ( !is_numeric( $_v ) ) { 
						if ( !preg_match( "/{$_k}[ |\t]*\+[ |\t]*\d/i", $_v ) ) { 
							$dt = "'{$_v}'";
						}
			    	}
					return sprintf( '`%s` = %s', $_k, $dt );
				}, 
				array_keys( $dat ), 
				array_values( $dat )
			);
		return $this -> 
					Inst -> 
						Update( 
							array(
								'tbl'	=> $_argv['tbl'], 
								'dat'	=> implode( ', ', $res ), 
								'whr'	=> $_argv['whr']
							)
						);
	}


	function IncrementOne( $_argv ) { 
		$dat	= $_argv['dat'];
		$res	= 
			array_map(
			    function( $_k, $_v ) { 
					return implode(' ', array( "`{$_k}`", '=', "{$_v}" ) );
				}, 
			    array_keys( $dat ), 
			    array_values( $dat )
			);
		return $this -> 
					Inst -> 
						Update( 
							array(
								'tbl'	=> $_argv['tbl'], 
								'dat'	=> implode( ', ', $res ), 
								'whr'	=> $_argv['whr']
							)
						);
	}


	function CountRecord( $_argv ) {
		$tbl	= $_argv['tbl'];

		$sql	= implode( ' ', array( 'SELECT', 'COUNT(*) AS i', 'FROM', $tbl ) );

		if ( isset( $_argv['whr'] ) && !empty( $_argv['whr'] ) ) {
			$sql	= implode(' ', array( $sql, 'WHERE', $_argv['whr'] ) );
		}

		$res	= $this -> Inst -> CountOne( $sql ) ;
		return ( $res ) ? (int)$res[0]['i'] : 0 ;

	}


	function DeleteOne( $_argv ) {
		$sql	= implode(' ', array( 'DELETE FROM', $_argv['tbl'] ) );
		if ( isset( $_argv['whr'] ) && !empty( $_argv['whr'] ) ) {
			$sql	= implode(' ', array( $sql, 'WHERE', $_argv['whr'] ) );
		} 
		else { 
			return ;
		}

		$qry = $this -> Inst -> Sql( $sql );
		$qry -> closeCursor();
		return $qry;
	}


	function DeleteAll( $_argv ) {
		$sql	= implode(' ', array( 'DELETE FROM', $_argv['tbl'] ) );

		$qry = $this -> Inst -> Sql( $sql );
		$qry -> closeCursor();
		return $qry;
	}


	function ListAllRecode( $_argv ) {
		$sql	= implode(' ', array( 'SELECT', $_argv['clm'], 'FROM', $_argv['tbl'] ) );

		if ( isset( $_argv['whr'] ) && !empty( $_argv['whr'] ) ) {
			$sql	= implode(' ', array( $sql, 'WHERE', $_argv['whr'] ) );
		}
		if ( isset( $_argv['oby'] ) && !empty( $_argv['oby'] ) ) {
			$sql	= implode(' ', array( $sql, 'ORDER BY', $_argv['oby'] ) );
		}
		if ( isset( $_argv['lmt'] ) && !empty( $_argv['lmt'] ) ) {
			$sql	= implode(' ', array( $sql, 'LIMIT', $_argv['lmt'] ) );
		}

		return $this -> Inst -> SelectAll( $sql );

	}


	function ListAllRecodeJoin( $_argv ) {
		$sql	= sprintf( 'SELECT %s FROM %s INNER JOIN %s ON %s', $_argv['clm'], $_argv['tbl'], $_argv['jin'], $_argv['on'] );

		if ( isset( $_argv['whr'] ) && !empty( $_argv['whr'] ) ) {
			$sql	= implode(' ', array( $sql, 'WHERE', $_argv['whr'] ) );
		}
		if ( isset( $_argv['oby'] ) && !empty( $_argv['oby'] ) ) {
			$sql	= implode(' ', array( $sql, 'ORDER BY', $_argv['oby'] ) );
		}
		if ( isset( $_argv['lmt'] ) && !empty( $_argv['lmt'] ) ) {
			$sql	= implode(' ', array( $sql, 'LIMIT', $_argv['lmt'] ) );
		}

		return $this -> Inst -> SelectAll( $sql );

	}

	function JoinClm( $_argv ) { 
		$clm  = array();
		foreach( $_argv as $k => $ary ) { 
			foreach( $ary as $v ) {
				$clm[] = sprintf( '%s.%s', $k, $v );
			}
		}
		return implode( ', ', $clm );
	}



	function DistinctList( $_argv ) {
		$sql	= implode(' ', array( 'SELECT DISTINCT ', $_argv['clm'], 'FROM', $_argv['tbl'] ) );

		if ( isset( $_argv['whr'] ) && !empty( $_argv['whr'] ) ) {
			$sql	= implode(' ', array( $sql, 'WHERE', $_argv['whr'] ) );
		}
		if ( isset( $_argv['oby'] ) && !empty( $_argv['oby'] ) ) {
			$sql	= implode(' ', array( $sql, 'ORDER BY', $_argv['oby'] ) );
		}
		if ( isset( $_argv['lmt'] ) && !empty( $_argv['lmt'] ) ) {
			$sql	= implode(' ', array( $sql, 'LIMIT', $_argv['lmt'] ) );
		}

		return $this -> Inst -> SelectAll( $sql );
	}




	function TempUpdate( $_argv ) { 
		extract( $_argv );

		$keys = array_keys( $clm );
		$vals = array_values( $clm );

		$mk_tmp = 
			array_map(
				function( $_k, $_v ) { 
					return sprintf( '%s %s', $_k, $_v );
				},
				$keys, 
				$vals
			);
		$tmp = 'CREATE TEMPORARY TABLE tmp(id INT(11), %s);';
		$tmp = sprintf( $tmp, implode( $mk_tmp, ', ' ) );
#var_dump($tmp);

		$mk_rcd = array();
		$rcd = 'INSERT INTO tmp( id, %s ) VALUES( %s );';
		$rcd = sprintf( $rcd, implode( $keys, ', ' ), '%s' );
		foreach ( $ids as $v ) { 
			$buf = array();
			foreach ( $keys as $k ) { 
				$buf[] =  is_numeric( $dat[ $k.$v ] ) ? $dat[ $k.$v ] : $this -> Quote( $dat[ $k.$v ] ) ;
			}
			array_unshift( $buf, $v );
			$mk_rcd[] = sprintf( $rcd, implode( $buf, ', ' ) );
		}
		$rcd = implode( $mk_rcd, PHP_EOL );
#var_dump($rcd);

		$udt = 'UPDATE %s INNER JOIN tmp ON %s.id=tmp.id SET %s;';
		$udt = sprintf( $udt, $tbl, $tbl, '%s' );
		$mk_udt = 
			array_map(
				function( $_k, $_tbl ) {
					return sprintf( '%s.%s=tmp.%s', $_tbl, $_k, $_k );
				},
				$keys, 
				array_fill( 0, count( $clm ), $tbl )
			);
		$udt = sprintf( $udt, implode( $mk_udt, ', ' ) );
#var_dump($udt);

#var_dump(implode( array( $tmp, $rcd, $udt ), PHP_EOL ));

		$qry = $this -> Inst -> Sql( implode( array( $tmp, $rcd, $udt ), PHP_EOL ) ) ;
		$qry -> closeCursor();
#var_dump($qry);
		return $qry;
	}



	# csv import # 
	function CsvImport( $_argv ) { 
		$dat	= $_argv['dat'];
		$ids	= $_argv['ids'];

		$co		= 0;
		$updt	= array();
		$upid   = array();

		foreach ( $dat as $ary ) {
			if ( 3 > count( $ary ) ) { 
				continue ;
			}
			if ( empty( $ary[0] ) || empty( $ary[1] ) || empty( $ary[2] ) ) { 
				continue ;
			}
			if ( !isset( $ids[ $co ] ) ) { 
				continue ;
			}
			$id = $ids[ $co ];
			$upid[] = $id;

			$domain = explode( '@', $ary[0] );
			$domain = $domain[1];	#str_replace( '.', '', $domain[1] );
			$updt[ "domain{$id}" ]      = $domain;
			$updt[ "address{$id}" ]     = $ary[0];
			$updt[ "user{$id}" ]        = $ary[1];
			$updt[ "pass{$id}" ]        = $ary[2];
			$updt[ "from_name{$id}" ]   = '';
			$updt[ "send_length{$id}" ] = 0;
			$updt[ "error_count{$id}" ] = 0;
			$updt[ "error_txt{$id}" ]   = '';
			$updt[ "error_time{$id}" ]  = '0000-00-00 00:00:00';
			$updt[ "memo{$id}" ]        = '';
			$updt[ "activate{$id}" ]    = 0;
			$updt[ "created{$id}" ]     = date('Y/m/d H:i:s');
			++$co;
		}


		$clm = array( 
			'domain'      => 'VARCHAR(256)',
			'address'     => 'VARCHAR(256)',
			'user'        => 'VARCHAR(256)',
			'pass'        => 'VARCHAR(256)', 
			'from_name'   => 'TEXT', 
			'send_length' => 'INT(11)', 
			'error_count' => 'INT(11)', 
			'error_txt'   => 'TEXT', 
			'error_time'  => 'DATETIME', 
			'memo'        => 'TEXT', 
			'activate'    => 'INT(1)', 
			'created'     => 'DATETIME'
		);

		return 
			$this -> Inst -> 
				TempUpdate(
					array( 
						'tbl' => $_argv['tbl'], 
						'clm' => $clm, 
						'dat' => $updt, 
						'ids' => $upid
					)
				);
	}




	
/*
	# csv import # 
	function CsvImport( $_argv ) { 
		$dat	= $_argv['dat'];

		$qry	= array();

		# insert用 date 生成 #
		foreach ( $dat as $ary ) {
			if ( 3 > count( $ary ) ) { 
				continue ;
			}
			if ( empty( $ary[0] ) || empty( $ary[1] ) || empty( $ary[2] ) ) { 
				continue ;
			}


			$domain = explode( '@', $ary[0] );
			$domain = $domain[1];	#str_replace( '.', '', $domain[1] );
			$ones   = 
				array(
					/* id			/ 'NULL', 
					/* domain		/ "'{$domain}'", 
					/* address		/ "'{$ary[0]}'", 
					/* user			/ "'{$ary[1]}'", 
					/* pass			/ "'{$ary[2]}'", 
					/* from_name	/ "''", 
					/* send_length	/ "0", 
					/* error_count	/ "0", 
					/* error_txt	/ "''", 
					/* error_time	/ "'0000-00-00 00:00:00'", 
					/* memo			/ "''", 
					/* activate		/ "0", 
					/* created		/ "'" .date('Y/m/d H:i:s'). "'"
				);
			$qry[]	= '( ' .implode( $ones, ', ' ). ' )';

		}

		# insert 重複無視 #
		return $this -> 
			   		Inst -> 
						InsertIg(
							array(
								'tbl'	=> $_argv['tbl'], 
								'dat'	=> implode( $qry, ', ' )
							)
						);
	}
*/


	function __destruct() {
	}

}



