<?php
/**
 * Annex functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, annex_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'annex_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage Annex
 * @since Twenty Eleven 1.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 584;

/**
 * Tell WordPress to run annex_setup() when the 'after_setup_theme' hook is run.
 */
add_action( 'after_setup_theme', 'annex_setup' );

if ( ! function_exists( 'annex_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override annex_setup() in a child theme, add your own annex_setup to your child theme's
 * functions.php file.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To style the visual editor.
 * @uses add_theme_support() To add support for post thumbnails, automatic feed links, and Post Formats.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses add_custom_background() To add support for a custom background.
 * @uses add_custom_image_header() To add support for a custom header.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Twenty Eleven 1.0
 */
function annex_setup() {

	/* Make Annex available for translation.
	 * Translations can be added to the /languages/ directory.
	 * If you're building a theme based on Annex, use a find and replace
	 * to change 'annex' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'annex', TEMPLATEPATH . '/languages' );

	$locale = get_locale();
	$locale_file = TEMPLATEPATH . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Load up our theme options page and related code.
	require( dirname( __FILE__ ) . '/inc/theme-options.php' );

	// Grab Annex's Ephemera widget.
	require( dirname( __FILE__ ) . '/inc/widgets.php' );

	// Add default posts and comments RSS feed links to <head>.
	add_theme_support( 'automatic-feed-links' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menu( 'primary', __( 'Primary Menu', 'annex' ) );

	// Add support for a variety of post formats
	add_theme_support( 'post-formats', array( 'aside', 'link', 'gallery', 'status', 'quote', 'image' ) );

	// Add support for custom backgrounds
	add_custom_background();

	// This theme uses Featured Images (also known as post thumbnails) for per-post/per-page Custom Header images
	add_theme_support( 'post-thumbnails' );

	// The next four constants set how Annex supports custom headers.

	// The default header text color
	define( 'HEADER_TEXTCOLOR', '000' );

	// By leaving empty, we allow for random image rotation.
	define( 'HEADER_IMAGE', '' );

	// The height and width of your custom header.
	// Add a filter to annex_header_image_width and annex_header_image_height to change these values.
	define( 'HEADER_IMAGE_WIDTH', apply_filters( 'annex_header_image_width', 1000 ) );
	define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'annex_header_image_height', 288 ) );

	// We'll be using post thumbnails for custom header images on posts and pages.
	// We want them to be the size of the header image that we just defined
	// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
	set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );

	// Add Annex's custom image sizes
	add_image_size( 'large-feature', HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true ); // Used for large feature (header) images
	add_image_size( 'small-feature', 500, 300 ); // Used for featured posts if a large-feature doesn't exist

	// Turn on random header image rotation by default.
	add_theme_support( 'custom-header', array( 'random-default' => true ) );

	// Add a way for the custom header to be styled in the admin panel that controls
	// custom headers. See annex_admin_header_style(), below.
	add_custom_image_header( 'annex_header_style', 'annex_admin_header_style', 'annex_admin_header_image' );

	// ... and thus ends the changeable header business.

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( array(
		'annex' => array(
			'url' => '%s/images/headers/annex.jpg',
			'thumbnail_url' => '%s/images/headers/annex-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Wheel', 'annex' )
		)
	) );
}
endif; // annex_setup

if ( ! function_exists( 'annex_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @since Twenty Eleven 1.0
 */
function annex_header_style() {

	// If no custom options for text are set, let's bail
	// get_header_textcolor() options: HEADER_TEXTCOLOR is default, hide text (returns 'blank') or any hex value
	if ( HEADER_TEXTCOLOR == get_header_textcolor() )
		return;
	// If we get this far, we have custom styles. Let's do this.
	?>
	<style type="text/css">
	<?php
		// Has the text been hidden?
		if ( 'blank' == get_header_textcolor() ) :
	?>
		#site-title,
		#site-description {
			position: absolute !important;
			clip: rect(1px 1px 1px 1px); /* IE6, IE7 */
			clip: rect(1px, 1px, 1px, 1px);
		}
	<?php
		// If the user has set a custom color for the text use that
		else :
	?>
		#site-title a,
		#site-description {
			color: #<?php echo get_header_textcolor(); ?> !important;
		}
	<?php endif; ?>
	</style>
	<?php
}
endif; // annex_header_style

if ( ! function_exists( 'annex_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header() in annex_setup().
 *
 * @since Twenty Eleven 1.0
 */
function annex_admin_header_style() {
?>
	<style type="text/css">
	.appearance_page_custom-header #headimg {
		border: none;
	}
	#headimg h1,
	#desc {
		font-family: "Helvetica Neue", Arial, Helvetica, "Nimbus Sans L", sans-serif;
	}
	#headimg h1 {
		margin: 0;
	}
	#headimg h1 a {
		font-size: 32px;
		line-height: 36px;
		text-decoration: none;
	}
	#desc {
		font-size: 14px;
		line-height: 23px;
		padding: 0 0 3em;
	}
	<?php
		// If the user has set a custom color for the text use that
		if ( get_header_textcolor() != HEADER_TEXTCOLOR ) :
	?>
		#site-title a,
		#site-description {
			color: #<?php echo get_header_textcolor(); ?>;
		}
	<?php endif; ?>
	#headimg img {
		max-width: 1000px;
		height: auto;
		width: 100%;
	}
	</style>
<?php
}
endif; // annex_admin_header_style

if ( ! function_exists( 'annex_admin_header_image' ) ) :
/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header() in annex_setup().
 *
 * @since Twenty Eleven 1.0
 */
function annex_admin_header_image() { ?>
	<div id="headimg">
		<?php
		if ( 'blank' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) || '' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) )
			$style = ' style="display:none;"';
		else
			$style = ' style="color:#' . get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) . ';"';
		?>
		<h1><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<div id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
		<?php $header_image = get_header_image();
		if ( ! empty( $header_image ) ) : ?>
			<img src="<?php echo esc_url( $header_image ); ?>" alt="" />
		<?php endif; ?>
	</div>
<?php }
endif; // annex_admin_header_image

/**
 * Sets the post excerpt length to 40 words.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 */
function annex_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'annex_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 */
function annex_continue_reading_link() {
	return ' <a href="'. esc_url( get_permalink() ) . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'annex' ) . '</a>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and annex_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 */
function annex_auto_excerpt_more( $more ) {
	return ' &hellip;' . annex_continue_reading_link();
}
add_filter( 'excerpt_more', 'annex_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 */
function annex_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= annex_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'annex_custom_excerpt_more' );

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 */
function annex_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'annex_page_menu_args' );

/**
 * Register our sidebars and widgetized areas. Also register the default Epherma widget.
 *
 * @since Twenty Eleven 1.0
 */
function annex_widgets_init() {

	register_widget( 'Twenty_Eleven_Ephemera_Widget' );

	register_sidebar( array(
		'name' => __( 'Main Sidebar', 'annex' ),
		'id' => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Showcase Sidebar', 'annex' ),
		'id' => 'sidebar-2',
		'description' => __( 'The sidebar for the optional Showcase Template', 'annex' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Area One', 'annex' ),
		'id' => 'sidebar-3',
		'description' => __( 'An optional widget area for your site footer', 'annex' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Area Two', 'annex' ),
		'id' => 'sidebar-4',
		'description' => __( 'An optional widget area for your site footer', 'annex' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Area Three', 'annex' ),
		'id' => 'sidebar-5',
		'description' => __( 'An optional widget area for your site footer', 'annex' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
add_action( 'widgets_init', 'annex_widgets_init' );

/**
 * Display navigation to next/previous pages when applicable
 */
function annex_content_nav( $nav_id ) {
	global $wp_query;

	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo $nav_id; ?>">
			<h3 class="assistive-text"><?php _e( 'Post navigation', 'annex' ); ?></h3>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'annex' ) ); ?></div>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'annex' ) ); ?></div>
		</nav><!-- #nav-above -->
	<?php endif;
}

/**
 * Return the URL for the first link found in the post content.
 *
 * @since Twenty Eleven 1.0
 * @return string|bool URL or false when no link is present.
 */
function annex_url_grabber() {
	if ( ! preg_match( '/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', get_the_content(), $matches ) )
		return false;

	return esc_url_raw( $matches[1] );
}

/**
 * Count the number of footer sidebars to enable dynamic classes for the footer
 */
function annex_footer_sidebar_class() {
	$count = 0;

	if ( is_active_sidebar( 'sidebar-3' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-4' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-5' ) )
		$count++;

	$class = '';

	switch ( $count ) {
		case '1':
			$class = 'one';
			break;
		case '2':
			$class = 'two';
			break;
		case '3':
			$class = 'three';
			break;
	}

	if ( $class )
		echo 'class="' . $class . '"';
}

if ( ! function_exists( 'annex_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own annex_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Eleven 1.0
 */
function annex_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'annex' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'annex' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					<?php
						$avatar_size = 68;
						if ( '0' != $comment->comment_parent )
							$avatar_size = 39;

						echo get_avatar( $comment, $avatar_size );

						/* translators: 1: comment author, 2: date and time */
						printf( __( '%1$s on %2$s <span class="says">said:</span>', 'annex' ),
							sprintf( '<span class="fn">%s</span>', get_comment_author_link() ),
							sprintf( '<a href="%1$s"><time pubdate datetime="%2$s">%3$s</time></a>',
								esc_url( get_comment_link( $comment->comment_ID ) ),
								get_comment_time( 'c' ),
								/* translators: 1: date, 2: time */
								sprintf( __( '%1$s at %2$s', 'annex' ), get_comment_date(), get_comment_time() )
							)
						);
					?>

					<?php edit_comment_link( __( 'Edit', 'annex' ), '<span class="edit-link">', '</span>' ); ?>
				</div><!-- .comment-author .vcard -->

				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'annex' ); ?></em>
					<br />
				<?php endif; ?>

			</footer>

			<div class="comment-content"><?php comment_text(); ?></div>

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply <span>&darr;</span>', 'annex' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->

	<?php
			break;
	endswitch;
}
endif; // ends check for annex_comment()

if ( ! function_exists( 'annex_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 * Create your own annex_posted_on to override in a child theme
 *
 * @since Twenty Eleven 1.0
 */
function annex_posted_on() {
	printf( __( '<span class="sep">Posted on </span><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a><span class="by-author"> <span class="sep"> by </span> <span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'annex' ),
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		sprintf( esc_attr__( 'View all posts by %s', 'annex' ), get_the_author() ),
		esc_html( get_the_author() )
	);
}
endif;

/**
 * Adds two classes to the array of body classes.
 * The first is if the site has only had one author with published posts.
 * The second is if a singular post being displayed
 *
 * @since Twenty Eleven 1.0
 */
function annex_body_classes( $classes ) {

	if ( ! is_multi_author() ) {
		$classes[] = 'single-author';
	}

	if ( is_singular() && ! is_home() && ! is_page_template( 'showcase.php' ) && ! is_page_template( 'sidebar-page.php' ) )
		$classes[] = 'singular';

	return $classes;
}
add_filter( 'body_class', 'annex_body_classes' );





/**
* Annex additional scripts
*/

// http://digwp.com/2010/03/wordpress-functions-php-template-custom-functions/ 
// enable threaded comments
function enable_threaded_comments(){
	if (!is_admin()) {
		if (is_singular() AND comments_open() AND (get_option('thread_comments') == 1))
			wp_enqueue_script('comment-reply');
		}
}
add_action('get_header', 'enable_threaded_comments');

// remove junk from head
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

// kill the admin nag
if (!current_user_can('edit_users')) {
	add_action('init', create_function('$a', "remove_action('init', 'wp_version_check');"), 2);
	add_filter('pre_option_update_core', create_function('$a', "return null;"));
}

// remove version info from head and feeds
// http://digwp.com/2010/04/wordpress-custom-functions-php-template-part-2/
function complete_version_removal() {
	return '';
}
add_filter('the_generator', 'complete_version_removal');

// remove login errors
// http://tutzone.net/2011/02/how-to-hide-login-errors-in-wordpress.html
add_filter('login_errors', create_function('$a', "return null;"));

// add to robots.txt
// http://codex.wordpress.org/Search_Engine_Optimization_for_WordPress#Robots.txt_Optimization
add_action('do_robots', 'annex_robots');
function annex_robots() {
	echo "Disallow: /cgi-bin\n";
	echo "Disallow: /wp-admin\n";
	echo "Disallow: /wp-includes\n";
	echo "Disallow: /wp-content/plugins\n";
	echo "Disallow: /plugins\n";
	echo "Disallow: /wp-content/cache\n";
	echo "Disallow: /wp-content/themes\n";
	echo "Disallow: /trackback\n";
	echo "Disallow: /feed\n";
	echo "Disallow: /comments\n";
	echo "Disallow: /category/*/*\n";
	echo "Disallow: */trackback\n";
	echo "Disallow: */feed\n";
	echo "Disallow: */comments\n";
	echo "Disallow: /*?*\n";
	echo "Disallow: /*?\n";
	echo "Allow: /wp-content/uploads\n";
	echo "Allow: /assets";
}

// vCard generator widget
class annex_vcard extends WP_Widget {

	function annex_vcard() {
		$widget_ops = array('description' => 'Display a vCard');
		parent::WP_Widget(false, __('vCard', 'annex'), $widget_ops);      
	}
   
	function widget($args, $instance) {  
		extract($args);
		$title = $instance['title'];
		$street_address = $instance['street_address'];
		$locality = $instance['locality'];
		$region = $instance['region'];
		$postal_code = $instance['postal_code'];
		$country = $instance['country'];
		$tel = $instance['tel'];
		$fax = $instance['fax'];
		$email = $instance['email'];
	?>
		<?php echo $before_widget; ?>
		<?php if ($title) echo $before_title . $title . $after_title; ?>  
		<p class="vcard">
			<a class="fn org url" href="<?php echo home_url('/'); ?>"><?php bloginfo('name'); ?></a>
			<div class="adr">
				<?php if ($street_address) { ?><div class="street-address"><?php echo $street_address; ?></div><?php } ?>
				<?php if ($locality) { ?><span class="locality"><?php echo $locality; ?></span>,<?php } ?>
				<?php if ($region) { ?><span class="region"><?php echo $region; ?></span>&nbsp;<?php } ?>
				<?php if ($postal_code) { ?><span class="postal-code"><?php echo $postal_code; ?></span><?php } ?>
				<?php if ($country) { ?><div class="country-name"><?php echo $country; ?></div><?php } ?>
			</div>
			<?php if ($tel) { ?><div class="tel"><span class="type">Office: </span><span class="value"><?php echo $tel; ?></span></div><?php } ?>
			<?php if ($fax) { ?><div class="fax"><span class="type">Fax: </span><span class="value"><?php echo $fax; ?></span></div><?php } ?>
			<?php if ($email) { ?><a class="email" href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a><?php } ?>
		</p>        
        
        <?php echo $after_widget; ?>
        
	<?php
	}
	
	function update($new_instance, $old_instance) {                
		return $new_instance;
	}

	function form($instance) {
		if (isset($instance['title'])) { $title = esc_attr($instance['title']); } else { $title = ''; }
		if (isset($instance['street_address'])) { $street_address = esc_attr($instance['street_address']); } else { $street_address = ''; }
		if (isset($instance['locality'])) { $locality = esc_attr($instance['locality']); } else { $locality = ''; }
		if (isset($instance['region'])) { $region = esc_attr($instance['region']); } else { $region = ''; }
		if (isset($instance['postal_code'])) { $postal_code = esc_attr($instance['postal_code']); } else { $postal_code = ''; }
		if (isset($instance['country'])) { $country = esc_attr($instance['country']); } else { $country = ''; }
		if (isset($instance['tel'])) { $tel = esc_attr($instance['tel']); } else { $tel = ''; }
		if (isset($instance['email'])) { $email = esc_attr($instance['email']); } else { $email = ''; }
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title (optional):', 'annex'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
		</p>       
		<p>
			<label for="<?php echo $this->get_field_id('street_address'); ?>"><?php _e('Street Address:', 'annex'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('street_address'); ?>" value="<?php echo $street_address; ?>" class="widefat" id="<?php echo $this->get_field_id('street_address'); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('locality'); ?>"><?php _e('City/Locality:', 'annex'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('locality'); ?>" value="<?php echo $locality; ?>" class="widefat" id="<?php echo $this->get_field_id('locality'); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('region'); ?>"><?php _e('State/Region:', 'annex'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('region'); ?>" value="<?php echo $region; ?>" class="widefat" id="<?php echo $this->get_field_id('region'); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('postal_code'); ?>"><?php _e('Zipcode/Postal Code:', 'annex'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('postal_code'); ?>" value="<?php echo $postal_code; ?>" class="widefat" id="<?php echo $this->get_field_id('postal_code'); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('country'); ?>"><?php _e('Country:', 'annex'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('country'); ?>" value="<?php echo $country; ?>" class="widefat" id="<?php echo $this->get_field_id('country'); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('tel'); ?>"><?php _e('Telephone:', 'annex'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('tel'); ?>" value="<?php echo $tel; ?>" class="widefat" id="<?php echo $this->get_field_id('tel'); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('fax'); ?>"><?php _e('Fax Number:', 'annex'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('fax'); ?>" value="<?php echo $fax; ?>" class="widefat" id="<?php echo $this->get_field_id('fax'); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('email'); ?>"><?php _e('Email:', 'annex'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('email'); ?>" value="<?php echo $email; ?>" class="widefat" id="<?php echo $this->get_field_id('email'); ?>" />
		</p>                                   
	<?php
	}
} 
register_widget('annex_vcard');

// custom admin login logo
function annex_custom_login_logo() {
	echo '<style type="text/css">
	h1 a { background-image: url('.get_bloginfo('template_directory').'/images/custom-login-logo.png) !important; }
	</style>';
}
add_action('login_head', 'annex_custom_login_logo');

// Add Annex Admin Panel
locate_template( 'annex-admin/admin-menu.php', true );

// Custom Hooks
function annex_credits() { do_action('annex_credits'); } //footer credit	
 
/**
* END Annex
*/
