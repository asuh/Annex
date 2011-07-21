<?php

// custom admin login logo for child theme
function annex_child_custom_login_logo() {
	echo '<style type="text/css">
	h1 a { background-image: url('.get_bloginfo('stylesheet_directory').'/img/custom-login-logo.png) !important; }
	</style>';
}
add_action('login_head', 'annex_child_custom_login_logo');

?>