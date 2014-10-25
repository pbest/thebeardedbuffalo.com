		<!-- OFF CANVAS CONTENT -->
		
		<!-- page header & desktop player controls -->
		<section id="header-wrapper">
		  <div id="header-container">
		  		<div class="player-control-wrapper pull-left">
			  		<div class="desktop-player player-control skipback">
			     		<i class="ss-skipback"></i>
			     	</div>
			     	<div class="desktop-player player-control playpause">
		     		 	<i class="ss-play"></i>
		     	   </div>
					
					<div class="desktop-player player-control skipforward">
			     		<i class="ss-skipforward"></i>
			     	</div>
			    </div>
			    
			<img src="<?php echo get_template_directory_uri(); ?>/img/buffalo-light.png" id="header-branding" />
			
			<div class="pull-right" id="header-menu">
				<div class="button-trans about">About</div>
				<div class="button upload"><i class="ss-icon">upload</i>&nbsp;&nbsp;Submit track </div>
			</div>

		   </div>

		   <div id="active-track-details">
		     <div id="active-track-wrapper">
		     	<div class="pull-left">
		     	     <div class="desktop-player player-control volume">
		     		 <a class="jp-volume-bar"> 
		     		    <i class="ss-volume"></i>
		     		    <input id="volumeslider" type="range" min="0" max="100">
		     		    <i class="ss-volumehigh"></i>
		     		 </a>
		     	   </div>	   
		     	</div>
		     	<div id="active-track">
			      	<h6 id="active-track-name"></h6>
			     	<div id="play-info">(<span id="current-time"></span>&nbsp;of&nbsp;<span id="duration"></span>)</div>	
		     	</div>
		     	<!--<div class="pull-right" id="sharebutton">Share current track <i class="ss-action"></i></div>-->
		   	 </div>
		  </div>
		</section>
		<!-- end .header-wrapper :: header & desktop player controls -->

		<!-- mobile player controls -->
		<section id="mobile-player-wrapper">
			<div class="player-control skipback">
	     		<i class="ss-skipback"></i>
	     	</div>
	     	<div class="player-control playpause">
     		 	<i class="ss-play"></i>
     	    </div>
			
			<div class="player-control skipforward">
	     		<i class="ss-skipforward"></i>
	     	</div>
	     	<div id="mobile-active-track">
			      	<h6 id="mobile-active-track-name">Just Press Play</h6>
			     	<div id="mobile-play-info">(<span id="mobile-current-time"></span>&nbsp;of&nbsp;<span id="mobile-duration"></span>)</div>	
		     	</div>
		</section>

		<section id="about">
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

        <section id="submit-track">
			<h4>Submit a track to the Bearded Buffalo</h4>
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
		
			<div id="footer">
				<div class="pull-left">&copy; 2014 The Bearded Buffalo</div>
				<div class="pull-right">
  					<a href="http://instagram.com/thebeardedbuffalo"><i class="ss-icon ss-social">instagram</i></a>&nbsp;
  					<a href="https://www.facebook.com/thebeardedbuffalo"><i class="ss-icon ss-social">facebook</i></a>&nbsp;
 					<a href="mailto:info@thebeardedbuffalo.com"><i class="ss-icon ss-social">email</i></a>&nbsp;		
				</div>
			</div>
	

		<?php wp_footer(); ?>

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
		<!--<script src="<?php echo get_template_directory_uri(); ?>/js/main.js"></script>-->

	</body>
</html>