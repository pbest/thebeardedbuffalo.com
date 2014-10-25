jQuery(document).ready(function($) {
   cs.utility.init();

   $(window).resize(function(){ cs.utility.resize(); });
   $(window).scroll(function(){ cs.utility.onScroll(); });

   // JPLAYER STUFF, WE'll NEED TO PUT IN RIGHT PLACE SOON
   	// Local copy of jQuery selectors, for performance.


	// Create click handlers for the different tracks
	//$(".item").click(function(e) {
		//console.log($(this).data("mp3"));
		
	//});
	
	//END JPLAYER

});


/*
=============================================================================
	FUNCTION DECLARATIONS
=============================================================================
*/
/* GLOBAL VARIABLES */
	var	my_jPlayer = $("#jquery_jplayer"),
        my_trackName = $("#active-track-name"),
	    my_playState = $("#play-state"),
	    my_extraPlayInfo = $("#extra-play-info");

var cs = function($) {

	
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

			

			// FUNCTIONS
			
			initJPlayer();

			
			// REPEATING FUNCTIONS
			// var example = setInterval(function(){
			// 	// do stuff
			// }, 200);


			/*
				USER INTERACTION
			*/
			// Click events and whatnot.
			$('.about').click(function(){ cs.uiMod.toggleAbout(); });
			$('#close-about').click(function() { cs.uiMod.toggleAbout(); });
			$('.upload').click(function() { cs.uiMod.toggleUpload(); });
			

			$('.item').click(function() { cs.track.trackClick($(this)); });
			//$(".container").on("click", ".item", cs.track.trackClick($(this));

			$('.playpause').click(function() { cs.track.spaceBar(); });
			$('.skipforward').click(function() { cs.track.nextTrack(); });
			$('.skipback').click(function() { cs.track.previousTrack(); });

			
			

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
			//console.log('so am I!');
			// Change the time format
			$.jPlayer.timeFormat.padMin = false;
			$.jPlayer.timeFormat.padSec = false;
			//$.jPlayer.timeFormat.sepMin = " min ";
			//$.jPlayer.timeFormat.sepSec = " sec";
			$.jPlayer.keys(true); 
			// Initialize the play state text
			//my_playState.text(opt_text_selected);

			// Instance jPlayer
			$("#jquery_jplayer").jPlayer({
				ready: function () {
				//$(".track-default").click();
				console.log('player ready!');
				},
				timeupdate: function(event) {
					my_extraPlayInfo.text(parseInt(event.jPlayer.status.currentPercentAbsolute, 10) + "%");
				},
				play: function(event) {
					my_playState.text(opt_text_playing);
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
				cssSelectorAncestor: "#header-wrapper",
				supplied: "m4a,mp3",
				keys: true,	
				wmode: "window"
			});
		}

		var showNav = function() {
  			var scrollTop = $(window).scrollTop();
  		  	if (scrollTop > 70) {
      			$( "#header-wrapper" ).addClass('translateY-0px');
    		} else if (scrollTop == 0) {
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
	/* 
		UI Modifications 

		Various functions which operate on elements to achieve visual
		effects that are impossible to create with CSS alone.
	*/
	var track = (function() {
			var init = function(e) {
			// init stuff
			}

			//variables for track functions
			var activeTrack, //the mp3 URL of active media
			    isPlaying = false,
			    trackPosition = 0;  // is there a track playing now? init false

			
			var trackClick = function(el) {  // trackClick happens when a user clicks a track item
											// it handles logic so we know what the user expects
				//alert('!!! first function call!'); 
				//return false;
				//console.log(el);
				e = $('#' + el)
				showPlayer();
				scrollToTrack(e);
				
				
				if ((e.attr("id") === activeTrack) && (isPlaying === true)) {  
					// active Track is currently the same as clicked and playing so we need to pause
					pauseTrack(e);
					//console.log(e.attr("id"));
				} else {
					// otherwise  switch track
					playTrack(e);
					//console.log('play');

				}
				

				//console.log(event.jPlayer.status);
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
				console.log(e);
				newActiveTrackName = e.find('#track-details').text();
				my_trackName.text(e.find('#track-details').text());

				if (e.attr("id") === activeTrack) { // they want to play the active track, because its paused
					console.log('unpause');
					$("#jquery_jplayer").jPlayer("play");
					$('.playpause').html('<i class="ss-pause"></i>');
					$('#' + activeTrack ).find('.item--play').html('<i class="ss-pause"></i>'); //set current to pause

				} else {  // they're playing a new song 
					//console.log(activeTrack);
					$("#jquery_jplayer").jPlayer("setMedia", {
						m4a: e.data("mp3"),
						mp3: e.data("mp3")
					});
					my_jPlayer.jPlayer("play");
					$('#' + activeTrack ).find('.item--play').html('<i class="ss-play"></i>').removeClass('viewable'); //set current track to 'play'
					$newAffordanceButton = e.find('.item--play');
					$newAffordanceButton.html('<i class="ss-pause"></i>').addClass('viewable'); 
					//$newAffordanceButton.css('opacity','1');
					$('.playpause').html('<i class="ss-pause"></i>');
				}
				
				isPlaying = true;
				//e.find('.item--play').html('<i class="ss-icon">pause</i>');
				activeTrack = e.attr("id"); //set global variable to active track
				
				//}
				
				first_track = false;
				e.blur();
				return false;
			}

			var pauseTrack = function() { 
				console.log("Pause me");
				my_jPlayer.jPlayer("pause");
				isPlaying = false;
				$('#' + activeTrack ).find('.item--play').html('<i class="ss-play"></i>');
				$('.playpause').html('<i class="ss-play"></i>');

				
			}

			var $active_track_details = $('#active-track-details');
			var showPlayer = function() {
				if ($('#header-wrapper').hasClass('translateY-0px')) {  
					// no need to show sticky nav 
				} else {
					cs.uiMod.showStickyNav();
				}
				$active_track_details.velocity({height:"30px"});
				$('#active-track-wrapper').velocity({opacity:1});			
			}

			var scrollToTrack = function(e) {
				if($active_track_details.height() == 30) {
					offset_Y = 0 - ($('#header-wrapper').height());
				} else {
					offset_Y = -30 - ($('#header-wrapper').height());
				}	

				//console.log(offset_Y);
				e.velocity("scroll", { duration:780, offset: offset_Y});  // smooth scroll to the song
				//console.log(offset_Y);
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
			}
		})();

	var uiMod = (function() {

		var showStickyNav = function() {
			$( "#header-wrapper" ).addClass('translateY-0px');
		};	

		var toggleAbout = function() {
			if($('section#about').hasClass('offcanvas-visible')) {
				$('section#about').removeClass('offcanvas-visible');
			} else {
				$('section#about').addClass('offcanvas-visible');				
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
		track: track, 
		//cs: cs
	};
}(jQuery); // var cs = (function() {



