<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.1//EN"
  "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title>m.twittangle.com</title>
		<link href="http://assets.ms-cdn.com/static/tangle/images/favicon.ico" rel="shortcut icon">		
		<style type="text/css">
			body { margin: 0; padding: 0; font-family: 'Lucida Grande',sans-serif; }
			div#hd { background: #FFA43E; height: 50px; font-size: 13px; }
			div#hd img { margin: 8px 0 0 5px; }
			div#hd table td a { color: #fff; }
			div#bd { padding: 10px; }
			img { border: none; }
			table { margin: 0; padding: 0; }
			.error { color: red; }
			h1 { margin-top: 0;}
			ol, ol li, ul, ul li { padding: 0; margin: 0; list-style: none; }
			.timeline { border-top: solid 1px #eee;  }
			.timeline .hd { display: none; }
			.timeline .bd { color: #333; }
			.timeline .bd a { color: #333; text-decoration: none; font-weight: bold; }
			.timeline .bd ul { display: none; }
			.timeline .ft { font-size: 11px; color: #888; }
			.timeline .ft a { color: #888; }
			.timeline .ft span.usr { display: none; } 
			.timeline li { border-bottom: solid 1px #eee; padding: 5px; }
			.nav li { color: #666; text-align: right; font-size: 12px; padding: 3px; }
			.nav li a { color: #666; }
			table.menu { margin: 5px 0;  }			
			table.menu td { background: #eee; text-align: center; font-size: 12px; padding: 3px; }			
			table.menu td a { color: #333; text-decoration: none; }
			table.menu td.on { background: #fff; font-weight: bold; }
			ol.groups { border-top: solid 1px #eee; margin-top: 10px; }
			ol.groups li { border-bottom: solid 1px #eee; padding: 5px; font-size: 10px; color: #888; }
			ol.groups li a { color: #333; font-weight: bold; text-decoration: none; font-size: 13px; }
			a.back { font-size: 11px; color: #888; }
			div.tags { font-size: 12px; color: #555;}
			div.tags a { color: #555; }
			#ft { margin-top: 20px; border-top: solid 1px #ccc; font-size: 12px; color: #555; text-align: center; padding: 5px; }
			#ft a { color: #555; }
		</style>
	</head>
	<body>
		<div id='hd'>
			<table cellspacing="0" cellpadding="0" width="98%" >
				<tr>
					<td width="60%"><a href='http://m.twittangle.com'><img src='http://assets.ms-cdn.com/static/tangle/images/mobile-logo.gif'/></a></td>
					<td width="40%" align="right">
						<?php
							if ( $this->loged ) {
								echo "
									Hello <strong>".$this->user['user']."</strong><br>
									<a href='/groups'>Groups</a> &nbsp;																
									<a href='/logout'>Logout</a> 
								";
							}
						?>					
					</td>
				</tr>
			</table>
		</div>
		<div id='bd'>
			<?php echo $body; ?>
		</div>
		<div id="ft">
			&copy; Copyright twitTangle 2008 | <a href="http://twittangle.com">Full Site</a>
		</div>
	</body>
</html>