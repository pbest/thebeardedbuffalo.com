		<!-- OFF CANVAS CONTENT -->
		
		
		
<div class="md-modal md-effect-1" id="modal-1">
    <div class="md-content">
    <div class="pull-right"> <i class="md-close ss-delete"></i></div>
        <h4 id="shareText">How do you want to share song name?</h4>
        
        <div>
           <ul class="md-icon-gallery">
           <li><a href="#" id="copyLink"><i class="ss-icon ss-link"></i><br>Copy Link</a></li>
           <li><a href="#" id="emailShare"><i class="ss-icon ss-mail"></i><br>Email</a></li>
           
           <li><a href="#" id="facebookShare" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;"
   target="_blank" title="Share on Facebook"><i class="ss-icon ss-social ss-facebook"></i><br>Facebook</a></li>
           <li><a href="#" id="twitterShare" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;"
   target="_blank" title="Share on Twitter"><i class="ss-icon ss-social ss-twitter"></i><br>Twitter</a></li>
           </ul>
           <p id="copyLinkcontents"></p>
        </div>
    </div>
</div>
<div class="md-overlay"></div>


		<!-- sticky footer player controls -->
		<section id="player-wrapper">
		<div id="player-image"><div id="player-image-cover"></div></div>
		 <div class="row-container">
			
			<div class="pull-left">
				<!--<div class="button button-emphasize"><i class="ss-icon">heart</i>&nbsp;&nbsp;Favorite</div>
				<div class="button button-emphasize"><i class="ss-skipforward"></i>&nbsp;&nbsp;Skip </div>-->
			
				<div class="player-control skipback">
		     		<i class="ss-skipback"></i>
		     	</div>
		     	<div class="player-control playpause">
	     		 	<i class="ss-play"></i>
	     	    </div>
				
				<div class="player-control skipforward">
		     		<i class="ss-skipforward"></i>
	     	</div>

	     	</div>
	     	<div id="active-track">
		      	<h6 id="active-track-name"></h6>
		     	<div id="play-info">(<span id="current-time"></span>&nbsp;of&nbsp;<span id="duration"></span>)</div>
		     </div>

		    <div class="pull-right">
		    <div class="button"><i class="ss-icon">heart</i>&nbsp;&nbsp;Save to favorites</div>
				<div class="button md-trigger" data-modal="modal-1"><i class="ss-icon">share</i>&nbsp;&nbsp;Share track</div>
			</div>
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
		<script src="<?php echo get_template_directory_uri(); ?>/js/vendor/classie.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/js/vendor/modalEffects.js"></script>
		<!--<script src="<?php echo get_template_directory_uri(); ?>/js/vendor/60fps-scroll.js"></script>-->
		<script src="<?php echo get_template_directory_uri(); ?>/js/main.js"></script>

	</body>
</html>