<div class="yui-gc">
	<div class="yui-u first">
	
		<h2>
			<?php echo "<img id='up|{$user['id']}|{$user['screen_name']}' class='user-panel' src='{$user['profile_image_url']}' align='left'>"; ?>
			<?php echo $user['name'] ?>
			<div><?php echo $user['screen_name']; ?> | <?php echo "<a href='http://twitter.com/{$user['screen_name']}'>twitter.com/{$user['screen_name']}</a>"; ?></div>
		</h2>
		
		<?php
		
			if (isset($error))  {
				echo "<div class='red'>{$error}</div>";
			}
			
			if ( !$timeline AND $user["protected"] == 'true' ) {
				echo "			
					<br><br>	
					<div class='center gray'>
						<em class='b'>This Account Is Protected</em><br>
						We were unable to retreive this users timeline <br>
						as their account is protected
					</div>	
				";		
			}
			else {
				echo '<ul class="timeline timeline-no-pic" id="timeline">';
					foreach ( $timeline as $t ) {
						echo $this->_bit_displayStatus($t,false,false);
					}
				echo '</ul>';
			}	
		?>
	
	</div>
	<div class="yui-u side">
		<div class="box no-box-padd">
		
			<ul class='side-menu'>
			<?php
				
				// are they friends
				if ( !array_key_exists($user['id'],$this->allFriends) ) {
					echo "<li><a class='ignore' href='/user/{$user['screen_name']}/follow'>Follow {$user['name']}</a></li>";
				}
				
			?>
			<li><a href='/search?q=<?php echo urlencode("@{$user['screen_name']}"); ?>'>Search @<?php echo $user['screen_name']; ?></a></li>
			</ul>
			<br>
		
			<ul class='user-stats cf'>
				<li>
					<em><?php echo number_format($user['friends_count']); ?></em>
					following
				</li>
				<li>
					<em><?php echo number_format($user['followers_count']); ?></em>
					followers
				</li>
				<li>
					<em><?php echo number_format($user['statuses_count']); ?></em>
					updates
				</li>								
			</ul>		
		
			<?php
			
			
				// some suser info
				echo "<ul class='user-info'>";
					
					// go for it 
					foreach ( $user as $k => $v ) {
						switch($k) {
							case 'description': echo "<li><em>Description:</em> {$v}</li>"; break;
							case 'location': echo "<li><em>Location:</em> {$v}</li>"; break;
							case 'url':
								$url = $v;
								if ( strlen($v) > 100 ) {
									$url = substr($v,0,100).'...';
								}
								echo "<li><em>URL:</em> <a href='{$v}'>{$url}</a></li>";
								break;
						};
					}
				
				echo "</ul>";			
					
			?>		


			<div class='padd10'>
			<?php
				
				// loged in
				if ( $this->loged AND isset($friends) AND count($friends) > 0 ) {
					
					echo "
						<h3>Friends in Common</h3>
						<ul class='mini-users'>
					";
						
						// i
						$i = 0;
					
						foreach ( $friends as $usr ) {
						
							if ( $i++ > 90 ) { break; }
						
							// mini
							$mini = $this->getMiniPic($usr['img']);
						
							// echo 
							echo "<li><a href='/user/{$usr['sn']}'><img title='{$usr['name']} ({$usr['sn']})' class='defer bubble' src='".BLANK."' style='background-image: url($mini)'></a></li>";

						}
					
					echo "</ul>";
				
				}
				
			?>
			</div>
		
			<b class="tr"></b>
			<b class="tl"></b>
			<b class="bl"></b>
			<b class="br"></b>
		</div>
	</div>
</div>
