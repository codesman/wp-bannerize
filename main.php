<?php
/*
Plugin Name: WP Bannerize
Plugin URI: http://www.saidmade.com/prodotti/wordpress/wp-bannerize/
Description: WP Bannerize is an Amazing Banner Manager. For more info and plugins visit <a href="http://www.saidmade.com">Saidmade</a>.
Version: 3.0.22
Author: Giovambattista Fazioli
Author URI: http://www.saidmade.com
Disclaimer: Use at your own risk. No warranty expressed or implied is provided.

	Copyright © 2008-2011 Saidmade Srl (email : g.fazioli@undolog.com - g.fazioli@saidmade.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

require_once('main.h.php');
require_once('Classes/wpBannerizeClass.php');

if (@isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
	require_once('Classes/wpBannerizeAdmin.php');
	//
	$wpBannerizeAdmin = new WPBannerizeAdmin(__FILE__);
	require_once('Classes/wpBannerizeAjax.php');
} else {
	if (is_admin()) {
		require_once('Classes/wpBannerizeAdmin.php');
		//
		$wpBannerizeAdmin = new WPBannerizeAdmin(__FILE__);
		$wpBannerizeAdmin->register_plugin_settings(__FILE__);
		register_activation_hook(__FILE__, array(&$wpBannerizeAdmin, 'pluginDidActive'));
		register_activation_hook(__FILE__, array(&$wpBannerizeAdmin, 'pluginDidDeactive'));
	} else {
		require_once('Classes/wpBannerizeFrontend.php');
		$wpBannerizeFrontend = new WPBannerizeFrontend(__FILE__);
		require_once('Classes/wpBannerizeFunctions.php');
	}
}

?>