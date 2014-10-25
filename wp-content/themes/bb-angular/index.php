<?php get_header(); ?>


			
      <div class="container" ng-controller="songFeed">
 
      <div class="item" ng-repeat="post in postdata" ng-click="playSong(post.slug)" id="{{post.slug}}" data-mp3="{{post.acf.track_mp3}}" style="background-image: url('{{post.acf.track_image}}');">
        <div class="item-wrapper">
           <h3>{{post.title}}</h3>
           <div class="details--wrapper">
              <div class="playbutton"><a class="item--play"><i class="ss-icon">play</i></a></div>
              <span class="item--songtitle"><i class="ss-icon">play</i> <span id="track-details"><em>{{post.acf.track_name}}</em> by {{post.acf.artist_name}}</span></span>
               <!-- <a class="item--submitter" href="<? echo $base_url; the_field('handle'); ?>">@<? the_field('handle'); ?></a>-->
           </div>
        </div>
      </div>


 	    
 	    
     </div>
     <div class="row">
		<div class="button" style="margin:0 auto; width:130px">Load More</div>
	</div>
	
	</div>
<?php get_footer(); ?>