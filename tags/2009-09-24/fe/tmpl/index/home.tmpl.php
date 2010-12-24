
<h1><?php echo $title; ?></h1>
<?php
	if ( isset($this->rssLink) ) {
		echo "<a class='rss ignore' href='{$this->rssLink}'><img class='ignore' src='http://ms-cdn.com/static/tangle/images/v3/rss.png'></a>";
	}
?>

<div class="yui-ge">
	<div class="yui-u first">
		<div class="module">
			<div class="bd big">
			
				<?php
					if ( $path[1] == 'columns' ) {
						echo "<div id='timeline-loading' class='timeline-loading'><em>Loading Columns</em>";
						echo "<div> </div></div>";					
					}
					else if ( !$loadnow ) {
						echo "<div id='timeline-loading' class='timeline-loading'><em>Loading Your Timeline</em>";
						echo "<div>We're loading your timeline from Twitter. Because we have to connect to Twitter";
						echo " this may take up to 60 seconds. We promise we'll make this as quick as possible </div></div>";					
					}
				?>
							
				<ul class='timeline <?php echo ($path[1]=='dm'?'dm':''); ?>' id='timeline'>
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
							It looks like you have not created any groups. You should give that a try!.
							<div><a class='b' href='".$this->url('my-groups')."'>Go To My Groups</a></div>
						</div>
					</div>
					<br>
				";
			}
		?>
	
			<div class="module">
				<div class="bd center">
					<div class="b"><a id='open-columns' href="http://twittangle.com/home/columns">Column View</a></div>
					<div class="small gray">View multiple timelines all at once with the new column layout</div>
				</div>
			</div>
			
			<br>
			
		<div class="module side-menu" id="side-menu">			
			<ul class="side-menu">
				<li class="side-menu-top first <?php echo ( ($path[0]=='timeline' OR $path[0] =='columns')?'open':''); ?>">
					<h4><a class='side-menu-expand' href='#'>Timeline</a></h4>
					<ul>
						<?php
							foreach ( $menu_timeline as $k => $t ) {
								echo "<li class='".($path[1]==$k?'on':'')."' id='link-".md5("/home/timeline/{$k}")."'><a href='/home/timeline/{$k}'>{$t[0]}</a></li>";
							}
						?>
					</ul>					
				</li>
				<li class="side-menu-top open <?php echo ( ($path[0]=='my-groups' OR $path[0] =='columns')?'open':''); ?>">
					<h4><a class='side-menu-expand' href='#'>Groups</a></h4>
					<ul>
						<?php
							if ( is_array($groups) ) {
								foreach ( $groups as $g ) {										
								
									// echo 
									echo "
										<li class='".($path[1]==$g['id']?'on':'')."' id='link-".md5("/home/my-groups/{$g['id']}")."'>
											<a href='/home/my-groups/{$g['id']}'>{$g['name']}</a>
											<div id='group-count-{$g['id']}' class='box hide'>
												<span>0</span>
												<b class='tl'></b>
												<b class='tr'></b>
												<b class='bl'></b>
												<b class='br'></b>
											</div>
										</li>
									";	
								}
							}	
						?>
					</ul>
				</li>				
				<li class="side-menu-top <?php echo ( ($path[0]=='my-networks' OR $path[0] =='columns')?'open':''); ?>">
					<h4><a class='side-menu-expand' href='#'>Networks</a></h4>
					<ul>
						<?php
							foreach ( $networks as $n ) {
								echo "<li class='".($path[1]==$n['id']?'on':'')."' id='link-".md5("/home/my-networks/{$n['id']}")."'><a href='/home/my-networks/{$n['id']}'>{$n['title']}</a></li>";
							}
						?>
					</ul>					
				</li>
				<li class="side-menu-top <?php echo ( ($path[0]=='my-searches' OR $path[0] =='columns')?'open':''); ?>">
					<h4><a class='side-menu-expand' href='#'>Saved Searches</a></h4>
					<ul>
						<?php
							foreach ( $this->savedSearches as $k => $s ) {
								echo "<li class='".($path[1]==$k?'on':'')."' id='link-".md5("/home/my-searches/{$k}")."'><a href='/home/my-searches/{$k}'>".htmlentities($s['q'],ENT_QUOTES,'utf-8')."</a></li>";
							}
						?>
					</ul>					
				</li>		
				<li class="side-menu-top last <?php echo ( ($path[0]=='messages' OR $path[0] =='columns')?'open':''); ?>">
					<h4><a class='side-menu-expand' href='#'>Messages</a></h4>
					<ul>
						<?php
							foreach ( $menu_messages as $k => $m ) {
								echo "<li class='".($path[1]==$k?'on':'')."' id='link-".md5("/home/messages/{$k}")."'><a href='/home/messages/{$k}'>{$m[0]}</a></li>";
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
	
	TT.data.columnSettings = <?php echo ($this->user['cols']?$this->user['cols']:'{"columns":{"length":0}}'); ?>;
	
	<?php if ( $path[0] == 'columns' ) { ?>
		
		// get
		TT.addToQueue(function(){		
			YAHOO.util.Get.script("<?php echo $this->asset('js','columns','1.0'); ?>",{
				'onSuccess': function() {
				
					// coluns 
					TT.data.columns = new $Y.twitTangle.Columns({});
					
					// load
					TT.data.columns.setup();
					
				}
			});			
		});
		
	<?php } else if ( !$loadnow ) {  ?>
	
		TT.addToQueue(function(){
			TT.Global.loadXhrPage({'href':"<?php echo URI . 'home/'. $pageUrl; ?>"},false,true);
		});				
		
	<?php } else { ?>
	
		// get
		TT.addToQueue(function(){
			YAHOO.util.Get.script("<?php echo $this->asset('js','columns','1.0'); ?>",{
				'onSuccess': function() {
					TT.data.columns = new $Y.twitTangle.Columns({});
				}
			});	
		});
	
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