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

	<!-- page header & desktop player controls -->
		<section id="header-wrapper">
		  <div class="row-container">
		  		<div class="player-control-wrapper pull-left">
		  		<!--<div class="button playpause"><i class="ss-play"></i>&nbsp;&nbsp;Play </div>-->
		  		<div class="button favorites"><i class="ss-heart"></i>&nbsp;&nbsp;My favorites </div>
		  		<div class="button about"><i class="ss-icon">email</i>&nbsp;&nbsp;Get new tracks</div>
		  		
			  		
			    </div>
			    
			<img src="<?php echo get_template_directory_uri(); ?>/img/buffalo-light.png" id="header-branding" />
			
			<div class="pull-right" id="header-menu">
				
				<div class="button upload"><i class="ss-upload"></i>&nbsp;&nbsp;Submit track</div>
				<div class="button login"><i class="ss-user"></i>&nbsp;&nbsp;Login/Signup </div>
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
		     	     <a href="#" class="header-link">About this site<i class="ss-help"></i></a>
		    	</div>
		     	<!--<div id="active-track">
			      	<h6 id="active-track-name"></h6>
			     	<!--<div id="play-info">(<span id="current-time"></span>&nbsp;of&nbsp;<span id="duration"></span>)</div>--

		     	</div>-->
		     	<!--<div class="pull-right" id="sharebutton">Share current track <i class="ss-action"></i></div>-->
		   	 </div>
		  </div>
		</section>

		<section class="offCanvasPanel" id="about">
  		   <h4>About the Bearded Buffalo</h4>
		   <hr>
		   <div class="about-container">
  			 <p>Bearded Buffalo is community based music forum that reviews tracks in five words. It is a shared platform that allows anyone to submit their own track and review.  How it works:<br><br>
				1. hear a song you like<br>
				2. come to our site<br>
				3. click on the submit button at the top of the page<br>
				4. write your five word review and link the song (ex: youtube, soundcloud, etc.)<br>
				5. click send<br>
				6. tell a friend</p>
  			 <div class="button pull-right" id="close-about">Close &#215;</div>
  		   </div>
	    </section>

	    <section class="offCanvasPanel" id="submit-track">
			<h4>Submit an awesome track for all to hear!</h4>
			<hr>
  			<div id="form--wrapper">
    		  <!--<form>
    			<div class="fieldgroup song">
      				<input type="text" class="pull-left" placeholder="Song title">
      				<span class="matcher">by</span>
      				<input type="text" class="pull-right" placeholder="Artist">
      			</div>
      		    <div class="fieldgroup words">
      		      <textarea placeholder="Five Words about the track"></textarea>
        		  <button class="pull-right">Submit →</button>
        		  <a class="button pull-right" id="close-form" style="margin-right:5px;">Close ×</a>
      			</div>
     		 </form>-->
     		 <?php gravity_form("Submit Song", $display_title=false, $display_description=false, $display_inactive=false, $field_values=null, $ajax=false, $tabindex); ?>
    		</div>
		</section>

		<section class="offCanvasPanel" id="my-favorites">
		</section>

		<section class="offCanvasPanel" id="account">
		</section>

		<div id="jquery_jplayer"></div>
		<div id="wrapper">
		
		

			