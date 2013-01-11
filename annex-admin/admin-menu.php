<?php

/*
	Begin Annex Admin panel.
*/

/*	Define Annex URI */
	define('BP_THEME_URL', get_template_directory_uri());
	define('BP_CHILD_THEME_URL', get_stylesheet_directory_uri());

/*
	There are essentially 5 sections to this:
	1)	Add "Annex Admin" link to left-nav Admin Menu & callback function for clicking that menu link
	2)	Add Admin Page CSS if on the Admin Page
	3)	Add "Annex Admin" Page options
	4)	Create functions to add above elements to pages
	5)	Add Annex options to page as requested
*/

/*	1)	Add "Annex Admin" link to left-nav Admin Menu & callback function for clicking that menu link */

	//	Add option if in Admin Page
	if ( ! function_exists( 'create_annex_admin_page' ) ):
		function create_annex_admin_page() {
			add_theme_page('Annex Admin', 'Annex Admin', 'administrator', 'annex-admin', 'build_annex_admin_page');
		}
		add_action('admin_menu', 'create_annex_admin_page');
	endif; // create_annex_admin_page

	//	You get this if you click the left-column "Annex Admin" (added above)
	if ( ! function_exists( 'build_annex_admin_page' ) ):
		function build_annex_admin_page() {
		?>
			<div id="annex-options-wrap">
				<div class="icon32" id="icon-tools"><br /></div>
				<h2>Annex Admin</h2>
				<p>So, there's actually a tremendous amount going on here.  If you're not familiar with <a href="http://html5boilerplate.com/">HTML5 Boilerplate</a> or the <a href="http://wordpress.org/extend/themes/twentyeleven">Twenty Eleven theme</a> (upon which this theme is based) you should check them out.</p>
				<p>Choose below which options you want included in your site.</p>
				<p>The clumsiest part of this plug-in is dealing with the CSS files.  Check the <a href="<?php echo BP_THEME_URL ?>/readme.txt">Read Me file</a> for details on how I suggest handling them.</p>
				<form method="post" action="options.php" enctype="multipart/form-data">
					<?php settings_fields('plugin_options'); /* very last function on this page... */ ?>
					<?php do_settings_sections('annex-admin'); /* let's get started! */?>
					<p class="submit"><input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>"></p>
				</form>
			</div>
		<?php
		}
	endif; // build_annex_admin_page

/*	2)	Add Admin Page CSS if on the Admin Page */
	if ( ! function_exists( 'admin_register_head' ) ):
		function admin_register_head() {
			echo '<link rel="stylesheet" href="' .BP_THEME_URL. '/annex-admin/admin-style.css">'.PHP_EOL;
		}
		add_action('admin_head', 'admin_register_head');
	endif; // admin_register_head

/*	3)	Add "Annex Admin" Page options */
	//	Register form elements
	if ( ! function_exists( 'register_and_build_fields' ) ):
		function register_and_build_fields() {
			register_setting('plugin_options', 'plugin_options', 'validate_setting');
			add_settings_section('main_section', '', 'section_cb', 'annex-admin');
			add_settings_field('google_chrome', 'IE-edge / Google Chrome?:', 'google_chrome_setting', 'annex-admin', 'main_section');
			add_settings_field('google_verification', 'Google/Bing Verification?:', 'google_verification_setting', 'annex-admin', 'main_section');
			add_settings_field('viewport', '<em><abbr title="iPhone, iTouch, iPad...">iThings</abbr></em> use full zoom?:', 'viewport_setting', 'annex-admin', 'main_section');
			add_settings_field('favicon', 'Got Favicon?:', 'favicon_setting', 'annex-admin', 'main_section');
			add_settings_field('favicon_ithing', 'Got <em><abbr title="iPhone, iTouch, iPad...">iThing</abbr></em> Favicon?', 'favicon_ithing_setting', 'annex-admin', 'main_section');
			add_settings_field('modernizr_js', 'Modernizr JS?:', 'modernizr_js_setting', 'annex-admin', 'main_section');
			add_settings_field('respond_js', 'Respond JS?:', 'respond_js_setting', 'annex-admin', 'main_section');
			add_settings_field('jquery_js', 'jQuery JS?:', 'jquery_js_setting', 'annex-admin', 'main_section');
			add_settings_field('plugins_js', 'jQuery Plug-ins JS?:', 'plugins_js_setting', 'annex-admin', 'main_section');
			add_settings_field('site_js', 'Site-specific JS?:', 'site_js_setting', 'annex-admin', 'main_section');
			add_settings_field('chrome_frame', 'Chrome-Frame?:', 'chrome_frame_setting', 'annex-admin', 'main_section');
			add_settings_field('google_analytics_js', 'Google Analytics?:', 'google_analytics_js_setting', 'annex-admin', 'main_section');
			add_settings_field('cache_buster', 'Cache-Buster?:', 'cache_buster_setting', 'annex-admin', 'main_section');
			add_settings_field('footer_credit', 'Footer Credit?:', 'footer_credit_setting', 'annex-admin', 'main_section');
		}
		add_action('admin_init', 'register_and_build_fields');
	endif; // register_and_build_fields

	//	Add Admin Page validation
	if ( ! function_exists( 'validate_setting' ) ):
		function validate_setting($plugin_options) {
			$keys = array_keys($_FILES);
			$i = 0;
			foreach ( $_FILES as $image ) {
				// if a files was upload
				if ($image['size']) {
					// if it is an image
					if ( preg_match('/(jpg|jpeg|png|gif)$/', $image['type']) ) {
						$override = array('test_form' => false);
						// save the file, and store an array, containing its location in $file
						$file = wp_handle_upload( $image, $override );
						$plugin_options[$keys[$i]] = $file['url'];
					} else {
						// Not an image.
						$options = get_option('plugin_options');
						$plugin_options[$keys[$i]] = $options[$logo];
						// Die and let the user know that they made a mistake.
						wp_die('No image was uploaded.');
					}
				} else { // else, the user didn't upload a file, retain the image that's already on file.
					$options = get_option('plugin_options');
					$plugin_options[$keys[$i]] = $options[$keys[$i]];
				}
				$i++;
			}
			return $plugin_options;
		}
	endif; // validate_setting

	//	Add Admin Page options

	//	in case you need it...
	if ( ! function_exists( 'section_cb' ) ):
		function section_cb() {}
	endif; // section_cb

	//	callback fn for google_chrome
	if ( ! function_exists( 'google_chrome_setting' ) ):
		function google_chrome_setting() {
			$options = get_option('plugin_options');
			$checked = (isset($options['google_chrome']) && $options['google_chrome']) ? 'checked="checked" ' : '';
			echo '<input class="check-field" type="checkbox" name="plugin_options[google_chrome]" value="true" ' .$checked. '/>';
			echo '<p>Force the most-recent IE rendering engine or users with <a href="http://www.chromium.org/developers/how-tos/chrome-frame-getting-started">Google Chrome Frame</a> installed to see your site using Google Frame.</p>';
			echo '<p>Selecting this option will add the following code to the <code class="html">&lt;head&gt;</code> of your pages:</p>';
			echo '<code>&lt;meta http-equiv=<span>"X-UA-Compatible"</span> content=<span>"IE=edge,chrome=1"</span>&gt;</code>';
		}
	endif; // google_chrome_setting

	//	callback fn for google_verification
	if ( ! function_exists( 'google_verification_setting' ) ):
		function google_verification_setting() {
			$options = get_option('plugin_options');
			$checked = (isset($options['google_verification']) && $options['google_verification'] && $options['google_verification_account'] && $options['google_verification_account'] !== 'XXXXXXXXX...') ? 'checked="checked" ' : '';
			$bingchecked = (isset($options['bing_verification']) && $options['bing_verification'] && $options['bing_verification_account'] && $options['bing_verification_account'] !== 'XXXXXXXXX...') ? 'checked="checked" ' : '';
			$account = (isset($options['google_verification_account']) && $options['google_verification_account']) ? $options['google_verification_account'] : 'XXXXXXXXX...';
			$bingaccount = (isset($options['bing_verification_account']) && $options['bing_verification_account']) ? $options['bing_verification_account'] : 'XXXXXXXXX...';
			$msg = ($account === 'XXXXXXXXX...') ? ', where </code>XXXXXXXXX...</code> will be replaced with the code you insert above' : '';
			echo '<input class="check-field" type="checkbox" name="plugin_options[google_verification]" value="true" ' .$checked. '/>';
			echo '<p>Add <a href="http://www.google.com/support/webmasters/bin/answer.py?answer=35179">Google Verificaton</a> or <a href="https://onlinehelp.microsoft.com/en-US/bing/hh204501.aspx">Bing Verification</a> code to the <code class="html">&lt;head&gt;</code> of all your pages.</p>';
			echo '<p>To include Google Verification, select this option and include your Verification number here:<br />';
			echo '<input type="text" size="40" name="plugin_options[google_verification_account]" value="'.$account.'" onfocus="javascript:if(this.value===\'XXXXXXXXX...\'){this.select();}"></p>';
			echo '<p>Selecting this option will add the following code to the <code class="html">&lt;head&gt;</code> of your pages'.$msg.'</p>';
			echo '<code>&lt;meta name=<span>"google-site-verification"</span> content=<span>"'.$account.'"</span>&gt;</code>';
			echo '<input class="check-field" type="checkbox" name="plugin_options[bing_verification]" value="true" ' .$checked. '/>';
			echo '<p>To include Bing Verification, select this option and include your Verification number here:<br />';
			echo '<input type="text" size="40" name="plugin_options[bing_verification_account]" value="'.$bingaccount.'" onfocus="javascript:if(this.value===\'XXXXXXXXX...\'){this.select();}"></p>';
			echo '<p>Selecting this option will add the following code to the <code class="html">&lt;head&gt;</code> of your pages'.$msg.'</p>';
			echo '<code>&lt;meta name=<span>"msvalidate.01"</span> content=<span>"'.$bingaccount.'"</span>&gt;</code>';
		}
	endif; // google_verification_setting

	//	callback fn for viewport
	if ( ! function_exists( 'viewport_setting' ) ):
		function viewport_setting() {
			$options = get_option('plugin_options');
			$checked = (isset($options['viewport']) && $options['viewport']) ? 'checked="checked" ' : '';
			echo '<input class="check-field" type="checkbox" name="plugin_options[viewport]" value="true" ' .$checked. '/>';
			echo '<p>Force <em><abbr title="iPhone, iTouch, iPad...">iThings</abbr></em> to <a href="http://developer.apple.com/library/safari/#documentation/AppleApplications/Reference/SafariWebContent/UsingtheViewport/UsingtheViewport.html#//apple_ref/doc/uid/TP40006509-SW19">show site at full-zoom</a>, instead of trying to show the entire page.</p>';
			echo '<p>Selecting this option will add the following code to the <code class="html">&lt;head&gt;</code> of your pages:</p>';
			echo '<code>&lt;meta name=<span>"viewport"</span> content=<span>"width=device-width"</span>&gt;</code>';
		}
	endif; // viewport_setting

	//	callback fn for favicon
	if ( ! function_exists( 'favicon_setting' ) ):
		function favicon_setting() {
			$options = get_option('plugin_options');
			$checked = (isset($options['favicon']) && $options['favicon']) ? 'checked="checked" ' : '';
			echo '<input class="check-field" type="checkbox" name="plugin_options[favicon]" value="true" ' .$checked. '/>';
			echo '<p>If you plan to use a <a href="http://en.wikipedia.org/wiki/Favicon">favicon</a> for your site, place the "favicon.ico" file in the root directory of your site.</p>';
			echo '<p>If the file is in the right location, you don\'t really need to select this option, browsers will automatically look there and no additional code will be added to your pages.</p>';
			echo '<p>Selecting this option will add the following code to the <code class="html">&lt;head&gt;</code> of your pages:</p>';
			echo '<code>&lt;link rel=<span>"shortcut icon"</span> href=<span>"/favicon.ico"</span> /&gt;</code>';
		}
	endif; // favicon_setting

	//	callback fn for favicon_ithing
	if ( ! function_exists( 'favicon_ithing_setting' ) ):
		function favicon_ithing_setting() {
			$options = get_option('plugin_options');
			$checked = (isset($options['favicon_ithing']) && $options['favicon_ithing']) ? 'checked="checked" ' : '';
			echo '<input class="check-field" type="checkbox" name="plugin_options[favicon_ithing]" value="true" ' .$checked. '/>';
			echo '<p>To allow <em><abbr title="iPhone, iTouch, iPad...">iThing</abbr></em> users to <a href="http://developer.apple.com/library/safari/#documentation/AppleApplications/Reference/SafariWebContent/ConfiguringWebApplications/ConfiguringWebApplications.html">add an icon for your site to their Home screen</a>, place the "apple-touch-icon.png" file in the root directory of your site.</p>';
			echo '<p>If the file is in the right location, you don\'t really need to select this option, browsers will automatically look there and no additional code will be added to your pages.</p>';
			echo '<p>Based upon <a href="http://mathiasbynens.be/notes/touch-icons">this Touch Icons research</a>, the icon code will be added in the specific order seen below.';
			echo '<p>Selecting this option will add the following code to the <code class="html">&lt;head&gt;</code> of your pages:</p>';
			echo '<p><strong>(Be sure to relocate all the icons to the root directory!)</strong></p>';
			echo '<code>&lt;link rel=<span>"apple-touch-icon"</span> sizes=<span>"144x144"</span> href=<span>"/apple-touch-icon-144x144-precomposed.png"</span>&gt;</code>';
			echo '<code>&lt;link rel=<span>"apple-touch-icon"</span> sizes=<span>"114x114"</span> href=<span>"/apple-touch-icon-114x114-precomposed.png"</span>&gt;</code>';
			echo '<code>&lt;link rel=<span>"apple-touch-icon"</span> sizes=<span>"72x72"</span> href=<span>"/apple-touch-icon-72x72-precomposed.png"</span>&gt;</code>';
			echo '<code>&lt;link rel=<span>"apple-touch-icon"</span> sizes=<span>"57x57"</span> href=<span>"/apple-touch-icon-57x57-precomposed.png"</span>&gt;</code>';
			echo '<code>&lt;link rel=<span>"apple-touch-icon"</span> href=<span>"/apple-touch-icon-precomposed.png"</span>&gt;</code>';
			echo '<code>&lt;link rel=<span>"apple-touch-icon"</span> href=<span>"/apple-touch-icon.png"</span>&gt;</code>';
		}
	endif; // favicon_ithing_setting

	//	callback fn for modernizr_js
	if ( ! function_exists( 'modernizr_js_setting' ) ):
		function modernizr_js_setting() {
			$options = get_option('plugin_options');
			$checked = (isset($options['modernizr_js']) && $options['modernizr_js']) ? 'checked="checked" ' : '';
			echo '<input class="check-field" type="checkbox" name="plugin_options[modernizr_js]" value="true" ' .$checked. '/>';
			echo '<p><a href="http://modernizr.com/">Modernizr</a> is a JS library that appends classes to the <code class="html">&lt;html&gt;</code> that indicate whether the user\'s browser is capable of handling advanced CSS, like "cssreflections" or "no-cssreflections".  It\'s a really handy way to apply varying CSS techniques, depending on the user\'s browser\'s abilities, without resorting to CSS hacks.</p>';
			echo '<p>Selecting this option will add the following code to the <code class="html">&lt;head&gt;</code> of your pages (note the lack of a version, when you\'re ready to upgrade, simply copy/paste the new version into the file below, and your site is ready to go!):</p>';
			echo '<code><b>&lt;</b>script src=<span>"' .BP_THEME_URL. '/js/modernizr.js"</span><b>&gt;&lt;/</b>script<b>&gt;</b></code>';
			echo '<p><strong>Note: If you do <em>not</em> include Modernizr, the IEShiv JS <em>will</em> be added to accommodate the HTML5 elements used in Annex in weaker browsers:</strong></p>';
			echo '<code class="comment">&lt;!--[if lt IE 9]&gt;</code>';
			echo '<code class="comment">&lt;script src="//html5shiv.googlecode.com/svn/trunk/html5.js" onload="window.ieshiv=true;"&gt;&lt;/script&gt;</code>';
			echo '<code class="comment">	&lt;script&gt;!window.ieshiv && document.write(unescape(\'&lt;script src="' .BP_THEME_URL. '/js/ieshiv.js"&gt;&lt;/script&gt;\'))&lt;/script&gt;</code>';
			echo '<code class="comment">&lt;![endif]--&gt;</code>';
		}
	endif; // modernizr_js_setting

	//	callback fn for respond_js
	if ( ! function_exists( 'respond_js_setting' ) ):
		function respond_js_setting() {
			$options = get_option('plugin_options');
			$checked = (isset($options['respond_js']) && $options['respond_js']) ? 'checked="checked" ' : '';
			echo '<input class="check-field" type="checkbox" name="plugin_options[respond_js]" value="true" ' .$checked. '/>';
			echo '<p><a href="http://filamentgroup.com/lab/respondjs_fast_css3_media_queries_for_internet_explorer_6_8_and_more/">Respond.js</a> is a JS library that helps IE<=8 understand <code>@media</code> queries, specifically <code>min-width</code> and <code>max-width</code>, allowing you to more reliably implement <a href="http://www.alistapart.com/articles/responsive-web-design/">responsive design</a> across all browsers.</p>';
			echo '<p>Selecting this option will add the following code to the <code class="html">&lt;head&gt;</code> of your pages (note the lack of a version, when you\'re ready to upgrade, simply copy/paste the new version into the file below, and your site is ready to go!):</p>';
			echo '<code><b>&lt;</b>script src<b>=</b><span>"' .BP_THEME_URL. '/js/respond.js"</span><b>&gt;&lt;/</b>script<b>&gt;</b></code>';
		}
	endif; // respond_js_setting

	//	callback fn for jquery_js
	if ( ! function_exists( 'jquery_js_setting' ) ):
		function jquery_js_setting() {
			$options = get_option('plugin_options');
			$checked = (isset($options['jquery_js']) && $options['jquery_js']) ? 'checked="checked" ' : '';
			$version = (isset($options['jquery_version']) && $options['jquery_version'] && $options['jquery_version'] !== '') ? $options['jquery_version'] : '1.8.3';
			$inhead = (isset($options['jquery_head']) && $options['jquery_head']) ? 'checked="checked" ' : '';
			echo '<input class="check-field" type="checkbox" name="plugin_options[jquery_js]" value="true" ' .$checked. '/>';
			echo '<p><a href="http://jquery.com/">jQuery</a> is a JS library that aids greatly in developing high-quality JavaScript quickly and efficiently.</p>';
			echo '<p>Selecting this option will add the following code to your pages just before the <code class="html">&lt;/body&gt;</code>:</p>';
			echo '<code><b>&lt;</b>script src<b>=</b><span>"//ajax.googleapis.com/ajax/libs/jquery/'.$version.'/jquery.min.js"</span><b>&gt;&lt;/</b>script<b>&gt;</b></code>';
			echo '<code><b>&lt;</b>script<b>&gt;</b><span class="support">window</span>.<b>jQuery</b> <span class="html">||</span> <span class="support">document</span>.write<b>(</b>\'<span>&lt;script src="'.BP_THEME_URL.'/js/vendor/jquery.js"&gt;&lt;</span><b>\/</b><span>script&gt;\'</span><b>)&lt;/</b>script<b>&gt;</b></code>';
			echo '<p><input class="check-field" type="checkbox" name="plugin_options[jquery_head]" value="true" ' .$inhead. '/>';
			echo '<strong>Note: <a href="http://developer.yahoo.com/blogs/ydn/posts/2007/07/high_performanc_5/">Best-practices</a> recommend that you load JS as close to the <code class="html">&lt;/body&gt;</code> as possible.  If for some reason you would prefer jQuery and jQuery plug-ins to be in the <code class="html">&lt;head&gt;</code>, please select this option.</strong></p>';
			echo '<p>The above code first tries to download jQuery from Google\'s CDN (which might be available via the user\'s browser cache).  If this is not successful, it uses the theme\'s version.</p>';
			echo '<p><strong>Note: This plug-in tries to keep current with the most recent version of jQuery.  If for some reason you would prefer to use another version, please indicate that version:</strong><br />';
			echo '<input type="text" size="6" name="plugin_options[jquery_version]" value="'.$version.'"> (<a href="http://code.google.com/apis/libraries/devguide">see all versions available via Google\'s CDN</a>)</p>';
		}
	endif; // jquery_js_setting

	//	callback fn for plugins_js
	if ( ! function_exists( 'plugins_js_setting' ) ):
		function plugins_js_setting() {
			$options = get_option('plugin_options');
			$checked = (isset($options['plugins_js']) && $options['plugins_js']) ? 'checked="checked" ' : '';
			echo '<input class="check-field" type="checkbox" name="plugin_options[plugins_js]" value="true" ' .$checked. '/>';
			echo '<p>If you choose to use any <a href="http://plugins.jquery.com/">jQuery plug-ins</a>, I recommend downloading and concatenating them together in a single JS file, as below.  This will <a href="http://developer.yahoo.com/performance/rules.html">reduce your site\'s HTTP Requests</a>, making your site a better experience.</p>';
			echo '<p>Selecting this option will add the following code to your pages just before the <code class="html">&lt;/body&gt;</code>:</p>';
			echo '<code><b>&lt;</b>script src<b>=</b><span>\'' .BP_CHILD_THEME_URL. '/js/plugins.js?ver=x\'</span><b>&gt;&lt;</b>/script<b>&gt;</b></code>';
			echo '<p>(The single quotes and no-longer-necessary attributes are from WP, would like to fix that... maybe next update...)</p>';
			echo '<p><strong>Note: If you do <em>not</em> include jQuery, this file will <em>not</em> be added to the page.</strong></p>';
		}
	endif; // plugins_js_setting

	//	callback fn for site_js
	if ( ! function_exists( 'site_js_setting' ) ):
		function site_js_setting() {
			$options = get_option('plugin_options');
			$checked = (isset($options['site_js']) && $options['site_js']) ? 'checked="checked" ' : '';
			echo '<input class="check-field" type="checkbox" name="plugin_options[site_js]" value="true" ' .$checked. '/>';
			echo '<p>If you would like to add your own site JavaScript file, Annex provides a starter file located in:</p>';
			echo '<code><span>' .BP_THEME_URL. '/js/main.js</span></code>';
			echo '<p>Add what you want to that file and select this option.</p>';
			echo '<p>Selecting this option will add the following code to your pages just before the <code class="html">&lt;/body&gt;</code>:</p>';
			echo '<code><b>&lt;</b>script src<b>=</b><span>\'' .BP_CHILD_THEME_URL. '/js/main.js?ver=x\'</span><b>&gt;&lt;</b>/script<b>&gt;</b></code>';
			echo '<p>(The single quotes and no-longer-necessary attributes are from WP, would like to fix that... maybe next update...)</p>';
		}
	endif; // site_js_setting

	//	callback fn for chrome_frame
	if ( ! function_exists( 'chrome_frame_setting' ) ):
		function chrome_frame_setting() {
			$options = get_option('plugin_options');
			$checked = (isset($options['chrome_frame']) && $options['chrome_frame']) ? 'checked="checked" ' : '';
			echo '<input class="check-field" type="checkbox" name="plugin_options[chrome_frame]" value="true" ' .$checked. '/>';
			echo '<p>Prompt IE 6 users to upgrade or install <a href="http://chromium.org/developers/how-tos/chrome-frame-getting-started">Chrome Frame</a>.</p>';
			echo '<p>Selecting this option will add the following code just after the <code class="html">&lt;body&gt;</code>:</p>';
			echo '<code class="comment">&lt;!--[if lt IE 7]&gt;&lt;p class=chromeframe&gt;You are using an &lt;em&gt;outdated&lt;/em&gt; browser. Please &lt;a href="http://browsehappy.com/"&gt;upgrade your browser&lt;/a&gt; or &lt;a href="http://www.google.com/chromeframe/?redirect=true"&gt;activate Google Chrome Frame&lt;/a&gt; to improve your experience.&lt;/p&gt;&lt;![endif]--&gt;</code>';
		}
	endif; // chrome_frame_setting

	//	callback fn for google_analytics_js
	if ( ! function_exists( 'google_analytics_js_setting' ) ):
		function google_analytics_js_setting() {
			$options = get_option('plugin_options');
			$checked = (isset($options['google_analytics_js']) && $options['google_analytics_js'] && isset($options['google_analytics_account']) && $options['google_analytics_account'] && $options['google_analytics_account'] !== 'XXXXX-X') ? 'checked="checked" ' : '';
			$account = (isset($options['google_analytics_account']) && $options['google_analytics_account']) ? str_replace('UA-','',$options['google_analytics_account']) : 'XXXXX-X';
			$msg = ($account === 'XXXXX-X') ? ', where </code>XXXXX-X</code> will be replaced with the code you insert above' : '';
			echo '<input class="check-field" type="checkbox" name="plugin_options[google_analytics_js]" value="true" ' .$checked. '/>';
			echo '<p>To include Google Analytics, select this option and include your account number here:<br />(<strong>Note</strong>: This will not activate if default <strong>X</strong> value is present)<br />';
			echo 'UA-<input type="text" size="6" name="plugin_options[google_analytics_account]" value="'.$account.'" onfocus="javascript:if(this.value===\'XXXXX-X\'){this.select();}" /></p>';
			echo '<p>Selecting this option will add the following code to your pages just before the <code class="html">&lt;/body&gt;</code>'.$msg.':</p>';
			echo '<code><b>&lt;</b>script<b>&gt;</b></code>';
			echo '<code>var <b>_gaq</b>=<b>[[</b><span>"_setAccount"</span><b>,</b><span>"UA-'.(($account !== 'XXXXX-X') ? $account : 'XXXXX-X').'"</span><b>],[</b><span>"_trackPageview"</span><b>]]</b>;</code>';
			echo '<code><b>(</b>function<b>(d,t){</b>var <b>g</b>=<b>d</b>.createElement<b>(t),s</b>=<b>d</b>.getElementsByTagName<b>(t)[</b><span class="constant">0</span><b>];</code>';
			echo '<code><b>g</b>.<span class="constant">src</span>=<b>(</b><span>"https:"</span>==<b>location</b>.<span class="constant">protocol</span><b>?</b><span>"//ssl"</span><b>:</b><span>"//www"</span><b>)</b>+<span>".google-analytics.com/ga.js"</span><b>;</b></code>';
			echo '<code><b>s</b>.<span class="constant">parentNode</span>.insertBefore<b>(g,s)}(</b><span class="support">document</span><b>,</b><span>"script"</span><b>));</b></code>';
			echo '<code><b>&lt;</b>/script<b>&gt;</b></code>';
			echo '<p><strong>Note: You must check the box <em>and</em> provide a UA code for this to be added to your pages.</strong></p>';
		}
	endif; // google_analytics_js_setting


	//	callback fn for cache_buster
	if ( ! function_exists( 'cache_buster_setting' ) ):
		function cache_buster_setting() {
			$options = get_option('plugin_options');
			$checked = (isset($options['cache_buster']) && $options['cache_buster']) ? 'checked="checked" ' : '';
			$version = (isset($options['cache_buster_version']) && $options['cache_buster_version']) ? $options['cache_buster_version'] : '1';
			echo '<input class="check-field" type="checkbox" name="plugin_options[cache_buster]" value="true" ' .$checked. '/>';
			echo '<p>To force browsers to fetch a new version of a file, versus one it might already have cached, you can add a "cache buster" to the end of your CSS and JS files.  ';
			echo 'To increment the cache buster version number, type something here:<br />';
			echo '<input type="text" size="4" name="plugin_options[cache_buster_version]" value="'.$version.'"></p>';
			echo '<p>Selecting this option will add the following code to the end of all of your CSS and JS file names on all of your pages:</p>';
			echo '<code>?ver='.$version.'</code>';
		}
	endif; // cache_buster_setting

	//	callback for footer credit
	if ( ! function_exists( 'footer_credit_setting' ) ):
		function footer_credit_setting() {
			$options = get_option('plugin_options');
			$checked = (isset($options['footer_credit']) && $options['footer_credit']) ? 'checked="checked" ' : '';
			$business_name = (isset($options['your_business_name']) && $options['your_business_name']) ? $options['your_business_name'] : 'Your Business Name';
			$business_title = (isset($options['your_business_title']) && $options['your_business_title']) ? $options['your_business_title'] : 'Your Business Title';
			$website = (isset($options['your_business_website']) && $options['your_business_website']) ? $options['your_business_website'] : 'yourbusiness.com';
			$credit = (isset($options['your_business_credit']) && $options['your_business_credit']) ? $options['your_business_credit'] : 'maintained';
			$blog_title = get_bloginfo();
			echo '<input class="check-field" type="checkbox" name="plugin_options[footer_credit]" value="true" ' .$checked. '/>';
			echo '<p>If you are developing a website for a client and want a linkback to your site in the footer, here\'s an easy way to give your business site credit. <strong>All fields are required</strong>.</p>';
			echo '<p><label for="business_name">Your Business Name: </label><input type="text" size="40" id="business_name" name="plugin_options[your_business_name]" value="'.$business_name.'" onfocus="javascript:if(this.value===\'Your Business Name\'){this.select();}" /></p>';
			echo '<p><label for="business_title">Your Business Title: </label><input type="text" size="40" id="business_title" name="plugin_options[your_business_title]" value="'.$business_title.'" onfocus="javascript:if(this.value===\'Your Business Title\'){this.select();}" /></p>';
			echo '<p><label for="business_website">Your Business URI (minus http://): </label><input type="text" size="40" id="business_website" name="plugin_options[your_business_website]" value="'.$website.'" onfocus="javascript:if(this.value===\'yourbusiness.com\'){this.select();}" /></p>';
			echo '<p><label for="business_credit">Your Business Credit: </label><input type="text" size="40" id="business_credit" name="plugin_options[your_business_credit]" value="'.$credit.'" onfocus="javascript:if(this.value===\'Your Business Credit\'){this.select();}" /></p>';
			echo '<p>The code will look like this:</p>';
			echo '<code><em>'.$blog_title.'</em> is '.$credit.' by &lt;a href=<span>"'.(($website !== 'yourbusiness.com') ? 'http://'.$website : 'http://yourbusiness.com').'"</span> title=<span>"'.(($business_title !== 'Your Business Title') ? $business_title : 'Your Business Title').'"</span>&gt;'.(($business_name !== 'yourbusiness.com') ? $business_name : 'Your Business Name').'&lt;/a&gt;</code>';
		}
	endif; // footer_credit_setting


/*	4)	Create functions to add above elements to pages */

	//	$options['google_chrome']
	if ( ! function_exists( 'add_google_chrome' ) ):
		function add_google_chrome() {
			echo '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">'.PHP_EOL;
		}
	endif; // add_google_chrome

	//	$options['google_verification']
	if ( ! function_exists( 'add_google_verification' ) ):
		function add_google_verification() {
			$options = get_option('plugin_options');
			$account = $options['google_verification_account'];
			echo '<meta name="google-site-verification" content="'.$account.'">'.PHP_EOL;
		}
	endif; // add_google_verification

	//	$options['google_verification']
	if ( ! function_exists( 'add_bing_verification' ) ):
		function add_bing_verification() {
			$options = get_option('plugin_options');
			$bingaccount = $options['bing_verification_account'];
			echo '<meta name="msvalidate.01" content="'.$bingaccount.'">'.PHP_EOL;
		}
	endif; // add_bing_verification

	//	$options['viewport']
	if ( ! function_exists( 'add_viewport' ) ):
		function add_viewport() {
			echo '<meta name="viewport" content="width=device-width">'.PHP_EOL;
		}
	endif; // add_viewport

	//	$options['favicon']
	if ( ! function_exists( 'add_favicon' ) ):
		function add_favicon() {
			echo '<link rel="shortcut icon" href="/favicon.ico">'.PHP_EOL;
		}
	endif; // add_favicon

	//	$options['favicon_ithing']
	if ( ! function_exists( 'add_favicon_ithing' ) ):
		function add_favicon_ithing() {
			echo '<link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144-precomposed.png" />'.PHP_EOL;
			echo '<link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114-precomposed.png" />'.PHP_EOL;
			echo '<link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72-precomposed.png" />'.PHP_EOL;
			echo '<link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57-precomposed.png" />'.PHP_EOL;
			echo '<link rel="apple-touch-icon" href="/apple-touch-icon-precomposed.png" />'.PHP_EOL;
			echo '<link rel="apple-touch-icon" href="/apple-touch-icon.png" />'.PHP_EOL;
		}
	endif; // add_favicon_ithing

	//	$options['modernizr_js']
	if ( ! function_exists( 'add_modernizr_script' ) ):
		function add_modernizr_script() {
			$cache = cache_buster();
			wp_deregister_script( 'ieshiv' ); // get rid of IEShiv if it somehow got called too (IEShiv is included in Modernizr)
			wp_deregister_script( 'modernizr' ); // get rid of any native Modernizr
			echo '<script src="' .BP_THEME_URL. '/js/vendor/modernizr.js'.$cache.'"></script>'.PHP_EOL;
		}
	endif; // add_modernizr_script

	//	$options['ieshiv_script']
	if ( ! function_exists( 'add_ieshiv_script' ) ):
		function add_ieshiv_script() {
			$cache = cache_buster();
			echo '<!--[if lt IE 9]>'.PHP_EOL;
			echo '	<script src="//html5shiv.googlecode.com/svn/trunk/html5.js" onload="window.ieshiv=true;"></script>'.PHP_EOL; // try getting from CDN
			echo '<script>!window.ieshiv && document.write(unescape(\'<script src="' .BP_THEME_URL. '/js/ieshiv.js'.$cache.'"><\/script>\'))</script>'.PHP_EOL; // fallback to local if CDN fails
			echo '<![endif]-->'.PHP_EOL;
		}
	endif; // add_ieshiv_script

	//	$options['respond_js']
	if ( ! function_exists( 'add_respond_script' ) ):
		function add_respond_script() {
			$cache = cache_buster();
			echo '<script src="' .BP_THEME_URL. '/js/vendor/respond.js'.$cache.'"></script>'.PHP_EOL;
		}
	endif; // add_respond_script

	//	$options['jquery_js']
	if ( ! function_exists( 'add_jquery_script' ) ):
		function add_jquery_script() {
			$cache = cache_buster();
			$options = get_option('plugin_options');
			$version = ($options['jquery_version']) ? $options['jquery_version'] : '1.8.3';
			wp_deregister_script( 'jquery' ); // get rid of WP's jQuery
			echo '<script src="//ajax.googleapis.com/ajax/libs/jquery/'.$version.'/jquery.min.js"></script>'.PHP_EOL; // try getting from CDN
			echo '<script>window.jQuery || document.write(\'<script src="' .BP_THEME_URL. '/js/vendor/jquery.js'.$cache.'"><\/script>\')</script>'.PHP_EOL; // fallback to local if CDN fails
		}
	endif; // add_jquery_script

	//	$options['plugins_js']
	if ( ! function_exists( 'add_plugin_script' ) ):
		function add_plugin_script() {
			$cache = cache_buster();
			echo '<script src="' .BP_CHILD_THEME_URL. '/js/plugins.js'.$cache.'"></script>'.PHP_EOL;
		}
	endif; // add_plugin_script

	//	$options['site_js']
	if ( ! function_exists( 'add_site_script' ) ):
		function add_site_script() {
			$cache = cache_buster();
			echo '<script src="' .BP_CHILD_THEME_URL. '/js/main.js'.$cache.'"></script>'.PHP_EOL;
		}
	endif; // add_site_script

	//	$options['chrome_frame']
	if ( ! function_exists( 'add_chrome_frame' ) ):
		function add_chrome_frame() {
			echo '<!--[if lt IE 7]><p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p><![endif]-->'.PHP_EOL;
		}
	endif; // add_chrome_frame

	//	$options['google_analytics_js']
	if ( ! function_exists( 'add_google_analytics_script' ) ):
		function add_google_analytics_script() {
			$options = get_option('plugin_options');
			$account = $options['google_analytics_account'];
			echo PHP_EOL.'<script>'.PHP_EOL;
			echo 'var _gaq=[["_setAccount","UA-'.str_replace('UA-','',$account).'"],["_trackPageview"]];'.PHP_EOL;
			echo '(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];'.PHP_EOL;
			echo 'g.src=("https:"==location.protocol?"//ssl":"//www")+".google-analytics.com/ga.js";'.PHP_EOL;
			echo 's.parentNode.insertBefore(g,s)}(document,"script"));'.PHP_EOL;
			echo '</script>'.PHP_EOL;
		}
	endif; // add_google_analytics_script

	//	$options['cache_buster']
	if ( ! function_exists( 'cache_buster' ) ):
		function cache_buster() {
			$options = get_option('plugin_options');
			return (isset($options['cache_buster']) && $options['cache_buster']) ? '?ver='.$options['cache_buster_version'] : '';
		}
	endif; // cache_buster

	//	$options['footer_credit']
	if ( ! function_exists( 'add_footer_credit' ) ):
		function add_footer_credit() {
			$options = get_option('plugin_options');
			$blog_title = get_bloginfo();
			$website = $options['your_business_website'];
			$business_title = $options['your_business_title'];
			$business_name = $options['your_business_name'];
			$credit = $options['your_business_credit'];
			echo $blog_title.' is '.$credit.' by <a href="http://'.$website.'" title="'.$business_title.'">'.$business_name.'</a>.';
		}
	endif; // add_footer_credit

/*	5)	Add Annex options to page as requested */
		if (!is_admin() ) {

			// get the options
			$options = get_option('plugin_options');

			// check if each option is set (meaning it exists) and check if it is true (meaning it was checked)
			if (isset($options['google_chrome']) && $options['google_chrome']) {
				add_action('wp_print_styles', 'add_google_chrome');
			}

			if (isset($options['google_verification']) && $options['google_verification'] && $options['google_verification_account'] && $options['google_verification_account'] !== 'XXXXXXXXX...') {
				add_action('wp_print_styles', 'add_google_verification');
			}

			if (isset($options['bing_verification']) && $options['bing_verification'] && $options['bing_verification_account'] && $options['bing_verification_account'] !== 'XXXXXXXXX...') {
				add_action('wp_print_styles', 'add_bing_verification');
			}
			
			if (isset($options['viewport']) && $options['viewport']) {
				add_action('wp_print_styles', 'add_viewport');
			}

			if (isset($options['favicon']) && $options['favicon']) {
				add_action('wp_print_styles', 'add_favicon');
			}

			if (isset($options['favicon_ithing']) && $options['favicon_ithing']) {
				add_action('wp_print_styles', 'add_favicon_ithing');
			}

			if (isset($options['modernizr_js']) && $options['modernizr_js']) {
				add_action('wp_print_styles', 'add_modernizr_script');
			} else {
				// if Modernizr isn't selected, add IEShiv inside an IE Conditional Comment
				add_action('wp_print_styles', 'add_ieshiv_script');
			}

			if (isset($options['respond_js']) && $options['respond_js']) {
				add_action('wp_print_styles', 'add_respond_script');
			}

			if (isset($options['jquery_js']) && $options['jquery_js'] && isset($options['jquery_version']) && $options['jquery_version'] && $options['jquery_version'] !== '') {
				// check if should be loaded in <head> or at end of <body>
				$hook = (isset($options['jquery_head']) && $options['jquery_head']) ? 'wp_print_styles' : 'wp_footer';
				add_action($hook, 'add_jquery_script');
			}
			// for jQuery plug-ins, make sure jQuery was also set
			if (isset($options['jquery_js']) && $options['jquery_js'] && isset($options['plugins_js']) && $options['plugins_js']) {
				add_action('wp_footer', 'add_plugin_script');
			}

			if (isset($options['site_js']) && $options['site_js']) {
				add_action('wp_footer', 'add_site_script');
			}

			if (isset($options['chrome_frame']) && $options['chrome_frame']) {
				add_action('ie_chrome_frame', 'add_chrome_frame');
			}
			if (isset($options['google_analytics_js']) && $options['google_analytics_js'] && isset($options['google_analytics_account']) && $options['google_analytics_account'] && $options['google_analytics_account'] !== 'XXXXX-X') {
				add_action('wp_footer', 'add_google_analytics_script');
			}
			if (isset($options['footer_credit']) && $options['footer_credit'] && $options['footer_credit'] !== '' ) {
				add_action('annex_credits', 'add_footer_credit');
			}
		} // if (!is_admin() )

?>
