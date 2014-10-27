<?php
add_action('after_setup_theme', 'blankslate_setup');
function blankslate_setup() {
	load_theme_textdomain('blankslate', get_template_directory() . '/languages');
	add_theme_support('automatic-feed-links');
	add_theme_support('post-thumbnails');
	global $content_width;
	if ( ! isset( $content_width ) ) $content_width = 640;
	register_nav_menus(
		array( 'main-menu' => __( 'Main Menu', 'blankslate' ) )
	);
}
add_action('wp_enqueue_scripts', 'blankslate_load_scripts');
function blankslate_load_scripts() {
	wp_enqueue_script('jquery');
}
add_action('comment_form_before', 'blankslate_enqueue_comment_reply_script');
function blankslate_enqueue_comment_reply_script() {
	if (get_option('thread_comments')) { wp_enqueue_script('comment-reply'); }
}
add_filter('the_title', 'blankslate_title');
function blankslate_title($title) {
	if ($title == '') {
		return '&rarr;';
	} else {
		return $title;
	}
}
add_filter('wp_title', 'blankslate_filter_wp_title');
function blankslate_filter_wp_title($title) {
	return $title . esc_attr(get_bloginfo('name'));
}
//add custom size for thumbnails
if ( function_exists( 'add_image_size' ) ) { 
	
	add_image_size( 'gallery-small', 500, 430, true ); //(cropped)
	
}

add_action('widgets_init', 'blankslate_widgets_init');
function blankslate_widgets_init() {
	register_sidebar( array (
		'name' => __('Sidebar Widget Area', 'blankslate'),
	'id' => 'primary-widget-area',
	'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
	'after_widget' => "</li>",
	'before_title' => '<h3 class="widget-title">',
	'after_title' => '</h3>',
	) );
}
function blankslate_custom_pings($comment) {
	$GLOBALS['comment'] = $comment;
?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>"><?php echo comment_author_link(); ?></li>
<?php 
}
add_filter('get_comments_number', 'blankslate_comments_number');
function blankslate_comments_number($count) {
	if (!is_admin()) {
	global $id;
	$comments_by_type = &separate_comments( get_comments( 'status=approve&post_id=' . $id ) );
	return count($comments_by_type['comment']);
} else {
	return $count;
}
}
?>
<?
/*************************
WEB REVENUE INFINITE SCROLLING
*************************/
/*
Function that will register and enqueue the infinite scolling's script to be used in the theme.
*/
function twentytwelve_script_infinite_scrolling(){
    wp_register_script(
        'infinite_scrolling',//name of script
        get_template_directory_uri().'/js/vendor/jquery.infinitescroll.min.js',//where the file is
        array('jquery'),//this script requires a jquery script
        null,//don't have a script version number
        true//script will de placed on footer
    );

    if(!is_singular()){ //only when we have more than 1 post
        //we'll load this script
        wp_enqueue_script('infinite_scrolling');        
    }
}


//Register our custom scripts!
add_action('wp_enqueue_scripts', 'twentytwelve_script_infinite_scrolling');

/*
Function that will set infinite scrolling to be displayed in the page.
*/
function set_infinite_scrolling(){

    if(!is_singular()){//again, only when we have more than 1 post
    //add js script below
    ?>    
        <script type="text/javascript">            
            /*
                This is the inifinite scrolling setting, you can modify this at your will
            */
            var inf_scrolling = {                
                /*
                    img: is the loading image path, add a nice gif loading icon there                    
                    msgText: the loading message                
                    finishedMsg: the finished loading message
                */
                loading:{
                    img: "<? echo get_template_directory_uri(); ?>/images/ajax-loading.gif",
                    msgText: "Loading next posts....",
                    finishedMsg: "Posts loaded!!",
                },

                /*Next item is set nextSelector. 
                NextSelector is the css class of page navigation.
                In our case is #nav-below .nav-previous a
                */
                "nextSelector":"#nav-below .nav-previous a",

                //navSelector is a css id of page navigation
                "navSelector":"#nav-below",

                //itemSelector is the div where post is displayed
                "itemSelector":".item",

                //contentSelector is the div where page content (posts) is displayed
                "contentSelector":"#allItems"
            };

            /*
                Last thing to do is configure contentSelector to infinite scroll,
                with a function jquery from infinite-scroll.min.js
            */
            jQuery(inf_scrolling.contentSelector).infinitescroll(inf_scrolling);
        </script>        
    <?
    }
}

/*
    we need to add this action on page's footer.
    100 is a priority number that correpond a later execution.
*/
add_action( 'wp_footer', 'set_infinite_scrolling',100 );
?>