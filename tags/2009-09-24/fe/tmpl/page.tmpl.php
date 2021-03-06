<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>		
		<title> <?php if ( $this->title ) { echo strip_tags($this->title) . ' on '; } ?> twitTangle | untangling your messy timeline </title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">			
		<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/combo?2.6.0/build/reset-fonts-grids/reset-fonts-grids.css&2.6.0/build/container/assets/skins/sam/container.css&2.6.0/build/slider/assets/skins/sam/slider.css&2.7.0/build/autocomplete/assets/skins/sam/autocomplete.css">
		<link rel="stylesheet" href="<?php echo $this->asset('css','global'); ?>" type="text/css">
		<link href="http://assets.ms-cdn.com/static/tangle/images/v4/favicon.ico" rel="shortcut icon">

		<script type="text/javascript">
			if ( window.location.hash && window.location.hash.indexOf('p=') != -1 ) {
				window.location.href = "<?php echo URI; ?>" + window.location.hash.replace(/\#p=/,'');
			}	
			var TT = { 'queue': [], 'unLoadQueue': [], 'data': { 'preLinks': [] }, 'Global': {} };
			TT.addToQueue = function(fn) {
				TT.queue[TT.queue.length] = fn;
			}
			TT.executeQueue = function() { 
				for ( var i in TT.queue ) {
					TT.queue[i].call();
					delete(TT.queue[i]);
				}
			}
			TT.addToUnLoadQueue = function(fn) {
				TT.unLoadQueue[TT.unLoadQueue.length] = fn;
			}
			TT.executeUnLoadQueue = function() {
				for ( var i in TT.unLoadQueue ) {
					TT.unLoadQueue[i].call();
					delete(TT.unLoadQueue[i]);
				}			
			}							
		</script>
		
		<style id='customStyle' type="text/css">
			<?php echo $this->profileCss; ?>
		</style>
	
		<?php
			if ( isset($this->extraHead) ) {
				echo $this->extraHead;
			}
		?>
	
	</head>
	<body class='<?php echo $this->bodyClass; ?> yui-skin-sam'>	
		<div id="title-bubble" style="left: -999px; top: -999px;"><span></span></div>	
		<div id="doc4">
			<div id="hd">
				<div id="strip" class="strip">
					<em>Beta v3.3</em> &nbsp; <a class="b" href='#' onclick="window.open('http://code.google.com/p/twittangle/issues/list');">Report a Bug</a>
				
					<?php
						if ( getenv("TT_BETA") == 'true' ) {
							echo '&nbsp; <span style="font-style:italic;">This beta build may be unstable</span>';
						}
						else {
							echo "<div style='text-align: center; position: relative; top: -15px; margin-bottom: -15px;'> <sup class='b' style='color:yellow'>NEW</sup><a href='http://beta.twittangle.com'>Check out Beta 4.0</a></div>";
						}
					?>
				
					
					<div class="right">
						<a href="/logout">Logout</a>
					</div>
				</div>
				<div class="wrap">
					<div class="cnt">
						<a class="logo" href="/">twittangle.com</a>
								
						<?php if ( $this->loged ) { ?>										
						<form class="update" id="update-status" name="<?php echo time(); ?>">
						<input type="hidden" id="update-status-reply">
						<input type="hidden" id="update-status-network">				
						
							<div class="user">
								<a href="#">
									<?php echo "<img align='middle' class='pic' src='".$this->getUserPic($this->user['info']['profile_image_url'])."'>"; ?>
									<em><?php echo $this->user['user']; ?></em>
								</a>
							</div>						
						
							<fieldset>
								<div id="update-status-title">What are you doing?</div>
								<div class="act">
									<a class="update-status-dm bubble" title="Send Direct Message" href='#dm'>DM</a> |
									<a class="update-status-upload bubble" title="Upload a Photo" href="#upload">Upload Photo</a> | 
									<a class="update-status-tiny bubble" title="Make a Tiny Link" href="#tiny">Tiny Link</a>
								</div>
								<div id="update-status-cur" class='current'><?php echo $this->getLatestStatus(); ?></div>
								<textarea onfocus="this.className='focus';$d.addClass('update-status','focused');" onblur="this.className='';" id="update-status-txt"></textarea>
								<div id="update-status-chars" max="140">140</div>
								<a class="update-status-btn" href="#">Update</a>
								
							</fieldset>																
						</form>
						
						<div id="update-status-o-photo">
							<form id="update-status-photo-frm" enctype="multipart/form-data" method="post" action="/xhr/upload" target="postback" onsubmit="TT.Global.uploadPhotoStart(this);">
								<input type="hidden" name="req" value="<?php echo $this->req; ?>">
								<input id="update-status-photo-file" type="file" name="file"> <button type="submit">Upload</button>
								<div>JPEG, GIF or PNG less than 4mb. Powered by <a href='http://twitpic.com'>TwitPic</a></div>
							</form>
						</div>				
						<div id="update-status-o-tiny">
							<form id="update-status-tiny-frm">
								<input type="text" id="update-status-tiny-txt"> <button class="update-status-tiny-btn">Tiny!</button>
								<div>One URL per line. Powered by <a href='http://tinyurl.com'>TinyURL</a></div>
							</form>
						</div>						
						<?php } ?>						
						
						<ul class="menu">
							<li class="first tab-home"><a href="<?php echo $this->url('home'); ?>">Home</a><b></b></li>
				<?php /*	<li class="tab-directory"><a href="<?php echo $this->url('dir'); ?>">Directory</a><b></b></li> */ ?>
							<li class="tab-networks"><a href="<?php echo $this->url('networks'); ?>">Networks</a><b></b></li>	
							<li class="tab-search"><a href="<?php echo $this->url('search'); ?>">Search</a><b></b></li>
							
							<?php if ( $this->loged ) { ?>
								<li class="tab-groups"><a href="<?php echo $this->url('my-groups'); ?>">My Groups</a><b></b></li>
								<li class="tab-friends"><a href="<?php echo $this->url('my-friends'); ?>">My Friends</a><b></b></li>
							<?php } ?>
						</ul>							
					</div>
				</div>
				<form class="search" action="/search" id="search" name="<?php echo time(); ?>">
					<fieldset class="cnt">
						<legend>Search</legend>
						
						<?php $def = ( $this->bodyClass == 'search' ? $this->param('q','Search',$_REQUEST,'search') : 'Timeline Search' ); ?>
						
						<input id='search-txt' type="text" name="q" value="<?php echo $def; ?>" onfocus="if(this.value=='<?php echo $def; ?>'){this.value='';}" onblur="if(this.value==''){this.value='<?php echo $def; ?>';}"  autocomplete="off">
						<a href='#' class="search-btn"></a>
						<a class="search-btn" href='#'></a>
						<button type="submit">Go</button>
						<a id='saved-count-wrap' class='saved' href='/search'>
							<?php if ( $this->loged ) { ?>
							<span id='saved'> <b id='saved-count'></b> <b class="tr"></b><b class="tl"></b><b class="bl"></b><b class="br"></b></span>						
							<em>Saved</em> 
							<?php } ?>
						</a>
						<?php
							
							// loged in?
							if ( $this->loged AND $this->bodyClass != 'search' ) {
							
								// names
								$name1 = $name2 = $name3 = "friend";
							
								// rand
								if ( count($this->allFriends) > 0 ) {
									
									// keys
									$keys = array_rand($this->allFriends,3);
									
									$name1 = $this->allFriends[$keys[0]]['sn'];
									$name2 = $this->allFriends[$keys[1]]['sn'];
									$name3 = $this->allFriends[$keys[2]]['sn'];
									
								}
								
								// examples
								$examples = array( "to:me", "@me", "from:{$name1}", "has:pic", "@{$name2}", "from:{$name3} to:me" );
						
						?>						
						<div id="saved-examples">
							<em>Example: </em>
							<?php
								foreach ($examples as $e) {
									echo "<a onclick=\" TT.Global.showSearchExample('{$e}'); \" href='#'>{$e}</a>";
								}
							?>
						</div>
						<?php } ?>
						<div id="saved-list" class="saved wrap">
							<ul id="save-list">
								<?php
									foreach ( $this->savedSearches as $k => $q ) {
										echo "
											<li id='saved-search-{$k}'>
												<a href='/search?q=".urlencode($q['q']).";id={$k}'>
													{$q['q']}
													<span id='saved-{$k}'> 
														<b id='saved-count-{$k}'></b> 
														<b class='tr'></b><b class='tl'></b><b class='bl'></b><b class='br'></b>
													</span>				
												</a>									
											</li>
										";
									}
								?>
							</ul>
						</div>
						<div id="search-result">
							<div class='yui-gc'>
								<div class='yui-u first'>
									<em class="title">Your Timeline</em>
									<ul id="search-timeline" class='timeline'></ul>
								</div>
								<div class='yui-u'>
									<div class='twitter' id='search-twitter'>
										<em class="title">All Twitter</em>
									</div>
								</div>
							</div>						
						</div>												
					</fieldset>					
				</form>
			</div>
			<div id="bd">
				<div id="page-content"><?php echo $body; ?></div>				
				<div id='user-panel' class="wrap">
					<div class="wrap-hd">
						<b class="l"></b>
						<b class="r"></b>
					</div>
					<div class="wrap-bd">
						<b class="cnt"></b>
						<div class="cnt user-panel-content" id="user-panel-content"></div>
					</div>
					<div class="wrap-ft">
						<b class="l"></b>					
						<b class="r"></b>
					</div>
				</div>
				<div id='dropover'></div>				
			</div>
			<div id="ft">
				<div>
					&copy; Copyright 2008 - All Rights Reserved - 
					<a href='mailto:info@twittangle.com'>Contact Us</a> - 
					<a target="_blank" href='http://groups.google.com/group/twittangle'>Support</a> -
					<a href='http://twittangle.com/terms'>Privacy &amp; Terms of Service</a> - 
					<a href='http://blog.twittangle.com'>Blog</a>
				</div>
				
				<p class='powered'>
					Powered by <a href='http://twitter.com'>Twitter</a> &amp; 
					<a onclick=" try{ pageTracker._trackPageview('out/twitter-travis'); }cache(e){}; " href='http://twitter.com/traviskuhl'>@traviskuhl</a>
				</p>				
			</div>

		</div>
	
		<div id='payload'></div>

		<script type="text/javascript" src="<?php echo $this->asset('js','global'); ?>;a=true"></script>
		<script type="text/javascript">
		
			// user agenet
			TT.data.ua = [];

			// check for browser 			
			if ( /opera/.test(navigator.userAgent.toLowerCase()) ) { TT.data.ua.push('opera'); $d.addClass(document.body,'opera'); }
			if ( /firefox/.test(navigator.userAgent.toLowerCase()) ) { TT.data.ua.push('firefox'); $d.addClass(document.body,'firefox'); }	
			if ( /chrome/.test(navigator.userAgent.toLowerCase()) ){ TT.data.ua.push('chrome'); $d.addClass(document.body,'chrome'); }
			if ( /safari/.test(navigator.userAgent.toLowerCase()) ) { TT.data.ua.push('safari'); $d.addClass(document.body,'safari'); } 		
			if ( /msie/.test(navigator.userAgent.toLowerCase()) ) { TT.data.ua.push('ie'); $d.addClass(document.body,'ie'); }
					

			TT.Global = new $Y.twitTangle.Global({
					'loged': <?php echo $this->loged; ?>,
					'id': <?php echo $this->uid; ?>,
					'req': '<?php echo $this->req; ?>',
					'domain': '<?php echo "www".DOMAIN; ?>',
					'search': <?php echo json_encode($this->savedSearches); ?>
				});
				
		</script>
		<script type="text/javascript">
			var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
			document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">
			try {
			
				// setup page tracer
				var pageTracker = _gat._getTracker("UA-123654-3");
				
				<?php
					if ( $this->loged ) {
						echo "pageTracker._setVar('track: {$this->uid}');";
					}
				?>
				
				// track
				pageTracker._trackPageview();
				
				<?php
					
					/* check for a referer */			 
					$ref = preg_replace('/[^a-zA-Z0-9\.\/\?\&\#]/i','',@$_SERVER['HTTP_REFERER']);
					if ( $ref AND strpos($ref,'twittangle.com') === false ) {
						echo "pageTracker._trackPageview('referer/{$ref}')";
					}
					
					/* check for a src */
					$src = $this->param('_src',false,null,'alphanum');
					if ( $src ) {
						echo "pageTracker._trackPageview('source/{$src}')";
					}
					
					// beta
					if ( $this->beta ) {
						echo "pageTracker._setVar('isBeta: true');";
					}
					
				?>						
				
			} catch(err) {}
		</script>
		<iframe id="postback" name="postback" class="hide"></iframe>			
	
	</body>
</html>
<!-- <?php echo date('r') . " - " . $_SERVER['SERVER_NAME']; ?> -->