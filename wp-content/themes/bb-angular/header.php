<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js" ng-app="bb">
	
	<head>
		<meta charset="<?php bloginfo('charset'); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<title><?php wp_title(' | ', true, 'right'); ?></title>
		<link rel="icon" href="favicon.ico" type="image/x-icon" />
		
		<link href='//fonts.googleapis.com/css?family=Libre+Baskerville:400,700,400italic' rel='stylesheet' type='text/css' />
   		<link href='http://fonts.googleapis.com/css?family=Lato:300,400,600' rel='stylesheet' type='text/css' />
		<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/app/app.css" />
		<link href="<?php echo get_template_directory_uri(); ?>/fonts/ss-social.css" media="all" rel="stylesheet" type="text/css" />
		<link href="<?php echo get_template_directory_uri(); ?>/fonts/ss-standard.css" media="all" rel="stylesheet" type="text/css" />

		<!-- move to footer -->
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-53295326-1', 'auto');
		  ga('send', 'pageview');

		</script>
		<!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script> -->
		<script>window.jQuery || document.write('<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"><\/script>')</script>
		<script src="<?php echo get_template_directory_uri(); ?>/js/vendor/modernizr-2.6.2.min.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/js/vendor/conditionizr.min.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/js/vendor/jquery.jplayer.min.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/js/vendor/jquery.velocity.min.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/js/main.js"></script>
		<!--./ move to footer -->


		<!--[if lt IE 9]>
            <script src="<?php echo get_template_directory_uri(); ?>/js/vendor/html5shiv.min.js"></script>
        <![endif]-->
		<?php wp_head(); ?>
	</head>
	
	<body>
		<div id="jquery_jplayer"></div>
		<div id="wrapper">
		
		<header class="branding">
    		<div class="branding-wrapper">
        		<img src="<?php echo get_template_directory_uri(); ?>/img/logo-text.png" /><Br>
      			<h1>Discover new songs reviewed in five words</h1>
      			<a class="button button-primary"><i class="ss-icon">play</i> Play a song</a>
      		</div>
     	</header>

     	<nav class="header-nav" id="headerheader">
        	<div class="header-nav--block block-left">
        	<a id="nav-play" href="#" class="nav-item">Listen <i class="ss-icon">music</i></a>
        	   <a href="#" class="nav-item about">About</a> 
          		
          		
        	</div>
        	<a href="#" class="nav-item--home"><img src="<?php echo get_template_directory_uri(); ?>/img/buffalo.png" /></a>
        	<div class="header-nav--block block-right">
          		<a href="http://therealbeardedbuffalo.tumblr.com/" target="new" class="nav-item">Blog</a>
          		<a class="nav-item upload">Submit</a>
          		

        	</div>
        </nav>
        <div class="clearfix"></div>

			