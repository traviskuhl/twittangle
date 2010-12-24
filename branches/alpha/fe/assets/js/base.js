var _yui_load_conf = {
	'combine': true,
	'filter': 'min',
	'force': false,
	'modules': {
		"tt-panel": {
			"requires": ['base','node','event','io','json','anim','selector-css3',"overlay","event-custom"],
			"fullpath": T.Env.Urls.base + 'assets/js/panel.js'
		}
	}
};

// YUI
YUI(_yui_load_conf).use('base','node','anim','event','io','json','selector-css3','tt-panel',"get", function(Y) { 

	// called to load
	Y.on('domready',function(){ T.Obj = new T.Class.Base(); T.execute('l'); },window);

	// some default stuff
	var $ = Y.get, $j = Y.JSON, $a = Y.Anim;

	// base
	T.Class.Base = function() {
		this.init();
	}

	// extend
	T.Class.Base.prototype = {
	
		// properies
		store : { 'fixed': [], 'timelinePage': 1 },
			
		// init
		init : function() {
		
			// click and such
			$('#doc').on('click',this.click,this);
			$('#doc').on('mouseover',this.mouse,this);
			$('#doc').on('mouseout',this.mouse,this);
			
			// events
			this.publish('t-base:bottompage');
					
			// when the window moves
			Y.on('scroll',function(){

				// figure if we're att the bottom
				var h = $(document).get('docHeight');
				var y = $(document).get('docScrollY');				
				var vp = $(document).get('winHeight')
			
				// bottom of page
				if ( (y+vp) >= h ) {
					this.fire('t-base:bottompage');
				}
			
				// for each fixed
				for ( var f in this.store.fixed ) {
				
					// get stuff
					var el = this.store.fixed[f].el;
					var orig = this.store.fixed[f].pos;
								
					// go to			
					var end = orig[1];			
								
					// if the current offset is bigger lets move
					if ( y > orig[1] ) {
						end = y+10;
					}
					
					// animate
					var a = new $a({'node':el,'to':{ 'top': end },'duration':.2});
					
					// run
					a.run();
					
				}
			
			},window,this);
			
			// panel
			this.panel = new T.Class.Panel({});
		
		},
		
		// ckick
		click : function(e) {
		
			// tar
			var tar = oTar = e.target;
			
			// user
			if ( oTar.hasClass('user-overlay') ) {
			
				// stop the click
				e.halt();
			
				// load the pane
				this.panel.load( this.getUrl(oTar.get('href'),{'.format':'panel'}), {'openAfter':true} );
			
			}
			else if ( oTar.test('img') && ( tar = this.getParent(oTar,'image-overlay') ) ) {
			
				// stop
				e.halt();
			
				// get the str of the image
				var src = oTar.get('src');
			
				// split out the params of the src
				var qs = src.split('?')[1].split('&');
				var params = {};
					
					// place
					for ( var q in qs ) {
						params[qs[q].split('=')[0]] = qs[q].split('=')[1];
					}
			
					// make width should be 500
					params['size'] = '500x500';
			
				// now we need to load a new image
				var img = new Image();	
					img.src = this.getUrl( T.Env.Urls.base + 'image.php', params );
				
				// set body of the panel
				this.panel.obj.set('bodyContent',"<img src='"+img.src+"'>");
			
				// get img
				var image = this.panel.obj.get('contentBox').one("img");
					
				// image
				image.on('load',function(){
					this.panel.obj.centered();
				},this);					
					
				// open
				this.panel.open();
				
			}
		
		},
		
		// mouse
		mouse : function(e) { 
		
			// target
			var tar = oTar = e.target;
		
			// tweet
			if ( ( tar = this.getParent(oTar,'tweet') ) ) {
							
				// flip on or off
				if ( e.type == 'mouseout' ) {
					tar.removeClass('on');
				}
				else {
					tar.addClass('on');
				}
			
			}
			else if ( ( tar = this.getParent(oTar,'bubble') ) ) {
				this.titleBubble(tar,e,e.type);
			}					
		
		},
		
		// title bubble
		titleBubble : function(tar,event,type) {
									
			// bubble
			var bubble = $('#title-bubble');			
			
			// off 
			if ( type == 'mouseout' ) {
				
				// hide the bubble
				bubble.setXY([-9999,-9999]);
				
				// reset the title 
				tar.setAttribute('title', tar.getAttribute('xtitle'));
				
				// reutrn
				return;
				
			}
			
			// get the title
			// no title exit 
			var title = tar.getAttribute('title');
			
				// nope 
				if ( !title || title == "" || title == 'null' ) return;
				
			// enter the title into the bubble
			bubble.set('innerHTML', title + "<span></span>");
			
			// hide the title of the tar
			tar.setAttribute('title','');
			tar.setAttribute('xtitle',title);
			
			// figure the bubble's width 
			var bReg = bubble.get('region');
			var tReg = tar.get('region');
			
			// button size
			var bw = ( bReg.right - bReg.left );
			var bh = ( bReg.bottom - bReg.top );
			
			// tar size 
			var tw = ( tReg.right - tReg.left );
							
			// get the overal xy of the el 
			var txy = tar.getXY();
			
			// find the middle 
			var x = ( txy[0] + tw / 2 ) - ( bw / 2 );
			var y = (txy[1] - (bh+5) );
			
			// set the bubbl's xy 
			bubble.setXY([x,y]);
				
		},   			
		
		// reigster fixed element
		registerFixedElement : function(els) {
		
			// not an arary
			if ( typeof els == 'string' ) {
				var els = [els];
			}
		
			// loop each el
			for ( var e in els ) {
			
				// el
				var el = $(els[e]);
				
				// store it
				this.store.fixed.push({ 'el': el, 'pos': el.getXY() });
								
				// absol
				el.setStyles({'position':'absolute','width':el.getStyle('width')});
			
				// set 
				el.setXY( el.getXY() );
				
			}
			
		},

		loadTimeline : function(args) {
		
			// add page to args
			args['page'] = ++this.store.timelinePage;
		
			// url
			var url = this.getXhrUrl('timeline',args);
		
			// do it 
			Y.io(url,{
				'method': 'get',
				'context': this,
				'on': {
					'complete': function(id,o){
					
						// json
						var json = $j.parse(o.responseText);
						
						// loop through each and add to timeline
						for ( var item in json.resp.html ) {
							$('#timeline').append( json.resp.html[item] );
						}
						
						// mentions
						if ( $('#popular-mentions').inDoc() ) {
							for ( var item in json.resp.mentions ) {
							
								// user
								var user = json.resp.mentions[item].user;
							
								// not already there
								if ( $('#popular-mentions ul').all("a.user-"+user)._nodes.length == 0 ) {
									$('#popular-mentions ul').prepend("<li>"+json.resp.mentions[item].html+"</li>");
								}
								
							}
							$('#popular-mentions').removeClass('hide');
						}
						
						// expand
						var expand = [];
						
						// links
						if ( $('#popular-links').inDoc() ) {
							for ( var item in json.resp.links ) {
							
								// user
								var id = json.resp.links[item].id;
							
								// not already there
								if ( $('#popular-links ul').all("a."+id)._nodes.length == 0 ) {
								
									// not in
									$('#popular-links ul').prepend("<li>"+json.resp.links[item].html+"</li>");
									
									// need to expand
									expand.push({'url':json.resp.links[item].url,'type':'l'});
									
								}
								
							}
							$('#popular-links').removeClass('hide');
						}						
					
						// links
						if ( $('#popular-images').inDoc() ) {
							for ( var item in json.resp.images ) {
							
								// user
								var id = json.resp.images[item].id;
							
								// not already there
								if ( $('#popular-images ul').all("img."+id)._nodes.length == 0 ) {
								
									// not in
									$('#popular-images ul').prepend("<li>"+json.resp.images[item].html+"</li>");
									
									// need to expand
									if ( json.resp.images[item].expand ) {
										expand.push({'url':json.resp.images[item].url,'type':'i','m':json.resp.images[item].m });
									}
									
								}
								
							}
							$('#popular-images').removeClass('hide');
						}						
					
						// expand
						this.expandLinks(expand);
					
					}
				}
			});
		
		},
		
		expandLinks : function(links) {
		
			// groups
			var groups = [[]];
			var g = 0;
			var i = 0;
		
			// loop
			for ( var l in links ) {
				if ( i++ > 3 ) { g++; groups[g] = []; i = 0; }
				groups[g].push(links[l]);
			}
		
			// url
			var url = this.getXhrUrl('expand');	
		
			// loop
			for ( var gr in groups ) {
			
				// do it
				Y.io(url,{
					'method': 'POST',
					'data': 'payload='+$j.stringify(groups[gr]),
					'context': this,
					'on': {
						'complete': function(id,o) {
						
							// json
							var json = $j.parse(o.responseText);					
						
							// find all links on the page
							for ( var item in json.resp.expand ) {
								
								// link 
								var link = json.resp.expand[item]; 								
								
								// replace
								$('#doc').all('.'+link.id).each(function(el){
									el.insert(link.html,'after');								
									$(el).remove();
								});

								// check the timeline
								$('#timeline').all('.'+link.id.replace(/image-/,'')).each(function(el){
								
									// get hte bd
									var bd = this.getParent(el,'bd');

									// append to the bd
									if ( bd.one("div.image") ) {
										bd.one("div.image").append(link.html);
									}
									else {
										bd.append("<div class='image image-overlay'>"+link.html+"</div>");
									}
									
								},this);
																	
							}
						
						}
					}
				});
				
			}
		
		},

        getParent : function(tar,g,max) {
       	       	
			// no tar
			if ( !tar )	{ return false; }
       	       	
       		// max
       		if ( !max ) { max = 10; }
        
            // local
            var gt = g;
           	var i = 0;            
           	var m = max;
            
            if ( typeof g == 'object' ) {
            
            	// current
            	if ( tar.get('tagName') == gt.tag.toUpperCase() ) { return tar; }
            
            	// reutrn
                return tar.ancestor(function(el){
                	if ( i++ > max ) { return false; }
					return (el.get('tagName') == gt.tag.toUpperCase()); }
				);
				
            }
            else {
            
            	// current
            	if ( tar.hasClass(gt) ) { return tar; }            
            
            	// moreve
                return tar.ancestor(function(el){ 
                	if ( i++ > max ) { return false; }                
                	return el.hasClass(gt); 
                });
                
            }
        },
        
        getUrl : function(url,params) {
        
			// qp
			var qp = [];
			
				// add 
				for ( var p in params ) {
					qp.push(p+"="+ escape(params[p]) );
				}
        
        	// do it 
        	return url + (url.indexOf('?')==-1?'?':';') + qp.join(';');
        
        },
		
		
		getXhrUrl : function(act,params) {
		
			// reurn
			return this.getUrl( T.Env.Urls.xhr+act, params);
			
		}
		
	
	}

	// we fire some custom events
	Y.augment(T.Class.Base, Y.EventTarget);

});