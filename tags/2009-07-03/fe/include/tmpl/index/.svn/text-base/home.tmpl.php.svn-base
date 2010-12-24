
<div class="yui-ge">
	<div class="yui-u first">
		<div class="module">
			<div class="hd">
				<h2><?php echo $title; ?></h2>
			</div>
			<div class="bd big">
			
				<?php
					if ( !$loadnow ) {
						echo "<div id='timeline-loading' class='timeline-loading'><em>Loading Your Timeline</em>";
						echo "<div>We're loading your timeline from Twitter. Because we have to connect to Twitter";
						echo " this may take up to 60 seconds. We promise we'll make this as quick as possible </div></div>";					
					}
				?>
							
				<ul class='timeline' id='timeline'>
				<?php
					foreach ( $timeline as $t ) {
						echo $this->_bit_displayStatus($t);
					}
					
					if ( count($timeline) == 0 AND $loadnow ) {
						echo "<li class='empty center gray'><br><br><br> <b>This timeline is empty</b><br> You should probably try something different</li>";
					}
					
				?>
				</ul>
				
				<?php
				
					if ( isset($pager) AND $pager->_totalPages > 0 ) {
						echo "<ul class='clear cf pager'><li class='h'>Pages:</li>";
						for ($i=1;$i <= $pager->_totalPages; $i++) {
							echo "<li><a class='".($this->param('page')==$i?'b':'')."' href='/home/{$pageUrl}?page={$i}'>{$i}</a></li>";
						}
						echo "</ul>";
					}					
				?>
			</div>			
		</div>		
	</div>
	<div class="yui-u side">
	
		<?php
			if ( count($groups) == 0 ) {
				echo "
					<div class='module'>
						<div class='bd'>
							It looks like you have created any groups. You should give that a try!.
							<div><a class='b' href='".$this->url('my-groups')."'>Go To My Groups</a></div>
						</div>
					</div>
					<br>
				";
			}
		?>
	
		<div class="module side-menu">
			<ul class="side-menu" id="side-menu">
				<li class="side-menu-top first <?php echo ($path[0]=='timeline'?'open on':''); ?>">
					<h4><a class='side-menu-expand' href='#'>Timeline</a></h4>
					<ul>
						<?php
							foreach ( $menu_timeline as $k => $t ) {
								echo "<li class='".($path[1]==$k?'on':'')."'><a href='/home/timeline/{$k}'>{$t[0]}</a></li>";
							}
						?>
					</ul>					
				</li>
				<li class="side-menu-top open <?php echo ($path[0]=='my-groups'?'open on':''); ?>">
					<h4><a class='side-menu-expand' href='#'>Groups</a></h4>
					<ul>
						<?php
							foreach ( $groups as $g ) {
								
								// get last
								$last = $this->getTimeline(array(
									'countOnly' => true,
									'groups' => array($g['id']),
									'since' => 'last'
								));
							
								// echo 
								echo "
									<li class='".($path[1]==$g['id']?'on':'')."'>
										<a href='/home/my-groups/{$g['id']}'>{$g['name']}</a>
										<div id='group-count-{$g['id']}' class='box ".($last==0?'hide':'')."'>
											<span>$last</span>
											<b class='tl'></b>
											<b class='tr'></b>
											<b class='bl'></b>
											<b class='br'></b>
										</div>
									</li>
								";
							}
							
						?>
					</ul>
				</li>				
				<li class="side-menu-top <?php echo ($path[0]=='my-networks'?'open on':''); ?>">
					<h4><a class='side-menu-expand' href='#'>Networks</a></h4>
					<ul>
						<?php
							foreach ( $networks as $n ) {
								echo "<li class='".($path[1]==$n['id']?'on':'')."'><a href='/home/my-networks/{$n['id']}'>{$n['title']}</a></li>";
							}
						?>
					</ul>					
				</li>
				<li class="side-menu-top <?php echo ($path[0]=='my-searches'?'open on':''); ?>">
					<h4><a class='side-menu-expand' href='#'>Saved Searches</a></h4>
					<ul>
						<?php
							foreach ( $this->savedSearches as $k => $s ) {
								echo "<li class='".($path[1]==$k?'on':'')."'><a href='/home/my-searches/{$k}'>".htmlentities($s['q'],ENT_QUOTES,'utf-8')."</a></li>";
							}
						?>
					</ul>					
				</li>		
				<li class="side-menu-top last <?php echo ($path[0]=='messages'?'open on':''); ?>">
					<h4><a class='side-menu-expand' href='#'>Messages</a></h4>
					<ul>
						<?php
							foreach ( $menu_messages as $k => $m ) {
								echo "<li class='".($path[1]==$k?'on':'')."'><a href='/home/messages/{$k}'>{$m[0]}</a></li>";
							}
						?>
					</ul>
				</li>							
			</ul>
		</div>
		
		<div id='trending' class='hide module'></div>		
		
	</div>
</div>

<script type="text/javascript">
	
	<?php if ( !$loadnow ) { ?>
		TT.addToQueue(function(){
			TT.Global.loadXhrPage({'href':"<?php echo URI . 'home/'. $pageUrl; ?>"},false,true);
		});
		
	<?php } else { ?>
	
		// load timeline 
		TT.data.timeline = <?php echo $this->json($timeline); ?>;
	
		// load
		TT.addToQueue(function(){
		
			// open and load 
			TT.Global.loadAndOpen("trending",{'ttl':2},'trending');	
	
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
			
		<?php } } ?>
		
</script>