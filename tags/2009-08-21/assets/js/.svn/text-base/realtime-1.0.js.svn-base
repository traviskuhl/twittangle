(function(){

	YAHOO.namespace('twitTangle.RealTime');

	YAHOO.twitTangle.RealTime = function(p) {
		this.init(p);
	}
	
		YAHOO.twitTangle.RealTime.prototype = {
		
			/* proeprties */
			flags : {},
		
			/** init */
			init : function(p) {
			
				// params
				this.params = p;
			
				// add keymovements 
				$e.on('real-text','keyup',this.triggerKeyPress,this,true);
				$e.on('real-text','keydown',this.triggerKeyPress,this,true);				
				
				$e.on('real-text','focus',function(e) {
					var tar = $e.getTarget(e);
					if ( tar.value == 'Timeline Search' ) {
						tar.value = "";
						$d.addClass(tar,'focus');
					}
				});

				$e.on('real-text','blur',function(e) {
					var tar = $e.getTarget(e);
					if ( tar.value == '' ) {
						tar.value = "Timeline Search";
						$d.removeClass(tar,'focus');
					}
				});
			
			},
			
			toggleAll : function(obj) {
			
				obj.blur();
			
				if ( $d.getStyle('realtime-ops','display') == 'block' ) {
					$d.setStyle('realtime-ops','display','none');
					obj.innerHTML = 'More Operators';
				}
				else {
					$d.setStyle('realtime-ops','display','block');				
					obj.innerHTML = 'Close';					
				}
			
			},
			
			triggerKeyPress : function(e) {
			
				// stop auto updates
				TT.Global.endTimelineWatch();
			
				// target
				var tar = $e.getTarget(e);
				
				// value
				var val = tar.value;
			
				// if value
				if ( val == "" || val == this.flags.lastVal ) {
					return;
				}
			
				// clear
				clearTimeout(this.flags.timeout);
				
				// what
				var self = this;
			
				// timoeut
				this.flags.timeout = window.setTimeout(function(){
					self.doSearch(val);
				},300);
			
			},
			
			showExample : function(value) {

				// set
				$('real-text').value = value;
				
				// do
				this.doSearch(value);
			
			},
			
			doSearch : function(value) {
			
				// no longer on
				$d.removeClass( $d.getElementsByClassName('on','li',$('side-menu')), 'on' );

				// last
				this.flags.lastVal = value;
			
				// abort
				$c.abort(this.flags.lastXhr);

				// loading			
				$d.addClass('real-text','loading');
			
				// callback
				var callback = {
					'success': function(o) {
												
						// empty timeline
						$('timeline').innerHTML = "";
						$d.setStyle( $d.getElementsByClassName('pager','ul',$('page-content')), 'display', 'none' );												
										
						// remove class	
						$d.removeClass('real-text','loading');												
												
						// start j 
						var j = { 'stat': 0 };
					
						// parse
						try {
							j = $j.parse(o.responseText);
						} catch(e) {}
						
						// no stat
						if ( j.stat != 1 ) { return; }
						if ( j.resp.raw.length == 0 ) { return; }
					
						// first add to payload
						$('payload').innerHTML = j.resp.html;
						
						// now all items
						var items = $('payload').getElementsByTagName('li');
					
						// first
						var first = false;						
						
							// go for it
							for ( var i = 0; i < items.length; i++ ) {
							
								// id
								var id = items[i].id;
											
								// add it 
								$('timeline').appendChild(items[i]);
																									
							}
							
							// add to raw
							for ( var r in j.resp.raw ) {
								TT.data.timeline[r] = j.resp.raw[r];
							}
							
						// execute bootstrap
						TT.Global.bootstrap(j.resp.bootstrap);
				
						// parse any links	
						TT.Global.parseTimelineLinks();																
					
					},
					'argument': [this]
				};
			
				// url 
				var url = TT.Global.xhrUrl('realtime',{'q': encodeURIComponent(value)});
			
				// go
				this.flags.lastXhr = $c.asyncRequest('GET',url,callback);
			
			}
		
		}

})();