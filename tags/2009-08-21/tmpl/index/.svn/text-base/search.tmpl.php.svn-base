
<h1><?php echo $this->title;  ?></h1>

<div class="yui-gc">
	<div class="yui-u first">
		<div class="module">
			<div class="hd">
				<?php
					// saved
					$saved = false;
					
					// check if it's saved
					foreach ( $this->savedSearches as $s ) {
						if ( $s['q'] == $q ) {
							$saved = true; break;
						}
					}
				
					// print 
					if ( !$saved AND $q ) {
						echo " <div style='text-align:right' class='padd10'>".( $saved ? "Saved" : "<a href='#save' class='save-this-search' id='s|".htmlentities($q,ENT_QUOTES,'utf-8')."'>Save this Search</a>" )."</div>";				
					}
				?>
			</div>
			<div class="bd big">
			
				<?php
					if ( !$q ) {
						echo "<div class='no-query'>Type your query in the search box above</div>";
					}
				?>
					
				<ul class="timeline" id="timeline">
				<?php
					if ( $q ) {
						foreach ( $timeline as $t ) {
							echo $this->_bit_displayStatus($t);					
						}
					}
				?>
				</ul>
				
			</div>
		</div>	
	</div>
	<div class="yui-u side">
			
		<div class="module">	
			<ul class='side-menu' id='side-menu'>
				<?php if ( $this->loged ) { ?>
					<li class="side-menu-top open on first">
						<h4><a class='side-menu-expand' href='#'>Saved Searches</a></h4>
						<ul>
							<?php
								foreach ( $this->savedSearches as $k => $s ) {
									echo "
										<li>
											<a href='/search?q=".urlencode($s['q']).";id={$k}'>{$s['q']}</a>
											<a id='search|{$k}' class='delete-search delete' href='#'>Remove</a>
										</li>
									";
								}
							?>
						</ul>
					</li>
				<?php } ?>
				<li class="side-menu-top last open">
					<h4><a class='side-menu-expand' href='#'>Trending Topics</a></h4>
					<ul>
						<?php
							foreach ( $trend['trends'] as $t ) {
								echo "<li><a href='/search?q=".urlencode($t['name']).";src=trend'>{$t['name']}</a></li>";
							}
						?>
					</ul>
				</li>
			</ul>
		</div>
			
			<div class="module">
				<div class="hd"><h3>Search Operators</h3></div>
				<div class="bd">
					<ul class="ops">
						<li> 
							<em>twit OR Tangle</em>
							<div>containing "twit" or "tangle"</div>
						</li>
						<li>
							<em>twit -Tangle</em>
							<div>containing "twit" not "tangle"</div>
						</li>
						<li>
							<em>from:twitTangle</em>
							<div>from user "twitTangle"</div>
						</li>
						<li>		
							<em>to:twitTangle</em>
							<div>to user "twitTangle"</div>
						</li>
						<li>
							<em>@twitTangle</em>
							<div>referencing user "twitTangle"</div>
						</li>
					</ul>
				</div>
			</div>
	</div>
</div>

<script type="text/javascript">
	
	// timeline
	TT.data.timeline = <?php echo $this->json($timeline); ?>;
		
	// reset count 	
	TT.addToQueue(function(){		
		var id = "<?php echo $this->param('id',false,null,'num'); ?>";
		if ( id != "" ) {
			// reset saved search
			TT.Global.resetSavedSearch(id);

		}
		$('search-txt').value = "<?php echo htmlentities($q,ENT_COMPAT,'utf-8',true); ?>";
		TT.Global.toggleSearchExamples(0);
	});
	
	TT.addToUnLoadQueue(function(){
		$('search-txt').value = "Timeline Search";
		TT.Global.toggleSearchExamples(1);		
	});
	
</script>