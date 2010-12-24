<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>		
		<title> <?php if ( $this->title ) { echo $this->title . ' on '; } ?> twitTangle | untangling the mess of too many friends </title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">			
		<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/combo?2.6.0/build/reset-fonts-grids/reset-fonts-grids.css&2.6.0/build/container/assets/skins/sam/container.css&2.6.0/build/slider/assets/skins/sam/slider.css&2.7.0/build/autocomplete/assets/skins/sam/autocomplete.css">
		<link rel="stylesheet" href="<?php echo $this->asset('css','global'); ?>" type="text/css">
		<link href="http://assets.ms-cdn.com/static/tangle/images/favicon.ico" rel="shortcut icon">

		<script type="text/javascript">
			if ( window.location.hash && window.location.hash.indexOf('p=') != -1 ) {
				window.location.href = "http://www.twittangle.com/" + window.location.hash.replace(/\#p=/,'');
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
	
	</head>
	<body class='<?php echo $this->bodyClass; ?> yui-skin-sam'>	

		
			<div id="strip">
				<p> 
		<?php /*			<b>BETA</b> &nbsp; 
					<a href='mailto:info@twittangle.com'>Report a bug</a> or <a href="http://twitter.com/twittangle">@twitTangle</a>
					<em class="red hide">Twitter is responding with capacity issues, limiting the use of some parts of twitTangle </em> */ ?>
					<em>  <a style="color: #1E4F97; font-weight: bold; text-decoration: none" href='http://beta.twittangle.com?.src=farnorth'>Try twitTangle Beta &nbsp; <i style="font-weight:normal;color:#888;">tons of new features &amp; improvements</i> </a></em>
				</p>
				
				<?php if ( $this->loged ) { ?>
				<div>
					<em class='b accounts-top'>					
						<?php echo  $this->user['user'] . " <img align='middle' class='pic accounts-top' src='".$this->getMiniPic($this->user['info']['profile_image_url'])."'>"; ?> 
						<img align="middle" src="<?php echo BLANK; ?>">						
					</em>
				</div>
				<?php } ?>
				<span></span>
			</div>
		
		<div id="doc4">
			<div id="hd">
				<h1><a href="/">twitTangle.com</a></h1>
				
				<ul class="menu">
					<li class="first tab-home"><a href="<?php echo $this->url('home'); ?>">Home</a><b></b></li>
					<li class="tab-networks"><a href="<?php echo $this->url('networks'); ?>">Networks</a><b></b></li>
					<li class="tab-search"><a href="<?php echo $this->url('search'); ?>">Search</a><b></b></li>
					
					<?php if ( $this->loged ) { ?>
						<li class="tab-groups"><a href="<?php echo $this->url('my-groups'); ?>">My Groups</a><b></b></li>
						<li class="tab-friends"><a href="<?php echo $this->url('my-friends'); ?>">My Friends</a><b></b></li>
					<?php } ?>
				</ul>				
				
				<?php if ( $this->loged ) { ?>
				<form class="update" id="update-status" name="<?php echo time(); ?>">
				<input type="hidden" id="update-status-reply">
				<input type="hidden" id="update-status-network">				
					<fieldset>
						<legend id="update-status-title">What are you doing?</legend>
						<div class="act">
							<a class="update-status-upload" href="#upload">Upload Photo</a> | 
							<a class="update-status-tiny" href="#tiny">Tiny Link</a>
						</div>
						<div id="update-status-cur" class='current'><?php echo $this->getLatestStatus(); ?></div>
						<textarea onfocus="this.className='focus';" onblur="this.className='';" id="update-status-txt"></textarea>
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

				<?php if ( $this->loged ) { ?>				
				<form class="search" action="/search" id="search" name="<?php echo time(); ?>">
					<fieldset>
						<legend>Search</legend>
						<input id='search-txt' type="text" name="q" value="Search" onfocus="if(this.value=='Search'){this.value='';}" onblur="if(this.value==''){this.value='Search';}"  autocomplete="off">
						<button type="submit">Go</button>						
						<a id='saved-count-wrap' class='saved' href='/search'> <em>Saved</em> <span id='saved'> <b id='saved-count'></b> <b class="tr"></b><b class="tl"></b><b class="bl"></b><b class="br"></b></span></a>
					</fieldset>
					<b class="end"></b>
			
						<div id="saved-list" class="saved wrap">
							<div class="wrap-bd">
								<div class="cnt">
									<div id='search-other'></div>
									<h3>Saved</h3>
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
								<b class="cnt"></b>
							</div>
							<div class="wrap-ft"><b class="l"></b><b class="r"></b></div>
						</div>					
				</form>
				<?php } ?>				
				
			</div>
			<div id="bd">
				<div id="wrap" class="wrap">
					<div class="wrap-hd">
						<b class="l"></b>
						<b class="r"></b>
					</div>
					<div class="wrap-bd">
						<b class="cnt"></b>
						<div class="cnt" id="page-content">
							<?php echo $body; ?>
						</div>
					</div>
					<div class="wrap-ft">
						<b class="l"></b>					
						<b class="r"></b>
					</div>
				</div>			
				
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
			</div>
			<div id="ft">
				<div>
					&copy; Copyright 2008 - All Rights Reserved - 
					<a href='mailto:info@twittangle.com'>Contact Us</a> - 
					<a target="_blank" href='http://twittangle.uservoice.com/'>Feedback</a> -
					<a href='http://twittangle.com/terms'>Privacy &amp; Terms of Service</a> - 
					<a href='http://blog.twittangle.com'>Blog</a>
				</div>
				
				<p class='powered'>
					Powered by <a href='http://twitter.com'>Twitter</a> &amp; 
					<a onclick=" try{ pageTracker._trackPageview('out/twitter-travis'); }cache(e){}; " href='http://twitter.com/traviskuhl'>@traviskuhl</a>
				</p>				
			</div>
				
			<?php if ( $this->loged ) { ?>
			<div id='accounts' class="wrap accounts-wrap">
				<div class="wrap-bd">
					<b class="cnt"></b>
					<div class="cnt" id="">
						
						<h4>Your Accounts</h4>
						<ul class='accounts cf'>
							<?php						
			
								$act = $this->accounts[$this->uid];
														
								// token
								$token = $this->md5($aid.$act['sid']);							
							
								// print 
								echo "
									<li class='on'>
										<img align='center' src='".$this->getMiniPic($act['pic'])."'>
										<a class='ignore' href='/login/switch/{$aid}?token={$token}'>{$act['id']}</a>
										<div>								
											<a class='small ignore first' href='/logout/{$aid}?token={$token}'>Logout</a>
										</div>
									</li>
								";							
							
								foreach ( $this->accounts as $aid => $act ) {
									if ( $aid != $this->uid ) {
									
										// token
										$token = $this->md5($aid.$act['sid']);
									
										// print 
										echo "
											<li class='".($this->uid==$aid?'on':'')."'>
												<img align='center' src='".$this->getMiniPic($act['pic'])."'>
												<a class='ignore' href='/login/switch/{$aid}?token={$token}'>{$act['id']}</a>
												<div>								
													<a class='small ignore first' href='/logout/{$aid}?token={$token}'>Logout</a>
												</div>
											</li>
										";
									}
								}
							?>
						</ul>
						
						<h4>Add Accounts</h4>						
						<form method="post" class="box" action="/login">
						<input type="hidden" name="do" value="submit">
						<input type="hidden" name="type" value="switch">
						<input type="hidden" name="token" value="<?php echo $this->md5('account-swtich-'.$this->req); ?>">
							<ul>
								<li>
									<label>Username/Email Address</label>
									<input type="text" name="u">
								</li>
								<li>
									<label>Password</label>
									<input type="password" name="p">
								</li>
								<li>
									<button type="submit">Login</button>
								</li>
							</ul>
													
							<b class="tr"></b>
							<b class="tl"></b>
							<b class="br"></b>
							<b class="bl"></b>
						</form>
					
					</div>
				</div>
				<div class="wrap-ft">
					<b class="l"></b>					
					<b class="r"></b>
				</div>
			</div>				
			<?php } ?>
	
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
					
				?>						
				
			} catch(err) {}
		</script>
		<iframe id="postback" name="postback" class="hide"></iframe>			
	
	</body>
</html>
<!-- <?php echo date('r') . " - " . $_SERVER['SERVER_NAME']; ?> -->