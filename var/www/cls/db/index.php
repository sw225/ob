<?php

//header('Content-type: text/plain; charset=UTF-8');

CLASS DB {

	var $Pdo;

	function __construct( $_db ) {
		extract( $_db );
		$this -> Pdo = null;
		$this -> Pdo = new PDO( "mysql:host={$host}; dbname={$db}", $user, $pass );
		$this -> Pdo -> query('SET NAMES utf8');
		return $this;
	}


	function Sql( $_argv ) { 
		$res = $this -> Pdo -> query( $_argv );
		return $res;
	}


	# get AUTO_INCREMENT #
	function getAI( $_argv ) {
		# `` だとエラーはく
		$sql　= implode( ' ', array( 'SHOW TABLE STATUS LIKE ', $_argv['tbl'] ) );
		$qry　= $this -> Sql( $sql );
		$row　= $qry -> fetch( PDO::FETCH_ASSOC );
		$qry -> closeCursor();
		return ( $row ) ? (int)$row['Auto_increment'] : 0 ;
	}


	function CountOne( $_argv ) {
		$res	= array();
		$qry	= $this -> Sql( $_argv );
		while ( $row = $qry -> fetch( PDO::FETCH_ASSOC ) ) {
			$res[]	= $row;
		}
		$qry -> closeCursor();
		return $res;
	}


	# return array #
	function SelectAll( $_argv ) {
		$res	= array();
		$qry	= $this -> Sql( $_argv );
		while ( $row = $qry -> fetch( PDO::FETCH_ASSOC ) ) { 
			$res[]	= $row;
		}
		$qry -> closeCursor();
		return $res;
	}



	function SelectOne( $_argv ) {
		$res	= false;
		$qry	= $this -> Sql( $_argv );
		while ( $row = $qry -> fetch( PDO::FETCH_ASSOC ) ) {
			$res	= $row;
		}
		$qry -> closeCursor();
		return $res;
	}



	function Update( $_argv ) {
		$tbl = $_argv['tbl'];
		$dat = $_argv['dat'];
		$whr = $_argv['whr'];
		$sql = implode(' ', array( 'UPDATE', $tbl, 'SET', $dat, 'WHERE', $whr ) );
		$qry = $this -> Sql( $sql );
		$qry -> closeCursor();
		return $qry;
	}


	function UpdateId( $_argv ) {
		$tbl = $_argv['tbl'];
		$dat = $_argv['dat'];
		$id  = $_argv['id'];
		$sql = implode(' ', array( 'UPDATE', $tbl, 'SET', $dat, 'WHERE', 'id', '=', $id ) );
		$qry = $this -> Sql( $sql );
		$qry -> closeCursor();
		return $qry;
	}


	# Insert ( { tbl -> 'table', clm -> ( clm ), val -> ( val ) } ) #
	function Insert( $_argv ) {
		$tbl = $_argv['tbl'];
		$clm = $_argv['clm'];
		$val = $_argv['val'];
		$sql = "INSERT INTO {$tbl} {$clm} VALUES {$val};";
		$qry = $this -> Sql( $sql );
		$qry -> closeCursor();
		return $qry;
	}


	# Insert IGNORE ( { tbl -> 'table', dat -> '( date ), ( date ) ...' } ) #
	function InsertIg( $_argv ) {
		$tbl = $_argv['tbl'];
		$dat = $_argv['dat'];
		$qry = $this -> Sql( "INSERT IGNORE INTO {$tbl} VALUES {$dat};" );
		$qry -> closeCursor();
		return $qry;
	}



	function __destruct() {
		$this -> Pdo = null;
	}

}

