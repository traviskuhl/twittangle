<h1>Groups / <?php echo $tag; ?></h1>

<div class="yui-ge">
	<div class="yui-u first">

		<table class='groups'>
			<?php
				while ( $row = $sth->fetch_assoc() ) {
					
					// get a desc
					$desc = $this->_getDesc($row);
				
							$pic = $this->getMiniPic($row['pic']);
						
							$row['screen_name'] = $row['user'];				
				
					echo "
						<tr>
							<td>
								<a href='".$this->url('user-group',$row)."'>{$row['name']} <span>(".number_format($row['count'])." user)</span></a>
								<div class='desc'>{$desc}</div>
								<div class='info'>
									created ".$this->displayTimestamp($row['created'])." - 
									last updated ".$this->displayTimestamp($row['updated'])."
							</td>
							<td>
								<div class='usr'><img class='defer' src='{$pic}'> created by <a href='".$this->url('user',$row)."'>{$row['user']}</a></div>
							</td>
						</tr>
					";
				}
			?>
		</table>

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