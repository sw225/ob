( function( U ) { 

	this.CHARSET = 'utf-8';
	this.H = { 
		'Content-Type'     : 'application/x-www-form-urlencoded;charset=' + this.CHARSET,
		'X-Requested-With' : 'XMLHttpRequest'
	};

	var self = this, 
		prm  = {
			'type': 'pie',
			'startDuration': 0,
			'titleField': 'title',
			'valueField': 'value',
			'labelRadius': 5,
			'radius': '38%', 
			'innerRadius': '60%',
			'labelText': '',
			'export': {
				'enabled': true
			},
			'colorField': 'color'
		}, 
		dat  = this.GD()
		;

	this.response = function( _r ) {

		var rsp = _r['rsp'], 
			i_  = self.$I, 
			n_  = self.$N, 
			hh  = self.HH( rsp ), 
			chart, 
			dnam = hh['domainn'], 
			dcnt = hh['domainc'], 
			i, 
			clr = '#34A477|#2F59B1|#69B1BE|#32BB36|#84B12F|#34A477|#2F3AB1|#15B2ED|#42BB32|#AEB12F'.split('|'), 
			ids = 'send_day|send_month|account_regist|chart_regist|account_active|chart_active|account_error|chart_error|chart_domain|domains'.split('|'), 
			htm = {};
			;

		prm['dataProvider'] = [ {
			'title': 'regist',
			'value': hh['regist'], 
			'color': '#47A7CF'
		}, {
			'title': 'limit',
			'value': ( hh['limit']　- hh['regist'] ), 
			'color': '#47CF8A'
		} ];
		chart = AmCharts.makeChart( 'chart_regist', prm );

		prm['dataProvider'] = [ {
			'title': 'active',
			'value': hh['regist'], 
			'color': '#47A7CF'
		}, {
			'title': 'regist',
			'value': ( hh['regist']　- hh['active'] ), 
			'color': '#47CF8A'
		} ];
		chart = AmCharts.makeChart( 'chart_active', prm );

		prm['dataProvider'] = [ {
			'title': 'error',
			'value': hh['error'], 
			'color': '#C73D3D'
		}, {
			'title': 'regist',
			'value': ( hh['regist']　- hh['error'] ), 
			'color': '#47A7CF'
		} ];
		chart = AmCharts.makeChart( 'chart_error', prm );

		prm['dataProvider'] = [];
		for ( i = dnam.length; i--; ) { 
			prm['dataProvider'][ prm['dataProvider'].length ] = { 
				'title': dnam[ i ],
				'value': dcnt[ i ], 
				'color': clr[ i ]
			};
			htm["{'tag':'LABEL','innerHTML':'"+dnam[ i ]+"','css':{'color':'"+clr[ i ]+"'}}"] = {};
		}
		chart = AmCharts.makeChart( 'chart_domain', prm );


		i_('send_day').innerHTML   = hh['day'];
		i_('send_month').innerHTML = hh['month'];

		i_('account_regist').innerHTML = hh['regist']+'/'+hh['limit'];
		i_('account_active').innerHTML = hh['active']+'/'+hh['regist'];
		i_('account_error').innerHTML  = hh['error']+'/'+hh['regist'];
			
		self.AE( i_('domains'), htm, {} );

		for ( i = ids.length; i--; ) { 
			i_( ids[ i ] ).style.opacity = '1';
		}
	};

	this.HR( { 
		'mtd'  : 'POST', 
		'pth'  : 'ctt/info/ajax.php', 
		'qry'  : {
			'yyyy' : dat['yyyy'], 
			'mm'   : dat['mm'],
			'dd'   : dat['dd']
		}, 
		'cbk'  : this.response
	} );


} ).apply( this.self );
