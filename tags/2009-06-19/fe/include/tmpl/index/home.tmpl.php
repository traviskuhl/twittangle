<div class="yui-gc">
	<div class="yui-u first">
		<h2><?php echo $title; ?></h2>
		
		<?php
			
			if ( isset($path[1]) AND $path[1] == 'dm' ) {
			
				// sent
				if ( isset($path[2]) AND $path[2] == 'complete' ) {
					echo "<br><div class='red'>Messages Sent</div>";
				}
			
				echo "
					<br>
					<div class='box dm'>
						<h2>
							Send a Message to: 
							<span id='dm-list'></span> 
							<input id='dm-ac' type='text'>
							<div id='dm-results'></div>
						</h2>
						<form name='".time()."' method='post' action='/index/dm/send'>
							<input type='hidden' name='to' id='dm-to'>						
							<textarea id='dm-txt' name='txt'></textarea>
							<button>Send</button>
							<span class='count' id='dm-chars'>140</span>
							<b class='tr'></b>
							<b class='tl'></b>
							<b class='bl'></b>
							<b class='br'></b>
						</form>
					</div>
					<script type='text/javascript'>
						TT.addToQueue(function(){
							TT.Global.initDirectMessage();
						});
					</script>
				";
			}
			
			if ( $path[0] == 'timeline' ) {
			
				// rand user
				$u1 = array_rand($this->allFriends,1);
				$u2 = array_rand($this->allFriends,1);		
				
				$sn1 = $this->allFriends[$u1]['sn'];
				$sn2 = $this->allFriends[$u2]['sn'];
				
				// print 
				echo "
					<form class='box realtime' name='".time()."' onsubmit=' return false; '>
						
						<input type='text' id='real-text' value='Timeline Search'>	
						<div class='small gray'>
							<b>Search Your Timeline!</b> Example:
							<a title='Tweets containing twittangle' class='bubble' onclick=\" TT.data.RealTime.showExample('twittangle'); \" href='javascript:void(0);'>twittangle</a> / 
							<a title='Tweets to you' class='bubble' onclick=\" TT.data.RealTime.showExample('to:me'); \" href='javascript:void(0);'>to:me</a> /
							<a title='Tweets to you' class='bubble' onclick=\" TT.data.RealTime.showExample('@me'); \" href='javascript:void(0);'>@me</a> /							
							<a title='Tweets from {$sn1}' class='bubble' onclick=\" TT.data.RealTime.showExample('from:{$sn1}'); \" href='javascript:void(0);'>from:{$sn1}</a> / 
							<a onclick=' TT.data.RealTime.toggleAll(this); ' href='javascript:void(0);'>More</a>
							
							<div class='hide' id='realtime-ops'>
								<h3>All Operators</h3>
									<table>
									<tr><td width='150'><b>from:</b>twittangle</td><td>search for tweets from 'twittangle'</td></tr>
									<tr><td><b>from:me</b></td><td>search for tweets from you</td></tr>
									<tr><td><b>to:</b>twittangle</td><td>search for tweets to 'twittangle'</td></tr>
									<tr><td><b>to:</b>me</td><td>search for tweets to you</td></tr>
									<tr><td><b>@</b>twittangle</td><td>search for tweets that reference 'twittangle'</td></tr>
									<tr><td><b>friend:</b>kevin</td><td>search for tweets from any of your friends, who's name contains 'kevin'</td></tr>
									<tr><td><b>has:link</b></td><td>search for tweets that have a link</td></tr>
									<tr><td><b>has:pic</b></td><td>search for tweets that have a picture</td></tr>
									<tr><td><b>has:video</b></td><td>search for tweets that have a video</td></tr>
									</table>
								<h3>Combine Operators</h3>
									<table>
									<tr><td width='200'>from:twittangle to:me</td><td>search for all tweets from 'twittangle' to you</td></tr>
									<tr><td>@twittangle has:link friday</td><td>search for tweets that reference 'twittangle', contain a link & the word 'friday'</td></tr>
									</table>
							</div>
												
						</div>
					
						<b class='tl'></b>
						<b class='tr'></b>
						<b class='bl'></b>
						<b class='br'></b>
						
					</form>
				";
			
			}
		
		?>
				
		<br>
		<ul class='timeline' id='timeline'>
		<?php
			foreach ( $timeline as $t ) {
				echo $this->_bit_displayStatus($t);
			}
		?>
		</ul>
		
		<?php
			if ( isset($pages) AND $pages > 1 ) {
				echo "<ul class='clear cf pager'><li class='h'>Pages:</li>";
				for ($i=1;$i <= $pages; $i++) {
					echo "<li><a class='".($this->param('page')==$i?'b':'')."' href='/home/{$pageUrl}?page={$i}'>{$i}</a></li>";
				}
				echo "</ul>";
			}
		?>
	
	</div>
	<div class="yui-u side">
		<div class="box no-box-padd">
		
			<div style="padding: 20px 10px; text-align:center">
				<div>Like the new Timeline Search?</div>
				<a class='b' onclick=" $d.addClass('update-status','write'); $('update-status-txt').focus(); $('update-status-txt').value='Check out the new Timeline Search on http://twittangle.com'; " href='#'>Tweet about it</a>
			</div>
		
			<ul class="side-menu" id="side-menu">
				<li class="side-menu-top <?php echo ($path[0]=='timeline'?'open on':''); ?>">
					<a href="#">Timeline</a>
					<ul>
						<?php
							foreach ( $menu_timeline as $k => $t ) {
								echo "<li class='".($path[1]==$k?'on':'')."'><a href='/home/timeline/{$k}'>{$t[0]}</a></li>";
							}
						?>
					</ul>					
				</li>
				<li class="side-menu-top <?php echo ($path[0]=='messages'?'open on':''); ?>">
					<a href="#">Messages</a>
					<ul>
						<?php
							foreach ( $menu_messages as $k => $m ) {
								echo "<li class='".($path[1]==$k?'on':'')."'><a href='/home/messages/{$k}'>{$m[0]}</a></li>";
							}
						?>
					</ul>
				</li>				
				<li class="side-menu-top <?php echo ($path[0]=='my-groups'?'open on':''); ?>">
					<a href="#">Groups</a>
					<ul>
						<?php
							foreach ( $groups as $g ) {
								echo "<li class='".($path[1]==$g['id']?'on':'')."'><a href='/home/my-groups/{$g['id']}'>{$g['name']}</a></li>";
							}
						?>
					</ul>
				</li>
				<li class="side-menu-top <?php echo ($path[0]=='my-networks'?'open on':''); ?>">
					<a href="#">Networks</a>
					<ul>
						<?php
							foreach ( $networks as $n ) {
								echo "<li class='".($path[1]==$n['id']?'on':'')."'><a href='/home/my-networks/{$n['id']}'>{$n['title']}</a></li>";
							}
						?>
					</ul>					
				</li>
				<li class="side-menu-top <?php echo ($path[0]=='my-searches'?'open on':''); ?>">
					<a href="#">Saved Searches</a>
					<ul>
						<?php
							foreach ( $this->savedSearches as $k => $s ) {
								echo "<li class='".($path[1]==$k?'on':'')."'><a href='/home/my-searches/{$k}'>".htmlentities($s['q'],ENT_QUOTES,'utf-8')."</a></li>";
							}
						?>
					</ul>					
				</li>				
			</ul>
			
			<div id='sugg-users' class='padd10 hide cf'>
				<h3>Users You May Like</h3>
			</div>
			
			<div id='sugg-networks' class='padd10 hide cf'>
				<h3>Networks You May Like</h3>
			</div>
		
			<b class="tl"></b>
			<b class="tr"></b>
			<b class="bl"></b>
			<b class="br"></b>
		</div>
	</div>
</div>


<script type="text/javascript">

	// load timeline 
	TT.data.timeline = <?php echo $this->json($timeline); ?>;

	// load
	TT.addToQueue(function(){
	
		// get
		YAHOO.util.Get.script("<?php echo $this->asset('js','realtime','1.0'); ?>",{
			'onSuccess': function() {
				TT.data.RealTime = new YAHOO.twitTangle.RealTime({
					'req': '<?php echo $this->req; ?>'
				});
			}
		});
	
	});

	// timeline 	
	<?php if ( count($timeline) > 0 AND $reload ) { ?>
		
		// load live
		TT.addToQueue(function(){
			TT.Global.startTimelineWatch({
				'type': "<?php echo $path[0]; ?>",
				'id': "<?php echo $path[1]; ?>",
				'max': "<?php $max = array_slice($timeline,0,1); echo $max[0]['id']; ?>"
			});
		});
		
		// stop whenwe exit
		TT.addToUnLoadQueue(function(){
			TT.Global.endTimelineWatch();
		});
		
	<?php } ?>
		
</script>