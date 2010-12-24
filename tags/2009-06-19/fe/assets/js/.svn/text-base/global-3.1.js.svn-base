

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

	$d.setStyle('loading','display',( on ? 'block' : 'none')); 
		
	$d.setStyle('loading','height',$d.getDocumentHeight()+'px');
	
	$d.setY('loading-wrap',parseInt($d.getDocumentScrollTop(),10)+100 );
	
};

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
(function(){

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
		
			/* holders */
			params: {},
			flags: {},
			updates: [],
			
		
			/* init */
			init : function(p) {			
			
				// add our first entry
				dsHistory.addFunction( this.loadXhrPage, this, {'href':location.href} );
			
				// fold
				foldGroup.fetch();
				
				// global params
				this.params = p;		
				
				// set events
				$e.on('doc4','click',this.click,this,true);
				$e.on('doc4','mouseover',this.mouseover,this,true);
				$e.on('doc4','mouseout',this.mouseout,this,true);

				
				// check for status box
				if ( $d.inDocument('update-status') ) {
					$e.on('update-status-txt','keydown',function(){
						
						// len
						var len = $('update-status-txt').value.length;
						
						var max = parseInt($('update-status-chars').getAttribute('max'),10);
						
						// what up 
						$('update-status-chars').innerHTML = max - len;
						
					});
					$e.on('update-status-txt','keyup',function(){
						
						// len
						var len = $('update-status-txt').value.length;
						
						var max = parseInt($('update-status-chars').getAttribute('max'),10);
						
						// what up 
						$('update-status-chars').innerHTML = max - len;
						
					});					
				}			
				
				// watch saved searched
				this.watchSavedSearch();	
				
				// execute queue
				$e.on(window,'load',function(){	
	
					// execute			
					TT.executeQueue();				
				
					// timeline
					if ( $d.inDocument('timeline') ) {
						this.parseTimelineLinks();
					}				
				
					// attach autocomplete
					$e.on('search-txt','keydown',this.searchSuggest,this,true);
					$e.on('search-txt','keyup',this.searchSuggest,this,true);					
				
				},this,true);
			
			},
			
			stopForm : function() {
				return false;
			},
			
			/* click */
			click : function(e) {	
				
				// tar
				var tar = $e.getTarget(e);
				var oTar = tar;
								
				// e
				if ( $d.hasClass(tar,'user-panel-submit') ) {
				
					// no click
					$e.stopEvent(e);				
				
					// panel
					this.submitUserPanel(tar);
					
				}
				else if ( $d.hasClass(tar,'side-menu-expand') ) {
					
					// stop
					$e.stopEvent(e); tar.blur();					
					
					// get otp
					var top = $d.isGoodTarget(tar,'side-menu-top');
					
					// list
					var list = top.getElementsByTagName('ul')[0];
					
						// no list
						if ( !list ) {
							return;
						}
					
					// is open
					if ( $d.hasClass(top,'open') ) {
						
						// close it
						var a = new $a(list,{'height':{'to':0},'opacity':{'to':0}},.2);
							a.onComplete.subscribe(function(){
								$d.removeClass(top,'open');
								$d.setStyle(list,'height','');
								$d.setStyle(list,'display','none');
							});
							a.animate();
					
					}
					else {
					
						// get height
						$d.setStyle(list,'opacity',0);
						$d.setStyle(list,'display','block');
						
						// get height
						var h = parseInt($d.getStyle(list,'height'),10);	
					
						// set height
						$d.setStyle(list,'height','0px');
						
						console.log(h);
					
						// close it
						var a = new $a(list,{'height':{'to':h},'opacity':{'to':1}},.2);
							a.onComplete.subscribe(function(){
								$d.addClass(top,'open');	
							});
							a.animate();					
					
					}
				
				}
				else if ( $d.hasClass(tar,'search-btn') ) {
				
					// stop
					$e.stopEvent(e);
					
					// close
					this.closeSearchSuggest();
				
				}
				else if ( $d.hasClass(tar,'ext-link-catch') && tar.tagName.toLowerCase() == 'a' ) {
				
					// stop
					$e.stopEvent(e); tar.blur;
					
					// open
					window.open(tar.href);
				
				}
				else if ( $d.hasClass(tar,'update-status-tiny-btn') ) {
				
					// stop evnet
					$e.stopEvent(e);
					tar.blur();
					
					// do it
					this.doTinyUrl();				
				
				}
				else if ( $d.hasClass(tar,'update-status-tiny') ) {
					
					// stop evnet
					$e.stopEvent(e);
					tar.blur();
					
					if ( $d.getStyle('update-status-o-tiny','display') == 'block' ) {
						
						// reset 
						tar.innerHTML = "Tiny Link";
					
						// hide
						$d.setStyle('update-status-o-tiny','display','none');
						
					}
					else {
					
						// reset 
						tar.innerHTML = "Tiny Link (close)";					
					
						// display
						$('update-status-tiny-txt').focus();
						$d.setStyle('update-status-o-tiny','display','block');
						
					}
				
				}				
				else if ( $d.hasClass(tar,'update-status-upload') ) {
					
					// stop evnet
					$e.stopEvent(e);
					tar.blur();					
					
					if ( $d.getStyle('update-status-o-photo','display') == 'block' ) {
					
						// tar
						tar.innerHTML = "Upload Photo";
					
						// change
						$d.setStyle('update-status-o-photo','display','none	');
						
					}
					else {
					
						// tar
						tar.innerHTML = "Upload Photo (close)";
					
						// block
						$d.setStyle('update-status-o-photo','display','block');
						
					}
				
				}
				else if ( $d.hasClass(tar,'show-reply') ) {
				
					// stop click
					$e.stopEvent(e);	tar.blur();
					
					// show
					this.showInlineReply(tar);
				
				}
				else if ( $d.hasClass(tar,'delete-search') ) {
					
					// stop 
					$e.stopEvent(e);
					
					// to 
					this.removeSavedSearch(tar);
				
				}
				else if ( $d.hasClass(tar,'clear-status') ) {
				
					// no click
					$e.stopEvent(e);					
				
					// reset count
					$('update-status-txt').value = "";
					$('update-status-chars').innerHTML = $('update-status-chars').getAttribute('max');
					
					// title
					$('update-status-title').innerHTML = 'What are you doing?';
					$('update-status-reply').value = '0';				
				
				}
				else if ( $d.hasClass(tar,'reply') || $d.hasClass(tar,'retweet') ) {
				
					// no click
					$e.stopEvent(e);
					
					// do 
					this.doReply(tar);
					
				}
				else if ( $d.hasClass(tar,'fav') ) {
				
					// stop 
					$e.stopEvent(e);
					
					// do
					this.doFav(tar);
					
				}
				else if ( $d.hasClass(tar,'user-panel') ) {
					
					// stop click
					$e.stopEvent(e);
					
					// scroll to
					window.scrollTo(0,$d.getY('user-panel'));
					
					// load
					this.loadUserPanel(tar);
					
				}
				else if ( $d.hasClass(tar,'save-this-search') ) {
				
					// no click
					$e.stopEvent(e);
				
					// save
					this.saveSearch(tar);
					
				}
				else if ( $d.hasClass(tar,'update-status-btn') ) {

					// no click
					$e.stopEvent(e);
				
					// save
					this.submitUpdate(tar);
				
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
				else if ( (tar = $d.isGoodTarget(oTar,'user-panel')) ) {
					
					// stop 
					$e.stopEvent(e);
					
					// do
					this.displayUserPanelOverlay(tar);
				
				}				
				else if ( this.params.loged && !$d.hasClass(oTar,'ignore') && ( tar = $d.isGoodTarget(oTar,{'tag':'a'}) ) ) {
				
					// load some xhr
					this.loadXhrPage(tar,e);
					
				}									
			
			},
			
			/* mouseover */
			mouseover : function(e) {
			
				// target 
				var tar = $e.getTarget(e);
				var oTar = tar;
				
				// what 
				if ( $d.hasClass(tar,'bubble') ) {
					this.titleBubble(tar,e,'on');
				}				
				else if ( $d.hasClass(tar,'user-panel') ) {
			//		this.showUserPanel(tar);
				}
				else if ( ( tar = $d.isGoodTarget(oTar,'update',10) ) ) {
					$d.addClass(tar,'write');
				}
				else if ( ( tar = $d.isGoodTarget(oTar,'saved',10) ) ) {
					
					// not logged or on search
					if ( !this.params.loged || $d.hasClass(document.body,'search') ) {
						return;
					}										
					
					// open the saved
					$d.setStyle('saved-list','display','block');
					$d.setStyle('search','z-index','30');
				
				}
				else if ( $d.hasClass(oTar,'accounts-top') || ( tar = $d.isGoodTarget(oTar,'accounts-wrap',10) ) ) {
												
					// clear
					clearTimeout(this.flags.accountsTo);
				
					// show accounts
					$d.setStyle('accounts','display','block');
				
				}
				else if ( ( tar = $d.isGoodTarget(oTar,'status') ) ) {
					
					// turn it on
					$d.addClass(tar,'on');
				
				}
				else if ( ( tar = $d.isGoodTarget(oTar,'user-panel-content',30) ) ) {
					clearTimeout(this.flags.userPanelOutLastTo);
				}				
			
			},
			
			/* mouseout */ 
			mouseout : function(e) {

				// target 
				var tar = $e.getTarget(e);
				var oTar = tar;			
				
			
				// what 
				if ( $d.hasClass(tar,'bubble') ) {
					this.titleBubble(tar,e,'out');
				}
				else if ( $d.hasClass(oTar,'user-panel') ) {
			//		clearTimeout(this.flags.userPanelTo);
				}				
				else if ( ( tar = $d.isGoodTarget(tar,'saved',20) ) ) {
					
					// not logged or on search
					if ( !this.params.loged || $d.hasClass(document.body,'search') ) {
						return;
					}					
					
					// open the saved
					$d.setStyle('saved-list','display','none');
					$d.setStyle('search','z-index','0');
				
				}
				else if ( ( tar = $d.isGoodTarget(oTar,'update',20) ) ) {
					
					// does txt have focus
					if ( $d.hasClass('update-status-txt','focus') || $d.hasClass('update-status','sticky')) {
						return;
					}
					
					if ( $d.getStyle('update-status-o-photo','display') == 'block' || $d.getStyle('update-status-o-tiny','display') == 'block' ) {
						return;
					}
					
					// turn off write
					$d.removeClass(tar,'write');
					
				}				
				else if ( ( tar = $d.isGoodTarget(oTar,'status') ) ) {
					
					// turn it on
					$d.removeClass(tar,'on');
				
				}
				else if ( $d.hasClass(oTar,'accounts-top') || ( tar = $d.isGoodTarget(oTar,'accounts-wrap',10) ) ) {
				
					// clear
					this.flags.accountsTo = window.setTimeout(function(){

						// show accounts
						$d.setStyle('accounts','display','none');
					
					},400);
	
				}				
				else if ( ( tar = $d.isGoodTarget(oTar,'user-panel-content',30) ) ) {				
				
					// not if we're saving
					if ( this.flags.userPanelSaving || $d.hasClass('user-panel','sticky') ) {
						return;
					}
					
					// set timeout
					this.flags.userPanelOutLastTo = setTimeout(function(){
					
						// fade the timeline
						if ( $d.inDocument('timeline') ) {
							var a = new $a('timeline',{opacity:{to:1}},.2);
								a.animate();
						}					
					
						// set 
						$d.setStyle('user-panel-content','width','50px');
						$d.setStyle('user-panel-content','height','50px');
					
						// remove pannel
						$d.setXY('user-panel',[-999,-999]);
						
					},300);
				
				}
			
			},
			
			/* displayUserPanelOverlay */
			displayUserPanelOverlay : function(tar) {
				
				// blur
				tar.blur();
				
				// loading
				$d.addClass(tar,'loading');
				
				// local
				var t = tar;
				
				// what 
				if ( !this.userPanelOverlay ) {
					this.userPanelOverlay = new $Y.twitTangle.Panel({
						'name': 'userpanel',
						'overlay': true,
						'noOpen': true
					});
				}
			
				// load
				this.userPanelOverlay.load({
					'url': this.xhrUrl('userpanel',{'id':tar.id.split('|')[1]}),
					'open': true,
					'onLoad': function() {
				
						// make slide
						var slide = YAHOO.widget.Slider.getHorizSlider("slider-bg-new","slider-thumb-new",0,200); 
					
						// set value
						slide.subscribe('change', function (newOffset) { 
							$("user-panel-rate-new").value = newOffset*2;
						});			
						
						slide.setValue(parseInt($("user-panel-rate-new").value)/2);		
						
						$d.removeClass(t,'loading');			
					
					}
				});
							
			
			},
			
			/* closeSearchSuggest */
			closeSearchSuggest : function() {
			
				// not open
				if ( !$d.hasClass('search','open') ) {
					return;
				}
						
				// close 
				$d.removeClass('search','open');
				
				$d.setStyle('search-result','height','auto');
				$d.setStyle('search-result','overflow','hidden');				
				
				var a = new $a('search-result',{'height':{'to':0},'opacity':{'to':0}},.5);
					a.onComplete.subscribe(function(){
						$d.setStyle('search-result','height','auto');
						$d.setStyle('search-result','overflow','visible');
						$d.setStyle('search-result','display','none');
						$d.setStyle('search-result','opacity',1);
						$('search-twitter').innerHTML = "";
						$('search-timeline').innerHTML = "";																				
					});
					a.animate();			
			
			},
			
			/* do account */
			addAccountCallback : function(data) {
			
				// error
				if ( data.error ) {
					$('account-add-err').innerHTML = data.error;
				}
			
			},
			
			/*  initNonGroupSelector */
			initNonGroupSelector : function(friends,groups) {
			
				// holders
				this.flags.groupsDD = {};
				this.flags.friendsDD = {};
			
				// go through each group
				for ( var g in groups ) {
					this.flags.groupsDD[g] = new YAHOO.util.DDTarget("group-"+g,'groups');
				}			
			
				// go through each friend
				for ( var f in friends ) {
					this.flags.friendsDD[friends[f].id] = new $Y.twitTangle.Global.DDGroupsItem("usr-"+friends[f].id,'groups');
				}	
				
				// globalize friends
				this.flags.friendsData = friends;	
			
			},
			
			/* init dm */
			initDirectMessage : function() {
			
			
				// ds 
				var ds = new YAHOO.util.XHRDataSource( TT.Global.xhrUrl('friendSearch'), {'connMethodPost':true} ); 
					ds.responseType = YAHOO.util.XHRDataSource.TYPE_JSON;								
					ds.responseSchema = { 
						resultsList : 'resp', 
						fields : ['name','id','img','sn'] 
					}; 
				
				// data 
				var ac = new YAHOO.widget.AutoComplete(
					'dm-ac',
					'dm-results', 
					ds,
					{
						'useShadow': true,
						'queryDelay': .5,
						'typeAhead': true,
						'applyLocalFilter': true,
						'queryMatchSubset': true,
						'forceSelection': true
					});
				
				// when selected
				ac.itemSelectEvent.subscribe(function(type,args) {
					
					// get lit
					var list = $('dm-to').value.split(',');
					
					// zero
					if ( $('dm-to').value == "" ) {
						$('dm-to').value = args[2][1];
						$('dm-list').innerHTML = args[2][0];						
					}
					else {					
						$('dm-to').value += ','+args[2][1];
						$('dm-list').innerHTML += ", "+args[2][0];
					}
					
					// clear
					$('dm-ac').value = "";					
					
					// at 10
					if ( list.length == 9 ) {
						$('dm-ac').parentNode.removeChild($('dm-ac'));
					}				
				
				});
				
				ac.containerExpandEvent.subscribe(function() {
					var re = $('dm-results');
					var txt = $('dm-ac');
					$d.setXY(re,[$d.getX(txt),$d.getY(txt)+25]);
				});
			

				$e.on('dm-txt','keydown',function(){
					
					// len
					var len = $('dm-txt').value.length;
					
					var max = 140;
					
					// what up 
					$('dm-chars').innerHTML = max - len;
					
				});
				$e.on('dm-txt','keyup',function(){
					
					// len
					var len = $('dm-txt').value.length;
					
					var max = 140;
					
					// what up 
					$('dm-chars').innerHTML = max - len;
					
				});			
									
			
			},
			
			/* search suggest */
			searchSuggest : function(e) {
				
				// q
				var q = $('search-txt').value;							
			
				// not logged or on search
				if ( !this.params.loged || $d.hasClass(document.body,'search') ) {
				
					if ( e.charCode == 13 || e.keyCode == 13 ) {
						
						// stop
						$e.stopEvent(e);											
						
						// load search page
						this.loadXhrPage({'href':'/search?q='+escape(q)});						
						
					}
				
					// stop
					return;
					
				}
			
				// clear
				clearTimeout(this.flags.searchSugg);
				
					// no q
					if ( q == "" || q == this.flags.searchSuggLast ) {
						return;
					} 
				
				// self
				var self = this;
				
				$c.abort(this.flags.searchSuggXHR);
				$c.abort(this.flags.searchSuggTwitterXHR);
				
				var tcallback = {
					'success': function(o) {
					
						// parse
						var j = $j.parse(o.responseText);
						
						// no r
						if ( j.stat != 1 || !j.resp ) {
							return;
						}
					
						// just add
						$('search-twitter').innerHTML = "<em class='title'>All Twitter</em><ul class='timeline'>" + j.resp.html+"</ul>";
						
						// defer
						$d.removeClass( $d.getElementsByClassName('defer','img',$('search-result')), 'defer' );
					
						// bs
						o.argument[0].bootstrap({'js':j.resp.bootstrap});	
						
						// parse any links	
						o.argument[0].parseTimelineLinks();				
						
						// add to raw
						for ( var r in j.resp.raw ) {
							TT.data.timeline[r] = j.resp.raw[r];
						}														
					
					},
					'argument': [self]
				};				
				
					var turl = self.xhrUrl('searchtwitter',{'q':q});				
				
				// set 
				this.flags.searchSugg = window.setTimeout(function(){
				
					// callback
					var callback = {
						'success': function(o) {
						
							o.argument[0].flags.searchSuggTwitterXHR = $c.asyncRequest('GET',turl,tcallback);						
						
							// parse
							var j = $j.parse(o.responseText);
							
							// no r
							if ( j.stat != 1 || !j.resp ) {
								return;
							}
						
							// just add
							$('search-timeline').innerHTML = j.resp.html;
							
							// defer
							$d.removeClass( $d.getElementsByClassName('defer','img',$('search-result')), 'defer' );
						
							// nope
							$d.removeClass('search','loading');
							$d.addClass('search','open');
							
							// bs
							o.argument[0].bootstrap({'js':j.resp.bootstrap});
							
							// parse any links	
							o.argument[0].parseTimelineLinks();									
						
						},
						'argument': [self]
					};									
				
					// url
					var url = self.xhrUrl('realtime',{'q':q});

					// go
					self.flags.searchSuggXHR = $c.asyncRequest('GET',url,callback);
				
					// loading
					$d.addClass('search','loading');
												
					$d.setStyle('search-result','display','block');					
					
					// make bigger
					var a = new $a('search-result',{'height':{ 'to': 150 } },.5);
						a.animate();
						
					self.flags.searchSuggLast = q;						
				
				},700);
			
			},
			
			/* conditionalLoad */
			loadAndOpen : function(uri,params,holder) {
			
				// callback
				var callback = {
					'success': function(o) {
						
						// get json
						var j = $j.parse(o.responseText);
						
						// check stat
						if (j.stat != 1 || !j.resp) {
							return;
						}
					
						// load
						var div = document.createElement('div');
							div.innerHTML = j.resp.html;
						
						// add to holder
						$(o.argument[1]).appendChild(div);	
						
						// show	
						$d.setStyle(o.argument[1],'display','block');							
					
					},
					'argument': [this,holder]
				};
			
				// uri
				var url = this.xhrUrl(uri,params);
			
				// send
				var r = $c.asyncRequest('GET',url,callback);
			
			},
			
			/* doTinyUrl */
			doTinyUrl : function() {
			
				// vis
				$d.setStyle('update-status-tiny-frm','visibility','hidden');			
			
				// get it 
				var callback = {
					'success': function(o) {
					
						// whatever it is add 
						$('update-status-txt').value += o.responseText;
					
						// close 
						$d.setStyle('update-status-o-tiny','display','none');					
					
						// vis
						$d.setStyle('update-status-tiny-frm','visibility','visible');					
						
						// non
						$('update-status-tiny-txt').value = "";
					
						// text
						$d.getElementsByClassName('update-status-tiny','a',$('update'))[0].innerHTML = "Tiny URL";					
					
					},
					'argument': [this]
				};
			
				// url
				var url = this.xhrUrl('getTinyUrl');
				
				// pst
				var r = $c.asyncRequest('POST',url,callback,'url='+encodeURIComponent($('update-status-tiny-txt').value));
			
			},
			
			/* uploadPhotoStart */
			uploadPhotoStart : function(frm) {
			
				// form no longer visible
				$d.setStyle(frm,'visibility', 'hidden');
			
			},
			
			/* uploadPhotoDone */
			uploadPhotoDone : function(url) {
				
				// close 
				$d.setStyle('update-status-o-photo','display','none');
			
				// vis
				$d.setStyle('update-status-photo-frm','visibility','visible');
				
				// add to tet
				$('update-status-txt').value += " "+url;
				
				$('update-status-photo-file').value = "";
				
				$d.getElementsByClassName('update-status-upload','a',$('update'))[0].innerHTML = "Upload Photo";				
			
			},
			
			/* startTimelineWatch */
			startTimelineWatch : function(data) {
				
				// self
				var self = this;
				var d = data;
				
				// global data 
				this.flags.timelineUpdateData = data;
				
				// check for TO
				this.flags.timelineTo = window.setInterval(function(){
				
					// callback
					var callback = {
						'success': function(o) {
							
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
						
								// go for it
								for ( var i = 0; i < items.length; i++ ) {
								
									// id
									var id = items[i].id;
								
									// check if it's an update
									if ( !inArray(o.argument[0].updates,id.split('|')[1]) ) {
										
										// opacity
										$d.setStyle(items[i],'opacity',0);								
										
										// first
										var first = $('timeline').getElementsByTagName('li')[0];
										
										// add it 
										$d.insertBefore(items[i],first);
										
										// now fade in
										var a = new $a(id,{'opacity':{'to':1}},.5);
											a.animate();
											
									}
																		
								}
								
								// add to raw
								for ( var r in j.resp.raw ) {
									TT.data.timeline[r] = j.resp.raw[r];
								}
								
							// execute bootstrap
							o.argument[0].bootstrap(j.resp.bootstrap);
					
							// parse any links	
							o.argument[0].parseTimelineLinks();
							
							// max 
							o.argument[0].flags.timelineUpdateData.max = j.resp.max;
							
							// remove defer
							$d.removeClass( $d.getElementsByClassName('defer','img', $('timeline')), 'defer' );							
						
						},
						'argument': [self]
					};
					
					// r 
					var url = self.xhrUrl('timelineUpdate',self.flags.timelineUpdateData);
					
					// r 
					var r = $c.asyncRequest('GET',url,callback);
				
				},60000);
			
			},
			
			/* endTimelineWatch */
			endTimelineWatch : function() {
				window.clearInterval(this.flags.timelineTo);
			},
			
			/* showInlineReply */
			showInlineReply : function(tar) {
			
				// get info 
				var info = tar.id.split('|');
							
				// get li
				var top = $d.isGoodTarget(tar,'status');
				
					// top info
					var status = top.id.split('|');				
			
				// create our ul 
				if ( !$d.inDocument('replies-'+status[1]) ) {
					
					// create ul
					var ul = document.createElement('ul');
						ul.className = 'timeline replies';
						ul.style.display = 'none';
						ul.id = 'replies-'+status[1];
						
					// append 
					top.appendChild(ul);		
						
				}		
				
				//open 
				if ( $d.hasClass('replies-'+status[1],'open') )	{
				
					// open
					tar.removeChild(tar.getElementsByTagName('span')[0]);
					
					// close
					$d.setStyle('replies-'+status[1],'display','none');
					
					// replace class
					$d.replaceClass('replies-'+status[1],'open','closed');
					
					// done
					return;
									
				}
				else if ( $d.hasClass('replies-'+status[1],'closed') )	{				
				
					// open
					tar.innerHTML += " <span class='small gray'>(close)</span>";				
				
					// close
					$d.setStyle('replies-'+status[1],'display','block');
					
					// replace class
					$d.replaceClass('replies-'+status[1],'closed','open');
					
					// done
					return;				
				
				}
				
				// callbacks
				var callback = {
					'success': function(o) {
						
						// get jsn
						var j = $j.parse(o.responseText);
						
						// no good
						if ( j.stat != 1 ) {
							window.location.href = o.argument[2].href; return;
						}
												
						// add to payliad
						$('payload').innerHTML = j.resp.html;
					
						// show list
						$d.setStyle('replies-'+o.argument[3][1],'display','block');					
						$d.addClass('replies-'+o.argument[3][1],'open');
						
						// get from payload
						var li = $('payload').getElementsByTagName('li')[0];
					
						// set opacity
						$d.setStyle(li,'opacity',0);					
					
						// append to list
						$('replies-'+o.argument[3][1]).appendChild(li);
						
						// add to timeline
						TT.data.timeline[j.resp.raw.id] = j.resp.raw;
						
						// fade in
						var a = new $a(li,{'opacity':{to:1}},.5);
							a.animate();
							
						// open
						o.argument[2].innerHTML += " <span class='small gray'>(close)</span>";
						
					
					},
					'argument': [this,info,tar,status]
				};
				
				// url
				var url = this.xhrUrl('getReply',{'id':info[1]});
			
				// go
				var r = $c.asyncRequest('GET',url,callback);
			
			},
			
			/* resetSavedSearch */
			resetSavedSearch : function(id) {
			
				// in document
				if ( $d.inDocument('saved-search-'+id) ) {
					
					// get the saved search 
					$d.setStyle('saved-'+id,'display','none');
					
					// get count
					$('saved-count-'+id).innerHTML = 0;				
					
					this.flags.searchLastTotal[0] -= this.flags.searchLastTotal[id];
					
					// remove from total count 
					$('saved-count').innerHTML = this.flags.searchLastTotal[0];
					
						if ( this.flags.searchLastTotal[0] == 0 ){
							$d.setStyle('saved','opacity',0);
						}
					
					// remove from count
					this.flags.searchLastTotal[id] = 0;					
					
				}
				
			},
			
			/* removeSavedSearch */
			removeSavedSearch : function(tar) {
				
				// get info
				var info = tar.id.split('|');
		
					// no id
					if ( !info[1] ) {
						return;
					}
		
				// callback
				var callback = {
					'success': function(o) {
					
						// go up
						var li = $d.isGoodTarget(o.argument[1],{'tag':'li'});
					
						// remove
						li.parentNode.removeChild(li);
						
						// id 
						var id = o.argument[2][1];
						
						// delete
						delete(o.argument[0].params.search[id]);
						
						// check if it exists
						if ( $d.inDocument('saved-search-'+id) ) {
							$('saved-search-'+id).parentNode.removeChild($('saved-search-'+id));
						}
					
					},
					'argument': [this,tar,info]
				};
		
				// url 
				var url = this.xhrUrl('removeSavedSearch',{'id':info[1]});
		
				// do it
				var r = $c.asyncRequest('GET',url,callback);
		
			},
			
			/* doReply */
			doReply : function(tar) {
			
				// scroll to top
				window.scrollTo(0,0);
				
				// info
				var info = tar.id.split('|');
				
				// id 
				var id = info[1];
			
				// change title 
				var tweet = TT.data.timeline[id];
			
				// write
				$d.addClass('update-status','write');
				
				// title
				$('update-status-title').innerHTML = "@"+tweet.user.screen_name + " <a class='gray clear-status' href='#'>clear</a>";
				
				// update
				$('update-status-txt').value = "@"+tweet.user.screen_name;
				
				// id 
				$('update-status-reply').value = id;
		
				// max 
				var max = parseInt( $('update-status-chars').getAttribute('max') );			
				
				// max
				$('update-status-chars').innerHTML = max;
			
				// check for tr
				if ( $d.hasClass(tar,'retweet') ) {
				
					// msg 
					var msg = 'RT @'+tweet.user.screen_name+' '+tweet.text;
				
					// add tweet
					$('update-status-txt').value = msg;
					
					// max 
					var max = parseInt( $('update-status-chars').getAttribute('max') );
					
					// set
					$('update-status-chars').innerHTML = max - msg.length;
					
				
				}
				
				// focus
				$('update-status-txt').focus();
			
			},
			
			/* fav */
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
				
				oTar.innerHTML = "Saving...";
				
				// info 
				var info = li.id.split('|');
			
				// callback
				var callback = {
					'success': function(o) {
						
						// r 
						var r = o.responseText;
					
						// what up
						if ( r != 'true' ) {
							oTar.innerHTML = "Error";
							alert(r);
						}
						else {
							oTar.innerHTML = "Favorite!";					
							$d.addClass(oTar,'done');						
						}
					
					},
					'argument': [this]
				};
			
				// url
				var url = this.xhrUrl('fav',{'id':info[1]});
								
				// li on
				$d.addClass(li,'on');
			
				// send
				var r = $c.asyncRequest('GET',url,callback);
			
			},
			
			
			/* addUserToGroup */
			addUserToGroup : function(group,args) {
				
				// make nice
				if ( !args.id ) {
					args.id = args[2][1];
					args.img = args[2][2];
					args.sn = args[2][3];
					args.name = args[2][0];
				}
				
				// callback
				var callback = {
					'success': function(o) {
					
						// remove
						$d.removeClass('group-frame','group-frame-loading');
					
						// a 
						var a = o.argument[2];
						
						// create li 
						var li = document.createElement('li');
						
						// try to get empty
						var em = $d.getElementsByClassName('empty','li', $('group-list-'+o.argument[1]) );
						
							if ( em.length > 0 ) {
								em[0].parentNode.removeChild(em[0]);
							}
							
						// li 
						li.innerHTML = "<img class='bubble' title='"+a.name+" ("+a.sn+")' src='"+a.img+"'>"+
										"<a href='/user/"+a.sn+"'>"+a.sn+"</a>" +
										"<em>"+a.name+"</em>";
						
						// append to list
						$('group-list-'+o.argument[1]).appendChild(li);
						
						// clean
						if ( o.argument[2][0] ) {
							o.argument[2][0].getInputEl().value = '';
						}
						
						// add to current list
						if ( TT.data.ac && TT.data.ac[o.argument[1]] ) {
							TT.data.ac[o.argument[1]].push(a[1]);
						}
										
						$d.removeClass('group-'+o.argument[1],'stiky loading');				
										
					},
					'argument': [this,group,args]
				};

				// keep it open
				$d.addClass('group-'+group,'open stiky loading');

				// uid
				var uid = args.id;
				
				// url
				var url = this.xhrUrl('addUserToGroup');
						
				// go
				var r = $c.asyncRequest('POST',url,callback,"gid="+group+"&uid="+uid);

			},			

			/* show/hide title bubble */
			titleBubble : function(tar,event,type) {				
				
				// bubble
				var bubble;
					
				// check for a bubble 
				if ( !this.flags.titleBubbleRef ) {
				
					// create the bubble
					bubble = document.createElement('div');
					bubble.id = 'title-bubble';
					
					// set the flag
					this.flags.titleBubbleRef = bubble;
					
					// append to the page 
					$('doc4').appendChild(bubble);
					
				}
				else {		
					bubble = this.flags.titleBubbleRef;
				}
				
				// off 
				if ( type == 'out' ) {
					
					// hide the bubble
					$d.setXY(bubble,[-99,-99]);
					
					// reset the title 
					tar.setAttribute('title', tar.getAttribute('xtitle'));
					
					// reutrn
					return;
					
				}
				
				// get the title
				// no title exit 
				var title = tar.getAttribute('title');
				
					// nope 
					if ( !title ) return;
					
				// enter the title into the bubble
				bubble.innerHTML = title + "<span></span>";
				
				// hide the title of the tar
				tar.setAttribute('title','');
				tar.setAttribute('xtitle',title);
				
				// figure the bubble's width 
				var bReg = $d.getRegion(bubble);
				var tReg = $d.getRegion(tar);
				
				// button size
				var bw = ( bReg.right - bReg.left );
				var bh = ( bReg.bottom - bReg.top );
				
				// tar size 
				var tw = ( tReg.right - tReg.left );
								
				// get the overal xy of the el 
				var txy = $d.getXY(tar);
				
				// find the middle 
				var x = ( txy[0] + tw / 2 ) - ( bw / 2 );
				var y = (txy[1] - (bh+5) );
				
				// set the bubbl's xy 
				$d.setXY(bubble,[x,y]);
					
			},			
			
			/* submitUpdate */
			submitUpdate : function(tar) {
			
				// lets do it 
				if ( $d.hasClass('update-status','saving') ) {
					return;
				}
			
				// callback
				var callback = {
					'success': function(o) {
					
						// get json
						var j = $j.parse(o.responseText);
					
						// no stat
						if ( j.stat != 1 ) {
							alert(j.msg); return;
						}
					
						// update current
						$('update-status-cur').innerHTML = j.resp.parsed;
						
						// reset count
						$('update-status-txt').value = "";
						$('update-status-chars').innerHTML = $('update-status-chars').getAttribute('max');
						
						// title
						$('update-status-title').innerHTML = 'What are you doing?';
						$('update-status-reply').value = '0';
						
						// if timeline 
						if ( $d.inDocument('timeline') ) {
							
							// append to payload
							$('payload').innerHTML = j.resp.full;
						
							// does timeline have any 
							if ( $('timeline').childNodes.length > 0 ) {
														
								// first fird 
								$d.insertBefore( $('payload').getElementsByTagName('li')[0], $('timeline').getElementsByTagName('li')[0] );
							
							}
							else {
								$('timeline').appendChild( $('payload').firstChild );
							}
							
							// empty payload
							$('payload').innerHTML = "";
							
							// add to update list
							o.argument[0].updates.push(j.resp.raw.id);
						
						}
						
					},
					'argument': [this]				
				};
				
				// url 
				var url = this.xhrUrl('updateStatus');
				
				// params
				var params = "reply="+$('update-status-reply').value+"&network="+$('update-status-network').value;
				
				// add status
				params += "&status="+encodeURIComponent($('update-status-txt').value);
			
				// run it
				var r = $c.asyncRequest('POST',url,callback,params);
			
			},
			
			/* save search */
			saveSearch : function(tar) {
				
				// what
				if ( $d.hasClass(tar,'saving') ) {
					return;
				}
				
				// change inner
				tar.innerHTML = 'Saving...';	
			
				// save that shit
				var info = tar.id.split('|');
				
				// callback
				var callback = {
					'success': function(o) {
						
						// what up 
						var j = $j.parse(o.responseText);
					
						// stat
						if ( j.stat != 1 ) {
							return;
						}
					
						// reset the global 
						o.argument[0].params.search = j.resp;
						
						// now change the text 
						var t = o.argument[1];
						
						// get parent
						var p = t.parentNode;
						
						// remove t
						p.removeChild(t);
						
						// set text
						p.innerHTML = 'Saved';
						
					},
					'argument': [this,tar]
				};
				
				// url
				var url = this.xhrUrl('saveSearch');
				
				// do it
				var r = $c.asyncRequest('POST',url,callback,'q='+info[1]);
				
				// save req
				$d.addClass(tar,'saving');
			
			},
			
			/* submit user panel */
			submitUserPanel : function(tar) {
			
				if ( this.flags.userPanelSaving ) {
					return;
				}
			
				// get els
				var inputs = $d.getElementsByClassName('group','input','user-panel-form');
				var textarea = $('user-panel-content').getElementsByTagName('textarea'); 
		
				// data	
				var data = [];
					
					// add rate
					data[data.length] = 'rate='+$('user-panel-rate').value;
				
					// add groups
					for ( var i in inputs ) {
						if ( inputs[1].type == 'checkbox' ) {
							data[data.length] = inputs[i].name + '='+ inputs[i].checked;
						}
					}
			
				// callback
				var callback = {
					'success': function(o) {
					
						if ( o.argument[0].userPanelOverlay ) {
	 						o.argument[0].userPanelOverlay.close();
 						}					
					
						// whatever the results we close
						o.argument[0].flags.userPanelSaving = false;
					
						// fade the timeline
						if ( $d.inDocument('timeline') ) {
							var a = new $a('timeline',{opacity:{to:1}},.2);
								a.animate();
						}					
						
						if ( !$d.hasClass('user-panel','sticky') ) {
						
							// set 
							$d.setStyle('user-panel-content','width','50px');
							$d.setStyle('user-panel-content','height','50px');
						
							// remove pannel
							$d.setXY('user-panel',[-999,-999]);					
							
						}
						
						// reset button
						var btn = $d.getElementsByClassName('user-panel-submit','button',$('doc4'));
						
							for ( var b in btn ) {
								
								btn[b].disabled = false;
								btn[b].innerHTML = "Update";
								
							}
					
					},
					'argument': [this]
				};
				
				// saving
				tar.disabled = true;
				tar.innerHTML = 'Saving...';
				
				// url 
				var url = this.xhrUrl('submitUserPanel',{'id':tar.id.split('|')[1]});
						
				// og
				var r = $c.asyncRequest('POST',url,callback,data.join('&'));
				
				// saving
				this.flags.userPanelSaving = true;
			
			},
			
			/* show user panel */
			showUserPanel : function(tar) {
				
					
				// params
				if ( !this.params.loged ) {
					return;
				}
				
				// clear timeout
				clearTimeout(this.flags.userPanelOutLastTo);
				clearTimeout(this.flags.userPanelTo);
						
				// info
				var info = tar.id.split('|');				
											
				// localize
				var t = tar;
				var self = this;		
						
				// show 
				this.flags.userPanelTo = window.setTimeout(function() {		
					
					// are they the same
					if ( self.flags.lastUserPanelOpenedId == info[1] ) {
						
						// place the panel
						var pos = $d.getXY(tar);
						
						// set panel
						$d.setXY('user-panel',[pos[0]-20,pos[1]-20] );				
						
						// fade the timeline
						if ( $d.inDocument('timeline') ) {
							var a = new $a('timeline',{opacity:{to:.5}},.5);
								a.animate();
						}
							
						// make that big
						$d.setStyle('user-panel-content','width','500px');
						$d.setStyle('user-panel-content','height','200px');
					
						// return
						return;
						
					}					
					
					// load panel
					self.loadUserPanel(t);
				
				},1000);			
			
			},
			
			/* loadUserPanel */
			loadUserPanel : function(t) {
			
				// get user info
				var info = t.id.split('|');
				
				// last user panel opened
				this.flags.lastUserPanelOpenedId = info[1];
			
				// place the panel
				var pos = $d.getXY(t);
				
				// empty 
				$('user-panel-content').innerHTML = "";
				
				// clone the node
				var img = t.cloneNode(true);
					img.className = "img";
					img.id = "";
				
				// append
				$('user-panel-content').appendChild(img);
				
				// fade the timeline
				if ( $d.inDocument('timeline') ) {
					var a = new $a('timeline',{opacity:{to:.5}},.5);
						a.animate();
				}
			
			
				// set panel
				// if not sticy
				if ( !$d.hasClass('user-panel','sticky') ) { 
				
					// set 
					$d.setXY('user-panel',[pos[0]-30,pos[1]-30] );				
		
					// animate out as we load 
					var a = new $a('user-panel-content',{width:{to:500},height:{to:150}},.3);
						a.animate();		
						
					if ( $d.hasClass(document.body,'ie') ) {
						$d.setStyle('user-panel','width','540px');
					}
					
				}			
							
				// load the content
				var callback = {
					'success': function(o) {
					
						// parse json
						var j = $j.parse(o.responseText);
					
						// stat 
						if ( j.stat != 1 ) {
							return;
						}
					
						// set
						$('user-panel-content').innerHTML += j.resp.html;
						
						// make slide
						var slide = YAHOO.widget.Slider.getHorizSlider("slider-bg","slider-thumb",0,100); 
					
						// set value
						slide.subscribe('change', function (newOffset) { 
							$("user-panel-rate").value = newOffset*2;
						});			
						
						slide.setValue(parseInt($("user-panel-rate").value)/2);
						
						// set title
						var h3 = $('user-panel-content').getElementsByTagName('h3')[0];
							h3.innerHTML = o.argument[1][2] + "<span><a href='/user/"+o.argument[1][2]+"'>profile</a></span>";
							
						// set height 
						$('user-panel-content').style.height = 'auto';
						
						// check for user panel holder
						if ( $d.inDocument('user-panel-holder') ) {
							$d.setStyle('user-panel-holder','height', parseInt($d.getStyle('user-panel','height'),10)+'px');
						}
						
				
						$d.removeClass('user-panel','loading');						
					
					},
					'argument': [this,info]
				};
				
				$d.addClass('user-panel','loading');
			
				// url
				var url = this.xhrUrl('userPanel',{'id':info[1]});
				
				// call
				var r = $c.asyncRequest('GET',url,callback);			
			
			},
			
			/* watchSavedSearch */
			watchSavedSearch : function() {
				
				// no saved searches
				if ( this.params.search.length == 0 ) {
					return;
				}
				
				// self
				var self = this;
			
				// stuff	
				this.flags.searchLastTotal = [0];
				this.flags.searchFirstDone = false;
			
				// set the timeout
				var getUpdateFunc = function() {
				
					// callback
					var callback = {
						'success': function(o) {
						
							// parse
							var j = $j.parse(o.responseText);
						
							// stat
							if ( j.stat != 1 ) {
								return;
							}							
							
							// t
							var t = o.argument[0];						
						
							// go for it
							for ( var i in j.resp[0] ) {
								
								// item
								var item = j.resp[0][i];
							
								// set
								t.params.search[i].since = item.since;
								t.params.search[i].num = item.num;
								
								if ( item.num > 0 && self.flags.searchFirstDone == true ) {
																
									// last val
									self.flags.searchLastTotal[i] += item.num;
																					
									// set count 
									$('saved-count-'+i).innerHTML = self.flags.searchLastTotal[i];
									$d.setStyle('saved-'+i,'display','block');
									
									// fade in
									var a = new $a('saved-'+i,{'opacity':{from:0, to:1}},.5);
										a.animate();																
									
								}
								else if ( !self.flags.searchFirstDone ) {
									self.flags.searchLastTotal[i] = 0;
								}								
							
							}
													
							// set total
							if ( j.resp[1] > 0 && self.flags.searchFirstDone == true ) {
												
								$d.setStyle('saved-count-wrap','display','block');				
												
								if ( isNaN(self.flags.searchLastTotal[0]) ) {
									self.flags.searchLastTotal[0] = 0;
								}												
												
								// last val
								self.flags.searchLastTotal[0] += j.resp[1];								
																				
								// set count 
								$('saved-count').innerHTML = self.flags.searchLastTotal[0];
								$d.setStyle('saved','display','block');
								
								// fade in
								var a = new $a('saved',{'opacity':{from:0, to:1}},.5);
									a.animate();																
								
							}
						
							// done
							self.flags.searchFirstDone = true;							
						
						},
						'argument': [self]
					};
				
					// get the url 
					var url = self.xhrUrl('searchCountUpdate');
										
					// run
					var r = $c.asyncRequest('POST',url,callback,'q='+$j.stringify(self.params.search));
					
				}
				
				// start by running
				getUpdateFunc();
				
				// to continue to run
				this.flags.searchTimeout = window.setInterval(function(){				
					getUpdateFunc();
				},50000);
			
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
				else if ( el.href.indexOf('yfrog.com') !== -1 ) {
					
					// get the id 
					var id = /yfrog\.com\/([a-zA-Z0-9]+)/.exec(el.href)[1];
					
					// url 
					url = "http://yfrog.com/"+id+":iphone";
					
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
					wrap.innerHTML = "<img src='"+url+"'>";
				
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
				
				this.preLinks = [];

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
			
			/* load xhr page */
			loadXhrPage : function(tar,e,noHistory) {		
						
				var href = tar.href;			
				var reg = false;
				var history = e;	
			
				// reg
				var reg = new RegExp("https?:\/\/([a-z]+\.)?twittangle\.com");		
			
				// lets check to see if the
				// url is the right subdomain
				if ( !href.match(reg) || href.indexOf('#') != -1 ) {
					window.location.href = href;
					return;
				}
				
				// stop
				try {
					$e.stopEvent(e); tar.blur();
				} catch(e) {}
				
				// abort last call
				if ( this.lastXhrPageReq ) {
					$c.abort(this.lastXhrPageReq);
				}
			
				// get the page 
				var rx = new RegExp("http(s)?:\/\/([a-z]+\.)?twittangle\.com\/",'gi');
				
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
			
				// are we loading a timeline
				if ( $d.hasClass(document.body,'home') && ( page.indexOf('home/timeline') != -1 || page == 'home' ) ) {
				
					// define holder
					var holder = document.createElement('div');
					holder.innerHTML = "<div id='timeline-loading' class='timeline-loading'><em>Loading Your Timeline</em>" +
												  "<div>We're loading your timeline from Twitter. Because we have to connect to Twitter" +
												  " this may take up to 60 seconds. We promise we'll make this as quick as possible </div></div>";
									
					// check for timeline
					if ( !$d.inDocument('timeline-loading') ) {													  
						$d.insertBefore(holder,$('timeline'));
						$d.setStyle('timeline','display','none');
						$d.setStyle( $d.getElementsByClassName('pager','ul',$('timeline').parentNode),'display','none' );
					}
					
				}
			
				// close
				this.closeSearchSuggest();			
			
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
						document.getElementsByTagName('body')[0].className = j.c + ' yui-skin-sam ' + TT.data.ua.join(' ');
						document.title = j.t;
						
							// r 
							if ( j.r ) {
								if ( $d.inDocument('rsslink') ) {
									$('rsslink').href = j.r;
								}
								else {
									var l = document.createElement('link');
										l.setAttribute('rel','alternate');
										l.href = j.r;
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
						$('page-content').innerHTML = j.html;					
						
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
						
							
						if ( $d.inDocument('timeline') ) {
							o.argument[0].parseTimelineLinks();
						}								
						
						// loading
						$d.removeClass(document.body,'loading');
						
					},
					'failure' : function(o) {
						window.location.href = o.argument[1]; return;
					},
					'argument': [this,href],
					'timeout': 10000
				};
				
				// unload
				TT.executeUnLoadQueue();
				this.preLinks = null;
				this.linkGroups = [];
				this.updates = [];
				$('payload').innerHTML = "";
				
				// in doc
				if ( $d.inDocument('search-other') ) {
					$('search-other').innerHTML = "";
				}
				
				// now build our url 
				var url = this.xhrUrl('context/'+path,params);							
				
				// no bubble
				$d.setXY( $('title-bubble') ,[-999,-999]);
				$d.setXY( $('user-panel'), [-999,-999]);
				
				// loading
				$l(1);	
				
				$d.addClass(document.body,'loading');							
			
				// history
				if ( !noHistory ) {
					try {				
						if (!history || !history.calledFromHistory) {					
							dsHistory.setQueryVar('p', page);
							dsHistory.bindQueryVars(this.loadXhrPage, this, {'href':'http://www.twittangle.com/'+page} );					
						}
					} catch(e) {}
				}
				
				// track 
				pageTracker._trackPageview(page); 
				
				// make the request
				this.lastXhrPageReq = $c.asyncRequest('GET',url,callback);						
			
			},
			
			
			/* bootstrap javascript */
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
			
			/* xhr */
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
		
		$Y.twitTangle.Global.DDGroupsItem = function(id, sGroup, config) {
		    $Y.twitTangle.Global.DDGroupsItem.superclass.constructor.apply(this, arguments);
		    this.initPlayer(id, sGroup, config);
		};
		
		YAHOO.extend($Y.twitTangle.Global.DDGroupsItem, YAHOO.util.DDProxy, {
		
		    TYPE: "DDGroupsItem",
		
		    initPlayer: function(id, sGroup, config) {
		        if (!id) { 
		            return; 
		        }
		
		        var el = this.getDragEl()
		        YAHOO.util.Dom.setStyle(el, "borderColor", "transparent");
		        YAHOO.util.Dom.setStyle(el, "opacity", 0.76);
		
		        // specify that this is not currently a drop target
		        this.isTarget = false;
		
		        this.originalStyles = [];
		
		        this.type = $Y.twitTangle.Global.DDGroupsItem.TYPE;
		        this.slot = null;
		
		        this.startPos = YAHOO.util.Dom.getXY( this.getEl() );
		    },
		
		    startDrag: function(x, y) {
		        var Dom = YAHOO.util.Dom;
		
		        var dragEl = this.getDragEl();
		        var clickEl = this.getEl();
		
		        dragEl.innerHTML = clickEl.innerHTML;
		        dragEl.className = clickEl.className;
		
		        Dom.setStyle(dragEl, "color",  Dom.getStyle(clickEl, "color"));
		        Dom.setStyle(dragEl, "backgroundColor", Dom.getStyle(clickEl, "backgroundColor"));
		
		        Dom.setStyle(clickEl, "opacity", 0.1);
		
		    },
		
		    getTargetDomRef: function(oDD) {
		        if (oDD.player) {
		            return oDD.player.getEl();
		        } else {
		            return oDD.getEl();
		        }
		    },
		
		    endDrag: function(e) {
		        // reset the linked element styles
		        YAHOO.util.Dom.setStyle(this.getEl(), "opacity", 1);
		    },
			
		    onDragDrop: function(e, id) {
		        // get the drag and drop object that was targeted
		        var oDD;
		        
		        if ("string" == typeof id) {
		            oDD = YAHOO.util.DDM.getDDById(id);
		        } else {
		            oDD = YAHOO.util.DDM.getBestMatch(id);
		        }
		        		
		        var el = this.getEl();
				
				// yes 
				if ( oDD.groups['groups'] == true ) {
				
					// get id 
					var id = oDD.id.replace(/group-/,'');				
					
					// friend
					var f = el.id.replace(/usr-/,'');

					$d.removeClass(oDD.id,'active');

					// laoding					
					$d.addClass('group-frame','group-frame-loading');
				
					// fire our ajax event 
					TT.Global.addUserToGroup(id, TT.Global.flags.friendsData[f] );
					
					// remove el from list
					el.parentNode.removeChild(el);
				
				}
		
		        
		    },
		    
		    onDragOver: function(e, id) { 
				if ( $d.hasClass(id,'group-top') ) {
					$d.addClass(id,'active');
				}
		    },
		    
		    onDragOut: function(e, id) { 
				if ( $d.hasClass(id,'group-top') ) {
					$d.removeClass(id,'active');
				}
		    }		    
		
		
		});
		
	// namespace
	$Y.namespace("twitTangle.Panel");
	
	// panel
	$Y.twitTangle.Panel = function(p) {
		this.init(p);
	}

		// prop
		$Y.twitTangle.Panel.prototype = {
		
			// properties
			params : {},
			wrap : null,
			cnt : null,
			width : 954,
			height : 500,
			
			// init 
			init : function(p) {
				
				// params
				this.params = p;
				
				// render 
				if ( !p.noRender ) {
					this.render();
				}
				
				// no open
				if ( !p.noOpen ) {
					this.open();
				}
				
				$e.on(window,'resize',this.resize,this,true);
				$e.on(window,'scroll',this.scroll,this,true);
				
			},
			
			// scroll
			scroll : function() {			
				if ( this.state == 'open') {

					// get
					var dh = parseInt($d.getViewportHeight(),10);
					var ph = parseInt($d.getStyle(this.wrap,'height'),10);
										
					// x
					var y = (dh/2) - (ph/2);
				
					// opven
					if ( y < 10 ) {
						y = 10;
					}
				
					// add scroll offset 
					y += $d.getDocumentScrollTop();						
					
					$d.setY(this.wrap,y);
					
				}			
			},
			
			// resize 
			resize : function() {
			
				$d.setStyle('panel-overlay','height', $d.getDocumentHeight()+'px' );
			
			},
			
			// render
			render : function() {
				
				// local
				var p = this.params;
				
				// wrap
				var wrap = document.createElement('div');
					wrap.className = 'panel-wrap';
					wrap.id = p.name + '-wrap';
					
				// inner
				wrap.innerHTML = "<div class='panel-hd'><b class='l'></b><b class='r'></b> <a class='panel-close' href='#'>close</a></div>" +
								 "<div class='panel-bd'><b class='cnt'></b><div id='"+p.name+"-content' class='cnt'></div></div>" +
								 "<div class='panel-ft'><b class='l'></b><b class='r'></b></div>";
				
				// set stype
				$d.setStyle(wrap,'display','none');
								 
				// append
				$('doc4').appendChild(wrap);
				
				// attach
				$e.on(wrap,'click',function(e){		
				
					// tar 
					var tar = $e.getTarget(e);
					
					// find
					if ( $d.hasClass(tar,'panel-close') ) {
						$e.stopEvent(e); this.close();
					}
				
				},this,true);
				
				// global
				this.wrap = wrap;
				this.cnt = $(p.name+"-content");
							
			},
			
			// close
			close : function() {

				// if overlay
				if ( this.params.overlay ) {
											
					// fade in
					var a = new $a('panel-overlay',{opacity:{to:0}},.2);
						a.onComplete.subscribe(function(type,args,me){
							$d.setStyle('panel-overlay','display','none');
						},this);
						a.animate();									
					
				}			
				
				// fade in
				var a = new $a(this.wrap,{opacity:{to:0}},.2);
					a.onComplete.subscribe(function(type,args,me){
						$d.setStyle(me.wrap,'display','none');
					},this);
					a.animate();									
			
				this.state = 'closed';
			
			},
			
			// open
			open : function(p) {
			
				this.state = 'open';
								
				// if overlay
				if ( this.params.overlay ) {
					
					// not in
					if ( !$d.inDocument('panel-overlay') ) {
						var d = document.createElement('div');
							d.id = 'panel-overlay';
						document.getElementsByTagName('body')[0].appendChild(d);
					}
					
					// set
					$d.setStyle('panel-overlay','opacity',0);
					
					// show
					$d.setStyle('panel-overlay','display','block');
						
					// set it's height
					$d.setStyle('panel-overlay','height', $d.getDocumentHeight()+'px' );
						
					// fade in
					var a = new $a('panel-overlay',{opacity:{to:.4}},.2);
						a.animate();									
					
				}

				// show
				$d.setStyle(this.wrap,'opacity',0);
						
				// show
				$d.setStyle(this.wrap,'display','block');		
			
				// figure where to center
				var dw = parseInt($d.getViewportWidth(),10);
				var dh = parseInt($d.getViewportHeight(),10);
				
				// center
				var pw = parseInt($d.getStyle(this.wrap,'width'),10);
				var ph = parseInt($d.getStyle(this.wrap,'height'),10);
				
					// is it too big 
					if ( pw > this.width ) {
						pw = this.width;
						$d.setStyle(this.wrap,'width',this.width+'px');
					}

					if ( ph > this.height ) {
						ph = this.height;
						$d.setStyle(this.wrap,'height',this.height+'px');
					}
				
				// x
				var x = (dw/2) - (pw/2);
				var y = (dh/2) - (ph/2);
				
					// opven
					if ( y < 10 ) {
						y = 10;
					}
				
				// add scroll offset 
				y += $d.getDocumentScrollTop();				
			
				// place
				$d.setXY(this.wrap,[x,y]);
			
				// fade in
				var a = new $a(this.wrap,{opacity:{to:1}},.4);
					a.animate();							
			
			},
			
			// load 
			load : function(p) {

				// callback
				var callback = {
					'success': function(o) {
						
						// j 
						var j = false;
						
						// try
						try {
							j = $j.parse(o.responseText);
						}
						catch(e) { return; }
						
						// good
						if ( j.stat != 1 ) {
							return;
						}
						
						// set
						o.argument[0].cnt.innerHTML = j.resp.html;
						
						if ( o.argument[1].onLoad ) {
							o.argument[1].onLoad.call();
						}
						
						// if open
						if ( o.argument[1].open ) {
							o.argument[0].open();
						}
						
					},
					'argument': [this,p]
				};
				
				// add 
				var url = this.addParamsToUrl(p.url,{'.name':this.params.name});
			
				// fire
				var r = $c.asyncRequest('GET',url,callback);
			
			},
			
			// add query string to url
			addParamsToUrl : function(url,params) {
				
				var qs = [];
				
				for ( var p in params ) {
					qs.push(p+"="+params[p]);
				}
			
				// what 
				if ( url.indexOf('?') !== -1 ) {
					return url + '&' + qs.join('&');
				}
				else {
					return url + '?' + qs.join('&');
				}
			
			}			
		
		}
		
		
		
})();




			