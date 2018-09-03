<?php


if ( !empty( $to ) && !empty( $title ) && !empty( $message ) ) { 
	$res_carrier_list = 
		$Inst -> 
			InsertOne( 
				array( 
					'tbl' => $TableQueue, 
					'dat' => 
						array( 
							'`to`'      => $to, 
							'from_name' => $from_name, 
							'title'     => $title, 
							'message'   => $message, 
							'activate'  => '1', 
							'error'     => '0', 
							'mail'      => '0'
						)
				)
			);
}

