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

// ========================
// SET UP OUR ANGULAR SCRIPTS
// =========================
function mytheme_enqueue_scripts() {
  // register AngularJS
  wp_register_script('angular-core', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.2.14/angular.js', array(), null, false);
 
  // register our app.js, which has a dependency on angular-core
  wp_register_script('angular-app', get_bloginfo('template_directory').'/app/app.js', array('angular-core'), null, false);
 
  // enqueue all scripts
  wp_enqueue_script('angular-core');
  wp_enqueue_script('angular-app');
 
  // we need to create a JavaScript variable to store our API endpoint...   
  wp_localize_script( 'angular-core', 'AppAPI', array( 'url' => get_bloginfo('wpurl').'/api/') ); // this is the API address of the JSON API plugin
  // ... and useful information such as the theme directory and website url
  wp_localize_script( 'angular-core', 'BlogInfo', array( 'url' => get_bloginfo('template_directory').'/', 'site' => get_bloginfo('wpurl')) );
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_scripts');
// ========================
// END REMOVE STYLE.CSS
// =========================

add_filter('json_api_encode', 'json_api_encode_acf');


function json_api_encode_acf($response) 
{
    if (isset($response['posts'])) {
        foreach ($response['posts'] as $post) {
            json_api_add_acf($post); // Add specs to each post
        }
    } 
    else if (isset($response['post'])) {
        json_api_add_acf($response['post']); // Add a specs property
    }

    return $response;
}

function json_api_add_acf(&$post) 
{
    $post->acf = get_fields($post->id);
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