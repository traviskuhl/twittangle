
<div id='yui-main'>

	<h2>Authenticate "<?php echo $app['title']; ?>" </h2>
		
	<div class="yui-gc" style="padding: 10px">
		<div class="yui-u first">
	
			<form method="post" action="http://auth.twittangle.com">
				<input type="hidden" name="do" value="auth">
				<input type="hidden" name="key" value="<?php echo $key; ?>">
				<input type="hidden" name="sig" value="<?php echo $sig; ?>">
				<input type="hidden" name="token" value="<?php echo $this->md5($key.$sig.date("mdY")); ?>">
				
				<div class='box' style="margin: 10px 0; text-align: center">
					<?php echo $app['title']; ?> wants to create a link between their application and your twitTangle.com account.<br>
					<span class='red'>You should only authenticate applications your trust!</span>								
					
					<br><br>
					
					<div align='center'> <button type="submit" style=" font-size: 116%; cursor:pointer; background:#fff; font-weight:bold;padding:5px;">Allow Them Access</button> </div>
					
					<b class="tr"></b>
					<b class="tl"></b>
					<b class="bl"></b>
					<b class="br"></b>
				</div>
				
			</form>			
		
			<br><br>		

			<strong class='b'>Here's how <?php echo $app['title']; ?> describes their app:</strong>
			<div><?php echo $app['info']; ?></div>			
		
		</div>
		<div class="yui-u" style="font-size:94%; color:#555;">
			<strong class='b'>What's the deal here?</strong><br>
			twitTangle encourages outside developers to build apps for our users. In order
			for those apps to work they usually need your data. In order to keep your data safe
			we require your to authorize apps before giving them access to your data.
			<br><br>
			<strong class="b">Want more info</strong><br>
			There's plenty more information available on 
			the <a href='http://twittangle.pbwiki.com/'>API wiki</a>.
			
			<br><br>
			
			<div class='small gray'>
				Your Twitter username and password will never be provided to this application. You can also revoke 
				authorization from this application at any time by visiting: <a class='b' href='/settings'>your settings</a>.
			</div>			
		</div>
	</div>


</div>
