
	<div class='yui-g top'>
		<div class='yui-u first'>
			<div class="module">
				<div class="bd">
				
					<div id='ie6msg' class='b' style="display: none; background: red; color: #fff; padding: 10px; text-align:center; margin-bottom: 10px;"></div>	
					<script type="text/javascript">
						TT.addToQueue(function(){
							if ( $Y.env.ua.ie > 0 && $Y.env.ua.ie < 7 ) {
								$('ie6msg').innerHTML = "It looks like you're using a version of Internet Explorer we don't currently support. Please upgrade to the latest version.";
								$d.setStyle('ie6msg','display','block');
							}
						});
					</script>				
				
					<h2>What Is twitTangle?</h2> <p>twitTangle is a free service that helps you untangle the mess of having too many friends on twitter. We allow you to rate and tag your friends and then filter your timeline to help you easily find the tweets that are most important to you! <strong class='b'>NEW</strong> Now you can create custom groups of your followers and filter your timeline by that group.					
					<br><br>		
					<h2>How Does It Work?</h2> <p>It's simple really. Just use your Twitter account to login. Then we'll show you a list of all of the people you're following on Twitter. Go through the list and rate and tag your favorites. Once you've got all your favorites rated and tagged, click 'Home' and we'll compile a custom timeline for you, with tweets from your favorite users first. Then you can filter by tag or go farther back in time to see more tweets.</p>	
				</div>
			</div>
		</div>
		<div class='yui-u second'>
			<div class="module">	
				<div class="hd"><h2>Login</h2></div>
				<div class="bd">					
				
					<a class="login-btn" href='/index/oauth?do=auth'>Login with Twitter</a>
					<div class="center small gray">This will forward you to Twitter. Once there, click 'Allow' and you will be returned to twitTangle</div>
				
				
					<form class="auth-login box" method='post' action='/login' name="<?php echo rand(); ?>">
					<input type="hidden" name="do" value="submit">
					<input type="hidden" name="a" value="<?php echo $this->param('r'); ?>">
						<div class="gray small">If you'd still like to use the normal login, enter your username and password below. We <strong class="b">strongly</strong> suggest using the link above</div>
						<ul class='form'>				
							<li>
								<label>
									<input type='text' name='u' value="Username" onfocus="this.value='';">
								</label>
							</li>
							<li>
								<label>
									<input type="type" name="p" value="Password" onfocus="this.value='';this.setAttribute('type','password');">
								</label>
								<button type="submit" onclick=" this.innerHTML='Please Wait...'; return true; this.disabled=true; ">Login</button>
							</li>
						</ul>
					
						<div class="hide" style='color:red'>Twitter is reporting intermittent capacity issues. This may limit your ability to use twitTangle</div> 
					
						<b class="tr"></b>
						<b class="tl"></b>
						<b class="bl"></b>
						<b class="br"></b>
					</form>
					
				
			
				</div>
			</div>
		</div>
	</div>
	<br><br>
	<div class="module">
		<div class="hd"><h3>What they're are saying about <a target='_new' href='http://twitter.com/twitTangle'>@twitTangle</a></h3></div>
		<div class="bd">
			<?php
				
				// get from search
				$cid = 'twittangle.search';
				$ttl = 60*5;
				
				// get
				$cache = $this->getCache($cid,$ttl);
				
					// nope
					if ( !$cache ) {
					
						// run it 
						$r = $this->curl("http://search.twitter.com/search.json?q=twittangle");
						
						// json
						$cache = json_decode($r,true);
						$i = 0;
						
						$this->saveCache($cid,$cache,$ttl);
																		
					}	
					
					$r = array();
					
					foreach ( $cache['results'] as $item ) {
						if ( strtolower($item['from_user']) != 'twittangle' ) {
							$r[] = array(
								'text' => $item['text'],
								'id' => $item['id'],					
								'created_at' => $item['created_at'],
								'user' => array(
									'screen_name' => $item['from_user'],
									'profile_image_url' => $item['profile_image_url'],
									'id' => $item['from_user_id']
								),
								'favorited' => false,
								'source' => "the web",
								'in_reply_to_status_id' => false
							);			
						}
					}
					
			?>		
							
				<div class="yui-g">
					<div class="yui-u first">
						<?php
							echo "<ul class='timeline'>";
							foreach ( array_slice($r,0,3,true) as $d ) {						
								echo $this->_bit_displayStatus($d);
							}
							echo "</ul>";
						
						?>		
					</div>
					<div class="yui-u">
						<?php
							echo "<ul class='timeline'>";
							foreach ( array_slice($r,3,3,true) as $d ) {						
								echo $this->_bit_displayStatus($d);
							}
							echo "</ul>";
						
						?>			
					</div>	
				</div>				
		</div>
	</div>
				
