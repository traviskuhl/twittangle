<?php

	// urls
	$urls = array(
		'base' => URI,
		'xhr' => URI . 'xhr/'
	);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title> twitTangle.com - untangle your messy timeline</title>
		<meta http-equiv="Content-Type" content="text/html; charset=uT-8" />
		<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/combo?2.8.0r4/build/reset-fonts-grids/reset-fonts-grids.css"> 
		<link rel="stylesheet" type="text/css" href="<?php echo URI; ?>assets/css/base.css">
		<link rel="stylesheet" type="text/css" href="<?php echo URI; ?>assets/css/panel.css">		
		<script type="text/javascript">
            if ( window.location.href.indexOf('#') != -1 ) {
                window.location = window.location.href.split('#')[1];
            }		
            var T = { 'Load': [], 'Unload': [], 'Store':{}, 'Class': {}, 'Obj': false, 'Env': { 'Urls': <?php echo json_encode($urls); ?> } };
            T.add = function(q,o,id) {
                var qs = {'l':'Load','u':'Unload','s':'Store'};
                var h = T[qs[q]];
                if ( typeof o == 'object' ) {
                    if ( !id ) { id = '_d'; }
                    if ( typeof h[id] == 'undefined' ) { h[id] = {}; }
                    for ( var e in o ) {
                        h[id][e] = o[e];
                    }
                }
                else {
                    h.push(o);
                }
            };
            T.get = function(id,k) {
				if ( !T.Stortags()[id] ) {
					return false;
				}
				else if ( !k ) {
					return T.Store[id];
				}
				else if ( T.Store[id][k] ) {
					return T.Store[id][k];
				}
				else {
					return false;
				}				
            }
            T.execute = function(q) {
                var qs = {'l':'Load','u':'Unload'};       
                var h = T[qs[q]];             
                for ( var e in h ) {
                    h[e].call();
                    delete(h[e]);
                }

            }
		</script>
		<script src="http://yui.yahooapis.com/3.0.0/build/yui/yui-min.js"></script> 
	</head>
	<body class="<?php echo $this->bodyClass; ?>">
		<div id="doc">
			<div id="hd">
				<ul class="menu">
					<li class="home"><a href="<?php echo $this->url('home'); ?>">Home</a></li>
					<li class="lists"><a href="<?php echo $this->url('lists'); ?>">Lists</a></li>
					<li class="networks"><a href="<?php echo $this->url('networks'); ?>">Networks</a></li>
					<li class="search"><a href="<?php echo $this->url('search'); ?>">Search</a></li>
				</ul>
				
				
				<a class="logo" href="/">twittangle.com</span></a>
			</div>
			<div id="bd">
				<?php echo $Body; ?>
			</div>
			<div id="ft">
			
			</div>
		</div>
		<script type="text/javascript" src="<?php echo URI; ?>assets/js/base.js"></script>
		<div id='title-bubble'></div>
		<div id="payload"></div>
	</body>
</html>
