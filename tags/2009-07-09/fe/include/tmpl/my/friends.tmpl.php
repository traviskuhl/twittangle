
<h1>My Friends <span>(<?php echo number_format(count($this->allFriends)); ?> total)</span></h1>
<div class="yui-gc">
	<div class="yui-u first">
		<div class="module">
			<div class="bd">
				
					<?php if ( count($this->allFriends) == 0 ) { ?>
					<div style="padding: 100px; text-align: center">
						<div class="b">We couldn't find any of your Friends</div>
						For some reason we don't have any friends for you. Try importing them again!
						<div><a href='/my/friends/import'>Import Your Friends</a></div>
					</div>
					<?php } ?>
						
					<ul class="user-list clear cf">
						<?php
							
							// first
							$first = key($friends);
						
							foreach ( $friends as $f ) {
								echo "
									<li><a id='up|{$f['id']}|{$f['sn']}' class='user-panel' href='".$this->url('user',array('screen_name'=>$f['sn']))."'><img width='48' height='48' class='defer bubble' title='{$f['name']}  ({$f['sn']})' src='".BLANK."' style='background-image: url(".$this->getUserPic($f['img']).")'></a></li>";
							}
						?>
					</ul>
					
					
					<?php
						if ( $pages > 0 ) {
							echo "<ul class='pager'><li class='h'>Page:</li>";
								for ( $i = 1; $i <= $pages; $i++ ) {
									echo "<li><a class='".($this->param('page',1)==$i?'b':'')."' href='/my/friends?page={$i}'>{$i}</a>";
								}
							echo "</ul>";
						}
					?>
					
			</div>			
		</div>	
	</div>
	<div class="yui-u side">
	
		<div class="module">
			<div class="bd center">
				<div class="b"><a href="<?php echo $this->url('my-groups'); ?>">Create Groups</a></div>
				<div class="small gray">Looking to create groups of your friends... Visit the My Groups section</div>
			</div>
		</div>		
	
		<?php /*
		<div class="module">
			<div class="hd"><h3>Search Your Friends</h3></div>
			<div class="bd">
			
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
						
						console.log({'id':"x|"+args[2][1]});
						
						// load
						TT.Global.displayUserPanelOverlay({'id':"x|"+args[2][1]});
						
						// clean
						$('friend-search-text').value = "";
						
					});				
					
				});
			</script>
			</div>		
		</div>
		*/ ?>
		
		<div class="module">
			<div class="bd">
				
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
			</div>
		</div>	
	</div>
</div>	

