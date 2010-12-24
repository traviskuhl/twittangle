
// namespace
$Y.namespace('twitTangle.Batch');

(function(){

	/* timeline function */
	$Y.twitTangle.Batch = function(p) {
		this.init(p);
	}

		/* extend */
		$Y.twitTangle.Batch.prototype = {
			
			params : {},
			targets: {},
			friends: {},
			current: {},
		
			init : function(p) {
				
				// params 
				this.params = p;
				
				// make group a target
				this.targets.group = new YAHOO.util.DDTarget("group-"+p.group,'group');
				this.targets.friend = new YAHOO.util.DDTarget("batch-friends",'friend');
			
				// for each froend
				for ( var f in TT.data.friends ) {
					this.friends[f] = new $Y.twitTangle.Batch.DD('friend|'+f,'group');
				}				
				
				// other way 
				for ( var g in TT.data.groups[p.group] ) {
					var id = TT.data.groups[p.group][g];
					this.current[id] = new $Y.twitTangle.Batch.DD('usr|'+p.group+"|"+id,'friend');				
				}
				
				// save
				$e.on('bd','click',function(e){	
					
					var tar = $e.getTarget(e);
					
					if ( $d.hasClass(tar,'batch-save') ) {
					
						// stop
						$e.stopEvent(e);				
					
						// get the list
						var list = $('group-'+this.params.group).getElementsByTagName('li');
						
						// users
						var users = {};		
						
	
						// each
						for ( var i = 0; i < list.length; i++ ) {
							
							// get info 
							var info = list[i].id.split('|');
							
							if ( info[0] == 'usr' ) {
								users[info[2]] = [true];
							}
							else {
								users[info[1]] = [false, TT.data.friends[info[1]]];
							}
	
						
						}
											
						// fire
						var callback = {
							'success': function(o) {
									
								// nice
								$l(false);
								$('batch-save').innerHTML = 'Save';
								$('batch-save').disabled = false;						
								
								// r
								var r = o.responseText;
								
								// check
								if ( r != 'true' ) {
									alert(r); return;
								}
								
							},
							'argument': [this]
						};
						
						// url 
						var url = TT.Global.xhrUrl('batch');
						
						// pb
						var pb = "id="+this.params.group+"&friends="+escape($j.stringify(users));
						
						// fire
						var r = $c.asyncRequest('POST',url,callback,pb);
						
						// loading
						$l(true);
						$('batch-save').innerHTML = 'Updating Group...';
						$('batch-save').disabled = true;
						
					}
					else if ( $d.hasClass(tar,'batch-help') ) {
						
						// stop click
						$e.stopEvent(e);
						tar.blur();
						
						if ( $d.hasClass(tar,'open') ) {
							$d.removeClass(tar,'open');
							$d.setStyle('batch-help','display','none'); 
							return;
						}
						
						// opacity
						$d.setStyle('batch-help','opacity',0);
						$d.setStyle('batch-help','display','block');
						
						// h 
						var h = parseInt($d.getStyle('batch-help','height'),10);
						
						// zero
						$d.setStyle('batch-help','height',0);
											
						// do it 
						var a = new $a('batch-help',{'height':{'to':h},'opacity':{'to':1}},.5);
							a.animate();
							
						// class
						$d.addClass(tar,'open');
					
					}
				
				},this,true);
				
				// tell TT we've loged batch
				TT.data.BatchHasLoaded = true;
			
			}
		
		}
			
	/* drag drop */
	$Y.twitTangle.Batch.DD = function(id, sGroup, config) {
	    $Y.twitTangle.Batch.DD.superclass.constructor.apply(this, arguments);
	    this.initPlayer(id, sGroup, config);
	};
	
	YAHOO.extend($Y.twitTangle.Batch.DD, YAHOO.util.DDProxy, {
	
	    TYPE: "DD",
	
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
	
	        this.type = $Y.twitTangle.Batch.DD.TYPE;
	        this.slot = null;
	
	        this.startPos = YAHOO.util.Dom.getXY( this.getEl() );
	        YAHOO.log(id + " startpos: " + this.startPos, "info", "example");
	    },
	
	    startDrag: function(x, y) {
	        YAHOO.log(this.id + " startDrag", "info", "example");
	        var Dom = YAHOO.util.Dom;
	
	        var dragEl = this.getDragEl();
	        var clickEl = this.getEl();
	
	        dragEl.innerHTML = clickEl.innerHTML;
	        dragEl.className = clickEl.className;
	
	        Dom.setStyle(dragEl, "color",  Dom.getStyle(clickEl, "color"));
	        Dom.setStyle(dragEl, "backgroundColor", Dom.getStyle(clickEl, "backgroundColor"));
	
	        Dom.setStyle(clickEl, "opacity", 0.1);
	
	        var targets = YAHOO.util.DDM.getRelated(this, true);
	        
			// highlight target
			$d.addClass(targets[0].id,'active');
    
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
			
			// what to do
			if ( oDD.groups.group === true )  {				
				this.removeFromGroup('group');
				this.addToGroup('friend');			
			}
			else {
				this.removeFromGroup('friend');
				this.addToGroup('group');			
			}
	
	        var el = this.getEl();
	        
	        $d.removeClass(id,'active');
	        
	        // append to list
	        $(id).appendChild(el);
	
	    },
	
	    onDragOver: function(e, id) {
			 var targets = YAHOO.util.DDM.getRelated(this, true);
			 for ( var i = 0; i < targets.length; i++ )  {
			 	$d.removeClass(targets[i].id,'active');
			 }
	    },
	
	    onDrag: function(e, id) {
	    }
	
	});
	
		
})();