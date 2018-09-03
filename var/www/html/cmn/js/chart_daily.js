( function( U ) { 

	this.CHARSET = 'utf-8';
	this.H = { 
		'Content-Type'     : 'application/x-www-form-urlencoded;charset=' + this.CHARSET,
		'X-Requested-With' : 'XMLHttpRequest'
	};

	var self = this, 
		prm  = {
			'type': 'serial',
			'theme': 'light',
			'marginRight':30,
			'legend': {
				'equalWidths': false,
				'periodValueText': 'total: [[value.sum]]',
				'position': 'top',
				'valueAlign': 'left',
				'valueWidth': 100
			},
			'graphs': [{
				'balloonText': '<b>[[value]]</b>',
				'fillAlphas': 0.6,
				'lineAlpha': 0.4,
				'title': 'Send',
				'valueField': 'Send', 
				'lineColor': '#3B9FC5', 
				'bullet': 'round'
			}, {
				'balloonText': '<b>[[value]]</b>',
				'fillAlphas': 0.6,
				'lineAlpha': 0.4,
				'title': 'Error',
				'valueField': 'Error', 
				'lineColor': '#fc4528', 
				'bullet': 'round'
			}],
			'dataProvider': [], 
			'plotAreaBorderAlpha': 0,
			'marginTop': 10,
			'marginLeft': 0,
			'marginBottom': 0,
			'chartScrollbar': {},
			'chartCursor': {
				'cursorAlpha': 0
			},
			'categoryField': 'hour',
			'categoryAxis': {
				'startOnAxis': true,
				'axisColor': '#DADADA',
				'gridAlpha': 0.07
			}
		}, 
		req  = this.GR(), 
		dat  = this.GD()
		;

	this.response = function( _r ) {

		var rsp = _r['rsp'], 
			i_  = self.$I, 
			n_  = self.$N, 
			hh  = self.HH( rsp ), 
			tbl = n_('table'), 
			tr  = n_( 'tr', tbl[0] ), 
			td, 
			i, j, l, 
			ok, ng, on, 
			sum = {}, 
			key, 
			mk = [ 'sends', 'errors', 'pct' ], 
			chart
			;

		hh['pct'] = [];

		for ( i = 0; i < 24; i++ ) { 
			prm['dataProvider'][ i ] = {
				'hour':  i,
				'Send':  ok = hh['sends'][ i ],
				'Error': ng = hh['errors'][ i ],
			}
			hh['pct'][ i ] = ( !ok && !ng ? 0 : ( ng / ok * 100 ) | 0 );
		}
		i_('data').value = hh['dat'];


		for ( i = 1, l = tr.length; i < l; i++ ) { 
			key = mk[ i - 1 ];
			sum[ key ] = 0;
			td  = n_( 'td', tr[ i ] );
			for ( j = 1; j < 25; j++ ) { 
				on = hh[ key ][ j - 1 ];
				sum[ key ] += on;
				td[ j ].innerHTML = on;
			}
			td[ j ].innerHTML = ( 
				'pct' !== key ? 
					sum[ key ] : ( 
						!sum['sends'] && !sum['errors'] ? 
						0 : 
						( ( sum['errors'] / sum['sends'] * 100 ) | 0 ) 
					) 
			);
		}

		chart = AmCharts.makeChart( 'chart', prm );

		self.Trs = tr;
	};

	if ( U !== req['report_smtp_daily'] ) { 
		var dt = this.DT( req['report_smtp_daily'] );
		dat = dt || dat;
	}

	this.HR( { 
		'mtd'  : 'POST', 
		'pth'  : 'ctt/report_smtp_daily/ajax.php', 
		'qry'  : {
			'yyyy' : dat['yyyy'], 
			'mm'   : dat['mm'],
			'dd'   : dat['dd']
		}, 
		'cbk'  : this.response
	} );




	this.MouseOver = function( _txt ) { 
		if ( !isFinite( _txt ) ) { 
			return ;
		}
		var n_ = this.$N, 
			i  = 0, l, 
			tr = this.Trs, 
			td, 
			n  = _txt - 0
			;
		for ( i = 1, l = tr.length; i < l; i++ ) { 
			td = n_( 'td', tr[ i ] );
			td[ n + 1 ].style.backgroundColor = '#EBD2FD';
		}
	};


} ).apply( this.self );



