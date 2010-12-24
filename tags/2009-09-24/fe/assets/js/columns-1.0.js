/* namespace Twit */
$Y.namespace('twitTangle.Columns');

// scope
(function(){

	$Y.twitTangle.Columns = function(p) {
		this.init(p);
	}

	$Y.twitTangle.Columns.prototype = {
	
		// params
		params : {},
		flags: {},
		columns: {},
		count : 0,	
		tick: false,
	
		// init
		init : function(p) {
			
			// params
			this.params = p;
		
			// when we click
			$e.on('open-columns','click',function(e) {
			
				// stop
				$e.stopEvent(e);
			
				// page it 
				document.location.href = "#p=home/columns";	
			
				// setup 
				this.setup(); 
				
				
			},this,true);
		
		},
		
		// click
		click : function(e) {
			
			// get target
			var tar = oTar = $e.getTarget(e);
		
			// check for 
			if ( tar.tagName.toLowerCase() == 'a' && (tar = $d.isGoodTarget(oTar,'side-menu-top')) ) {
				
				// stop
				$e.stopEvent(e); oTar.blur();
				
				// no side menu expand
				if ( $d.hasClass(tar,'side-menu-expand') ) {
					return;
				}
				
				// get an md5 of the href
				var id = md5(oTar.getAttribute('href'));
				
				// attach the id
				var li = $d.isGoodTarget(oTar,{'tag':'li'});
				
				// add id 
				li.id = 'link-'+id;
				
				// add class
				$d.addClass(li,'column-open');
				
				// check if this is open
				if ( this.columns[id] ) {
					this.goto(id); return;
				}
				
				// create it 
				this.create({
						'title': oTar.innerHTML,
						'uri': oTar.getAttribute('href') 
					});
				
			}
			else if ( $d.hasClass(oTar,'close') ) {
			
				// stop
				$e.stopEvent(e);
			
				// get module
				var column = $d.isGoodTarget(oTar,'column');
			
				// close 
				this.destroy( column.id.replace(/column-/,'') ); 
			
			}			
			
		},
		
		// mouse
		mouse : function(e,type) {
		
			// target
			var tar = oTar = $e.getTarget(e);
			
			// right arrow
			if ( $d.hasClass(tar,'column-right-arrow') ) {
			
				// clear active
				$d.removeClass($d.getElementsByClassName('column','div',$('page-content')),'active');				
			
				if ( type == 'out' ) {
					clearInterval(this.flags.scrollL);
				}
				else {
					this.flags.scrollL = window.setInterval(function(){
						$d.setX('page-content',$d.getX('page-content')+20)
					},50);
				}
			}
			else if ( $d.hasClass(tar,'column-left-arrow') ) {
			
				// clear active
				$d.removeClass($d.getElementsByClassName('column','div',$('page-content')),'active');							
			
				if ( type == 'out' ) {
					clearInterval(this.flags.scrollL);
				}
				else {
					this.flags.scrollL = window.setInterval(function(){
						$d.setX('page-content',$d.getX('page-content')-20)
					},50);
				}
			}		
		
		},
		
		// start
		setup : function() {	
		
			// disable ajax loading
			TT.Global.disableAjaxLoad = true;
			TT.Global.endTimelineWatch();
			TT.data.timeline = {};
			
			// attach our event to page content
			$e.on('bd','click',this.click,this,true);
			$e.on('doc4','mouseover',function(e){ this.mouse(e,'over'); },this,true);
			$e.on('doc4','mouseout',function(e){ this.mouse(e,'out'); },this,true);
		
			// set the style
			$d.setStyle('side-menu','position', 'absolute' );			
			$d.setStyle('side-menu','width', '200px' );
			$d.setXY('side-menu', $d.getXY('side-menu') );
		
			// open all
			$d.addClass( $d.getElementsByClassName('side-menu-top','li', $('side-menu') ), 'open' );
			
			// pop out the side menu
			$d.insertAfter( $('side-menu'), $('page-content') );	
		
			var y = $d.getY('page-content');
		
			// move the menu there
			var a = new $a('side-menu',{ 'top': {'to': y+20 }, 'left': {'to':20 }},.5);
				a.animate();
			
			// fade
			var a = new $a('page-content',{'opacity':{'to':0}},.5);
				a.onComplete.subscribe(function(){
					
					// relive
					$d.setStyle('bd','position','relative');
					$d.setStyle('side-menu','top','20px');							
						
					// make bd full page
					$d.setStyle('bd','width', 'auto' );
					
					// no content
					$('page-content').innerHTML = "";
					
					// no style
					$d.setStyle('page-content','opacity',1);
					
					// add page class
					$d.addClass( document.getElementsByTagName('body')[0] ,'columns');
					
					// add our arrows 
					var ra = new $el('a');
					
						// set 
						ra.addClass('column-right-arrow').html("&gt;");
					
					// add our arrows 
					var la = new $el('a');
					
						// set 
						la.addClass('column-left-arrow').html("&lt;");
					
					// append
					$('doc4').appendChild(ra.get());
					$('doc4').appendChild(la.get());
					
					// backing 
					var hide = new $el('div');
						hide.addClass('column-hider');
					
					// append
					hide.appendTo($('bd'));					
					
					// if none
					if ( TT.data.columnSettings.columns.length == 0 ) {
					
						// find out what's on
						var on = $d.getElementsByClassName('on','li',$('side-menu'))[0];
						
							
							// nothing on
							if ( !on ) {
							
								// get list
								on = $d.getElementsByClassName('side-menu-top')[0].getElementsByTagName('li')[0];
														
							}
													
						// get link
						var link = on.getElementsByTagName('a')[0];					
							
						// attach the id
						var li = $d.isGoodTarget(link,{'tag':'li'});
						
						// get an md5 of the href
						var id = md5(link.getAttribute('href'));					
						
						// add id 
						li.id = 'link-'+id;
						
						// add class
						$d.addClass(li,'column-open');
						$d.removeClass(li,'on');					
					
						this.create({
								'title': link.innerHTML,
								'uri': link.getAttribute('href'), 
								'noHistory': true
							});
							
					}
					else {
					
						// each one
						for ( var col in TT.data.columnSettings.columns) {
							if ( col != 'length' && $d.inDocument('link-'+col) ) {
				
								var f = function(self,c) {
									var self = self;
									var c = c;
									window.setTimeout(function(){
											self.create({
													'title': c.title,
													'uri': c.href, 
													'noHistory': true
												});
											},1);
	
								};	
									
								f(this,TT.data.columnSettings.columns[col]);
							
							}

						}

					
					}					
					
					// self
					var self = this;
					
					// tick
					this.tick = window.setInterval(function(){
					
						// run the updates
						self.update();
					
					},30000);
					
				},this,true);
				a.animate();			
		
				var self = this;
				
						
		},
		
		// destroy
		destroy : function(id) {
		
			// delete id 
			$('column-'+id).parentNode.removeChild($('column-'+id));
		
			// delete
			var columns = {};
			
			for ( var c in this.columns  ) {
				if ( c != id ) {
					columns[c] = this.columns[c];
				}
			}
			
			// remove 
			$d.removeClass('link-'+id,'column-open');
			
			// reset columns
			this.columns = columns;
		
			// count
			this.count -= 1;
		
			// resize 
			$d.setStyle('page-content','width', (this.count*380)+'px' );
			
			// remove 
			var ary = {};
			
			for ( var col in TT.data.columnSettings.columns ) {	
				if ( col != id ) {
					ary[col] = TT.data.columnSettings.columns[col];
				}
			}
			
			// add to our list 
			TT.data.columnSettings.columns = ary;
			TT.data.columnSettings.columns.length--;
		
			// now save it
			$c.asyncRequest("POST", TT.Global.xhrUrl('updateColSettings'), false, "data="+$j.stringify(TT.data.columnSettings) );			
		
		},
		
		// goto
		goto : function(id) {
		
			// col
			var col = 'column-'+id;
		
			// is it active
			if ( $d.hasClass(col,'active') ) {
				return;
			}		
			
			// remove other active	
			$d.removeClass($d.getElementsByClassName('column','div',$('page-content')),'active');

			// set style
			$d.setStyle('page-content','left','0px');

			// get it's placement 
			var reg = $d.getRegion(col);
		
			// do it 
			$d.setStyle('page-content','left', ((reg.left-240)*-1) + 'px' );

			// active 
			$d.addClass(col,'active');
		
		},
		
		// create column
		create : function(args) {
		
			// id
			var id = md5(args.uri);	
			
			// id
			args.id = id;
			
			// wrap
			var wrap = new $el('div');
			
				// set the id
				wrap.get().id = 'column-'+id;
				
				// set some class
				wrap.addClass(['module','column']);
				
				// set it 
				args.el = wrap;
		
			// hd
			var hd = new $el('div');
			
				// set style
				hd.addClass('hd').html( args.title + "<a class='close' href='#'>close</a>");
				
				
			// bd
			var bd = new $el('div');
			
				// add class
				bd.addClass('bd').html("<ul class='timeline' id='column-timeline-"+id+"'></ul>");			
				
				// set bd
				args.bd = bd;
				
			// append
			wrap.appendChild(hd).appendChild(bd);		

			// append to the page content
			$('page-content').appendChild( wrap.get() );		
			
			// more count 
			this.count += 1;
			
			// set width 
			$d.setStyle('page-content','width', (this.count*380)+'px' );
			
			// since
			args.since = 1;
			args.open = true;
			
			// add class to link
			$d.addClass('link-'+id,'column-open');
			
			// set it 
			this.columns[id] = args;
	
			// no hstory			
			if ( args.noHistory !== true ) {
				
				// add to our list 
				TT.data.columnSettings.columns[id] = {'title':args.title, 'href': args.uri };
				TT.data.columnSettings.columns.length++;
		
				// now save it
				$c.asyncRequest("POST", TT.Global.xhrUrl('updateColSettings'), false, "data="+$j.stringify(TT.data.columnSettings) );
				
			}
			
			// load
			this.load(id);
			
		},
		
		// load
		load : function(id) {
				
			// callback
			var callback = {
				'success': function(o) {
					
					// start j 
					var j = { 'stat': 0 };
				
					// parse
					try {
						j = $j.parse(o.responseText);
					} catch(e) {}

					// loading
					$d.removeClass( ['column-'+o.argument[2],'link-'+o.argument[2]],'loading');
					
					// no stat
					if ( j.stat != 1 ) { return; }
					if ( j.resp.raw.length == 0 ) { return; }
					
					// first add to payload
					$('payload').innerHTML = j.resp.html;

					// remove defer
					$d.removeClass( $d.getElementsByClassName('defer','img', $('payload')), 'defer' );
					
					// now all items
					var items = $('payload').getElementsByTagName('li');
				
						// go for it
						for ( var i = 0; i < items.length; i++ ) {
						
							// id
							var id = items[i].id;
															

							// what 
/*
							if ( o.argument[0].columns[o.argument[2]].since == 1 ) {
								o.argument[1].appendChild(items[1]);
							}
							else {
*/
							
								// opacity
								$d.setStyle(items[i],'opacity',0);															
							
								var first = o.argument[1].getElementsByTagName('li')[0];
								
								if ( !first ) {
									o.argument[1].appendChild(items[i]);
								}
								else {
									$d.insertBefore(items[i],first);								
								}

								// now fade in
								var a = new $a(id,{'opacity':{'to':1}},.5);
									a.animate();								
								
//							}
							

																
						}
						
						// add to raw
						for ( var r in j.resp.raw ) {
							TT.data.timeline[r] = j.resp.raw[r];
						}
						
						// set max
						o.argument[0].columns[o.argument[2]].since = j.resp.max;
						
					// execute bootstrap
					TT.Global.bootstrap(j.resp.bootstrap);
			
					// parse any links	
					TT.Global.parseTimelineLinks();									
										
					
				},
				'argument': [this, $("column-timeline-"+id), id ]
			};
			
			// breakdown the uri
			var parts = this.columns[id].uri.split('/');			
			
			// loading
			$d.addClass( ['column-'+id,'link-'+id], 'loading');
			
			// get a url
			var url = TT.Global.xhrUrl('timelineUpdate',{
					'type': parts[2],
					'id': parts[3],
					'max': this.columns[id].since,
					'columns': true
				});						
		
			// go
			$c.asyncRequest('GET',url,callback);
		
		},
		
		// update
		update : function() {
		
			// open
			var open = false;
		
			// flip through
			for ( var c in this.columns ) {
				if ( this.columns[c].open === true ) {
					open = c; break;
				}
			}
		
			// is there an open
			if ( open === false ) {
				
				// i 
				var i = 0;
				
				// reset all
				for ( var c in this.columns ) {
				
					// i 
					if ( i++ == 0 ) {
						open = c;
					}
					
					// reset
					this.columns[c].open = true;
				}
			
			}
		
			// set it 
			this.columns[open].open = false;
		
			// load it 
			this.load(open);
		
		}
		
	
	}

})();