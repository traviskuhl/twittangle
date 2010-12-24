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
					<em>Beta v4.0</em> &nbsp; <a class="b" href='#' onclick="window.open('http://code.google.com/p/twittangle/issues/list');">Report a Bug</a>
				
					<?php
						if ( getenv("TT_BETA") == 'true' ) {
							echo '&nbsp; <span style="font-style:italic;">This beta build may be unstable</span>';
						}
					?>
					
					
					<?php if ($this->loged) { ?>
					<div class="right">
						<?php echo "Hello ".$this->user['user']; ?> -
						<a href="/logout">Logout</a>
					</div>
					<?php } ?>
				</div>
				<div class="wrap">
					<div class="cnt">
						<a class="logo" href="/">twittangle.com</a>													
						
						<form class="search" action="/search" id="search" name="<?php echo time(); ?>">
							<fieldset>
								<legend>Search</legend>								
								<?php  $def = ($this->loged?'Timeline Search':'Search'); ?>
								<input id='search-txt' type="text" name="q" value="<?php echo $def; ?>" onfocus="if(this.value=='<?php echo $def; ?>'){this.value='';}" onblur="if(this.value==''){this.value='<?php echo $def; ?>';}"  autocomplete="off">
								<a href='#' class="search-btn"></a>
								<a class="search-btn" href='#'></a>
								<button type="submit">Go</button>
							</fieldset>
						</form>
						
						
						<ul class="menu">
							<li class="first tab-home"><a href="<?php echo $this->url('home'); ?>">Home</a><b></b></li>				
							<li class="tab-pub-groups"><a href="<?php echo $this->url('groups'); ?>">Groups</a><b></b></li>														
				<?php /*	<li class="tab-directory"><a href="<?php echo $this->url('dir'); ?>">Directory</a><b></b></li> */ ?>
							<li class="tab-networks"><a href="<?php echo $this->url('networks'); ?>">Networks</a><b></b></li>	
							<li class="tab-search"><a href="<?php echo $this->url('search'); ?>">Search</a><b></b></li>
							
							<?php if ( $this->loged ) { ?>
							<li class="tab-column"><a href="<?php echo $this->url('columns'); ?>">My Columns</a><b></b></li>							
								<li class="tab-groups"><a href="<?php echo $this->url('my-groups'); ?>">My Groups</a><b></b></li>
								<li class="tab-friends"><a href="<?php echo $this->url('my-friends'); ?>">My Friends</a><b></b></li>
							<?php } ?>
						</ul>							
					</div>
					<div id="search-result">
						<div class='yui-gc cnt'>
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
				</div>				
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