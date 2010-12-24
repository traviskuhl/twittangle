
<h1>Login</h1>
<form method='post' action='<?php echo $this->url('login'); ?>'>
<input type="hidden" name="do" value="submit">
<input type="hidden" name="mobile" value="true">

<?php
	if ( $this->param('invalid') == 'true' ) {
		echo "<p class='error'>Twitter told us your Account Info was invalid. Try again.</p>";
	}
?>

<p>Use your <a href='http://m.twitter.com'>Twitter</a> account to login. Don't have a Twitter account... <a href='https://twitter.com/signup'>get one now</a></p>

<table>
	<tr>
		<th align="right">Username or Email Address</th>
		<td><input type='text' name='u'></td>
	</tr>
	<tr>		
		<th align="right">Password</th>
		<td><input type="password" name="p"></td>
	</tr>
	<tr>
		<th align="right">Remember Me</th>
		<td><input type="checkbox" name="r" value='1'></td>
	</tr>
	<tr>			
		<td colspan='2' align="center"><button type="submit" onclick=" this.innerHTML='Please Wait...'; return true; this.disabled=true; ">Login</button></td>	
	</tr>
</table>
</form>
