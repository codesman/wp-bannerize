<?php
/*
Plugin Name: WP-BANNERIZE
Plugin URI: http://wordpress.org/extend/plugins/wp-bannerize/
Description: WP_BANNERIZE is an Amazing Banner Image Manager. For more info and plugins visit <a href="http://labs.saidmade.com">Labs Saidmade</a>.
Version: 2.4.4
Author: Giovambattista Fazioli
Author URI: http://labs.saidmade.com
Disclaimer: Use at your own risk. No warranty expressed or implied is provided.

	Copyright 2010 Saidmade Srl (email : g.fazioli@undolog.com - g.fazioli@saidmade.com)

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

require_once( 'wp-bannerize_class.php');

if( is_admin() ) {
	require_once( 'wp-bannerize_admin.php' );
	//
	$wp_bannerize_admin = new WPBANNERIZE_ADMIN();
	$wp_bannerize_admin->register_plugin_settings( __FILE__ );
} else {
	require_once( 'wp-bannerize_client.php');
	$wp_bannerize_client = new WPBANNERIZE_CLIENT();
	require_once( 'wp-bannerize_functions.php');
}
?>