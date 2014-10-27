<section class="offCanvasPanel" id="userActions">
 
<?php if (!(current_user_can('level_0'))){ ?>
<h4>Login to save your favorite tracks</h4>
		   <hr>
		   <div class="panel-container login-form-wrapper">
<form action="<?php echo get_option('home'); ?>/wp-login.php" method="post">
<input type="text" name="log" id="log" placeholder="Username/Email" value="<?php echo wp_specialchars(stripslashes($user_login), 1) ?>" size="20" /><br><br>
<input type="password" name="pwd" id="pwd" placeholder="Password" size="20" /><br>
<div class="pull-left login-option">
	<label for="rememberme"><input name="rememberme" id="rememberme" type="checkbox" checked="checked" value="forever" /> Remember me</label>
</div>
<div class="pull-right login-option">
	<a href="<?php echo get_option('home'); ?>/wp-login.php?action=lostpassword">Forgot Password?</a>
</div>
<input type="submit" name="submit" value="Login" class="button login-button" /><br>
<a href="#">Sign up for an account</a>
    <p>
       
       <input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
    </p>
</form>
<?php do_action( 'wordpress_social_login' ); ?> 
<p>We never post on your accounts or share any of your information.</p>
<?php } else { 
	  $current_user = wp_get_current_user();
?>
<h4>Welcome <? echo $current_user->user_firstname; ?></h4>
		   <hr>
		   <div class="panel-container">
<a href="<?php echo wp_logout_url(urlencode($_SERVER['REQUEST_URI'])); ?>">logout</a><br />
<a href="http://XXX/wp-admin/">admin</a>
<?php }?>
 
</section>