<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
	
	<head>
		<meta charset="<?php bloginfo('charset'); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<title><?php wp_title(' | ', true, 'right'); ?></title>
		
		<link href='//fonts.googleapis.com/css?family=Libre+Baskerville:400,700,400italic' rel='stylesheet' type='text/css' />
   		<link href='http://fonts.googleapis.com/css?family=Lato:300,400,600' rel='stylesheet' type='text/css' />
		<link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_uri(); ?>" />
		<link href="<?php echo get_template_directory_uri(); ?>/css/webfonts/ss-social.css" media="all" rel="stylesheet" type="text/css" />
		<link href="<?php echo get_template_directory_uri(); ?>/css/webfonts/ss-standard.css" media="all" rel="stylesheet" type="text/css" />

		<!--[if lt IE 9]>
            <script src="<?php echo get_template_directory_uri(); ?>/js/vendor/html5shiv.min.js"></script>
        <![endif]-->
		<?php wp_head(); ?>
	</head>
	
	<body <?php
	global $post;
	$slug = get_post( $post )->post_name;
	body_class($slug);
	?>>

	    <!-- sticky page header & menu -->
		<section id="header-wrapper">
		  <div class="row-container">
		  	<div class="player-control-wrapper pull-left">
		  		<a class="button offCanvas-link favorites" href="#favorites"><i class="ss-heart"></i>&nbsp;&nbsp;My favorites </a>
		  		<a class="button offCanvas-link" href="#inboxSignup"><i class="ss-icon">email</i>&nbsp;&nbsp;Get new tracks</a>
			</div>
			<img src="<?php echo get_template_directory_uri(); ?>/img/buffalo-light.png" id="header-branding" />
			<div class="pull-right" id="header-menu">
				<a class="button offCanvas-link upload" href="#submitTrack"><i class="ss-upload"></i>&nbsp;&nbsp;Submit track</a>
				<a class="button offCanvas-link login" href="#userActions"><i class="ss-user"></i>&nbsp;&nbsp;Login/Signup </a>
			</div>
		 </div>
		 <div class="header-secondary">
		     <div class="header-secondaryWrapper">
		     	<!--<div class="pull-left">
		     	     <div class="desktop-player player-control volume">
		     		 <a class="jp-volume-bar"> 
		     		    <i class="ss-volume"></i>
		     		    <input id="volumeslider" type="range" min="0" max="100">
		     		    <i class="ss-volumehigh"></i>
		     		 </a>
		     	   </div>	   
		     	</div>-->
		     	<div id="header-branding-label">The Bearded Buffalo</div>
		     	<div class="pull-right">
		     	     <a href="#about" class="offCanvas-link header-link">About this site<i class="ss-help"></i></a>
		    	</div>
		     	<!--<div id="active-track">
			      	<h6 id="active-track-name"></h6>
			     	<!--<div id="play-info">(<span id="current-time"></span>&nbsp;of&nbsp;<span id="duration"></span>)</div>--

		     	</div>-->
		     	<!--<div class="pull-right" id="sharebutton">Share current track <i class="ss-action"></i></div>-->
		   	 </div>
		  </div>
		</section> <!-- #header-wrapper-->

		<? // THESE ARE THE SEMI-TRANSPARENT SLIDING PANELS ?>
		<?php get_template_part('offCanvas', 'about'); ?>
		<?php get_template_part('offCanvas', 'inboxSignup'); ?>
		<?php get_template_part('offCanvas', 'submitTrack'); ?>
		<?php get_template_part('offCanvas', 'favorites'); ?>
		<?php get_template_part('offCanvas', 'userActions'); ?>

		<div id="jquery_jplayer"></div>
		<div id="wrapper">
		
		

			