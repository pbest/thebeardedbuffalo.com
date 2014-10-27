<?php
/*!
* WordPress Social Login
*
* http://hybridauth.sourceforge.net/wsl/index.html | http://github.com/hybridauth/WordPress-Social-Login
*    (c) 2011-2014 Mohamed Mrassi and contributors | http://wordpress.org/extend/plugins/wordpress-social-login/
*/

/**
* BuddyPress integration.
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit; 

// --------------------------------------------------------------------

function wsl_component_buddypress_notfound()
{
	// HOOKABLE:
	do_action( "wsl_component_buddypress_notfound_start" );
?> 
<style>
#wsl_div_warn { 
	padding: 10px;  
	border: 1px solid #ddd; 
	background-color: #fff; 
	
	width: 55%;
	margin: 0px auto;
	margin-top:30px;
}
</style>
<div id="wsl_div_warn">
	<h3 style="margin:0px;"><?php _wsl_e("BuddyPress plugin not found!", 'wordpress-social-login') ?></h3> 

	<hr />

	<p>
		<?php _wsl_e('<a href="https://buddypress.org/" target="_blank">BuddyPress</a> was not found on your website. The plugin is be either not installed or disabled', 'wordpress-social-login') ?> .
	</p>

	<p>
		<?php _wsl_e("If you believe you've found a problem with <b>WordPress Social Login</b>, be sure to let us know so we can fix it", 'wordpress-social-login') ?>.
	</p>

	<hr />

	<div>
		<a class="button-secondary" href="http://hybridauth.sourceforge.net/wsl/support.html" target="_blank"><?php _wsl_e( "Report as bug", 'wordpress-social-login' ) ?></a>
		<a class="button-primary" href="options-general.php?page=wordpress-social-login&wslp=components" style="float:right"><?php _wsl_e( "Check enabled components", 'wordpress-social-login' ) ?></a>
	</div> 
</div>
<?php
	// HOOKABLE: 
	do_action( "wsl_component_buddypress_notfound_end" );
}

// --------------------------------------------------------------------	
