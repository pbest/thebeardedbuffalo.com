<?php get_header(); ?>

    <?php if (!(current_user_can('level_0'))){ ?>
    <header class="branding">
        <div class="branding-wrapper">
            <img src="<?php echo get_template_directory_uri(); ?>/img/logo-text.png" /><Br>
            <h1>Discover new tracks reviewed in five words</h1>
            <a class="button button-primary" href="#" id="call-to-action"><i class="ss-icon">play</i> Listen Now</a>
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
    <? } ?>

		<div class="container" id="allItems">

    
 <?php
$catname = wp_title('', false);
$wp_query = new WP_Query();
$wp_query->query('showposts=12'.'&paged='.$paged);
?>

<?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
      <div class="item" id="<?php echo($post->post_name) ?>" data-trackID="<?php echo($post->ID) ?>" data-mp3="<? the_field('track_mp3'); ?>" data-artist="<? the_field('artist_name'); ?>" data-track="<? the_field('track_name'); ?>" data-image="<?php the_field('track_image'); ?>" style="background-image: url('<?php the_field('track_image'); ?>');">
      <div class="item-wrapper">
        <h3><? the_title(); ?></h3>
         <div class="details--wrapper">
           <div class="playbutton"><a class="item--play"><i class="ss-icon">play</i></a></div>
           <span class="item--songtitle"><i class="ss-icon">play</i> <span id="track-details"><em><? the_field('track_name'); ?></em> by <? the_field('artist_name'); ?></span></span>
           <? 
              $network = get_field('social_network');
              if($network == 'Instagram') {
                  $base_url = 'http://instagram.com/';
              } elseif ($network == 'Twitter') {
                  $base_url = 'http://twitter.com/'; 
              } 
           ?>
          <!-- // favorite post <?php wpfp_link() ?> -->
          <!-- <a class="item--submitter" href="<? echo $base_url; the_field('handle'); ?>">@<? the_field('handle'); ?></a>-->
         </div>
      </div>
     </div>
  

<?php endwhile; ?>

<?php get_template_part('nav', 'below'); ?>
  </div><!--/.container-->

  <!--<div class="row">
		<div class="button" id="loadMoreTracks" style="margin:0 auto; width:130px">Load More</div>
	</div>-->
	
	</div>
<?php get_footer(); ?>