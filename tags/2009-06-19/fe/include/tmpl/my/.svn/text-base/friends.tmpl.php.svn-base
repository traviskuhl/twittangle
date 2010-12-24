<div class="yui-gc">
	<div class="yui-u first">
	
		<h2>My Friends</h2>
		
		<div id="user-panel-holder"></div>
		
		<div class="friends-wrap">
			<?php
				if ( $pages > 0 ) {
					echo "<ul class='pager'><li class='h'>Page:</li>";
						for ( $i = 1; $i <= $pages; $i++ ) {
							echo "<li><a class='".($this->param('page',1)==$i?'b':'')."' href='/my/friends?page={$i}'>{$i}</a>";
						}
					echo "</ul>";
				}
			?>
			
			<ul class="user-list clear cf">
				<?php
					
					// first
					$first = key($friends);
				
					foreach ( $friends as $f ) {	
						echo "
							<li><a href='#'><img width='48' height='48' id='user|{$f['id']}|{$f['sn']}' class='defer bubble user-panel' title='{$f['name']}  ({$f['sn']})' src='".BLANK."' style='background-image: url(".$this->getUserPic($f['img']).")'></a></li>";
					}
				?>
			</ul>
		
		</div>
	
	</div>
	<div class="yui-u side keep-box">
		<div class="box">
			
			<h3>Search Your Friends</h3>
			<form class="cf" action="#" onsubmit="return false;">
				<input id="friend-search-text" type="text">
				<div class="results" id="friend-search-results"></div>				
				<div class="hide" id='img-payload'><img width='48' height='48' src='<?php echo BLANK; ?>'></div>
			</form>
		
			<script type='text/javascript'>
				TT.addToQueue(function(){
				
					// ds 
					var ds = new YAHOO.util.XHRDataSource( TT.Global.xhrUrl('friendSearch'), {'connMethodPost':true} ); 
						ds.responseType = YAHOO.util.XHRDataSource.TYPE_JSON;								
						ds.responseSchema = { 
							resultsList : 'resp', 
							fields : ['name','id','img','sn'] 
						}; 
					
					// data 
					var ac = new YAHOO.widget.AutoComplete(
						'friend-search-text',
						'friend-search-results', 
						ds,
						{
							'useShadow': true,
							'queryDelay': .5,
							'typeAhead': true,
							'applyLocalFilter': true,
							'queryMatchSubset': true
						});
					
					// when selected
					ac.itemSelectEvent.subscribe(function(type,args) {
						
						// get img 
						var img = $('img-payload').getElementsByTagName('img')[0];
											
						
						// set 
						img.id = 'user|'+args[2][1]+'|'+args[2][3];
						img.src = args[2][2];
						
						// add 
						TT.Global.loadUserPanel(img);
						
						// clean
						$('friend-search-text').value = "";
						
					});				
					
				});
			</script>		
	
			<h3>How This Works</h3>
			<p> It's simple. Scroll down a bit and you'll see a list
				of 200 of your friends. Find the friend you want to rate, tag or add to a 
				group and click on their pic. We'll load their user card and you're off. Rate them;
				add some tags; pick some groups for them and click 'update'. That's it. Don't want 
				to page through? Use the search above. </p>
		
			<br><br>
			<h3>Last Friend Sync</h3>	
			<p>
				In order to keep your list of friends on twitTangle in sync with Twitter, we will attempt to
				sync your friends once a day. If you've added a lot of friends and want to run it manually,
				just click the link below. You can only run the sync process once an hour.<br><br>
				<em class='b'>Last Run:</em> 
				<?php
				
					// t
					$t = $this->user['friends_updated'];
					
					// last run
					echo $this->displayTimestamp($t); 
					
					// can they run again
					if ( time()-$t > (60*60) ) {
						echo " (<a href='/my/friends/import'>Run Now</a>)";
					}
					
				?>
			</p>
		
			<b class="tr"></b>
			<b class="tl"></b>
			<b class="bl"></b>
			<b class="br"></b>
		</div>	
	</div>
</div>	


<script type="text/javascript">
	TT.addToQueue(function(){
		
		// move the user panel to holder
		$d.setXY('user-panel', $d.getXY('user-panel-holder') );
		
		// add class
		$d.addClass('user-panel','sticky');
		$d.setStyle('user-panel-content','width','560px');
		
		// load it 
		TT.Global.loadUserPanel($('user|<?php echo $first . '|' . $friends[$first]['sn']; ?>'));
	
	});
	TT.addToUnLoadQueue(function(){
	
		// move the user panel to holder
		$d.setXY('user-panel', [-999,-999] );
		
		// add class
		$d.removeClass('user-panel','sticky');	
	
	});
</script>