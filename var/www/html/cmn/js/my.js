( function( W, N, D, U ) { 
	var ns	= W[ N ], _;
	if ( !ns ) {
		ns	= {};
		W[ N ] = ns;
	}
	ns.cs	= cs;

	function cs() {
		this.W	= W;
		this.D	= D	= D || document ;

	};


	cs.prototype = { 
		$I	:	/*	getElementById ( id, parent ) */
			function( _v, _p ) {
				return ( _p || D || document ).getElementById( _v );
			}, 
		$N	:	/*	getElementsByTagName ( tag name, parent ) */
			function( _v, _p ) {
				return ( _p || D || document ).getElementsByTagName( _v );
			}, 
		$L	:	/*	getElementsByName ( name, parent ) */
			function( _v, _p ) {
				return ( _p || D || document ).getElementsByName( _v );
			}, 
		AE	:	/*	add element( parent, html, objects )	*/
			function( _p, _h, _o ) {
				var l, e, h, i, c
					;
				_	= _ || this;

				for ( l in _h ) {
					h = eval( '(' + l + ')' );
					e	= ( D || document ).createElement( h['tag'] );
	
					for ( i in h ) {
						if ( 'css' === i ) {
							for ( c in h[ i ] ) {
								e.style[ c ] = h[ i ][ c ];
							}
						} else 
						if ( 'event' === i ) {
							for ( c in h[ i ] ) {
								_.AD( e, h[ i ][ c ], c );
							}
						} else 
						if ( 'class' === i ) {
							e[ _.Cls ] = h[ i ];
						} else 
						if ( 'another' === i ) {
							for ( c in h[ i ] ) {
								e.setAttribute( c, h[ i ][ c ] );
							}
						} else {
							if ( i !== 'tag' ) {
								e[ i ] = h[ i ];
							}
						}
					}
					
					if ( U === h['id'] ) {
						h['id']	= ( h['tag'] + _.$N( h['tag'] ).length );
					}

					_o[ h['id'] ]	= _p.appendChild( e );
						
					_.AE( e, _h[ l ], _o );
				}
				return _o;
			},
		AD	:	/*	add event ( target, function, event )	*/
			function( _t, _f, _e, on ) { 
				if ( _t.addEventListener ) {
					_t.addEventListener( _e, _f, true );
				} 
				else {
					on = 'on' + _e;
					if ( _t.attachEvent ) {
						_t.attachEvent( on, _f );
					} else {
						_t[ on ] = _f;
					}					
				}
			}, 
		GA	:	/*	get attribute ( target, name )	*/
			function( _t, _n ) { 
				return _t[ _n ] || _t.getAttribute( _n ) || '' ;
			}, 
		CM	:	/*	confirm	*/
			function( _s ) {
				if ( ( W || window ).confirm( _s ) ) {
					return true ;				
				}
				return false ;			
			}, 
		EC	:	/*	event cancel ( event )	*/
			function( _e ) {
				if ( _e.preventDefault || _e.stopPropagation ) {
					_e.preventDefault();
					_e.stopPropagation();
				} 
				else {
					_e.cancelBubble	= true;
					_e.returnValue	= false;
				}
			}, 
		HR	:	/* http request ( { mtd:method, pth:path, qry:{query-name:query-value}, cbk:callback, cls,crossdomain } ) */
			function( _h ) {
				_ = _ || this ;
				var i	= false, 
					b	= false, 
					d	= 'Msxml2.XMLHTTP', 
					c	= [ 'Microsoft.XMLHTTP', ( d + '.3.0' ), ( d + '.6.0' ) ];
				if ( U !== _h['cls'] ) {
					try {
						new XDomainRequest();
						i = b = true;
					} catch( a ) {
						try{
							new ActiveXObject( d );
							i	= true;
						} catch( h ) {}
					}
				}
				var g = (
					function( j ) { 
						if ( i ) {
							for ( var k = c.length; k--; ) {
								try {
									new ActiveXObject( c[ k ] );
									j	= function() { 
											return new ActiveXObject( c[ k ] ); 
										};
									break;
								} catch( l ) {}
							}
						}
						else {
							j = function() {
									return new XMLHttpRequest();
								};
						}
			
						if ( b ) {
							return ( 
								function( _e ) {
									if ( 1 === _e ) {
										var m = new XDomainRequest();
											m.ie = 0;
										return m;
									}
									return j();
								}
							);
						}
						return j;
					}
				)();

				var f = function( _j, _e ) {
							if ( U !== _e.overrideMimeType && U !== this.CHARSET ) {
								_e.overrideMimeType( 'text/plain; charset=' + this.CHARSET );
							}
							/*
							if ( _j === 'POST' ) {
								_e.setRequestHeader( 'Content-type', 'application/x-www-form-urlencoded' );
							}*/
							_.SH( _e, _.H );
						};

				( function() {
					var q	= _h['mtd'],
						l	= _h['pth'],
						p	= _h['qry'],
						o	= _h['cbk']
						;
					q	= q.toUpperCase();
					l	+= ( ( -1 === l.indexOf('?') ) ? '?' : '&' ) + ( new Date().getTime() );

					var r	= {
							'rsp'	: '', 
							'p'		: _h
						}, 
						m	= ''
						;
					if ( p && typeof p === 'object' ) {
						for ( var j in p ) { 
						/*	m	+= ( '&' + j + '=' + encodeURIComponent( p[ j ] ) );	*/
							m	+= ( '&' + j + '=' + p[ j ] );
						}
					}
					else {
						m	= 	p;
					}
					if ( 'GET' === q ) {
						l	+= m;
						m	= null;
					}
					if ( U === o ) { 
						var e = g();
							e.open( q, l, false );
						f( q, e );
						e.send( m );
						
						r['rsp']	= e.responseText;
						
						return r;
					}
					else {
						var e = g( ( /^https?:\/\//i.test( l ) ) ? 1 : 0 );
						if ( e.ie == 0 ) {
							e.onprogress = 
								function() {
								};
							e.onload = 
								function() {
									r['rsp']	= e.responseText;
									o( r );
								};
							e.open( q, l );
						}
						else{
							e.open( q, l, true );
							e.onreadystatechange = 
								function() { 
									if ( 4 === e.readyState ) {
										if ( 200 === e.status ) {
											r['rsp']	= e.responseText;
										}
										else {
											if ( U !== this.HttpError ) {
												this.HttpError( _h );
											} 
											else {
												if ( U !== e ) {
													e.abort();
												}
											}
										}
										o( r );
									};
								}
						}
						f( q, e );
						e.send( m );
					}
				})();
			}, 
		SH	:	/*	set http header ( http, header )	*/
			function( _h, _r, k ) {
				for ( k in _r ) {
					_h.setRequestHeader( k, _r[ k ] );
				}
				return _h;
			}, 
		TO	:	/*	request time out check ( http )	*/
			function( _h ) {
				var c_	= this.C
					;
				if ( 0 > --this.error_count ) {
					c_( this.interval_id );
					if ( U !== _h ) {
						_h.abort();
					}
					return ;
				}
			}, 
		HH	:	/*	hash cast ( { hash } ) */
			function( _h ) { 
				return ( new Function( 'return ' + _h ) )();
			}, 
		GD	:	/*	get days ( new date, zero flag, start, len )	*/
			function( _n, _z, _s, _l ) {
				var zp_ = this.ZP;

				_n = _n || new Date();
				_z = _z || 1;
				_s = _s || 0 ;
				_l = _l || 5 ;

				var g = [ 'getYear', 'getMonth', 'getDate', 'getHours', 'getMinutes', 'getSeconds' ], 
					s = [ 'yyyy', 'mm', 'dd', 'hh', 'ii', 'ss' ], 
					p = [ 1900, 1 ], 
					r = {}, 
					i
					;

				for ( i = _s; i < _l; i++ ) {
					r[ s[ i ] ]	= _n[ g[ i ] ]();
					r[ s[ i ] ]	+= ( U !== p[ i ] ) ? ( ( 1900 > r[ s[ i ] ] ) ? p[ i ] : 0 ) : 0 ;
					if ( _z && 'yyyy' !== s[ i ] ) {
						r[ s[ i ] ]	= zp_(  r[ s[ i ] ] );
					}
				}
				return r;	
			}, 
		ZP	:	/*	zero padding( str ) */
			function( _s ) {
				return ( '0' + _s ).slice(-2);
			}, 
		GR	:	/*	get get request ( url )	*/
			function( _p ) {
				var s	= _p || W.location.search, 
					g	= []
					;
				if ( 1 < s.length ) {
					var r, i, p
						;
					if ( -1 < s.indexOf('?') ) {
						s	= s.split('?')[1];
					}
					r	= s.split('&');

					for ( i = 0; i < r.length; i++ ) {
						p	= r[i].split('=');
						g[ p[0] ]	= p[1];
					}
				}
				return g;		
			}, 
		DT	:	/* is date ( string ) */
			function( _s ) { 
				var zp_ = this.ZP, 
					mac = _s.match(/^(\d{4})[\/|-](\d{1,2})[\/|-](\d{1,2})(?: (\d{1,2}):(\d{1,2}):(\d{1,2}))?$/)
					;
				if ( mac ) { 
					var s = [ mac[1], zp_( mac[2] ), zp_( mac[3] ) ].join('/');
					if ( mac[4] ) { 
						s += ' ' + mac[4];
						s += ( mac[5] || 0 == mac[5] ) ? ':' + mac[5] : '';
						s += ( mac[6] || 0 == mac[6] ) ? ':' + mac[6] : '';
					} 
					else { 
						s += ' 00:00:00';
						mac[4] = '00';
						mac[5] = '00';
						mac[6] = '00';
					}

					d = new Date( s );
					if ( mac[1] == d.getFullYear() && ( mac[2] - 1 ) == d.getMonth() && mac[3] == d.getDate() && 
						mac[4] == d.getHours() && mac[5] == d.getMinutes() && mac[6] == d.getSeconds() ) { 
						return { 
							'yyyy' : mac[1], 
							'mm'   : mac[2], 
							'dd'   : mac[3], 
							'hh'   : mac[4], 
							'ii'   : mac[5], 
							'ss'   : mac[6]
						};
					}
				}
				return ;		
			}
	};


	( function( _ ) {

		var g_check = false, 
			g_point = 0
			;

		W.self = _;

		_.load = function() { 

			_.mk_check();

			_.mk_del_conf();

			_.shift_checked();

		};


		_.shift_checked = function() { 
			var n_  = _.$N, 
				ad_ = _.AD, 
				inp = n_('input'), 
				id, nam
				reg = new RegExp('\\d+')
				;

			if ( inp && inp.length ) { 

				for ( var i = inp.length; i--; ) { 
					if ( 'object' === typeof inp[ i ] ) { 
						if ( 'type' in inp[ i ] ) { 
							if ( 'checkbox' === inp[ i ].type ) { 
								if ( id = inp[ i ].id ) { 
									nam = id.replace( reg, '' );
									ad_( 
										inp[ i ], 
										( function( _inp, _id, _nam ) { 
												return function( e ) { 
													_.scheck( [ e, _inp, _id, _nam ] ); 
												}; 
										} )( inp[ i ], id, nam ), 
										'click'
									);
								}
							}
						}
					}
				}

			}
		};


		_.scheck = function( argv ) { 
			var e   = argv[0], 
				tgt = argv[1], 
				flg = false
				;
			if ( 'ctrlKey' in e && e.ctrlKey ) { 
				var n_  = _.$N, 
					i_  = _.$I, 
					inp = n_('input'), 
					nam = argv[3], 
					i   = g_point, 
					e   = argv[2].replace( nam, '' ), 
					l   = inp.length, 
					chk = g_check
					;
				if ( i == e ) { 
					return ;
				}
				if ( i < e ) { 
					for ( ; i < l; i++ ) { 
						if ( i <= e && ( inp = i_( nam+i ) ) ) { 
							inp.checked = chk;
						}					
					}
				} 
				else { 
					for ( ; i--; ) { 
						if ( i >= e && ( inp = i_( nam+i ) ) ) { 
							inp.checked = chk;
						}					
					}
				}
			} 
			else { 
				g_check = tgt.checked;
				g_point = argv[2].replace( argv[3], '' );
			}
		};


		_.mk_del_conf = function() { 
			var i_  = _.$I, 
				ad_ = _.AD, 
				btn = i_('btn_del'), 
				i
				;
			if ( btn ) { 
				ad_( 
					btn, 
					( function() { 
							return function( e ) { 
								if ( !_.CM('削除してもよろしいですか？') ) { 
									_.EC( e );
								}
							}; 
					} )(), 
					'click'
				);
			}
		};


		_.mk_check = function() { 
			var n_  = _.$N, 
				ga_ = _.GA, 
				ad_ = _.AD, 
				tbl = n_('table'), 
				tr, inp, 
				id, 
				i
				;
			if ( tbl.length ) { 
				tr  = n_( 'tr', tbl[0] );
				if ( tr.length ) { 
					inp = n_( 'input', tr[0] );
					if ( i = inp.length ) { 
						for ( ; i--; ) { 
							if ( id = ga_( inp[ i ], 'id' ) ) { 
								ad_( 
									inp[ i ], 
									( function( _id ) { return function() { _.checked( _id ); }; } )( id ), 
									'click'
								);
							}
						}
					}
				}
			}
		};


		_.checked = function( _tgt ) { 
			var ga_ = _.GA, 
				inp = _.$N('input'), 
				chk = _.$I( _tgt ).checked, 
				i
				;
			for ( i = inp.length; i--; ) { 
				if ( -1 !== ga_( inp[ i ], 'id' ).indexOf( _tgt ) ) { 
					inp[ i ].checked = chk;
				}
			}
		};


_.aaaaaaa = function(){};

		_.AD( W, _.load, 'load' );

	} )( new cs() ) ;

} )( this, 'relay' );



function cl( _s ) {
	console.log( _s );
};

