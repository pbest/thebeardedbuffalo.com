<?php
/*!
* WordPress Social Login
*
* http://hybridauth.sourceforge.net/wsl/index.html | http://github.com/hybridauth/WordPress-Social-Login
*    (c) 2011-2014 Mohamed Mrassi and contributors | http://wordpress.org/extend/plugins/wordpress-social-login/
*/

/**
* Authenticate users via social networks. 
*
** 
* Side note: I don't usually over-comment codes, but this is the main WSL script and I had to since
* many users with diffrent "skill levels" may want to understand how this piece of code works.
** 
* To sum things up, here is how WSL works (bit hard to explain, so bare with me):
*
*   [Widget Icons]                           A wild visitor appear and click on one of widget icons. Obviously he will redirected to wp-login.php (with specific args in the url: &action=wordpress_social_authenticate&provider=..)
*       => [wp-login.php]                    wp-login.php will first call wsl_process_login() which will attempt to authenticate the user through hybridauth library
*           => [Hybridauth] <=> [Provider]   Hybridauth will redirect the user to Provider API and ask for the user authorisation
*               => [Provider]                If the visitor consent and agrees to give your website a premission to access his priveate data, then the provider will then redirect the user to back wp-login.php
*                   => [wp-login.php]        wp-login.php will call wsl_process_login() again, which will attempt to grab the user profile form the provide api and identidy him. if he doesn't exist in database we create a new user
*                       => [callback URL]    If things goes as expected, the wsl_process_login will log the user into the website and redirect him to where he come from (or Redirect URL).
* 
* Ex:
* http://hybridauth.sourceforge.net/wsl/img/wsl_redirections.png
** 
* Functions execution order is the following:
*
*     wsl_process_login()
*     .    wsl_process_login_begin()
*     .    .    wsl_render_redirect_to_provider_loading_screen()
*     .    . 
*     .    .    Hybrid_Auth::authenticate()
*     .    .    .    wsl_process_login_render_error_page()
*     .    .
*     .    .    wsl_render_return_from_provider_loading_screen()
*     .
*     .    wsl_process_login_end()
*     .    .    wsl_process_login_end_get_user_data()
*     .    .    .    Hybrid_Auth::isConnectedWith()
*     .    .    .    .    wsl_process_login_render_error_page()
*     .    .    .
*     .    .    .    wsl_process_login_complete_registration()
*     .    .
*     .    .    wsl_process_login_create_wp_user()
*     .    .    wsl_process_login_update_wsl_user_data()
*     .    .    wsl_process_login_authenticate_wp_user() 
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// --------------------------------------------------------------------

/**
* Entry point to the authentication process
*
* This function runs after WordPress has finished loading but before any headers are sent.
* This function will analyse the current URL parameters and start the login process whenever an
* WSL action is found: $_REQUEST['action'] eq wordpress_social_*
* 
* Example of valid origin url:
*    wp-login.php
*       ?action=wordpress_social_authenticate                        // current step
*       &provider=Twitter                                            // selected provider
*       &redirect_to=http%3A%2F%2Fexample.com%2Fwordpress%2F%3Fp%3D1 // where the user come from
*
* Ref: http://codex.wordpress.org/Plugin_API/Action_Reference/init
*/
function wsl_process_login()
{
	// > action should be either 'wordpress_social_authenticate', 'wordpress_social_profile_completion' or 'wordpress_social_authenticated'
	$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : null;

	if( ! in_array( $action, array( "wordpress_social_authenticate", "wordpress_social_profile_completion", "wordpress_social_authenticated" ) ) ){
		return false;
	}

	// user already logged in?
	if( is_user_logged_in() ){
		global $current_user;

		get_currentuserinfo();

		return wsl_process_login_render_notice_page( sprintf( _wsl__( "You are already logged in as <b>%s</b>.", 'wordpress-social-login' ), $current_user->display_name ) );
	}

	// Bouncer :: Allow authentication?
	if( get_option( 'wsl_settings_bouncer_authentication_enabled' ) == 2 ){
		return wsl_process_login_render_notice_page( _wsl__( "Authentication through social networks is currently disabled.", 'wordpress-social-login' ) );
	}

	// HOOKABLE:
	do_action( "wsl_process_login_start" );

	// if action=wordpress_social_authenticate
	// > start the first part of authentication (redirect the user to the selected provider)
	if( $action == "wordpress_social_authenticate" ){
		return wsl_process_login_begin();
	}

	// if action=wordpress_social_authenticated or action=wordpress_social_profile_completion
	// > finish the authentication process (create new user if doesn't exist in database, then log him in within wordpress)
	wsl_process_login_end();
}

add_action( 'init', 'wsl_process_login' );

// --------------------------------------------------------------------

/**
* Start the first part of authentication
* 
* Steps:
*     1. Display a loading screen while hybridauth is redirecting the user to the selected provider
*     2. Build the hybridauth config for the selected provider (keys, scope, etc) 
*     3. Instantiate the class Hybrid_Auth and redirect the user to provider to ask for authorisation for this website
*     4. Display a loading screen after user come back from provider as we redirect the user back to Widget::Redirect URL
*/
function wsl_process_login_begin()
{
	// HOOKABLE:
	do_action( "wsl_process_login_begin_start" );

	$config     = null;
	$hybridauth = null;
	$provider   = null;
	$adapter    = null; 

	/* 1. Display a loading screen while hybridauth is redirecting the user to the selected provider */

	// the loading screen should reflesh it self with a new arg in url: &redirect_to_provider=ture
	if( ! isset( $_REQUEST["redirect_to_provider"] ) ){
		$_SESSION["HA::STORE"] = ARRAY();

		return wsl_render_redirect_to_provider_loading_screen( wsl_process_login_get_selected_provider() );
	}

	// if user come from loading screen (&redirect_to_provider=)
	// > check for required args and display an error if any is missing
	if( ! isset( $_REQUEST['provider'] ) || ! isset( $_REQUEST['redirect_to_provider'] ) ){
		return wsl_process_login_render_notice_page( _wsl__( 'Bouncer says this makes no sense.', 'wordpress-social-login' ) ); 
	}

	/*  2. Build the hybridauth config for the selected provider (keys, scope, etc) */

	// selected provider name
	$provider = wsl_process_login_get_selected_provider();

	// provider enabled?
	if( ! get_option( 'wsl_settings_' . $provider . '_enabled' ) ){
		return wsl_process_login_render_notice_page( _wsl__( "Unknown or disabled provider.", 'wordpress-social-login' ) );
	}

	// build required configuration for this provider
	$config = array();
	$config["base_url"] = WORDPRESS_SOCIAL_LOGIN_HYBRIDAUTH_ENDPOINT_URL; 
	$config["providers"] = array();
	$config["providers"][$provider] = array();
	$config["providers"][$provider]["enabled"] = true;
	$config["providers"][$provider]["keys"] = array( 'id' => null, 'key' => null, 'secret' => null );

	// provider application id ?
	if( get_option( 'wsl_settings_' . $provider . '_app_id' ) ){
		$config["providers"][$provider]["keys"]["id"] = get_option( 'wsl_settings_' . $provider . '_app_id' );
	}

	// provider application key ?
	if( get_option( 'wsl_settings_' . $provider . '_app_key' ) ){
		$config["providers"][$provider]["keys"]["key"] = get_option( 'wsl_settings_' . $provider . '_app_key' );
	}

	// provider application secret ?
	if( get_option( 'wsl_settings_' . $provider . '_app_secret' ) ){
		$config["providers"][$provider]["keys"]["secret"] = get_option( 'wsl_settings_' . $provider . '_app_secret' );
	}

	// set default scope and display mode for facebook
	if( strtolower( $provider ) == "facebook" ){
		$config["providers"][$provider]["scope"] = "email, user_about_me, user_birthday, user_hometown, user_website"; 
		$config["providers"][$provider]["display"] = "popup";
		$config["providers"][$provider]["trustForwarded"] = true;

		// switch to fb::display 'page' if wsl auth in page
		if ( get_option( 'wsl_settings_use_popup') == 2 ) {
			$config["providers"][$provider]["display"] = "page";
		}
	}

	// set default scope for google
	# https://developers.facebook.com/docs/facebook-login/permissions
	if( strtolower( $provider ) == "google" ){
		$config["providers"][$provider]["scope"] = "https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/plus.profile.emails.read";  
	}

	// if contacts import enabled for facebook, we request an extra permission 'read_friendlists'
	# https://developers.google.com/+/domains/authentication/scopes
	if( get_option( 'wsl_settings_contacts_import_facebook' ) == 1 && strtolower( $provider ) == "facebook" ){
		$config["providers"][$provider]["scope"] = "email, user_about_me, user_birthday, user_hometown, user_website, read_friendlists";
	}

	// if contacts import enabled for google, we request an extra permission 'https://www.google.com/m8/feeds/'
	if( get_option( 'wsl_settings_contacts_import_google' ) == 1 && strtolower( $provider ) == "google" ){
		$config["providers"][$provider]["scope"] = "https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/plus.profile.emails.read https://www.google.com/m8/feeds/";
	}

	// HOOKABLE: allow to overwrite scopes (some people have asked for a way to lower the number of permissions requested)
	$provider_scope = isset( $config["providers"][$provider]["scope"] ) ? $config["providers"][$provider]["scope"] : '' ; 
	$config["providers"][$provider]["scope"] = apply_filters( 'wsl_hook_alter_provider_scope', $provider_scope, $provider );

	/* 3. Instantiate the class Hybrid_Auth and redirect the user to provider to ask for authorisation for this website */

	// load hybridauth main class
	if ( ! class_exists('Hybrid_Auth', false) ){
		require_once WORDPRESS_SOCIAL_LOGIN_ABS_PATH . "/hybridauth/Hybrid/Auth.php"; 
	}

	// HOOKABLE:
	do_action( "wsl_hook_process_login_before_hybridauth_authenticate", $provider, $config );
	
	try{
		// create an instance oh hybridauth with the generated config 
		$hybridauth = new Hybrid_Auth( $config );

		// start the authentication process via hybridauth
		// > if not already connected hybridauth::authenticate() will redirect the user to the provider
		// > where he will be asked for his consent (most providers ask for consent only once). 
		// > after that, the provider will redirect the user back to this same page (and this same line). 
		// > if the user is successfully connected to provider, then this time hybridauth::authenticate()
		// > will just return the provider adapter
		$adapter = $hybridauth->authenticate( $provider );
	} 

	// if hybridauth fails to authenticate the user, then we display an error message
	catch( Exception $e ){
		return wsl_process_login_render_error_page( $e, $config, $hybridauth, $provider, $adapter );
	}

	// HOOKABLE:
	do_action( "wsl_hook_process_login_after_hybridauth_authenticate", $provider, $config );

	/* 4. Display a loading screen after user come back from provider as we redirect the user back to Widget::Redirect URL */

	// get Widget::Authentication display
	$wsl_settings_use_popup = get_option( 'wsl_settings_use_popup' );

	$redirect_to = isset( $_REQUEST[ 'redirect_to' ] ) ? $_REQUEST[ 'redirect_to' ] : site_url();

	// build the authenticateD, which will make wsl_process_login() fire the next step wsl_process_login_end()
	$authenticated_url = site_url( 'wp-login.php', 'login_post' ) . ( strpos( site_url( 'wp-login.php', 'login_post' ), '?' ) ? '&' : '?' ) . "action=wordpress_social_authenticated&provider=" . $provider;

	// display a loading screen
	return wsl_render_return_from_provider_loading_screen( $provider, $authenticated_url, $redirect_to, $wsl_settings_use_popup );
}

// --------------------------------------------------------------------

/**
* Finish the authentication process 
* 
* Steps:
*     1. Get the user profile from provider
*     2. Create new wordpress user if he didn't exist in database
*     3. Store his Hybridauth profile, contacts and BP mapping
*     4. Authenticate the user within wordpress
*/
function wsl_process_login_end()
{
	// HOOKABLE:
	do_action( "wsl_process_login_end_start" );

	// HOOKABLE: set a custom Redirect URL
	$redirect_to = apply_filters( 'wsl_hook_process_login_alter_redirect_to', wsl_process_login_get_redirect_to() ) ;

	// HOOKABLE: reset the provider id
	$provider = apply_filters( 'wsl_hook_process_login_alter_provider', wsl_process_login_get_selected_provider() ) ;

	// is it a new or returning user
	$is_new_user = false;

	// returns user data after he authenticate via hybridauth 
	list( 
		$user_id                , // user_id if found in database
		$adapter                , // hybriauth adapter for the selected provider
		$hybridauth_user_profile, // hybriauth user profile 
		$hybridauth_user_email  , // user email as provided by the provider
		$request_user_login     , // username typed by users in Profile Completion
		$request_user_email     , // email typed by users in Profile Completion
	)
	= wsl_process_login_end_get_user_data( $provider, $redirect_to );

	// if no associated user were found in wslusersprofiles, create new WordPress user
	if( ! $user_id ){
		$user_id = wsl_process_login_create_wp_user( $provider, $hybridauth_user_profile, $request_user_login, $request_user_email );

		$is_new_user = true;
	}

	// store user hybridauth profile (wslusersprofiles), contacts (wsluserscontacts) and buddypress mapping 
	wsl_process_login_update_wsl_user_data( $is_new_user, $user_id, $provider, $adapter, $hybridauth_user_profile );

	// finally create a wordpress session for the user
	wsl_process_login_authenticate_wp_user( $user_id, $provider, $redirect_to, $adapter, $hybridauth_user_profile );
}

// --------------------------------------------------------------------

/**
* Returns user data after he authenticate via hybridauth 
*
* Steps:
*    1. Grab the user profile from hybridauth
*    2. Run Bouncer::Filters if enabled (domains, emails, profiles urls)
*    3  Check if user exist in database by looking for the couple (Provider name, Provider user ID) or verified email
*    4. If Bouncer::Profile Completion is enabled and user didn't exist, we require the user to complete the registration (user name & email) 
*/
function wsl_process_login_end_get_user_data( $provider, $redirect_to )
{
	// HOOKABLE:
	do_action( "wsl_process_login_end_get_user_data_start", $provider, $redirect_to );

	$user_id                  = null;
	$config                   = null;
	$hybridauth               = null; 
	$adapter                  = null;
	$hybridauth_user_profile  = null; 
	$request_user_login       = '';
	$request_user_email       = '';

	// provider is enabled?
	if( ! get_option( 'wsl_settings_' . $provider . '_enabled' ) ){
		return wsl_process_login_render_notice_page( _wsl__( "Unknown or disabled provider.", 'wordpress-social-login' ) );
	}

	/* 1. Grab the user profile from hybridauth */ 

	// build required configuration for this provider. this we will only need provider keys
	$config = array(); 
	$config["providers"] = array();
	$config["providers"][$provider] = array();
	$config["providers"][$provider]["enabled"] = true;

	// provider application id ?
	if( get_option( 'wsl_settings_' . $provider . '_app_id' ) ){
		$config["providers"][$provider]["keys"]["id"] = get_option( 'wsl_settings_' . $provider . '_app_id' );
	}

	// provider application key ?
	if( get_option( 'wsl_settings_' . $provider . '_app_key' ) ){
		$config["providers"][$provider]["keys"]["key"] = get_option( 'wsl_settings_' . $provider . '_app_key' );
	}

	// provider application secret ?
	if( get_option( 'wsl_settings_' . $provider . '_app_secret' ) ){
		$config["providers"][$provider]["keys"]["secret"] = get_option( 'wsl_settings_' . $provider . '_app_secret' );
	}

	// load hybridauth main class
	if ( ! class_exists('Hybrid_Auth', false) ){
		require_once WORDPRESS_SOCIAL_LOGIN_ABS_PATH . "/hybridauth/Hybrid/Auth.php"; 
	}

	try{
		// create an instance of hybridauth with the generated config 
		$hybridauth = new Hybrid_Auth( $config );

		// if user authenticated successfully with social network
		if( $hybridauth->isConnectedWith( $provider ) ){
			$adapter = $hybridauth->getAdapter( $provider );

			// grab user profile via hybridauth api
			$hybridauth_user_profile = $adapter->getUserProfile();
		}
		
		// if user not connected to provider (ie: session lost, url forged)
		else{
			return wsl_process_login_render_notice_page( sprintf( _wsl__( "User not connected with <b>%s</b>", 'wordpress-social-login' ), $provider ) ); 
		} 
	}

	// if things didn't go as expected, we dispay the appropriate error message
	catch( Exception $e ){
		return wsl_process_login_render_error_page( $e, $config, $hybridauth, $provider, $adapter );
	}

	/* 2. Run Bouncer::Filters if enabled (domains, emails, profiles urls) */

	// check hybridauth profile 
	$hybridauth_user_email = sanitize_email( $hybridauth_user_profile->email ); 
	$hybridauth_user_login = sanitize_user( $hybridauth_user_profile->displayName, true );

	# {{{ module Bouncer
	// Bouncer::Filters by emails domains name
	if( get_option( 'wsl_settings_bouncer_new_users_restrict_domain_enabled' ) == 1 ){ 
		if( empty( $hybridauth_user_email ) ){
			return wsl_process_login_render_notice_page( _wsl__( get_option( 'wsl_settings_bouncer_new_users_restrict_domain_text_bounce' ), 'wordpress-social-login') );
		}

		$list = get_option( 'wsl_settings_bouncer_new_users_restrict_domain_list' );
		$list = preg_split( '/$\R?^/m', $list ); 

		$current = strstr( $hybridauth_user_email, '@' );

		$shall_pass = false;
		foreach( $list as $item ){
			if( trim( strtolower( "@$item" ) ) == strtolower( $current ) ){
				$shall_pass = true;
			}
		}

		if( ! $shall_pass ){
			return wsl_process_login_render_notice_page( _wsl__( get_option( 'wsl_settings_bouncer_new_users_restrict_domain_text_bounce' ), 'wordpress-social-login') );
		}
	}

	// Bouncer::Filters by e-mails addresses
	if( get_option( 'wsl_settings_bouncer_new_users_restrict_email_enabled' ) == 1 ){ 
		if( empty( $hybridauth_user_email ) ){
			return wsl_process_login_render_notice_page( _wsl__( get_option( 'wsl_settings_bouncer_new_users_restrict_email_text_bounce' ), 'wordpress-social-login') );
		}

		$list = get_option( 'wsl_settings_bouncer_new_users_restrict_email_list' );
		$list = preg_split( '/$\R?^/m', $list ); 

		$shall_pass = false;
		foreach( $list as $item ){
			if( trim( strtolower( $item ) ) == strtolower( $hybridauth_user_email ) ){
				$shall_pass = true;
			}
		}

		if( ! $shall_pass ){
			return wsl_process_login_render_notice_page( _wsl__( get_option( 'wsl_settings_bouncer_new_users_restrict_email_text_bounce' ), 'wordpress-social-login') );
		}
	}

	// Bouncer ::Filters by profile urls
	if( get_option( 'wsl_settings_bouncer_new_users_restrict_profile_enabled' ) == 1 ){ 
		$list = get_option( 'wsl_settings_bouncer_new_users_restrict_profile_list' );
		$list = preg_split( '/$\R?^/m', $list ); 

		$shall_pass = false;
		foreach( $list as $item ){
			if( trim( strtolower( $item ) ) == strtolower( $hybridauth_user_profile->profileURL ) ){
				$shall_pass = true;
			}
		}

		if( ! $shall_pass ){
			return wsl_process_login_render_notice_page( _wsl__( get_option( 'wsl_settings_bouncer_new_users_restrict_profile_text_bounce' ), 'wordpress-social-login') );
		}
	}

	/* 3. Check if user exist in database by looking for the couple (Provider name, Provider user ID) or verified email */

	// chech if user already exist in wslusersprofiles
	if( ! $user_id ){
		$user_id = (int) wsl_get_stored_hybridauth_user_id_by_provider_and_provider_uid( $provider, $hybridauth_user_profile->identifier );
	}

	// check if this user verified email is in use. if true, we link this social network profile to the found WP user
	if( ! empty( $hybridauth_user_profile->emailVerified ) ){
		$user_id = (int) email_exists( $hybridauth_user_profile->emailVerified );
	}

	/* 4. If Bouncer::Profile Completion is enabled and user didn't exist, we require the user to complete the registration (user name & email) */

	// if associated WP user not found in wslusersprofiles nor he have verified email in use
	if( ! $user_id ){
		// Bouncer :: Accept new registrations
		if( get_option( 'wsl_settings_bouncer_registration_enabled' ) == 2 ){
			return wsl_process_login_render_notice_page( _wsl__( "Registration is now closed.", 'wordpress-social-login' ) ); 
		}

		// Bouncer :: Profile Completion
		if(
			( get_option( 'wsl_settings_bouncer_profile_completion_require_email' ) == 1 && empty( $hybridauth_user_email ) ) || 
			get_option( 'wsl_settings_bouncer_profile_completion_change_username' ) == 1
		){
			do
			{
				list( 
					$shall_pass, 
					$request_user_login, 
					$request_user_email 
				) = wsl_process_login_complete_registration( $provider, $redirect_to, $hybridauth_user_email, $hybridauth_user_login );
			}
			while( ! $shall_pass );
		}
	}
	# }}} module Bouncer

	// if user is found in wslusersprofiles but the associated WP user account no longer exist
	// > this should never happen! but just in case: we delete the user wslusersprofiles/wsluserscontacts entries and we reset the process
	if( $user_id ){
		$user_data = get_userdata( $user_id );

		if( ! $user_data ){
			wsl_delete_stored_hybridauth_user_data( $user_id );

			return wsl_process_login_render_notice_page( _wsl__("Sorry, we couldn't connect you. Please try again.", 'wordpress-social-login') );
		}
	}

	// returns user data
	return array( 
		$user_id,
		$adapter,
		$hybridauth_user_profile, 
		$hybridauth_user_email, 
		$request_user_login, 
		$request_user_email, 
	);
}

// --------------------------------------------------------------------

/**
* Create a new wordpress user
*
* Ref: http://codex.wordpress.org/Function_Reference/wp_insert_user
*/
function wsl_process_login_create_wp_user( $provider, $hybridauth_user_profile, $request_user_login, $request_user_email )
{
	// HOOKABLE:
	do_action( "wsl_process_login_create_wp_user_start", $provider, $hybridauth_user_profile, $request_user_login, $request_user_email );

	$user_login = '';
	$user_email = '';

	// if coming from "complete registration form" 
	if( $request_user_login ){
		$user_login = $request_user_login;
	}

	if( $request_user_email ){
		$user_email = $request_user_email;
	}

	if ( ! $user_login ){
		// attempt to generate user_login from hybridauth user profile display name
		$user_login = $hybridauth_user_profile->displayName;

		// sanitize user login
		$user_login = sanitize_user( $user_login, true );

		// remove spaces and dots
		$user_login = trim( str_replace( array( ' ', '.' ), '_', $user_login ) );
		$user_login = trim( str_replace( '__', '_', $user_login ) );

		// if user profile display name is not provided
		if( empty( $user_login ) ){
			$user_login = strtolower( $provider ) . "_user";
		}

		// user name should be unique
		if( username_exists( $user_login ) ){
			$i = 1;
			$user_login_tmp = $user_login;

			do{
				$user_login_tmp = $user_login . "_" . ($i++);
			}
			while( username_exists ($user_login_tmp));

			$user_login = $user_login_tmp;
		}
	}

	if ( ! $user_email ){
		$user_email = $hybridauth_user_profile->email;

		// generate an email if none
		if ( ! isset ( $user_email ) OR ! is_email( $user_email ) ){
			$user_email = strtolower( $provider . "_user_" . $user_login ) . '@example.com';
		}

		// email should be unique
		if( email_exists ( $user_email ) ){
			do{
				$user_email = md5( uniqid( wp_rand( 10000,99000 ) ) ) . '@example.com';
			}
			while( email_exists( $user_email ) );
		} 
	}

	$display_name = $hybridauth_user_profile->displayName;

	if( $request_user_login ){
		$display_name = sanitize_user( $request_user_login, true );
	}

	if( empty( $display_name ) ){
		$display_name = strtolower( $provider ) . "_user";
	}

	$userdata = array(
		'user_login'    => $user_login,
		'user_email'    => $user_email,

		'display_name'  => $display_name,

		'first_name'    => $hybridauth_user_profile->firstName,
		'last_name'     => $hybridauth_user_profile->lastName, 
		'user_url'      => $hybridauth_user_profile->profileURL,
		'description'   => $hybridauth_user_profile->description,

		'user_pass'     => wp_generate_password()
	);

	# {{{ module Bouncer 
	# http://www.jfarthing.com/development/theme-my-login/user-moderation/
	// Bouncer::Membership level 
	// when enabled and != 'default', Bouncer::Membership level will defines the new user role
	$wsl_settings_bouncer_new_users_membership_default_role = get_option( 'wsl_settings_bouncer_new_users_membership_default_role' );

	if( $wsl_settings_bouncer_new_users_membership_default_role != "default" ){
		$userdata['role'] = $wsl_settings_bouncer_new_users_membership_default_role;
	}

	// Bouncer::User Moderation 
	// > if enabled (Yield to Theme My Login), then we overwrite the user role to 'pending'
	// > if User Moderation is set to Admin Approval then Membership level will be ignored 
	if( get_option( 'wsl_settings_bouncer_new_users_moderation_level' ) > 100 ){ 
		// Theme My Login : User Moderation
		// > Upon activation of this module, a new user role will be created, titled "Pending". This role has no privileges by default.
		// > When a user confirms their e-mail address or when you approve a user, they are automatically assigned to the default user role for the blog/site.
		// http://www.jfarthing.com/development/theme-my-login/user-moderation/
		$userdata['role'] = "pending";
	}
	# }}} module Bouncer

	// HOOKABLE: change the user data
	$userdata = apply_filters( 'wsl_hook_process_login_alter_wp_insert_user_data', $userdata, $provider, $hybridauth_user_profile );


/** IMPORTANT: wsl_hook_process_login_alter_userdata is DEPRECIATED since 2.2.1 and WILL BE REMOVED, please don't use it. See: http://hybridauth.sourceforge.net/wsl/developer.html */ 
$userdata = apply_filters( 'wsl_hook_process_login_alter_userdata', $userdata, $provider, $hybridauth_user_profile );
/** IMPORTANT: wsl_hook_process_login_alter_userdata is DEPRECIATED since 2.2.1 and WILL BE REMOVED, please don't use it. See: http://hybridauth.sourceforge.net/wsl/developer.html */ 


	// HOOKABLE: This action runs just before creating a new wordpress user.
	do_action( 'wsl_hook_process_login_before_wp_insert_user', $userdata, $provider, $hybridauth_user_profile );


/** IMPORTANT: wsl_hook_process_login_before_insert_user is DEPRECIATED since 2.2.1 and WILL BE REMOVED, please don't use it. See: http://hybridauth.sourceforge.net/wsl/developer.html */ 
do_action( 'wsl_hook_process_login_before_insert_user', $userdata, $provider, $hybridauth_user_profile );
/** IMPORTANT: wsl_hook_process_login_before_insert_user is DEPRECIATED since 2.2.1 and WILL BE REMOVED, please don't use it. See: http://hybridauth.sourceforge.net/wsl/developer.html */ 


	// HOOKABLE: This action runs just before creating a new wordpress user, it delegate user insert to a custom function.
	$user_id = apply_filters( 'wsl_hook_process_login_delegate_wp_insert_user', $userdata, $provider, $hybridauth_user_profile );

	// Create a new WordPress user
	if( ! $user_id || ! is_integer( $user_id ) ){
		$user_id = wp_insert_user( $userdata );
	}

	// update user metadata
	if( $user_id && is_integer( $user_id ) ){
		update_user_meta( $user_id, 'wsl_current_provider'   , $provider );
		update_user_meta( $user_id, 'wsl_current_user_image' , $hybridauth_user_profile->photoURL );
	}

	// do not continue without user_id
	else {
		return wsl_process_login_render_notice_page( _wsl__( "An error occurred while creating a new user!", 'wordpress-social-login' ) );
	}

	// Send notifications 
	if ( get_option( 'wsl_settings_users_notification' ) == 1 ){
		wsl_admin_notification( $user_id, $provider );
	}

	// HOOKABLE: This action runs just after a wordpress user has been created
	// > Note: At this point, the user has been added to wordpress database, but NOT CONNECTED.
	do_action( 'wsl_hook_process_login_after_wp_insert_user', $user_id, $provider, $hybridauth_user_profile );


/** IMPORTANT: wsl_hook_process_login_after_create_wp_user is DEPRECIATED since WSL 2.2.1 and WILL BE REMOVED, please don't use it. See: http://hybridauth.sourceforge.net/wsl/developer.html */ 
do_action( 'wsl_hook_process_login_after_create_wp_user', $user_id, $provider, $hybridauth_user_profile );
/** IMPORTANT: wsl_hook_process_login_after_create_wp_user is DEPRECIATED since WSL 2.2.1 and WILL BE REMOVED, please don't use it. See: http://hybridauth.sourceforge.net/wsl/developer.html */ 


	// returns the user created user id
	return $user_id;
}

// --------------------------------------------------------------------

/**
* Store WSL user data
*
* Steps:
*     1. Store Hybridauth user profile
*     2. Import user contacts
*     3. Launch BuddyPress Profile mapping
*/
function wsl_process_login_update_wsl_user_data( $is_new_user, $user_id, $provider, $adapter, $hybridauth_user_profile )
{
	// HOOKABLE:
	do_action( "wsl_process_login_update_wsl_user_data_start", $is_new_user, $user_id, $provider, $adapter, $hybridauth_user_profile );

	// store user hybridauth user profile in table wslusersprofiles
	wsl_store_hybridauth_user_profile( $user_id, $provider, $hybridauth_user_profile );

	// map hybridauth user profile to buddypress xprofile table, if enabled
	// > Profile mapping will only work with new users. Profile mapping for returning users will implemented in future version of WSL.
	if( $is_new_user ){
		wsl_buddypress_xprofile_mapping( $user_id, $provider, $hybridauth_user_profile );
	}

	// importt user contacts into wslusersprofiles, if enabled
	wsl_store_hybridauth_user_contacts( $user_id, $provider, $adapter );
}

// --------------------------------------------------------------------

/**
* Authenticate a user within wordpress
*
* Ref: http://codex.wordpress.org/Function_Reference/wp_set_auth_cookie
* Ref: http://codex.wordpress.org/Function_Reference/wp_safe_redirect
*/
function wsl_process_login_authenticate_wp_user( $user_id, $provider, $redirect_to, $adapter, $hybridauth_user_profile )
{
	// HOOKABLE:
	do_action( "wsl_process_login_authenticate_wp_user_start", $user_id, $provider, $redirect_to, $adapter, $hybridauth_user_profile );

	// update some fields in usermeta for the current user
	update_user_meta( $user_id, 'wsl_current_provider'   , $provider );
	update_user_meta( $user_id, 'wsl_current_user_image' , $hybridauth_user_profile->photoURL );

	# {{{ module Bouncer
	# http://www.jfarthing.com/development/theme-my-login/user-moderation/
	# https://wordpress.org/support/topic/bouncer-user-moderation-blocks-logins-when-enabled#post-4331601
	$role = ''; 
	$wsl_settings_bouncer_new_users_moderation_level = get_option( 'wsl_settings_bouncer_new_users_moderation_level' );

	// get user role 
	if( $wsl_settings_bouncer_new_users_moderation_level > 100 ){
		$role = current( get_userdata( $user_id )->roles );
	}

	// if role eq 'pending', we halt the authentication and we redirect the user to the appropriate url (pending=activation or pending=approval)
	if( $role == 'pending' ){
		// Bouncer::User Moderation : E-mail Confirmation
		if( $wsl_settings_bouncer_new_users_moderation_level == 101 ){
			$redirect_to = site_url( 'wp-login.php', 'login_post' ) . ( strpos( site_url( 'wp-login.php', 'login_post' ), '?' ) ? '&' : '?' ) . "pending=activation";

			// send a new e-mail/activation notification - if TML not enabled, we ensure WSL to keep it quiet
			@ Theme_My_Login_User_Moderation::new_user_activation_notification( $user_id );
		}

		// Bouncer::User Moderation : Admin Approval
		elseif( $wsl_settings_bouncer_new_users_moderation_level == 102 ){
			$redirect_to = site_url( 'wp-login.php', 'login_post' ) . ( strpos( site_url( 'wp-login.php', 'login_post' ), '?' ) ? '&' : '?' ) . "pending=approval";
		}
	}
	# }}} module Bouncer

	// otherwise, we connect the user with in wordpress (we give him a cookie)
	else{
		// HOOKABLE: This action runs just before logging the user in (before creating a WP cookie)
		do_action( "wsl_hook_process_login_before_wp_set_auth_cookie", $user_id, $provider, $hybridauth_user_profile );


/** IMPORTANT: wsl_hook_process_login_before_set_auth_cookie is DEPRECIATED since WSL 2.2.1 and WILL BE REMOVED, please don't use it. See: http://hybridauth.sourceforge.net/wsl/developer.html */ 
do_action( 'wsl_hook_process_login_before_set_auth_cookie', $user_id, $provider, $hybridauth_user_profile );
/** IMPORTANT: wsl_hook_process_login_before_set_auth_cookie is DEPRECIATED since WSL 2.2.1 and WILL BE REMOVED, please don't use it. See: http://hybridauth.sourceforge.net/wsl/developer.html */ 

		// Set WP auth cookie
		wp_set_auth_cookie( $user_id, true );
	}

	// HOOKABLE: This action runs just before redirecting the user back to $redirect_to
	// > Note: If you have enabled User Moderation, then the user is NOT NECESSARILY CONNECTED
	// > within wordpress at this point (in case the user $role == 'pending').
	// > To be sure the user is connected, use wsl_hook_process_login_before_wp_set_auth_cookie instead.
	do_action( "wsl_hook_process_login_before_wp_safe_redirect", $user_id, $provider, $hybridauth_user_profile, $redirect_to );


/** IMPORTANT: wsl_hook_process_login_before_redirect is DEPRECIATED since WSL 2.2.1 and WILL BE REMOVED, please don't use it. See: http://hybridauth.sourceforge.net/wsl/developer.html */ 
do_action( 'wsl_hook_process_login_before_redirect', $user_id, $provider, $hybridauth_user_profile );
/** IMPORTANT: wsl_hook_process_login_before_redirect is DEPRECIATED since WSL 2.2.1 and WILL BE REMOVED, please don't use it. See: http://hybridauth.sourceforge.net/wsl/developer.html */ 


	// That's it. We done. 
	wp_safe_redirect( $redirect_to );

	// for good measures
	die(); 
}

// --------------------------------------------------------------------

/**
* Returns redirect_to (callback url)
*
* By default, once a user  authenticate, he will be automatically redirected to the page where he come from (referer).
* If WSL wasn't able to identify the referer url (or if the user come wp-login.php), then they will be redirected to 
* Widget::Redirect URL instead. 
*
* When Widget::Force redirection is set to Yes, users will be always redirected to Widget::Redirect URL. 
*
* Note: Widget::Redirect URL can be customised using the filter 'wsl_hook_process_login_alter_redirect_to'
*/
function wsl_process_login_get_redirect_to()
{
	// force redirection?
	$wsl_settings_redirect_url = get_option( 'wsl_settings_redirect_url' );

	if( get_option( 'wsl_settings_force_redirect_url' ) == 1 ){
		return $wsl_settings_redirect_url;
	}

	// get a valid $redirect_to
	if ( isset( $_REQUEST[ 'redirect_to' ] ) && $_REQUEST[ 'redirect_to' ] != '' ){
		$redirect_to = $_REQUEST[ 'redirect_to' ];

		// Redirect to https if user wants ssl
		if ( isset( $secure_cookie ) && $secure_cookie && false !== strpos( $redirect_to, 'wp-admin') ){
			$redirect_to = preg_replace( '|^http://|', 'https://', $redirect_to );
		}

		// we don't go there..
		if ( strpos( $redirect_to, 'wp-admin') ){
			$redirect_to = $wsl_settings_redirect_url; 
		}

		// nor there..
		if ( strpos( $redirect_to, 'wp-login.php') ){
			$redirect_to = $wsl_settings_redirect_url; 
		}
	}

	if( empty( $redirect_to ) ){
		$redirect_to = $wsl_settings_redirect_url; 
	}

	if( empty( $redirect_to ) ){
		$redirect_to = site_url();
	}

	return $redirect_to;
}

// --------------------------------------------------------------------

/**
* Display an error message in case user authentication fails
*/
function wsl_process_login_render_error_page( $e, $config, $hybridauth, $provider, $adapter )
{
	// HOOKABLE:
	do_action( "wsl_process_login_render_error_page", $e, $config, $hybridauth, $provider, $adapter );

	$assets_base_url  = WORDPRESS_SOCIAL_LOGIN_PLUGIN_URL . '/assets/img/'; 

	$message = _wsl__("Unspecified error!", 'wordpress-social-login'); 
	$notes    = ""; 

	switch( $e->getCode() ){
		case 0 : $message = _wsl__("Unspecified error.", 'wordpress-social-login'); break;
		case 1 : $message = _wsl__("WordPress Social Login is not properly configured.", 'wordpress-social-login'); break;
		case 2 : $message = sprintf( __wsl__("WordPress Social Login is not properly configured.<br /> <b>%s</b> need to be properly configured.", 'wordpress-social-login'), $provider ); break;
		case 3 : $message = _wsl__("Unknown or disabled provider.", 'wordpress-social-login'); break;
		case 4 : $message = sprintf( _wsl__("WordPress Social Login is not properly configured.<br /> <b>%s</b> requires your application credentials.", 'wordpress-social-login'), $provider ); 
				 $notes   = sprintf( _wsl__("<b>What does this error mean ?</b><br />Most likely, you didn't setup the correct application credentials for this provider. These credentials are required in order for <b>%s</b> users to access your website and for WordPress Social Login to work.", 'wordpress-social-login'), $provider ) . _wsl__('<br />Instructions for use can be found in the <a href="http://hybridauth.sourceforge.net/wsl/configure.html" target="_blank">User Manual</a>.', 'wordpress-social-login'); 
				 break;
		case 5 : $message = sprintf( _wsl__("Authentication failed. Either you have cancelled the authentication or <b>%s</b> refused the connection.", 'wordpress-social-login'), $provider ); break; 
		case 6 : $message = sprintf( _wsl__("Request failed. Either you have cancelled the authentication or <b>%s</b> refused the connection.", 'wordpress-social-login'), $provider ); break;
		case 7 : $message = _wsl__("You're not connected to the provider.", 'wordpress-social-login'); break;
		case 8 : $message = _wsl__("Provider does not support this feature.", 'wordpress-social-login'); break;

		case 9 : $message = $e->getMessage(); break;
	}

	if( is_object( $adapter ) ){
		$adapter->logout();
	}

	$_SESSION = array();

	@ session_destroy();

	return wsl_render_error_page( $message, $notes, $e, array( $config, $hybridauth, $provider, $adapter ) );
}


// --------------------------------------------------------------------

/**
* Display an notice message 
*/
function wsl_process_login_render_notice_page( $message )
{
	// HOOKABLE:
	do_action( "wsl_process_login_render_notice_page", $message );

	return wsl_render_notice_page( $message );
}

// --------------------------------------------------------------------

/**
* Returns the selected provider from _REQUEST
*/
function wsl_process_login_get_selected_provider()
{
	return ( isset( $_REQUEST["provider"] ) ? sanitize_text_field( $_REQUEST["provider"] ) : null );
}

// --------------------------------------------------------------------
