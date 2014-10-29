jQuery(document).ready(function($) {
   cs.utility.init();

   $(window).resize(function(){ cs.utility.resize(); });
   $(window).scroll(function(){ cs.utility.onScroll(); });


});


/*
=============================================================================
	FUNCTION DECLARATIONS
=============================================================================
*/

var cs = (function($) {

	/* GLOBAL VARIABLES */
	var	my_jPlayer = $("#jquery_jplayer"),
        my_trackName = $("#active-track-name"),
	    my_playState = $("#play-state"),
	    my_extraPlayInfo = $("#extra-play-info");

	// Some options
	var	opt_play_first = false, // If true, will attempt to auto-play the default track on page loads. No effect on mobile devices, like iOS.
		opt_auto_play = true, // If true, when a track is selected, it will auto-play.
		opt_text_playing = "Now playing", // Text when playing
		opt_text_selected = "Track selected"; // Text when not playing

	// A flag to capture the first track
	var first_track = true;

	

	/*
		Utility
		
		Various utility functions that load/unload/route data,
		call other functions, etc.
	*/
	var utility = (function() {

		var debug = false;

		var iOS = ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false );

		var init = function() { // Called on page load, calls all other functions that should occur on page load
			
			// PLUGINS CALLS / DEVICE FIXES
			conditionizr({ // http://conditionizr.com/docs.html
				debug      : false,
				scriptSrc  : 'js/conditionizr/',
				styleSrc   : 'css/conditionizr/',
				ieLessThan : {
					active: true,
					version: '9',
					scripts: false,
					styles: false,
					classes: true,
					customScript: // Separate polyfills with commas
						'//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6.1/html5shiv.js, //cdnjs.cloudflare.com/ajax/libs/respond.js/1.1.0/respond.min.js'
					},
				chrome     : { scripts: false, styles: false, classes: true, customScript: false },
				safari     : { scripts: false, styles: false, classes: true, customScript: false },
				opera      : { scripts: false, styles: false, classes: true, customScript: false },
				firefox    : { scripts: false, styles: false, classes: true, customScript: false },
				ie10       : { scripts: false, styles: false, classes: true, customScript: false },
				ie9        : { scripts: false, styles: false, classes: true, customScript: false },
				ie8        : { scripts: false, styles: false, classes: true, customScript: false },
				ie7        : { scripts: false, styles: false, classes: true, customScript: false },
				ie6        : { scripts: false, styles: false, classes: true, customScript: false },
				retina     : { scripts: false, styles: false, classes: true, customScript: false },
				touch      : { scripts: false, styles: false, classes: true, customScript: false },
				mac        : true,
				win        : true,
				x11        : true,
				linux      : true
			});

			if (navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i)) { // Disable scaling until user begins a gesture (prevents zooming when user rotates to landscape mode)
				var viewportmeta = document.querySelector('meta[name="viewport"]');
				if (viewportmeta) {
					viewportmeta.content = 'width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0';
					document.body.addEventListener('gesturestart', function () {
						viewportmeta.content = 'width=device-width, minimum-scale=0.25, maximum-scale=1.6';
					}, false);
				}
			}

			

			// INITIALIZE OUR AUDIO PLAYER
			initJPlayer();


			// EVENT HOOKS FOR OFF CANVAS CONTENT PANELS
			$('.offCanvas-link').click(function(e){ 
				e.preventDefault();
				$thisEl = $(this);
				targetPanelID = $thisEl.attr('data-href');
				uiMod.togglePanel(targetPanelID,$thisEl); 
			});

			//$('.about').click(function(){ uiMod.toggleAbout(); });
			//$('#close-about').click(function() { uiMod.toggleAbout(); });
			//$('.upload').click(function() { uiMod.toggleUpload(); });
			
			// EVENT HOOKS FOR TRACK CONTROLS
			$('.container').on('click', '.item', function (){ cs.track.trackClick($(this)); });
			//$('.item').click(function() { cs.track.trackClick($(this)); });
			$('.playpause').click(function() { cs.track.spaceBar(); });
			$('.skipforward').click(function() { cs.track.nextTrack(); });
			$('.skipback').click(function() { cs.track.previousTrack(); });
			//$('#copyLink').click(function() { 
			//	$('#copyLink').toggleClass('max-height-visible');
			//});

			
			// KEYSTROKE EVENTS FOR TRACK SKIPPING AND PLAY/PAUSE
			$("body").keydown(function(e) {
			  if(e.keyCode == 37) { // left
			   		cs.track.previousTrack();
			  }
			  else if(e.keyCode == 39) { // right
			    	cs.track.nextTrack();
			  }
			  else if(e.keyCode == 32) { // space
			  		e.preventDefault();
			  		cs.track.spaceBar();
			  } 
			});
			
			
		};

		var initJPlayer = function() {

			// Change the time format
			$.jPlayer.timeFormat.padMin = false;
			$.jPlayer.timeFormat.padSec = false;
			//$.jPlayer.timeFormat.sepMin = " min ";
			//$.jPlayer.timeFormat.sepSec = " sec";
			$.jPlayer.keys(true); 
			// Initialize the play state text
			//my_playState.text(opt_text_selected);

			// Instance jPlayer
			my_jPlayer.jPlayer({
				ready: function () {
				//$(".track-default").click();
				//console.log('yes');
				},
				timeupdate: function(event) {
					my_extraPlayInfo.text(parseInt(event.jPlayer.status.currentPercentAbsolute, 10) + "%");
				},
				play: function(event) {
					my_playState.text(opt_text_playing);
					uiMod.showPlayerControls();
					//console.log('song played');
					//console.log($(this).find('#track-details').text());
				},
				pause: function(event) {
					my_playState.text(opt_text_selected);
				},
				cssSelector: {
					currentTime: "#current-time",
					duration: "#duration",
					play: "#sticky-nav--play"
				},
				ended: function(event) {
					my_playState.text(opt_text_selected);
					cs.track.nextTrack();
				},
				swfPath: "http://thebeardedbuffalo.com/wp-content/themes/the-bearded-buffalo-theme-1/js/vendor",
				cssSelectorAncestor: "#player-wrapper",
				supplied: "m4a,mp3",
				keys: true,	
				wmode: "window"
			});
		}

		var showNav = function() {
  			var scrollTop = $(window).scrollTop();
  		  	if (scrollTop > 70) {
      			$( "#header-wrapper" ).addClass('translateY-0px');
    		} else if (scrollTop < 70) {
    			 $( "#header-wrapper" ).removeClass('translateY-0px');
   			 }
  		};

		var onScroll = function() {   // Called when the browser window is scrolled
			showNav();	
		};

		var resize = function() {   // Called when the browser window is resized
			// Functions
		};

		var responsiveState = function(req) { // Returns what responsive state we're at. Values based on CSS media queries.
			// Below is an idiotic bug fix.
			// Chrome & Safari exclude scrollbars from window width for CSS media queries.
			// Firefox, Opera and IE include scrollbars in window width for CSS media queries, but not in JS.
			// So we have to add some px to the window width for Firefox, Opera and IE so that breakpoints
			// match up between CSS and JS. What a world.
			if ($('html').hasClass('chrome') || $('html').hasClass('safari')) {
				var winWidth = $(window).width();
			}
			else {
				var winWidth = $(window).width() + 17;
			}

			if (typeof req === 'undefined' || req === 'state') {
				// MODIFY THESE IF STATEMENTS TO MATCH YOUR RESPONSIVE WIDTHS
				if (winWidth >= 1025) {
					return 'full';
				}
				if (winWidth >= 768 && winWidth <= 1024) {
					return 'compressed';
				}
				if (winWidth <= 767) {
					return 'oneCol';
				}
				// STOP MODIFYING HERE.
			}
			else {
				return winWidth;
			}
		};

		return  {
			init: init,
			onScroll: onScroll,
			resize: resize,
			//playTrack: playTrack,
			responsiveState: responsiveState
		}
	})();


	/* =============================================================
		TRACK MODULE

		Controls track ations:
		trackClick
		nextTrack
		previousTrack
		spaceBar
		playTrack
		pauseTrack
		routeURL
		showPlayer
		scrollToTrack
	================================================================ */
	var track = (function() {
			var init = function(e) {
			// init stuff
			}

			//variables for track functions
			var activeTrack, //the mp3 URL of active media
			    isPlaying = false,
			    trackPosition = 0;  // is there a track playing now? init false

			
			var trackClick = function(e) {  // trackClick happens when a user clicks a track item
				// it handles logic so we know what the user expect
				showPlayer();
				scrollToTrack(e);
				
				
				if ((e.attr("id") === activeTrack) && (isPlaying === true)) {  
					// active Track is currently the same as clicked and playing so we need to pause
					pauseTrack(e);
				} else {
					// otherwise  switch track
					playTrack(e);
				}
			}

			var nextTrack = function() {
				//console.log("hey there");
				nextTrackID = $('#' + activeTrack).next('.item').attr("id");
				if(nextTrackID == undefined) {
					console.log('this is the last song!');
					return false;
				}
				$nextTrackQueue = $('#' + nextTrackID);
				trackClick($nextTrackQueue);
			}

			var previousTrack = function() {
				//console.log("hey there");
				prevTrackID = $('#' + activeTrack).prev('.item').attr("id");
				if(prevTrackID == undefined) {
					console.log('this is the first song!');
					return false;
				}
				$prevTrackQueue = $('#' + prevTrackID);
				trackClick($prevTrackQueue);
			}

			var spaceBar = function() {
				//my_jPlayer.jPlayer("pause");
				if (isPlaying === true) {  
					pauseTrack();
					
				} else {
					my_jPlayer.jPlayer("play");
					isPlaying = true;
					$('.playpause').html('<i class="ss-pause"></i>');
					$('#' + activeTrack ).find('.item--play').html('<i class="ss-pause"></i>');
				}
			}

			var playTrack = function(e) {   // play the song
				
				newActiveTrackName = e.find('#track-details').text();
				my_trackName.text(e.find('#track-details').text());

				if (e.attr("id") === activeTrack) { // they want to play the active track, because its paused
					my_jPlayer.jPlayer("play");
					$('.playpause').html('<i class="ss-pause"></i>');
					$('#' + activeTrack ).find('.item--play').html('<i class="ss-pause"></i>'); //set current to pause
				} else {  // they're playing a new song 

					//update mp3 to new track
					my_jPlayer.jPlayer("setMedia", {
						m4a: e.data("mp3"),
						mp3: e.data("mp3")
					});

					// play actual music
					my_jPlayer.jPlayer("play");

					// set URL to current track and push to browser history
					routeURL(e.data("trackid"),e.data("artist"),e.data("track"));
					//console.log(e.data("trackid"));

					imageProperty = "url('"+e.data("image")+"')";
					//console.log(imageProperty);
					$('#player-image').css("background",imageProperty);

					// set UI to play state, so user has affordances
					$('#' + activeTrack ).find('.item--play').html('<i class="ss-play"></i>').removeClass('viewable'); //set current track to 'play'
					$newAffordanceButton = e.find('.item--play');
					$newAffordanceButton.html('<i class="ss-pause"></i>').addClass('viewable'); 
					$('.playpause').html('<i class="ss-pause"></i>');

					//update Share modal content
					console.log('test');
					console.log($('#shareText').text());
					facebookShareURL = "https://www.facebook.com/sharer/sharer.php?u=http://thebeardedbuffalo.com/track/"+e.data("trackid")+"&t=Check+out+this+track+I+found+on+The+Bearded+Buffalo";
					twitterShareURL = "https://twitter.com/share?url=http://thebeardedbuffalo.com/track/"+e.data("trackid")+"&via=bearded_buffalo&text=Check+out+this+track+I+found+on+The+Bearded+Buffalo";
					mailtoURL = "mailto:?body=http://thebeardedbuffalo.com/track/"+e.data("trackid")+"&subject=Check+out+this+track+I+found+on+The+Bearded+Buffalo"
					//copylinkContents = ""

					$('#shareText').text("How do you want to share this track by "+e.data("artist")+"?");
					$('#facebookShare').attr('href',facebookShareURL);
					$('#twitterShare').attr('href',twitterShareURL);
					$('#emailShare').attr('href',mailtoURL);
				}
				
				isPlaying = true;
				activeTrack = e.attr("id"); //set global variable to active track
				first_track = false;
				e.blur();
				return false;
			}

			var pauseTrack = function() { 
				//console.log("Pause me");
				my_jPlayer.jPlayer("pause");
				isPlaying = false;
				$('#' + activeTrack ).find('.item--play').html('<i class="ss-play"></i>');
				$('.playpause').html('<i class="ss-play"></i>');	
			}

			var routeURL = function(trackID,trackArtist,trackTitle) {
				history.pushState({id: trackID}, null, "/development/thebeardedbuffalo.com/track/"+trackID);
		    	document.title = trackTitle +" by "+ trackArtist + " | Bearded Buffalo";
			}

			
			var showPlayer = function() {
				if ($('#header-wrapper').hasClass('translateY-0px')) {  
					// no need to show sticky nav 
				} else {
					cs.uiMod.showStickyNav();
				}	
			}

			var scrollToTrack = function(e) {
				offset_Y = 0 - ($('#header-wrapper').height());	
				e.velocity("scroll", { duration:780, offset: offset_Y});  // smooth scroll to the song
			}

			return {
				init: init,
				showPlayer: showPlayer,
				spaceBar: spaceBar,
				scrollToTrack: scrollToTrack,
				nextTrack: nextTrack,
				previousTrack: previousTrack,
				pauseTrack: pauseTrack,
				trackClick: trackClick,
				utility: utility
			}
		})();

	var uiMod = (function() {

		var showStickyNav = function() {
			$( "#header-wrapper" ).addClass('translateY-0px');
		};	

		var showPlayerControls = function() {
			if($('#player-wrapper').hasClass('translateY-0px')) {
				return false;
			} else {
				$('#player-wrapper').addClass('translateY-0px');
			}
		}
		var toggleAbout = function() {
			if($('section#about').hasClass('offcanvas-visible')) {
				$('section#about').removeClass('offcanvas-visible');
			} else {
				$('section#about').addClass('offcanvas-visible');				
			}
		}

		var togglePanel = function(targetEl,$buttonEl) {
			
			// settings
			activeButtonClass = "button-active";
			$activeButtonEl = $(".header-menu-item." + activeButtonClass);
			$targetEl = $(targetEl);
			panelClass = "offCanvasPanel";
			visibleClass = "offcanvas-visible";
			$currentVisiblePanel = $('.' + panelClass + '.' + visibleClass);
			animationDelay = 500;

			console.log($activeButtonEl);

			// functionality
			if($targetEl.hasClass(visibleClass) === true) {  // close this panel
				$targetEl.removeClass(visibleClass);
				$buttonEl.removeClass(activeButtonClass);
			} else if($currentVisiblePanel.length > 0) {  // another panel is open
				$currentVisiblePanel.removeClass(visibleClass);
				$activeButtonEl.removeClass(activeButtonClass);
				$buttonEl.addClass(activeButtonClass);
				setTimeout(function(){
					$targetEl.addClass(visibleClass);
					
				},animationDelay);
			} else {  // no other panels are open
				$targetEl.addClass(visibleClass);
				$buttonEl.addClass(activeButtonClass);
			}
		}


		var toggleUpload = function() {
			if($('section#submit-track').hasClass('offcanvas-visible')) {
				$('section#submit-track').removeClass('offcanvas-visible');
			} else {
				$('section#submit-track').addClass('offcanvas-visible');				
			}
		}

		// public
		return {
			toggleAbout: toggleAbout,
			toggleUpload: toggleUpload,
			togglePanel: togglePanel,
			showPlayerControls: showPlayerControls,
			showStickyNav: showStickyNav
		};
	})(); // var uiMod = (function() {


	/* 
		User interaction 

		Various functions which are called when the user intearcts
		with a piece of the site (eg. clicking, scrolling, etc)
	*/
	var userInput = (function() {

		var example = function() { // Matches the height of various elements to other elements in ways that are impossible with CSS alone
			
		};

		// public
		return {
			example: example
		};

	})(); // var uiMod = (function() {

	

	// public
	return {
		utility: utility,
		uiMod: uiMod,
		userInput: userInput,
		track: track
	};
})(jQuery); // var cs = (function() {