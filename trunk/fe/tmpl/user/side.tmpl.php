
<div class="module">
	<div class="bd">
	
		<ul class='menu'>
		<?php
			
			// are they friends
			if ( $this->loged AND !array_key_exists($user['id'],$this->allFriends) ) {
				echo "<li><a class='ignore' href='/user/{$user['screen_name']}/follow'>Follow {$user['name']}</a></li>";
			}
			
		?>
		<li><a href='/search?q=<?php echo urlencode("@{$user['screen_name']}"); ?>'>Search @<?php echo $user['screen_name']; ?></a></li>
<?php /*		<li><a href=''>Our Tweets</a></li>
		<li class="last"><a href=''>Send a DM</a></li> */ ?>
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
				tweets
			</li>								
		</ul>									
		
		<?php if ( count($this->aGroups) > 0 ) { ?>			
			<h3><?php echo $user['name']; ?>'s Groups</h3>
			<ul class='groups'>
				<?php
					foreach ( $this->aGroups as $g ) {
						$g['screen_name'] = $user['screen_name'];
						echo "<li><a href='".$this->url('user-group',$g)."'>{$g['name']} <span class='small gray'>({$g['count']})</span> </a></li>";
					}
				?>	
			</ul>				
		<?php } ?>

		<?php
			
			// loged in
			if ( $this->loged AND isset($this->_friends) AND count($this->_friends) > 0 ) {
				
				echo "
					<h3>Friends in Common</h3>
					<ul class='friends mini-users'>
				";
					
					// i
					$i = 0;
				
					foreach ( $this->_friends as $usr ) {
					
						if ( $i++ > 90 ) { break; }
					
						// mini
						$mini = $this->getMiniPic($usr['img']);
					
						// echo 
						echo "<li><a href='/user/{$usr['sn']}'><img title='{$usr['name']} ({$usr['sn']})' class='defer bubble' src='".BLANK."' style='background-image: url($mini)'></a></li>";

					}
				
				echo "</ul>";
			
			}
			
		?>	
		
		<div class="padd10 small gray">
			If you are <?php echo $user['name']; ?> and want your profile removed from twitTangle.com, 
			<a class='panel' href="/index/remove">remove it now</a>.
		</div>			
		
		<br><br>

<div style="text-align:center">		
<script type="text/javascript"><!--
google_ad_client = "pub-8118251418695668";
/* 200x200, created 3/1/10 */
google_ad_slot = "9175857407";
google_ad_width = 200;
google_ad_height = 200;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>		
</div>		
		
	</div>
</div>
