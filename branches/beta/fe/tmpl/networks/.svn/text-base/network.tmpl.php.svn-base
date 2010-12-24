<h1><a href='<?php echo $this->url('networks'); ?>'>Networks</a> / <?php echo $info['title'] ?></h1>

<div class="yui-gc">
	<div class="yui-u first">	
	
		<ul class="timeline network-timeline">
			<?php
				foreach ( $posts as $r ) {
					echo $this->_bit_displayNetworkPost($r);
				}
			?>
		</ul>	
		
		<?php
		
			if ( $pages > 0 ) {
				echo "<ul class='clear cf pager'><li class='h'>Pages:</li>";
				for ($i=1;$i <= $pages; $i++) {
					echo "<li><a class='".($this->param('page')==$i?'b':'')."' href='".$this->url('network',$info)."?page={$i}'>{$i}</a></li>";
				}
				echo "</ul>";
			}					
		?>				
		
	
	</div>
	<div class="yui-u side">
		
			<?php
				if ( $this->loged AND !array_key_exists($info['id'],$this->myNetworks) ) {
					echo "
						<div class='module'>
							<div class='bd join-module'>
								<a class='join' href='".$this->url('network-join',$info)."'>Join this Network</a>
							</div>
						</div>
						<br>
					";
				}
				else if ( $this->loged AND array_key_exists($info['id'],$this->myNetworks) ) {
					echo "
						<div class='module post'>
							<div class='hd'><h2>Post a Message</h2></div>
							<div class='bd'>
								<form method='post' action='".$this->url('network-post',$info)."'>
								<input type='hidden' name='token' value='".$this->md5('post-'.$this->uid.$info['id'])."'>
								<textarea name='text'></textarea>
								<label><input type='checkbox' name='twitter' value='yes' checked='checked'> Also post to Twitter</label>
								<button type='submit'>Post</button>
								</form>
							</div>
						</div>
						<br>
					";				
				}
			?>
		
			<div class="module">
				<div class="bd">
				
					<h4 class="first">About this Network</h4>
					<ul class="list">
						<li><em class="b">Description:</em> <?php echo $info['info']; ?></li>
						<?php if ( $info['url'] ) { ?>
							<li><em class="b">URL:</em> <?php echo "<a target='new' href='{$info['url']}'>".substr($info['url'],0,20)."</a>"; ?></li>
						<?php } ?>			
						<li>
							<em class="b">Categories:</em>
							<?php
								$sth = $this->query("SELECT name, id FROM network_cats WHERE FIND_IN_SET(id,'??') ",array($info['cats']));
								$list = array();
								while ( $row = $sth->fetch_assoc()) {
									$list[] = "<a href='".$this->url('net-cat',$row)."'>{$row['name']}</a>";
								}
								echo implode(", ",$list);
							?>	
						</li>				
					</ul>


						<?php
						
							if ( isset($myFriends) AND count($myFriends) > 0 ) {
								
								echo "							
									<h4>My Friends who are Members</h4>
									<ul class='mini-users backfill'>
								";								
								
									foreach ( $myFriends as $m ) {
						
										// mini
										$sn = $m['user'];
										
										// mini
										$mini = $this->getMiniPic($m['pic']);
										
										echo "<li><a href='/user/{$sn}' title='{$sn}'><img class='defer bubble' title='{$sn}' style='background-color: #fff; background-image: url({$mini});' src='".BLANK."' width='24' height='24'></a></li>";
									}
						
								echo "</ul>";
								
							}
						
						?>
					</ul>	
				
					<h4>Members</h4>
					<ul class="mini-users backfill">
						<?php
						
							foreach ( $members as $m ) {
				
								// mini
								$sn = $m['user'];
								
								// mini
								$mini = $this->getMiniPic($m['pic']);
								
								echo "<li><a href='/user/{$sn}' title='{$sn}'><img class='defer bubble' title='{$sn}' style='background-color: #fff; background-image: url({$mini});' src='".BLANK."' width='24' height='24'></a></li>";
							}
						
						
						?>
					</ul>	
						
				</div>
			</div>

		
	</div>
</div>

