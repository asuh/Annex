<?php
/**
 * Twenty Eleven Theme Options
 *
 * @package WordPress
 * @subpackage Annex
 * @since Annex 1.0
 */

/**
 * Properly enqueue styles and scripts for our theme options page.
 *
 * This function is attached to the admin_enqueue_scripts action hook.
 *
 * @since Annex 1.0
 *
 */
function annex_admin_enqueue_scripts( $hook_suffix ) {
	wp_enqueue_style( 'annex-theme-options', get_template_directory_uri() . '/inc/theme-options.css', false, '2011-04-28' );
}
add_action( 'admin_print_styles-appearance_page_theme_options', 'annex_admin_enqueue_scripts' );

/**
 * Register the form setting for our annex_options array.
 *
 * This function is attached to the admin_init action hook.
 *
 * This call to register_setting() registers a validation callback, annex_theme_options_validate(),
 * which is used when the option is saved, to ensure that our option values are complete, properly
 * formatted, and safe.
 *
 * We also use this function to add our theme option if it doesn't already exist.
 *
 * @since Annex 1.0
 */
function annex_theme_options_init() {

	// If we have no options in the database, let's add them now.
	if ( false === annex_get_theme_options() )
		add_option( 'annex_theme_options', annex_get_default_theme_options() );

	register_setting(
		'annex_options',       // Options group, see settings_fields() call in theme_options_render_page()
		'annex_theme_options', // Database option, see annex_get_theme_options()
		'annex_theme_options_validate' // The sanitization callback, see annex_theme_options_validate()
	);
}
add_action( 'admin_init', 'annex_theme_options_init' );

/**
 * Change the capability required to save the 'annex_options' options group.
 *
 * @see annex_theme_options_init() First parameter to register_setting() is the name of the options group.
 * @see annex_theme_options_add_page() The edit_theme_options capability is used for viewing the page.
 *
 * By default, the options groups for all registered settings require the manage_options capability.
 * This filter is required to change our theme options page to edit_theme_options instead.
 * By default, only administrators have either of these capabilities, but the desire here is
 * to allow for finer-grained control for roles and users.
 *
 * @param string $capability The capability used for the page, which is manage_options by default.
 * @return string The capability to actually use.
 */
function annex_option_page_capability( $capability ) {
	return 'edit_theme_options';
}
add_filter( 'option_page_capability_annex_options', 'annex_option_page_capability' );

/**
 * Add our theme options page to the admin menu, including some help documentation.
 *
 * This function is attached to the admin_menu action hook.
 *
 * @since Annex 1.0
 */
function annex_theme_options_add_page() {
	$theme_page = add_theme_page(
		__( 'Theme Layout', 'annex' ),    // Name of page
		__( 'Theme Layout', 'annex' ),    // Label in menu
		'edit_theme_options',             // Capability required
		'theme_options',                  // Menu slug, used to uniquely identify the page
		'annex_theme_options_render_page' // Function that renders the options page
	);

	if ( ! $theme_page )
		return;

	$help = '<p>' . __( 'Some themes provide customization options that are grouped together on a Theme Options screen. If you change themes, options may change or disappear, as they are theme-specific. Your current theme, Annex, provides the following Theme Option:', 'annex' ) . '</p>' .
			'<ol>' .
				'<li>' . __( '<strong>Default Layout</strong>: You can choose if you want your site&#8217;s default layout to have a sidebar on the left, the right, or not at all.', 'annex' ) . '</li>' .
			'</ol>' .
			'<p>' . __( 'Remember to click "Save Changes" to save any changes you have made to the theme options.', 'annex' ) . '</p>' .
			'<p><strong>' . __( 'For more information:', 'annex' ) . '</strong></p>' .
			'<p>' . __( '<a href="http://codex.wordpress.org/Appearance_Theme_Options_Screen" target="_blank">Documentation on Theme Options</a>', 'annex' ) . '</p>' .
			'<p>' . __( '<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>', 'annex' ) . '</p>';

	add_contextual_help( $theme_page, $help );
}
add_action( 'admin_menu', 'annex_theme_options_add_page' );

/**
 * Returns an array of layout options registered for Twenty Eleven.
 *
 * @since Annex 1.0
 */
function annex_layouts() {
	$layout_options = array(
		'content-sidebar' => array(
			'value' => 'content-sidebar',
			'label' => __( 'Content on left', 'annex' ),
			'thumbnail' => get_template_directory_uri() . '/inc/images/content-sidebar.png',
		),
		'sidebar-content' => array(
			'value' => 'sidebar-content',
			'label' => __( 'Content on right', 'annex' ),
			'thumbnail' => get_template_directory_uri() . '/inc/images/sidebar-content.png',
		),
		'content' => array(
			'value' => 'content',
			'label' => __( 'One-column, no sidebar', 'annex' ),
			'thumbnail' => get_template_directory_uri() . '/inc/images/content.png',
		),
	);

	return apply_filters( 'annex_layouts', $layout_options );
}

/**
 * Returns the default options for Twenty Eleven.
 *
 * @since Annex 1.0
 */
function annex_get_default_theme_options() {
	$default_theme_options = array(
		'theme_layout' => 'content-sidebar',
	);

	if ( is_rtl() )
 		$default_theme_options['theme_layout'] = 'sidebar-content';

	return apply_filters( 'annex_default_theme_options', $default_theme_options );
}

/**
 * Returns the options array for Twenty Eleven.
 *
 * @since Annex 1.0
 */
function annex_get_theme_options() {
	return get_option( 'annex_theme_options', annex_get_default_theme_options() );
}

/**
 * Returns the options array for Twenty Eleven.
 *
 * @since Twenty Eleven 1.2
 */
function annex_theme_options_render_page() {
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php printf( __( '%s Theme Layout', 'annex' ), get_current_theme() ); ?></h2>
		<?php settings_errors(); ?>

		<form method="post" action="options.php">
			<?php
				settings_fields( 'annex_options' );
				$options = annex_get_theme_options();
				$default_options = annex_get_default_theme_options();
			?>

			<table class="form-table">
				<tr valign="top" class="image-radio-option theme-layout"><th scope="row"><?php _e( 'Default Layout', 'annex' ); ?></th>
					<td>
						<fieldset><legend class="screen-reader-text"><span><?php _e( 'Color Scheme', 'annex' ); ?></span></legend>
						<?php
							foreach ( annex_layouts() as $layout ) {
								?>
								<div class="layout">
								<label class="description">
									<input type="radio" name="annex_theme_options[theme_layout]" value="<?php echo esc_attr( $layout['value'] ); ?>" <?php checked( $options['theme_layout'], $layout['value'] ); ?> />
									<span>
										<img src="<?php echo esc_url( $layout['thumbnail'] ); ?>" width="136" height="122" alt="" />
										<?php echo $layout['label']; ?>
									</span>
								</label>
								</div>
								<?php
							}
						?>
						</fieldset>
					</td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Sanitize and validate form input. Accepts an array, return a sanitized array.
 *
 * @see annex_theme_options_init()
 * @todo set up Reset Options action
 *
 * @since Annex 1.0
 */
function annex_theme_options_validate( $input ) {
	$output = $defaults = annex_get_default_theme_options();

	// Theme layout must be in our array of theme layout options
	if ( isset( $input['theme_layout'] ) && array_key_exists( $input['theme_layout'], annex_layouts() ) )
		$output['theme_layout'] = $input['theme_layout'];

	return apply_filters( 'annex_theme_options_validate', $output, $input, $defaults );
}

/**
 * Adds Twenty Eleven layout classes to the array of body classes.
 *
 * @since Annex 1.0
 */
function annex_layout_classes( $existing_classes ) {
	$options = annex_get_theme_options();
	$current_layout = $options['theme_layout'];

	if ( in_array( $current_layout, array( 'content-sidebar', 'sidebar-content' ) ) )
		$classes = array( 'two-column' );
	else
		$classes = array( 'one-column' );

	if ( 'content-sidebar' == $current_layout )
		$classes[] = 'right-sidebar';
	elseif ( 'sidebar-content' == $current_layout )
		$classes[] = 'left-sidebar';
	else
		$classes[] = $current_layout;

	$classes = apply_filters( 'annex_layout_classes', $classes, $current_layout );

	return array_merge( $existing_classes, $classes );
}
add_filter( 'body_class', 'annex_layout_classes' );