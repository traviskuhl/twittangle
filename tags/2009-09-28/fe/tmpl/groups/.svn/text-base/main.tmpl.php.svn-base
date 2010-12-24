<h1>Groups</h1>

<div class="yui-ge">
	<div class="yui-u first">
		<ul class="top-list">
			<li class='box'>
				<h2>Popular</h2>
				<ul>
					<?php
						foreach ( $popular as $g ) {
						
							// settings
							$desc = $this->_getDesc($g);
						
							$pic = $this->getMiniPic($g['pic']);
						
							$g['screen_name'] = $g['user'];
						
							echo "
								<li>
									<a href='".$this->url('user-group',$g)."'>{$g['name']}</a>
									<div>{$desc}</div>
									<div class='usr'><img class='defer' src='{$pic}'> created by <a href='".$this->url('user',$g)."'>{$g['user']}</a></div>
								</li>
							";
						}
					?>
				</ul>
				<b class="tl"></b>
				<b class="tr"></b>
				<b class="bl"></b>
				<b class="br"></b>			
			</li>
			<li class='box'>
				<h2>Newest</h2>
				<ul>
					<?php
						foreach ( $newest as $g ) {
						
							// settings
							$desc = $this->_getDesc($g);
						
							$pic = $this->getMiniPic($g['pic']);
						
							$g['screen_name'] = $g['user'];
						
							echo "
								<li>
									<a href='".$this->url('user-group',$g)."'>{$g['name']}</a>
									<div>{$desc}</div>
									<div class='usr'><img class='defer' src='{$pic}'> created by <a href='".$this->url('user',$g)."'>{$g['user']}</a></div>
								</li>
							";
						}
					?>
				</ul>
				<b class="tl"></b>
				<b class="tr"></b>
				<b class="bl"></b>
				<b class="br"></b>			
			</li>					
			
			<?php
				
				// by tag
				foreach ( $byTag as $tag => $groups ) {
					echo "
						<li class='box'>
							<h2>{$tag}</h2>
							<ul>
					";
					
						foreach ( $groups as $g ) {
						
							// settings
							$desc = $this->_getDesc($g);
						
							$pic = $this->getMiniPic($g['pic']);
						
							$g['screen_name'] = $g['user'];
						
							echo "
								<li>
									<a href='".$this->url('user-group',$g)."'>{$g['name']}</a>
									<div>{$desc}</div>
									<div class='usr'><img class='defer' src='{$pic}'> created by <a href='".$this->url('user',$g)."'>{$g['user']}</a></div>
								</li>
							";
						}

					echo '
							</ul>
							<a href="'.$this->url('groups-tag',array('tag'=>$tag)).'">more</a>
							<b class="tl"></b>
							<b class="tr"></b>
							<b class="bl"></b>
							<b class="br"></b>			
						</li>
					';					
				}
			
			?>
			
		</ul>
	</div>
	<div class="yui-u">
		<div class="module side-menu">
			<ul class="side-menu">
			
				<?php if ( $this->loged ) { ?>
					<li class="side-menu-top open on first">
						<h4><a class='side-menu-expand' href="#">My Public Groups</a></h4>
						<ul>
							<?php
							
								// my groups
								$groups = $this->getGroups();
								
								foreach ( $groups as $g ) {
									if ( isset($g['settings']['privacy']) AND $g['settings']['privacy'] == 'public' ) {
									
										$g['screen_name'] = $this->user['user'];
										
										echo "<li><a href='".$this->url('user-group',$g)."'>{$g['name']}</li>";
									}
								}
							?>
						</ul>
					</li>
				<li class="side-menu-top open on">
					<h4><a class='side-menu-expand' href="#">My Favorite Groups</a></h4>
					<ul>
						<?php
						
							// my groups
							$groups = $this->getFavGroups();
							
							foreach ( $groups as $g ) {
								if ( isset($g['settings']['privacy']) AND $g['settings']['privacy'] == 'public' ) {
								
									$g['screen_name'] = $this->user['user'];
									
									echo "<li><a href='".$this->url('user-group',$g)."'>{$g['name']}</li>";
								}
							}
						?>
					</ul>
				</li>									
				<?php } ?>
			
				<li class="side-menu-top open on <?php echo ($this->loged?'':'first'); ?>">
					<h4><a class='side-menu-expand' href="#">Tags</a></h4>
					<ul>
						<?php
							foreach ( $this->_tags as $tag => $count ) {
								echo "<li><a href='".$this->url('groups-tag',array('tag'=>$tag))."'>$tag <span>{$count}</a></li>";
							}
						?>
					</ul>
				</li>			
			</ul>
			
		</div>	
	</div>
</div>