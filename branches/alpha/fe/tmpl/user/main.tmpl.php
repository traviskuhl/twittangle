<div id="yui-main" class="yui-ge">
	<div class="yui-u first">
		<div class="mod">
			<div class="yui-ge">
				<div class="yui-u first">
					<ul id="timeline" class="timeline">
						<?php
							foreach ( $timeline as $tweet ) {
								echo $tweet->display();
							}
						?>
					</ul>
				</div>
				<div class="yui-u">
					<div id="timeline-rr" class="timeline-rr">
						<h3>Filter</h3>
						<ul>				
							<li>
								<h2>By Tag</h2>
								<ul>
									<li><input type="text"></li>
								</ul>
							</li>
						</ul>
					
						<div id="popular-mentions" class="<?php echo (count($mentions)==0?'hide':''); ?>">
							<h3>Popular Mentions</h3>
							<ul class='small-user-list'>					
								<?php
									
									// each
									foreach ( $mentions as $m ) {
										echo "<li>".$m['html']."</li>";
									}
								
								?>
							</ul>
						</div>
						
						<div id="popular-links" class="<?php echo (count($links)==0?'hide':''); ?>">
							<h3>Popular Links</h3>
							<ul class="link-list">
								<?php
									foreach ( $links as $l ) {
										
										// html 
										echo "<li>".$l['html']."</li>";
										
										// expand
										$expand[] = array( 'url' => $l['url'], 'type' => 'l' );
										
									}
								?>
							</ul>
						</div>
						
						<div id="popular-images" class="<?php echo (count($images)==0?'hide':''); ?>">
							<h3>Popular Images</h3>
							<ul class="image-thumb-list image-overlay">
								<?php
									foreach ( $images as $i ) {
										
										// expand
										if ( $i['expand'] ) {
																					
											// expand
											$expand[] = array( 'url' => $i['url'], 'type' => 'i', 'm' => $i['m'] );
											
										}

										// html me
										echo "<li>".$i['html']."</li>";
										
									}
								?>
							</ul>
						</div>						
						

					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="yui-u">

		<ul id="menu" class="side-menu">
			<li>
				<h2>Tweets</h2>
				<ul>
					<?php
					
						// other
						$main = array(
							'my' => 'My Timeline',
							'mentions' => 'Mentions',
							'fav' => 'Favorites',
						);
					
						foreach ( $main as $type => $title ) {
							echo "<li><a href='".$this->url("home-{$type}")."'>{$title}</a></li>";		
						}
					
					?>				
				</ul>					
			</li>		
			<li>
				<h2>My Lists</h2>
				<ul>
					<?php
						foreach ( $lists as $row ) {
							echo "<li><a href='".$this->url('home-list',$row)."'>{$row['name']}</a></li>";
						}
					?>
				</ul>
			</li>
			<li>
				<h2>List I Follow</h2>
				<ul>
					<?php
						foreach ( $follow as $row ) {
							echo "<li><a href='".$this->url('home-list',$row)."'>{$row['name']}</a></li>";
						}
					?>
				</ul>				
			</li>
			<li>
				<h2>Networks</h2>
				<ul>
					<li><a href=''>Network 1</a></li>
					<li><a href=''>Network 2</a></li>					
				</ul>				
			</li>
			<li>
				<h2>Saved Searches</h2>
				<ul>
					<?php
						foreach ( $searches as $row ) {
							echo "<li><a href='".$this->url('home-search',false,array('q'=>$row['query'],'id'=>$row['id']))."'>{$row['name']}</a></li>";
						}
					?>
				</ul>				
			</li>
		</ul>

	</div>
</div>

<script type="text/javascript">

	// load
	T.add('l',function(){
	
		// fixed
		T.Obj.registerFixedElement(['#menu','#timeline-rr']);
		
		// subscribe to page fire
		T.Obj.on('t-base:bottompage',function(){
			this.loadTimeline({
				'type': 'my'
			});	
		});
		
		// expand
		T.Obj.expandLinks(<?php echo json_encode($expand); ?>);
		
	});
</script>
