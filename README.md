# Appi
https://nicomorenof.github.io/Appi/
<h1>Instagram Basic Display API</h1>
<hr />
<?php if ($ig->hasUserAccessToken ) :?>
	<h4>IG Info</h4>
	<?php $user = $ig->getUser(); ?>
	<pre>
		<php print_r( $user ); ? >
	</pre>
	<h1>Username: <?php echo $user['username']; ?></h1>
	<h2>IG ID: <?php echo $user['id']; ?></h2>
	<h3>Media Count: <?php echo $user['media_count']; ?></h3>
	<h4>Account Type: <?php echo $user['account_type']; ?></h4>
<?php else : ?> 
	<a href="<?php echo $ig->authorizationUrl; ?>">
		Authorize w/Instagram
	</a>
