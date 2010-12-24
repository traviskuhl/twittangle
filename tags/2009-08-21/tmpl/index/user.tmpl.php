
<?php


	if ( $blocked ) {
	
		echo "
			<h1>User Account Blocked</h1>
			<div class='module'>
				<div class='bd center' style='padding: 100px'>
					<div class='b'>{$id} has blocked their Account</div>
					<div>{$id} has decided to block their account from twitTangle. To view their profile, visit twitter directly at: <a class='ext-link-catch' href='http://twitter.com/{$id}'>http://twitter.com/{$id}</a></div>
				</div>	
			</div>
		";		
	
	}
	else if ( $user['protected'] ) {
	
		// set prodile 
		$this->customProfile = false;
		
		echo "
			<h1>User Account Protected</h1>
			<div class='module'>
				<div class='bd center' style='padding: 100px'>
					<div class='b'>{$user['name']}'s account on Twitter is Protected</div>
					<div>Because this user has selected to keep their Twitter account protected, we do not show it on twitTangle. To view their profile, visit twitter directly at: <a class='ext-link-catch' href='http://twitter.com/{$user['screen_name']}'>http://twitter.com/{$user['screen_name']}</a></div>
				</div>	
			</div>
		";
	
	}	
	else {

?>

<div class="yui-gc">
	<div class="yui-u first">
		<div class="module big">
			<div class="hd">
			
				<h2>
					<?php echo "<img id='up|{$user['id']}|{$user['screen_name']}' class='user-panel' src='{$user['profile_image_url']}' align='left'>"; ?>
					<?php echo $user['name'] ?>
					<div><?php echo $user['screen_name']; ?> | <?php echo "<a href='http://twitter.com/{$user['screen_name']}'>twitter.com/{$user['screen_name']}</a>"; ?></div>
				</h2>
				
				<ul class='menu'>
				<?php
					
					// are they friends
					if ( $this->loged AND !array_key_exists($user['id'],$this->allFriends) ) {
						echo "<li><a class='ignore' href='/user/{$user['screen_name']}/follow'>Follow {$user['name']}</a></li>";
					}
					
				?>
				<li><a href='/search?q=<?php echo urlencode("@{$user['screen_name']}"); ?>'>Search @<?php echo $user['screen_name']; ?></a></li>
				</ul>				
				
			</div>
			<div class="bd">
				
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
		</div>	
	</div>
	<div class="yui-u side">

		<div class="module">
			<div class="bd">
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
				
			</div>
		</div>


			<?php
				
				// loged in
				if ( $this->loged AND isset($friends) AND count($friends) > 0 ) {
					
					echo "<br>
						<div class='module'>
							<div class='hd'><h3>Friends in Common</h3></div>
							<div class='bd'>
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
					
					echo "</ul></div></div>";
				
				}
				
			?>
			
			<br><br>
			
			<div class="padd10 small gray">
				If you are <?php echo $user['name']; ?> and want your profile removed from twitTangle.com, 
				<a class='panel' href="/index/remove">remove it now</a>.
			</div>

		

	</div>
</div>


<?php } ?>

