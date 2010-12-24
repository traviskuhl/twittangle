

// globals
var $Y = YAHOO, 
	$d = YAHOO.util.Dom, 
	$e = YAHOO.util.Event, 
	$c = YAHOO.util.Connect,
	$a = YAHOO.util.Anim,
	$j = YAHOO.lang.JSON,
	$ = YAHOO.util.Dom.get;

/* loading function */
var $l = function(on) { 
		
	// scroll top 
	var t = $d.getDocumentScrollTop();

	// show it 
	$d.setStyle('loading','display',(on?'block':'none')); 
		
	$d.setStyle('loading','height',$d.getDocumentHeight()+'px');
	
	$d.setY('loading-wrap',parseInt($d.getDocumentScrollTop(),10)+100 );
	
}

/* overload */
var inArray = function(ary,obj) {
	for ( var i = 0; i < ary.length; i++ ) {
		if ( ary[i] == obj ) {
			return true;
		}
	}
	return false;
}

/* yui defer loading */
var foldGroup = new YAHOO.util.ImageLoader.group(window, 'scroll', null); 
foldGroup.className = 'defer';
foldGroup.foldConditional = true; 
foldGroup.addTrigger(window, 'resize'); 

/* namespace Twit */
$Y.namespace('twitTangle');

// scope
//(function(){

	// is good target
	$d.isGoodTarget = function( el, cl, limit ) {	
	
		// limit 
		if ( !limit ) { limit = 5; } 
		var i = 0;
		
		if ( typeof cl == 'object' ) {
			while ( el && typeof el.tagName != 'undefined' && el.tagName.toLowerCase() != cl.tag.toLowerCase() &&  i < limit ) {
				el = el.parentNode;
				i++;				
			}
			if ( typeof el.tagName != 'undefined' && el.tagName.toLowerCase() == cl.tag.toLowerCase() ) {
				return el;
			}
		}
		else {
			while ( el && !$d.hasClass(el,cl) &&  i < limit ) {
				el = el.parentNode;
				i++;				
			}
			if ( $d.hasClass(el,cl) ) {
				return el;
			}			
		}
		
		// false	
		return false;
	
	}	

	/* timeline function */
	$Y.twitTangle.Global = function(p) {
		this.init(p);
	}

		/* extend */
		$Y.twitTangle.Global.prototype = {
			
			// params
			params : {},
			rated : {},
			tagged: {},
			flags: {},
			
			// init
			init : function(p) {
			
				// fold
				foldGroup.fetch();
				
				// global params
				this.params = p;
				
				// add our first entry
				dsHistory.addFunction( this.loadXhrPage, this, location.href );				
				
				// execute
				$e.on(window,'load',TT.executeQueue,window,true);
				
				// click
				$e.on(document.body,'click',function(e){
				
					// target 
					var tar = $e.getTarget(e);
					var oTar = tar;
					
					// link
					if ( $d.hasClass(tar,'do-reply') ) {
					
						// stop
						$e.stopEvent(e);
						
						// open 
						this.doReply(tar);
					
					}
					else if ( $d.hasClass(tar,'do-fav') ) {
					
						// stop 
						$e.stopEvent(e);
						
						// fav
						this.doFav(tar);
					
					}
					else if ( $d.hasClass(tar,'rate') ) {
					
						// stop 
						$e.stopEvent(e);
						
						// rate 
						this.openRate(tar);
					
					}
					else if ( $d.hasClass(tar,'do-rate') ) {
						
						// stop
						$e.stopEvent(e);
						
						// do 
						this.doRate(tar);
					
					}
					else if ( $d.hasClass(tar,'toggle-upload-pic') ) {
						
						$e.stopEvent(e);
						
						if ( $d.hasClass('pic-upload','open') ) {
							$d.removeClass('pic-upload','open');
						}
						else {
							$d.addClass('pic-upload','open');
						}
					
					}
					else if ( $d.hasClass(tar,'close-rate') ) {
						
						// stop 
						$e.stopEvent(e);
						
						// fade in
						$d.setXY('Rate',[-999,-999]);
						
						// fade
						var a = new $a('bd',{'opacity':{to:1}},.3);
							a.animate();
					
					}
					else if ( $d.hasClass(tar,'tag') ) {
					
						// stop
						$e.stopEvent(e);
						
						// do it 
						this.openTag(tar);
					
					}
					else if ( $d.hasClass(tar,'close-tag') ) {
					
						// stop 
						$e.stopEvent(e);
						
						// fade in
						$d.setXY('Tag',[-999,-999]);
						
						// fade
						var a = new $a('bd',{'opacity':{to:1}},.3);
							a.animate();					
					
					}
					else if ( $d.hasClass(tar,'do-tag') ) {
					
						// stop 
						$e.stopEvent(e);
						
						// do tag 
						this.doTag(tar);					
					
					}
					else if ( $d.hasClass(tar,'group') ) {
					
						// stop
						$e.stopEvent(e);
						
						// do it 
						this.openGroup(tar);					
					
					}
					else if ( $d.hasClass(tar,'close-group') ) {
					
						// stop 
						$e.stopEvent(e);
						
						// fade in
						$d.setXY('Group',[-999,-999]);
						
						// get groups
						var ul = $('Group').getElementsByTagName('ul')[0].getElementsByTagName('li');
												
						// go for each val
						for ( var i = 0; i < ul.length; i++ ) {						
							var box = ul[i].getElementsByTagName('input')[0];
							box.checked = false;
						}						
						
						// fade
						var a = new $a('bd',{'opacity':{to:1}},.3);
							a.animate();					
					
					}	
					else if ( $d.hasClass(tar,'do-group') ) {
					
						// stop event
						$e.stopEvent(e);
					
						// save
						this.doGroup(tar);
					
					}
					else if ( $d.hasClass(tar,'create-group') ) {
						
						// stop
						$e.stopEvent(e);
						
						$d.setStyle('NewGroupWrap','opacity','0');
						$d.setStyle('NewGroupWrap','display','block');
					
						// open
						var a = new $a('NewGroupWrap',{'opacity':{'to':1}},.2);
							a.animate();
							
						// focus
						$('NewGroup').focus();
					
					}
					else if ( $d.hasClass(tar,'do-create-group') ) {
					
						// stop
						$e.stopEvent(e);
						
						// post the 
						window.location.href = "/groups?do=create&name="+escape( $('NewGroup').value );
						
					}
					else if ( $d.hasClass(tar,'delete-group') ) {
						
						// stop
						$e.stopEvent(e);
						
						// do it 
						this.deleteGroup(tar);
					
					}
					else if ( $d.hasClass(tar,'view-pic') ) {
					
						// no event
						$e.stopEvent(e);
						
						// do it
						this.doPic(tar);
					
					}
					else if ( $d.hasClass(tar,'view-video') ) {
					
						// no event
						$e.stopEvent(e);
						
						// do it
						this.doVideo(tar);
					
					}					
					else if ( $d.hasClass(tar,'view-expand') ) {
					
						// no event
						$e.stopEvent(e);
						
						// get info
						var info = tar.id.split('|');
						
						// get link 
						$(info[1]).innerHTML = $(info[1]).href;
						
						// remove
						tar.parentNode.removeChild(tar);
					
					}	
					else if ( ( tar = $d.isGoodTarget(oTar,{'tag':'a'}) ) ) {
				
						// check the target 
						var href = tar.href;			
						var reg = false;
					
						// regex
						if ( location.href.indexOf('https:') !== -1 ) {
							reg = new RegExp("https:\/\/(www\.|networks\.)?twittangle\.com");
						}
						else {
							reg = new RegExp("http:\/\/(www\.|networks\.)?twittangle\.com");
						}
										
						// lets check to see if the
						// url is the right subdomain
						if ( !href.match(reg) || href.indexOf('#') != -1 ) {
							return;
						}
						
						// abort last call
						if ( this.lastXhrPageReq ) {
							$c.abort(this.lastXhrPageReq);
						}
						
						// stop event 
						$e.stopEvent(e);
						
						// blur
						oTar.blur();
					
						// load some xhr
						this.loadXhrPage(href);
						
					}				
				
				},this,true);
				
				// mouse over
				$e.on('doc','mouseover',this.mouseOver,this,true);
				$e.on('doc','mouseout',this.mouseOut,this,true);
				
				// create reply window
				this.reply = new YAHOO.widget.Panel("reply", { 
					'width':				"600px", 
					'visible':				false, 
					'text':					'', 
					'constraintoviewport':	true,
					'fixedcenter':			true,
					'modal':				true,
					'draggable': 			false, 
					'effect': 				{effect:YAHOO.widget.ContainerEffect.FADE,duration:0.25} 
				});
			
				var html = "<h2 id='reply-hd'></h2>" +
						   "<textarea id='reply-text'></textarea> <button id='reply-submit'>Update</button>" +
						   "<span id='reply-chars'>140</span>" +
						   "<blockquote id='reply-quote'></blockquote>" + 
						   "<div class='saving'><em>Sending...</em></div>";			
			
				// set body
				this.reply.setBody(html);			
			
				// render
				this.reply.render(document.body);
				
				// attach events
				$e.on('reply-submit','click',this.submitReply,this,true);
				$e.on('reply-text','keypress',function(){
					var len = parseInt($('reply-text').value.length,10);					
					var l = (140 - len);					
					$('reply-chars').innerHTML = l;
					
					// what 
					if ( l < 0 ) {
						$d.setStyle('reply-chars','color','red');
						$('reply-submit').disabled = true;
					}
					else {
						$d.setStyle('reply-chars','color','#555');
						$('reply-submit').disabled = false;					
					}
					
				});		
				
				// get height of content
				var ch = parseInt($d.getStyle('Content','height'),10);
				var mh = parseInt($d.getStyle('yui-main','height'),10);
				
				// check main
				if ( mh < ch ) {
					
					// get b
					var el = $d.getElementsByClassName('yui-b','div',$('yui-main'))[0];
					if ( el ) {
						$d.setStyle(el,'height',ch+'px');
					}
					
				}					
			
			},
			
			// mouse over
			mouseOver : function(e) {
				
				// tar
				var tar = $e.getTarget(e);
				
				if ( (tar = $d.isGoodTarget(tar,'status')) ) {
					if ( tar.tagName.toLowerCase() == 'li' ) {
						$d.addClass(tar,'on');
					}
				}
				
			},
			
			mouseOut : function(e) {
			
				// tar
				var tar = $e.getTarget(e);
				
				if ( (tar = $d.isGoodTarget(tar,'status')) ) {
					if ( tar.tagName.toLowerCase() == 'li' ) {
						$d.removeClass(tar,'on');
					}
				}			
			
			},	
			
			/* submit update */
			submitUpdate : function(e) {
			
				// stop
				$e.stopEvent(e);
			
				// val
				var update = $('update-txt').value;
			
				// callback
				var callback = {
					'success': function(o) {
					
						// json
						var j = $j.parse(o.responseText);
						
						// msg
						if ( j.stat != 1 ) {
							alert(j.msg);
						}
					
						// update last 
						$('update-current').innerHTML = "<strong>Latest:</strong> "+o.argument[1];
						$('update-txt').value = "";
						$('update-chars').innerHTML = 140;
						
						// check timeline 
						if ( $d.inDocument('status-list') ) {
							
							// add to the dom
							var ul = document.createElement('ul');	
								ul.innerHTML = j.resp.html;
							
							// append to doc
							document.body.appendChild(ul);
							
							// get node
							var li = ul.getElementsByTagName('li')[0];
							
							// timeline
							var tl = $('status-list');
							
							// remoce
							$d.removeClass(li.getElementsByClassName('defer'),'defer');							
							
							// has child
							if ( tl.getElementsByTagName('li').length > 0 ) {								
								$d.insertBefore(li,tl.getElementsByTagName('li')[0]);
							}
							else {
								tl.appendChild(li);
							}
							
							// remove holder
							ul.parentNode.removeChild(ul);
							
						}
					
					},
					'argument': [this,update]
				};
			
				// url 
				var url = this.xhrUrl('update');
			
				// pb
				var pb = 'status='+encodeURIComponent( update );
			
				// podt
				var r = $c.asyncRequest('POST',url,callback,pb);
			
			},
			
			/* doInlineRate */
			doInlineRate : function(id,cur) {
	
				// slider
				TT.data['slider'+id] = YAHOO.widget.Slider.getHorizSlider("slider-bg-"+id,"slider-thumb-"+id,0,200); 
				
				// ger real value
		    	TT.data['slider'+id].getRealValue = function() {
		            return Math.round(this.getValue() * 1);
		        }					
				
				TT.data['slider'+id].subscribe('change', function (newOffset) { 
					$("Rate-"+id).innerHTML = TT.data['slider'+id].getRealValue();
				});
				
				TT.data['slider'+id+'cur'] = cur;
	
				TT.data['slider'+id].subscribe('slideEnd', function () {
				
					var n = TT.data['slider'+id].getRealValue();
					
					if ( n != TT.data['slider'+id+'cur'] ) {
					
						clearTimeout(TT.data['slider'+id+'TO']);
						
						TT.data['slider'+id+'TO'] = setTimeout(function(){
							
							var callback = {
								'success': function(o) {}
							};
							
							var url = TT.Global.xhrUrl('rate',{'id': id});
							
							// go
							var r = $c.asyncRequest('POST',url,callback,'n='+n);
							
						},500);
					}
				});	
				
				TT.data['slider'+id].setValue(cur);
			
			},
			
			/* doPic */
			doPic : function(tar) {

				// blur
				tar.blur();
			
				// get some info
				var info = tar.id.split('|');
			
				// if already open we should close
				if ( $d.hasClass(tar,'open') ) {
					$d.removeClass(tar,'open');
					$d.setStyle(info[1]+'-wrap','display','none');
					tar.innerHTML = "(open pic)";
					return;
				}
				// not open but el exists
				else if ( $d.inDocument(info[1]+'-wrap') ) {
					$d.setStyle(info[1]+'-wrap','display','block');
					$d.addClass(tar,'open');
					tar.innerHTML = "(close pic)";
					return;
				}
				
				// el
				var el = $(info[1]);
				var url = "";
				
				// twitpic?
				if ( el.href.indexOf('twitpic.com') !== -1 ) {
					
					// get the id 
					var id = /twitpic\.com\/([a-zA-Z0-9]+)/.exec(el.href)[1];
					
					// url 
					url = "http://twitpic.com/show/large/"+id+".jpg";
					
				}
				
				// new img
				var img = new Image();
					img.src = url;
				
				// get status
				var status = $d.isGoodTarget(tar,'status',10);
					
					// create our warp 
					var wrap = document.createElement('div');
						wrap.id = info[1]+'-wrap';
						wrap.className = 'external-wrap';
				
					// add some html 
					wrap.innerHTML = "<b class='tl'></b><b class='tr'></b><b class='bl'></b><b class='br'></b>" +
									 "<img src='"+url+"'>";
				
				// append
				status.appendChild(wrap);
			
				// show
				$d.addClass(tar,'open');
			
				// close 		
				tar.innerHTML = "(close pic)";				
			
			},
			
			/* dp video */
			doVideo : function(tar) {

				// blur
				tar.blur();
			
				// get some info
				var info = tar.id.split('|');
			
				// if already open we should close
				if ( $d.hasClass(tar,'open') ) {
					$d.removeClass(tar,'open');
					$d.setStyle(info[1]+'-wrap','display','none');
					tar.innerHTML = "(open video)";
					return;
				}
				// not open but el exists
				else if ( $d.inDocument(info[1]+'-wrap') ) {
					$d.setStyle(info[1]+'-wrap','display','block');
					$d.addClass(tar,'open');
					tar.innerHTML = "(close video)";
					return;
				}
				
				// el
				var el = $(info[1]);
				var embed = "";
				
				// twitpic?
				if ( el.href.indexOf('youtube.com') !== -1 ) {
										
					var id = /youtube.com\/watch\?v=([a-zA-Z0-9\-\_]+)/.exec(el.href)[1];
										
					// url 
					embed = '<object width="425" height="344"><param name="movie" value="http://www.youtube.com/v/tX5hQeD71ns&hl=en&fs=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'+id+'&hl=en&fs=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="425" height="344"></embed></object>';
					
				}
				else if ( el.href.indexOf('qik.com') !== -1 ) {
				
					// we need to set off and get the quick ememb
					var callback = {
						'success': function(o) {
							// ememb
							$(o.argument[1]).innerHTML = "<b class='tl'></b><b class='tr'></b><b class='bl'></b><b class='br'></b>" +
														o.responseText;
						},
						'argument': [this,info[1]+'-wrap']
					};
				
					// url
					var url = this.xhrUrl('embed');
					
					// pb 
					var pb = "url="+escape(el.href);
					
					// go 
					var r = $c.asyncRequest('POST',url,callback,pb);
					
					// loading
					embed = "<span class='loading'>Loading Video...</span>";
				
				}
				
				// get status
				var status = $d.isGoodTarget(tar,'status',10);
					
					// create our warp 
					var wrap = document.createElement('div');
						wrap.id = info[1]+'-wrap';
						wrap.className = 'external-wrap';
				
					// add some html 
					wrap.innerHTML = "<b class='tl'></b><b class='tr'></b><b class='bl'></b><b class='br'></b>" +
									 embed;
				
				// append
				status.appendChild(wrap);
			
				// show
				$d.addClass(tar,'open');
			
				// close 		
				tar.innerHTML = "(close video)";
			
			},
			
			/* addPreLink */
			addPreLink : function(lnks) {
				
				// no list 
				if ( !this.preLinks ) {
					this.preLinks = [];
				}
			
				for ( var l in lnks ) {
					this.preLinks[this.preLinks.length] = lnks[l];
				}
			
			},
			
			/* parseTimelineLinks */
			parseTimelineLinks : function() {
			
				// links 
				var links = this.preLinks;
				
					// none
					if ( !links || links.length == 0 ) {
						return;
					}
			
				// links
				TT.data.links = {};
			
				// gorup
				var g = [];
				var n = Math.ceil( links.length / 3 );
			
				// foreach links 
				for ( var i = 0; i < n; i++ ) {
					var s = i*3;
					g[g.length] = links.slice(s,s+3);				
				}
				
				this.linkGroups = g;
				
				// if g > 0 
				if ( this.linkGroups.length > 0 ) {
				
					// do the first one now
					this.injectLinks(this.linkGroups.shift());
			
					// that 
					var that = this;
					
					this.linkInterval = window.setInterval(function(){							
						if ( that.linkGroups.length == 0 ) {
							clearInterval(that.linkInterval); return;
						}
						that.injectLinks(that.linkGroups.shift());						
					},500);					
						
				}

			},
			
			// inject linsk
			injectLinks : function(item) {
			
				// callback
				var callback = {
					'success': function(o) {
						
						// respinse 
						var j = $j.parse(o.responseText);
						
						// if j
						if ( j.stat != 1 ) {
							alert(j.msg); return;
						}
						
						// lenght
						if ( j.resp.length == 0 ) {
							return;
						}
					
						// foreach of the links
						for ( var l in j.resp ) {
						
							// object
							var o = j.resp[l];
							
							if ( $d.inDocument(l) ) {
								
								// links 
								TT.data.links[l] = o;
								
								// always change the source
								$(l).href = o.url;
								
								if ( o.type == 'video' ) {
								
									// set url
									$(l).innerHTML = o.short;
									
									// exapnd link 
									$(o.type+'|'+l).innerHTML = "(view video)";
									
									// set it 
									$d.removeClass(o.type+'|'+l,'hide');
									
								}
								else if ( o.type == 'pic' ) {
	
									// set url
									$(l).innerHTML = o.short;
									
									// exapnd link 
									$(o.type+'|'+l).innerHTML = "(view pic)";
									
									// set it 
									$d.removeClass(o.type+'|'+l,'hide');									
									
								}
								else {
								
									// show short
									$(l).innerHTML = o.short;
									
									// set it 
									$d.removeClass(o.type+'|'+l,'hide');									
									
									// if they're the same remove extend
									if ( o.short == o.url ) {
									//	$(o.type+'|'+l).parentNode.removeChild($(o.type+'|'+l));
									}
									
								}
								
							}
							
						}
					
					},
					'argument': [this]
				};
			
				// url 
				var url = this.xhrUrl('links');
			
				// pb
				var pb = 'links='+escape( $j.stringify(item) );
			
				// xhr
				var r = $c.asyncRequest('POST',url,callback,pb);			
			
			},
			
			// fav
			doFav : function(tar) {
			
				// blur
				tar.blur();
				
				// done
				if ( $d.hasClass(tar,'done') ) {
					return;
				}
			
				// info 
				var oTar = tar;
				var li = $d.isGoodTarget(tar,'status');
				
				// info 
				var info = li.id.split('|');
			
				// callback
				var callback = {
					'success': function(o) {
						
						// r 
						var r = o.responseText;
					
						// what up
						if ( r != 'true' ) {
							$d.removeClass(oTar,'saving');						
							alert(r);
						}
						else {
							$d.removeClass(oTar,'saving');						
							$d.addClass(oTar,'done');						
						}
					
					},
					'argument': [this]
				};
			
				// url
				var url = this.xhrUrl('fav',{'id':info[1]});
				
				// change to new image
				$d.addClass(oTar,'saving');
				
				// li on
				$d.addClass(li,'on');
			
				// send
				var r = $c.asyncRequest('GET',url,callback);
			
			},
			
			// submit reply
			submitReply : function(e) {
				
				// callback
				var callback = {
					'success': function(o) {
						
						// text
						var r = o.responseText;
					
						// hide
						o.argument[0].replying = false;
						o.argument[0].reply.hide();
						
						// response 
						if ( r != 'true' ) {
							alert(r);
						}
					
					},
					'argument': [this]
				};
			
				// url 
				var url = this.xhrUrl('reply');
				
				// post body
				var pb = "msg="+encodeURIComponent( $('reply-text').value )+"&to="+this.replying.id;
			
				$d.addClass('reply','saving');
				$('reply-submit').disabled = true;
				
				// post 
				var r = $c.asyncRequest('POST',url,callback,pb);
			
			},
			
			// do reply
			doReply : function(tar) {
			
				// get paretn 
				var li = $d.isGoodTarget(tar,'status');	
				
					// no li
					if ( !li ) { return; }		
			
				// info
				var info = li.id.split('|');
			
				// get data 
				var data = TT.data.tweets[info[1]];		
				
					// no data
					if ( !data ) { return; }
			
				// sn
				var sn = data.user.screen_name;		
			
				$('reply-hd').innerHTML = "Reply to "+sn;
				$('reply-text').value = "@"+sn + " ";
				$('reply-chars').innerHTML = 140 - parseInt(sn.length,10);
				$('reply-quote').innerHTML = "<em>"+sn+"</em> " + data.text;
					
				$d.setStyle('reply-chars','color','#555');
				$('reply-submit').disabled = false;						
				$d.removeClass('reply','saving');				
				
				// reply 
				this.replying = data;
					
				// show
				this.reply.show();
				
			},
			
			// open
			openGroup : function(tar) {
			
				// get info 
				var info = tar.id.split('|');			
			
				// fade the bd 
				var a = new $a('bd',{opacity:{to:.2}},.5);
					a.animate();
			
				// figure where they are
				var xy = $d.getXY(tar);
								
				// show
				$d.setStyle('Group','display','block');				
			
				// move 
				$d.setXY('Group',[xy[0],xy[1]+20]);
				
				// callback
				var callback = {
					'success': function(o) {
					
						// json 
						var j = $j.parse(o.responseText);
						
						// check resp 
						if ( j.stat != 1 ) {
							alert(j.msg); return;
						}
						 
						// no longer saving
						$d.removeClass('Group','saving');						 
						 
						// get groups
						var ul = $('Group').getElementsByTagName('ul')[0].getElementsByTagName('li');
												
						// go for each val
						for ( var i = 0; i < ul.length; i++ ) {	
							var gid = ul[i].className.replace(/group-/,'');	
							if ( inArray(j.resp,gid) ) {
								var box = ul[i].getElementsByTagName('input')[0];
								box.checked = true;
							}
						}
					
					
					},
					'argument': [this]
				};
			
				// groups
				this.grouping = info[1];
			
				// xhr
				var url = this.xhrUrl('group',{'friend':info[1]});
			
				// go 
				var r = $c.asyncRequest('GET',url,callback);
			
			},	
			
			// do group
			doGroup : function(tar) {
			
				// callback
				var callback = {
					'success': function(o) {
						
						// bring in
						var a = new $a('bd',{opacity:{to:1}},.3);
							a.animate();						
							
						// take out 
						$d.setXY('Group',[-999,-999]);						
						
						// json
						var j = {};
					
						// json 
						try {
							j = $j.parse(o.responseText);
						}
						catch(e) {
							alert("There was an error while adding this friend to group(s). Please try again.");
							Triangle("Parse error: "+e+" || "+o.responseText);
							return;
						}
						
						// check resp 
						if ( j.stat != 1 ) {
							alert(j.msg); return;
						}												 
						 
						// get groups
						var ul = $('Group').getElementsByTagName('ul')[0].getElementsByTagName('li');
												
						// go for each val
						for ( var i = 0; i < ul.length; i++ ) {	
							var box = ul[i].getElementsByTagName('input')[0];
							box.checked = false;
						}					
					
					},
					'failure': function(o) {
					
						// alert
						alert("There was an error adding this friend to a group. Please try again");
					
						// triangle
						Triangle("do-rate failed:"+o.statusText);
						
						// bring in
						var a = new $a('bd',{opacity:{to:1}},.3);
							a.animate();						
							
						// take out 
						$d.setXY('Group',[-999,-999]);						
						
					},
					'argument': [this],
					'timeout': 5000
				};
				
				// checked 
				var checked = [];
		
				// get groups
				var ul = $('Group').getElementsByTagName('ul')[0].getElementsByTagName('li');
								
				for ( var i = 0; i < ul.length; i++ ) {	
					var gid = ul[i].className.replace(/group-/,'');	
					var box = ul[i].getElementsByTagName('input')[0];
					if ( box.checked == true ) {
						checked[checked.length] = [gid,true];
					}
					else {
						checked[checked.length] = [gid,false];
					}
				}
				
				// no longer saving
				$d.addClass('Group','saving');							
			
				// xhr
				var url = this.xhrUrl('updategroup',{'friend':this.grouping});
			
			
				// go 
				var r = $c.asyncRequest('POST',url,callback,'g='+$j.stringify(checked)+'&u='+escape($j.stringify(TT.data.friends[this.grouping])));
			
			},
			
			// delete group
			deleteGroup : function(tar) {
			
				// info 
				var info = tar.id.split('|');
			
				// ask 
				var dl = new YAHOO.widget.SimpleDialog("dl", 
					 { width: "300px",
					   fixedcenter: true,
					   modal: true,
					   visible: false,
					   draggable: false,
					   close: true,
					   text: "Are you sure you want to delete this group. There's no way to get it back once you delete?",
					   icon: YAHOO.widget.SimpleDialog.ICON_HELP,
					   constraintoviewport: true,
					   buttons: [ { text:"Yes", handler:function(){ window.location.href = '/groups?do=delete;id='+info[1]; }, isDefault:true },
								  { text:"No",  handler: function() { this.hide(); this.destroy(); } } ]
					 } );
			
				dl.setHeader("Really?");
				dl.render( document.body );
				
				dl.show();
			
			},
								
			// do rate
			openRate : function(tar) {
			
				// no slider
				if ( !this.slider ) {
				
					// slider
					var slider = YAHOO.widget.Slider.getHorizSlider("slider-bg","slider-thumb",0,200); 				
					
					// ger real value
			    	slider.getRealValue = function() {
			            return Math.round(this.getValue() * 1);
			        }					
					
					slider.subscribe('change', function (newOffset) { 
						$("Rate-N").innerHTML = slider.getRealValue()+10;
					});
					
					this.slider = slider;
					
				}
							
				// get info 
				var info = tar.id.split('|');			
			
				// fade the bd 
				var a = new $a('bd',{opacity:{to:.2}},.5);
					a.animate();
			
				// figure where they are
				var xy = $d.getXY(tar);
				
				// show
				$d.setStyle('Rate','display','block');
				
				// set the id
				this.rating = info[1];
			
				// move 
				$d.setXY('Rate',[xy[0],xy[1]+20]);
		
				// check info 
				if ( this.rated[info[1]] ) {
					this.slider.setValue(this.rated[info[1]]);
				}
				else if ( info[2] != "" ) {			
					this.slider.setValue( parseInt(info[2],10) );
				}
				else {				
					this.slider.setValue(0);				
				}		
			
			},
			
			// open
			openTag : function(tar) {
			
				// get info 
				var info = tar.id.split('|');			
			
				// fade the bd 
				var a = new $a('bd',{opacity:{to:.2}},.5);
					a.animate();
			
				// figure where they are
				var xy = $d.getXY(tar);
				
				// set 
				if ( this.tagged[info[1]] ) {
					$('Tag-T').value = this.tagged[info[1]];
				}
				else if ( info[2] ) {
					$('Tag-T').value = info[2];
				}
				else {
					$('Tag-T').value = "";
				}
				
				// show
				$d.setStyle('Tag','display','block');
				
				// set the id
				this.tagging = info[1];
			
				// move 
				$d.setXY('Tag',[xy[0],xy[1]+20]);
			
			},			
			
			// do tag
			doTag : function(tar) {
			
				// do tag 
				var tags = $('Tag-T').value;
							
				// callback
				var callback = {
					'success': function(o) {
						
						// get json
						var j = $j.parse(o.responseText);
					
						// good 
						if ( j.stat != 1 ) {
							alert(j.msg); return;
						}
					
						// saved it so ipdated and classes
						var els = $d.getElementsByClassName('tag-'+o.argument[2],'span',$('bd'));
						
						// show 
						for ( var el in els ) {
							els[el].innerHTML = "("+o.argument[1].split(',').length+")";
						}
						
						// bring in
						var a = new $a('bd',{opacity:{to:1}},.3);
							a.animate();						
							
						// take out 
						$d.setXY('Tag',[-99,-99]);
						
						// rated
						o.argument[0].tagged[o.argument[2]] = o.argument[1];
						
						// no sacing
						$d.removeClass('Tag','saving');
						
					},
					'argument': [this,tags,this.tagging]
				};
			
				// make a url
				var url = this.xhrUrl('tag',{'id':this.tagging});		
				
				// saving
				$d.addClass('Tag','saving');	
				
				// go
				var r = $c.asyncRequest('POST',url,callback,"tags="+escape(tags));			
			
			},
			
			// do rate
			doRate : function(tar) {

				// number
				var n = this.slider.getValue() + 10;
				
				// callback
				var callback = {
					'success': function(o) {
						
						// get json
						var j = $j.parse(o.responseText);
					
						// good 
						if ( j.stat != 1 ) {
							alert(j.msg); return;
						}
					
						// saved it so ipdated and classes
						var els = $d.getElementsByClassName('rate-'+o.argument[2],'span',$('bd'));
						
						// show 
						for ( var el in els ) {
							els[el].innerHTML = "("+o.argument[1]+")";
						}
						
						// bring in
						var a = new $a('bd',{opacity:{to:1}},.3);
							a.animate();						
							
						// take out 
						$d.setXY('Rate',[-99,-99]);
						
						// rated
						o.argument[0].rated[o.argument[2]] = o.argument[1];
						
						// no sacing
						$d.removeClass('Rate','saving');
						
					},
					'argument': [this,n,this.rating]
				};
			
				// make a url
				var url = this.xhrUrl('rate',{'id':this.rating});		
				
				// saving
				$d.addClass('Rate','saving');	
				
				// go
				var r = $c.asyncRequest('POST',url,callback,"n="+n);
				
			},
			
			// load
			loadTimeline : function(p) {
			
				// not in p 
				var params = {};
			
					for ( var pp in p ) {
						if ( pp != 'id' && pp != 'req' && p != 'translate' ) {
							params[pp] = p[pp];
						}
					}			
			
				// make our url
			//	var url = this.xhrUrl('timeline',{'since':p.since,'page':p.page,'tag':p.tag});
			
			var url = this.xhrUrl('timeline',params);
			
				// callback
				var callback = {
					'success': function(o) {
						
						// parse json
						var j = $j.parse(o.responseText);
						
						// if good
						if ( j.stat != 1 ) {
							alert(j.msg); return;
						}
						
						// just add it
						$(o.argument[1].id).innerHTML = j.resp.html;
						
						// save data
						TT.data['tweets'] = j.resp.raw;
						
						// no loading
						$d.removeClass(o.argument[1].id,'loading');
						
						// remove defer class
						$d.removeClass($d.getElementsByClassName('defer'),'defer');				
						
						// exec
						TT.executeQueue();
						
						// check for links
						o.argument[0].parseTimelineLinks();
					
					},
					'argument': [this,p]
				};
			
				// go
				var r = $c.asyncRequest('GET',url,callback);
			
			},
			
		
			/**
			 * load live timeline
			 */
			 loadLiveTimeline : function(p) {
			 
			 	// taht 
			 	var that = this;
			 	var p = p;
			 	
			 	// need holder
			 	if ( !$d.inDocument('live-holder') ) {
			 		var div = document.createElement('div');
			 			div.id = 'live-holder';
			 			
			 		// style
			 		$d.setStyle(div,'display','none');
			 		
			 		// append
			 		$('doc').appendChild(div);
			 		
			 		// hilder
			 		this.flags.liveHolder = $('live-holder');
			 		
			 	}
			 	
			 	// set timout
			 	this.liveTimeout = window.setInterval(function(){
					that.doLiveTimeline(p);					 
				},(1000*60));			 
			 
			 },
			
			/**
			 */
			doLiveTimeline : function(p) {
			
			 	// callback
			 	var callback = {
			 		'success': function(o) {
			 		
						// parse json
						var j = $j.parse(o.responseText);
						
						// some arsg
						var t = o.argument[0];
						
						// if good
						if ( j.stat != 1 ) {
							return;
						}
									
						// check raw
						if ( j.resp.raw.length == 0 ) {
							return;
						}
					
						// load into holder
						t.flags.liveHolder.innerHTML = j.resp.html;	
					
						// get list 
						var list = $d.getElementsByClassName('status','li',t.flags.liveHolder.getElementsByTagName('ul')[0]);
					
						// how many 
						if ( list.length == 0 ) {
							return;
						}
					
						// how many divided by a second
						var per = Math.ceil(list.length*1/59);
						
						// el 
						var wrap = $(o.argument[1].id);
					
						// make sure the ul lis ther
						if ( wrap.getElementsByTagName('ul').length != 1 ) {
							var ul = document.createElement('ul');
								ul.className = 'timeline';
							wrap.innerHTML = "";
							wrap.appendChild(ul);
						}
						
						// el
						var el = wrap.getElementsByTagName('ul')[0];
						
						// everyone?
						var interval = 1000;
						
							if ( o.argument[1]['for'] == 'everyone' ) {
								interval = 3000;
								per = 1;
							}
					
						// start our loop 
						t.liveLoop = setInterval(function(){
						
							// is holder temp 
							if ( list.length == 0 ) {
								clearInterval(t.liveLoop);
							}
							else if ( list.length < per ) {
								per = list.length;
							}
							
							// else for how many we add
							for ( var i = 0; i < per; i++ ) {
							
								// giver a try 
								try {
									
									// set the opacity of the list item 
									var e = list[list.length-1];
								
									// opacity
									$d.setStyle(e,'opacity',0);
									
									var cur = el.getElementsByTagName('li').length;
								
									// insert to top of list
									if ( cur == 0 ) {
										el.appendChild(e);
									}
									else {
										$d.insertBefore(e,el.firstChild);
									}
								
									// fade in
									var a = new $a(e,{'opacity':{'to':1}},.4);
										a.animate();
										
									// pop the last off the list 
									if ( cur >= 20 ) {
										el.removeChild(el.lastChild);
									}
										
								}
								catch(e) {}
							
							}
						
						},interval);
					
						
						// up the counter
						o.argument[0].flags.liveCounter++;
									 		
			 		},
			 		'argument': [this,p]
			 	};
			 	
			 	clearInterval(this.liveLoop);
			 	
				// make our url
				var params = {'since':p.since,'pager':false};
			 	
			 	// no live if first`
			 	params['live'] = true;
			 
				// make our url
				var url = this.xhrUrl('timeline',params);
			 
			 	// go 
			 	var r = $c.asyncRequest('GET',url,callback);			
			
			},			 
			
			/**
			 
			 */
			destroyLiveTimeout : function() {
				clearInterval(this.liveTimeout);
				clearInterval(this.liveLoop);
			},
			
			/**
			 * load xhr page
			 */
			loadXhrPage : function(href,history) {
			
				// reg
				var reg = false;
			
				// regex
				if ( location.href.indexOf('https:') !== -1 ) {
					reg = new RegExp("https:\/\/(www\.|networks\.|api\.)?twittangle\.com");
				}
				else {
					reg = new RegExp("http:\/\/(www\.|networks\.|api\.)?twittangle\.com");
				}

				// typeof				
				if ( typeof href.match(reg)[1] == 'undefined' ) {
					window.location.href = href; return;
				}
			
				// sub
				var subdomain = href.match(reg)[1].replace(/\./,'');
			
				// check it out 
				if ( href.match(reg)[1] != location.href.match(reg)[1] ) {
					location.href = href; return
				}
			
				// lets check to see if the
				// url is the right subdomain
				if ( !href.match(reg) || href.indexOf('#') != -1 ) {
					return;
				}
				
				// abort last call
				if ( this.lastXhrPageReq ) {
					$c.abort(this.lastXhrPageReq);
				}
			
				// get the page 
				var rx = new RegExp("http(s)?:\/\/(www\.|networks\.|api\.)?twittangle\.com\/",'gi');
				
				// figure the page 
				var page = href.replace(rx,'');
				
					// no page 
					if ( page == "" || page == 'login' || page == 'logout' || page == 'start' ) {
						location.href = href; return;
					}			
				
				// parse out qp
				var path = page.split('?')[0];
					
					// is there a query
					if ( page.indexOf('?') != -1  ) {
						var query = page.split('?')[1].split(';');
					}
				
					// now get params 
					var params = {};
					
					// for each 
					for ( var q in query ) {
						params[query[q].split('=')[0]] = encodeURI(query[q].split('=')[1]);
					}	
			
				// now define our callback
				var callback = {
					'success': function(o) {
						
						// j
						var j;
						
						// try to parse. if we fail
						// we just redirect to the proper page
						try {
							j = $j.parse(o.responseText);
						}
						catch(e) { 
							window.location.href = o.argument[1]; return; 
						}
													
						// if theres a bad stat
						// we need to stop 
						if ( j.stat != 1 ) {
							window.location.href = o.argument[1]; return;
						}												
					
						// set class name and title
						document.getElementsByTagName('body')[0].className = j.c + ' yui-skin-sam';
						document.title = j.t;
						
							// r 
							if ( j.r ) {
								if ( $d.inDocument('rsslink') ) {
									$('rsslink').href = j.r;
								}
								else {
									var l = document.createElement('link');
										l.setAttribute('rel','alternate');
										l.href = j.r
										l.setAttribute('type','application/rss+xml');
										l.setAttribute('title','twitTangle.com');
										l.id = 'rsslink';
									document.getElementsByTagName('head')[0].appendChild(l);
								}
							}
							else if ( $d.inDocument('rsslink') ) {
								$('rsslink').parentNode.removeChild($('rsslink'))
							}
						
						if ( $d.inDocument('rateLimit') ) {
							$('rateLimit').innerHTML = j.l;
						}
						
						// remove any set event handlers 
						$e.purgeElement( $('bd'), true );
						
						// set the body 
						$('Content').innerHTML = j.html;					
						
						// bootstrap
						o.argument[0].bootstrap(j.bootstrap);						
						
						// scroll
						window.scrollTo(0,0);
						
						// done loading
						$l(0);
								
						// no bubble
						$d.setXY( $('title-bubble') ,[-999,-999]);				
						
						// remove
						if ( $d.inDocument('customStyle') ) {
							$('customStyle').parentNode.removeChild($('customStyle'));
						}
						
						// remove defer class
						$d.removeClass($d.getElementsByClassName('defer'),'defer');						
						
						// new 
						if ( j.p ) {
						
							try {

								var s = document.createElement('style');
									s.setAttribute('type','text/css');
									s.id = 'customStyle';
								
								if ( s.styleSheet ) {
									s.styleSheet.cssText = j.p;
								}
								else {
									s.innerHTML = j.p;
								}						
	
								// append
								document.getElementsByTagName('head')[0].appendChild(s);
								
							} catch(e) {}

							
						}								
						
						// get height of content
						var ch = parseInt($d.getStyle('Content','height'),10);
						var mh = parseInt($d.getStyle('yui-main','height'),10);
						
						// check main
						if ( mh < ch ) {
							
							// get b
							var el = $d.getElementsByClassName('yui-b','div',$('yui-main'))[0];
							if ( el ) {
								$d.setStyle(el,'height',ch+'px');
							}
							
						}
						
					},
					'failure' : function(o) {
						window.location.href = o.argument[1]; return;
					},
					'argument': [this,href],
					'timeout': 5000
				};
				
				// unload
				TT.executeUnLoadQueue();
				this.preLinks = null;
				this.linkGroups = [];
				
				// now build our url 
				var url = this.xhrUrl('context/'+path,params);							
				
				// no bubble
				$d.setXY( $('title-bubble') ,[-999,-999]);
				
				// loading
				$l(1);								
			
				// history
				try {				
					if (!history || !history.calledFromHistory) {					
						dsHistory.setQueryVar('p', page);
						dsHistory.bindQueryVars(this.loadXhrPage, this, 'http://'+subdomain+'.twittangle.com/'+page);					
					}
				} catch(e) {}
				
				// track 
				pageTracker._trackPageview(page); 
				
				// make the request
				this.lastXhrPageReq = $c.asyncRequest('GET',url,callback);						
			
			},
			
			
			/**
			 * bootstrap javascript
			 */
			bootstrap : function(data) {
			
				// check for bootstrap
				if ( data.js ) {
				
					// print out each block of javacript 
					for ( var s in data.js ) {
						eval(data.js[s]);
					}
										
				}		
				
				// execute the queue
				TT.executeQueue();				
					
			},			
			
			// xhr
			xhrUrl : function(act,args) {
				
				// qs 
				var qs = ['req='+this.params.req];
				
				// develope qs 
				for ( arg in args  ) {
					if ( typeof arg == 'string' ) {
						qs[qs.length] = arg + '=' + args[arg];
					}
				}
					
				// return it 
				return '/xhr/' + act + '?' + qs.join(';');
				
			}				
		
		}

// })();

/* Triangle */
var Triangle = function(m) { $('triangle').src = '/triangle.php?m='+escape(m); }


/*!
 * dsHistory, v1-beta1 $Rev: 70 $
 * Revision date: $Date: 2008-10-24 14:25:17 -0700 (Fri, 24 Oct 2008) $
 * Project URL: http://code.google.com/p/dshistory/
 * 
 * Copyright (c) Andrew Mattie (http://www.akmattie.net)
 * Licensed under the MIT License (http://www.opensource.org/licenses/mit-license.php)
 * THIS IS FREE SOFTWARE, BUT DO NOT REMOVE THIS COMMENT BLOCK
 */

var dsHistory = function() {
	// we need a good browser detection library. these detections were kindly borrowed from the Prototype library
	var browser = (function() {
		var userAgent = window.navigator.userAgent;
		var isIE = !!(window.attachEvent && !window.opera); // may want to rethink this in light of http://ejohn.org/blog/bad-object-detection/
		
		return {
			IE: isIE,
			IE6: isIE && userAgent.indexOf('MSIE 6') != -1,
			IE7: isIE && userAgent.indexOf('MSIE 7') != -1,
			Opera: !!window.opera && userAgent.indexOf('Opera'),
			WebKit: userAgent.indexOf('AppleWebKit/') > -1,
			Gecko: userAgent.indexOf('Gecko') > -1 && userAgent.indexOf('KHTML') == -1
		};
	})();
	var supportsChangingHistoryViaFrame = browser.IE || browser.Gecko;
	var supportsDataProtocol = browser.Gecko; // other browsers may support the data protocol, but if they don't support changing the history via a frame, they aren't in here
	var returnsEncodedWindowHash = browser.IE || browser.WebKit; // some browsers return the encoded value of the window hash vs the decoded value
	var fluxCapacitorInterval = 15;
	var lastFrameIteration = 0;
	var lastHash = lastRawHash = '';
	var encodeURIComponent = window.encodeURIComponent; // close a reference to this function since we'll be calling it so often and since it will be faster than going up the scope each time
	var dirtyHash = initialHash = getEncodedWindowHash(true);
	var hashCache = []; // holds all previous hashes
	var forwardHashCache = []; // hashes that are removed from hashCache as the user goes back are concat'd here
	var eventCache = []; // holds all events
	var forwardEventCache = []; // events that are removed from eventCache as the user goes back are concat'd here
	var isInHistory = false; // if we're somewhere in the middle of the history stack, this will be set to true
	var frameWindow; // since we're going to be looking at the internals of the frame so often, we will cache a reference to it and just unload it when the page unloads
	// if the reference to this script file is included in the head tag, frameWindow won't be set up properly. this will be a reference to the setInterval that will ultimately set up frameWindow in that case
	var frameWindowWatcher;
	var executionQueue = []; // if the frameWindow wasn't set up on load, we need a place to queue up actions until it's available
	var watcherInterval; // save the handle returned by setInterval so we can unregister it on unload
	var isGoingBackward, isGoingForward; // assists us in knowing whether we're going back through the history or forward
	var usingStringIndicators = false; // if the developer called dsHistory.setUsingStringIndicators(), this will be set to true
	var returnObject;
	
	// internal function to make sure we don't leave any memory leaks when the visitor leaves
	function unload() {
		window.clearInterval(watcherInterval);
		frameWindow = null;
		eventCache = null;
	};
	// internal function to curry the scope argument and object argument (if either) so that the subscriber can be called once it's appropriately hit in the
	// history
	function internalCurry(fnc, scope, objectArg) {
		if (typeof objectArg != 'undefined') {
			return function(historyObj) {
				fnc.call(scope || window, objectArg, historyObj);
			};
		} else {
			return function(historyObj) {
				fnc.call(scope || window, historyObj);
			};
		}
	};
	// internal function to return the iteration we are on
	function readIteration() {
		// lazy function definition pattern used for performance
		if (!supportsChangingHistoryViaFrame) {
			readIteration = function() {
				return 0;
			};
		} else if (supportsDataProtocol) {
			readIteration = function() {
				return frameWindow.document.body ? parseInt(frameWindow.document.body.textContent) : 0;
			};
		} else {
			readIteration = function() {
				return parseInt(frameWindow.document.body.innerText);
			};
		}
		
		return readIteration();
	};
	// internal function to save the iteration we are on
	function writeIteration(iteration) {
		// lazy function definition pattern used for performance
		if (supportsDataProtocol) {
			writeIteration = function(iteration) {
				frameWindow.document.body.textContent = String(iteration);
			};
		} else {
			writeIteration = function(iteration) {
				frameWindow.document.body.innerText = String(iteration);
			};
		}
		
		writeIteration(iteration);
	};
	// internal function to get the decoded value of a (sub)string from the hash
	function getDecodedHashValue(value) {
		if (returnsEncodedWindowHash) {
			var decodeURIComponent = window.decodeURIComponent;
			
			getDecodedHashValue = function(value) {
				return decodeURIComponent(value);
			};
		} else {
			getDecodedHashValue = function(value) {
				return value;
			};
		}
		
		return getDecodedHashValue(value);
	};
	// internal function to return the window hash after the keys and values have been run through encodeURIComponent
	function getEncodedWindowHash(forceRecompute) {
		var hash = window.location.hash;
		
		// there's no need to go through this function again if the hash that was read out (encoded or decoded, doesn't matter) is the same hash that was read out last time.
		// whenever lastHash is set, it's set to the return value of the function
		if (!forceRecompute && hash == lastRawHash) return lastHash;
		lastRawHash = hash;
		
		var hashItems = hash.substring(1).split('&');
		var encodedHash;
		
		// for performance, we'll assume that if we're doing more than 9 concats that it will be quicker to use and array and then use the .join('&') trick
		if (hashItems.length > 9) {
			var encodedHashItems = [];
			
			for (var i = 0, len = hashItems.length; i < len; ++i) {
				hashSplit = hashItems[i].split('=');
				encodedHashItems.push(encodeURIComponent(getDecodedHashValue(hashSplit[0])) + (hashSplit.length == 2 ? '=' + encodeURIComponent(getDecodedHashValue(hashSplit[1])) : ''));
			}
			encodedHash = encodedHashItems.join('&');
		} else {
			encodedHash = ''
			for (var i = 0, len = hashItems.length; i < len; ++i) {
				hashSplit = hashItems[i].split('=');
				encodedHash += (i == 0 ? '' : '&') + encodeURIComponent(getDecodedHashValue(hashSplit[0])) + (hashSplit.length == 2 ? '=' + encodeURIComponent(getDecodedHashValue(hashSplit[1])) : '');
			}
		}
		
		return encodedHash;
	};
	// internal function to load and split our query vars into our QueryElements object
	function loadQueryVars() {
		// flush out the object each time this is called
		returnObject.QueryElements = {};
		
		if (window.location.hash == '' || window.location.hash == '#') return;
		
		var hashItems = window.location.hash.substring(1).split('&');
		var hashSplit;
		
		for (i = 0, len = hashItems.length; i < len; ++i) {
			hashSplit = hashItems[i].split('=');
			returnObject.QueryElements[getDecodedHashValue(hashSplit[0])] = hashSplit.length == 2 ? getDecodedHashValue(hashSplit[1]) : '';
		}
		
		lastHash = getEncodedWindowHash(true);
	};
	// internal function to be called when we want to actually add something to the browser's history
	function updateFrameIteration(comingFromQueryBind) {
		var currentIteration = supportsChangingHistoryViaFrame ? readIteration() : 0;
		var lastEvent, newEvent;
		
		// it seems that gecko has a sweet bug / feature / something that prevents the history from changing with a frame iteration after a hash has changed the history
		// therefore, we have to mess with the hash enough to get it to add to the browser's history and then change it back so we don't screw up any values in the hash
		if ( (hashCache.length > 0 && browser.Gecko) || browser.WebKit || (!supportsChangingHistoryViaFrame && readIteration() > 0) ) {
			
			// since it's not IE, and since other browsers don't seem to have a performance problem with setting the window hash when there are lots of
			// elements on a page, we're not going to worry about handling the defer processing attribute here
			
			if (lastHash == '' && hashCache.length > 1) {
				window.location.hash = '_'; // this can be anything, as long as the hash changes
				lastHash = getEncodedWindowHash(true);
				hashCache.push(lastHash);
			} else if (lastHash != '' || browser.WebKit) {
				// splice the event off the stack so we can add it on later
				lastEvent = eventCache.splice(eventCache.length - 1, 1)[0];
				
				window.location.hash = lastHash + String(hashCache.length); // this can be anything, as long as the hash changes
				hashCache.push(lastHash + String(hashCache.length));
				
				// lastHash should only be empty if the browser is WebKit.
				window.location.hash = lastHash == '' ? '-' : lastHash; // the value if lastHash is empty can't be the same as the value in the if-case above
				hashCache.push(lastHash == '' ? '-' : lastHash);
				
				// since we popped off the last event on the history stack, we're going to add it back on _after_ we add on a function to get back to our unadultered hash
				eventCache.push(function(indicator) {
					if (usingStringIndicators ? indicator : indicator.direction == 'back') {
						isGoingBackward = true;
						window.history.back();
					} else {
						isGoingForward = true;
						window.history.forward();
					}
				});
				eventCache.push(lastEvent);
			}
			
			return;
		}
		
		// there's no reason to change the frame source if we're only adding the first event to the history
		if (
			currentIteration == 0
			&& ( (hashCache.length == (comingFromQueryBind ? 1 : 0) && !browser.IE) || (hashCache.length == 2 && browser.IE) ) // extra hash for ie
			&& eventCache.length <= 1
			) {
			writeIteration(1);
		} else {
			if (supportsDataProtocol)
				document.getElementById('dsHistoryFrame').src = 'data:,' + String(currentIteration + 1);
			else {
				frameWindow.document.open();
				frameWindow.document.write(String(currentIteration + 1));
				frameWindow.document.close();
			}
		}
	};
	// internal function that is called every few ms to check to see if we've gone back or forward in time
	function fluxCapacitor() {
		var frameIteration = supportsChangingHistoryViaFrame ? readIteration() : 0;
		var windowHash = getEncodedWindowHash();
		
		// if the frame iteration is different or the window hash is different, we'll start a sequence of events to go back in time
		if (
			!isGoingForward
			&& (
				frameIteration < lastFrameIteration
				|| (lastHash != windowHash && hashCache[hashCache.length - 2] == windowHash && !browser.IE)
				)
			) {
			
			// this will be the pre-qual for our people hitting the forward button
			isInHistory = true;
			isGoingBackward = false;
			
			// if the hash has changed, or if we're using IE (in which case we change the hash with every event), make sure we
			// keep the hashCache and related items up-to-date
			if ((lastHash != windowHash && hashCache[hashCache.length - 2] == windowHash) || browser.IE) {
				forwardHashCache = forwardHashCache.concat(hashCache.splice(hashCache.length - 1, 1));
				
				// IE doesn't change the window hash when the user goes back, so we have to do it manually from our hashCache
				if (browser.IE) {
					if (returnObject.deferProcessing) {
						window.setTimeout(function() { window.location.hash = hashCache[hashCache.length - 1]; }, 10);
					} else {
						window.location.hash = hashCache[hashCache.length - 1];
					}
				}
				
				// we need to set this here so that if history.back() is one of the functions on the eventCache,
				// it will know we're on a different hash
				lastHash = getEncodedWindowHash(true);
				dirtyHash = lastHash;
			}
			
			// subtract 2 from eventCache.length since we're gonna end up calling the second function from the end when someone clicks the
			// back button. we can assume that another function was pushed onto the stack since the time the function we are going to call was
			// added. essentially, the last function in the array is the function we are on now, so we need to ignore it in here.
			
			// all functions that are pushed onto the history stack must consume either the 'back' string or, preferrably, the object literal
			// containing the history event information. this must be done to prevent having the called function from pushing itself back onto
			// the history stack as soon as it is called.
			
			if (eventCache.length > 1) {
				eventCache[eventCache.length - 2](usingStringIndicators ? 'back' : { calledFromHistory: true, direction: 'back' });
				forwardEventCache = forwardEventCache.concat(eventCache.splice(eventCache.length - 1, 1));
			}
		}
		
		// handle the forward button. we determine whether we're moving forward if 1) the user hit the back button and we haven't added a
		// function or bound the query vars since, 2) we haven't hit our built-in history.back function to work around gecko's
		// updating-frame-doesnt-update-history-after-hash-has-been-added bug, and 3) the secondary conditions that allow us to know
		// whether we're going back in our history are inversed
		
		else if (
			isInHistory
			&& !isGoingBackward
			&& (
				frameIteration > lastFrameIteration
				|| (lastHash != windowHash && forwardHashCache[forwardHashCache.length - 1] == windowHash && !browser.IE)
				)
			) {
			isGoingForward = false;
			
			// the internals of this are nearly the same as the way we handle the visitor going back, except we use different caches
			// for reading the events and hashes we spliced off as we went back
			if ((lastHash != windowHash && forwardHashCache[forwardHashCache.length - 1] == windowHash) || browser.IE) {
				if (browser.IE)
					window.location.hash = forwardHashCache[forwardHashCache.length - 1];
				lastHash = getEncodedWindowHash(true);
				dirtyHash = lastHash;
				hashCache = hashCache.concat(forwardHashCache.splice(forwardHashCache.length - 1, 1));
			}
			
			// see the notes above about the called function consuming the argument
			forwardEventCache[forwardEventCache.length - 1](usingStringIndicators ? 'forward' : { calledFromHistory: true, direction: 'forward' });
			eventCache = eventCache.concat(forwardEventCache.splice(forwardEventCache.length - 1, 1));
		}
		
		// so we always have something to compare to the next time this is called
		lastFrameIteration = frameIteration;
	};
	
	returnObject = {
		QueryElements: {}, // name/value collection to hold the values in the window hash
		// if there are a ton of elements on a page, IE can chunk when the window hash is set since, i assume, it's trying to find the element on that
		// page that has a matching name attribute. while we can't speed it up any, we can at least appear to make it go faster by deferring the hash
		// setter until the next cycle so that the UI can update
		deferProcessing: false,
		initialize: function(initFnc) {
			// the library itself is actually initialized before the anonymous function that is this library is returned, but we use
			// this function call for backwards compatibility
			
			if (typeof initFnc == 'function') initFnc();
		},
		addFunction: function(fnc, scope, objectArg) {
			if (supportsChangingHistoryViaFrame && (!frameWindow || !frameWindow.document || !frameWindow.document.body)) {
				executionQueue.push({type: arguments.callee, fnc: fnc, scope: scope, objectArg: objectArg});
				return;
			}
			// flush out anything that would have been used for the forward action if the user had used the back action
			isInHistory = false;
			forwardEventCache = [];
			forwardHashCache = [];
			
			// with IE, we want to make sure they're a hash entry put into the cache every time we change the frame
			// since moving back won't change the location hash. we'll use the hash cache to manually change the cache then.
			if (browser.IE)
				hashCache.push(getEncodedWindowHash());
			
			eventCache.push(internalCurry(fnc, scope, objectArg));
			updateFrameIteration();
		},
		// this will conditionally add or update the name / value that was passed in. it will also add / update the QueryElements object
		setQueryVar: function(key, value) {
			var encodedKey, encodedValue;
			
			key = String(key);
			value = String(typeof value == 'undefined' ? '' : value);
			
			encodedKey = key;
			encodedValue = value;

				dirtyHash = key+"="+value;
			
/*
			if (dirtyHash == '#' || dirtyHash == '' || dirtyHash.indexOf('#_serial') == 0) {
				if (encodedValue != '')
					dirtyHash = '#' + encodedKey + '=' + encodedValue;
				else
					dirtyHash = '#' + encodedKey;
			} else {
				if (typeof this.QueryElements[key] != 'undefined' && value != '') {
					dirtyHash = dirtyHash.substr(0, dirtyHash.indexOf(encodedKey) + encodedKey.length + 1) + encodedValue + dirtyHash.substr(dirtyHash.indexOf(encodedKey) + encodedKey.length + 1 + String(encodeURIComponent(this.QueryElements[key])).length);
				} else if (typeof this.QueryElements[key] == 'undefined') {
					if (value == '') {
						dirtyHash += '&' + encodedKey;
					}
					else {
						dirtyHash += '&' + encodedKey + '=' + encodedValue;
					}
				}
			}
*/
			
			this.QueryElements[key] = value;
			
			if (hashCache > 1 && hashCache[hashCache.length - 2] == dirtyHash)
				dirtyHash += '&_serial=' + hashCache.length;
			else if (dirtyHash.indexOf('_serial') != -1)
				this.removeQueryVar('_serial');
		},
		// this will remove the property of the QueryElements object and remove the name and value of the object in the dirtyHash
		removeQueryVar: function(key) {
			if (!this.QueryElements[key] && key != '_serial') return;
			
			var dataToStrip, indexOfData, removeAmpersand;
			
			if (this.QueryElements[key] == '')
				dataToStrip = encodeURIComponent(key);
			else
				dataToStrip = encodeURIComponent(key) + '=' + encodeURIComponent(this.QueryElements[key]);
			
			indexOfData = dirtyHash.indexOf(dataToStrip);
			if (dirtyHash[indexOfData - 1] == '&') {
				dataToStrip = '&' + dataToStrip;
				indexOfData--;
			}
			dirtyHash = dirtyHash.substr(0, indexOfData) + dirtyHash.substr(indexOfData + dataToStrip.length);
			if (dirtyHash[0] == '&')
				dirtyHash = dirtyHash.substr(1, dirtyHash.length - 1);
			
			delete this.QueryElements[key];
			
			// if the hash is empty, a serial number is appended to it so we can keep track of whether we're going forward or backward in the
			// frame watcher. this is needed specifically when a value is added, removed, and added again.
			if (dirtyHash == '#' || dirtyHash == '') dirtyHash = '_serial=' + hashCache.length;
		},
		// the time in Gecko browsers.
		// we don't want to update the window has until this function is called since, otherwise, the history will change all
		bindQueryVars: function(fnc, scope, objectArg, continueProcessing) {
			if (supportsChangingHistoryViaFrame && (!frameWindow || !frameWindow.document || !frameWindow.document.body)) {
				executionQueue.push({type: arguments.callee, fnc: fnc, scope: scope, objectArg: objectArg});
				return;
			}
			// if desired, one could check if the result of this function is === false and operate accordingly. shouldn't really be necessary though
			// dirty hash will always be encoded, so replace('#', '') will only replace the inital # if it's there
			if (getEncodedWindowHash() == dirtyHash.replace('#', '') && eventCache.length > 0) return false;
			
			// if the option to defer processing has been set and our continueProcessing argument has been set, defer the function call to the
			// next available cycle so that the UI can update and other processing can continue. 
			if (this.deferProcessing && !continueProcessing) {
				var currentFnc = arguments.callee;
				window.setTimeout(function() { currentFnc(fnc, scope, objectArg, true) }, 10);
				return;
			}
			
			// flush out anything that would have been used for the forward action if the user had used the back action
			isInHistory = false;
			forwardEventCache = [];
			forwardHashCache = [];
			
			// so we have an empty hash to go back to on our first time around (but only if we're not using IE since otherwise we're adding
			// to the hashCache every single time anyway)
			if (hashCache.length == 0 && eventCache.length > 0 && !browser.IE)
				hashCache.push(getEncodedWindowHash());
			
			window.location.hash = dirtyHash;
			lastHash = getEncodedWindowHash(true);
			
			hashCache.push(lastHash);
			eventCache.push(internalCurry(fnc, scope, objectArg));
			
			if (browser.IE)
				updateFrameIteration(true);
			
			loadQueryVars();
		},
		setFirstEvent: function(fnc, scope, objectArg) {
			if (eventCache.length > 0)
				eventCache[0] = internalCurry(fnc, scope, objectArg);
		},
		setUsingStringIndicators: function() {
			// use a setter function for this deprecated functionality instead of a property so that calls to this function will error once it's removed
			usingStringIndicators = true;
		}
	};
	
	// initialize the library
	
	// we use a frame to track history all the time in IE since window.location.hash doesn't update the history.
	// in Gecko, we only use a frame to track history when we're not also trying to update the window hash.
	// in WebKit, we mess with the hash just a little bit to change the history if they aren't explicitly changing the window hash
	if (supportsChangingHistoryViaFrame) {
		if (supportsDataProtocol)
			document.write('<iframe id="dsHistoryFrame" name="dsHistoryFrame" style="display:none" src="data:,0"></iframe>');
		else
			document.write('<iframe id="dsHistoryFrame" name="dsHistoryFrame" style="display:none" src="javascript:document.open();document.write(\'0\');document.close();"></iframe>');
		
		frameWindow = window.frames['dsHistoryFrame'];
		if (!frameWindow || !frameWindow.document || !frameWindow.document.body) {
			frameWindowWatcher = window.setInterval(function() {
				frameWindow = window.frames['dsHistoryFrame'];
				if (frameWindow && frameWindow.document && frameWindow.document.body) {
					window.clearInterval(frameWindowWatcher);
					watcherInterval = window.setInterval(fluxCapacitor, fluxCapacitorInterval);
					
					for (i = 0, len = executionQueue.length; i < len; ++i) {
						var executionItem = executionQueue[i];
						executionItem.type(executionItem.fnc, executionItem.scope, executionItem.objectArg);
					}
					executionQueue = null;
				}
			}, 50);
		} else {
			watcherInterval = window.setInterval(fluxCapacitor, fluxCapacitorInterval);
		}
	} else {
		watcherInterval = window.setInterval(fluxCapacitor, fluxCapacitorInterval);
	}
	
	if (browser.IE || browser.WebKit)
		hashCache.push(initialHash);
	//if (browser.WebKit && window.location.hash == '')
		//window.location.hash = '_';
	
	// initialize the QueryElements object
	loadQueryVars();
	
	// make sure we don't leave any memory leaks when the visitor leaves
	if (window.addEventListener)
		window.addEventListener('unload', unload, false);
	else if (window.attachEvent)
		window.attachEvent('onunload', unload);
	
	// end initialization
	
	return returnObject;
}();