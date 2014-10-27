<?php get_header(); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<header class="single-track-header">
	   <!--<div class="single-track-image-blur" style="background-image: url('<?php the_field('track_image'); ?>');"></div>-->
        <div class="single-track-wrapper">
        	<div class="pull-left">
        		<img src="<?php the_field('track_image'); ?>">
        	</div>
        	<div class="pull-left">
       		<h1><? the_title(); ?></h1>
          <?php wpfp_link() ?> 
       		</div>
       	</div>
    </header>
<?php endwhile; endif; ?>
<div class="page-title-row">
	<h4>Latest Tracks</h4>
	<hr>
</div>
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
</div>
<?php get_footer(); ?>