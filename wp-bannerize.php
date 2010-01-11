<?php
/*
Plugin Name: WP-BANNERIZE
Plugin URI: http://wordpress.org/extend/plugins/wp-bannerize/
Description: WP_BANNERIZE is an Amazing Banner Image Manager. For more info and plugins visit <a href="http://labs.saidmade.com">Labs Saidmade</a>.
Version: 2.3.5
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


        == Changelog ==

        = 2.3.5 =
        * Rev Widget Class implement

        = 2.3.4 =
        * Revision readme.txt

        = 2.3.3 =
        * Split Widget code
        * Fix "random" select on Widget

        = 2.3.2 =
        * Add "alt" class in HTML output
        * Add additional class for link TAG A
        * Fix Widget output

        = 2.3.0 =
        * Add Wordpress Categories Filter - Show Banner Group for Categories ID
        * Improve admin

        = 2.2.2 =
        * Fix minor bugs + prepare major release

        = 2.2.1 =
        * Fix to Wordpress MU compatibilities
        * Fix minor bugs

        = 2.2.0 =
        * Add Widget support
        * Fix compatibility with Wordpress 2.8.6

        = 2.1.0 =
        * Add thickbox support for preview thumbnail
        * Resize key field to 128 chars
        * Minor Fix

        = 2.0.8 =
        * Minor Fix, improve admin

        = 2.0.3 =
        * Minor Fix

        = 2.0.2 =
        * Add random option
        * Fix minor bugs

        = 2.0.1 =
        * Fix bugs on varchar size in create table

        = 2.0.0 =
        * Add edit banner
        * Combo menu for group/key and target
        * Contextual HELP
        * Icon
        * Limit option
        * rev list and rev code!

        = 1.4.3 =
        * Fix patch on old version database table

        = 1.4.2 =
        * Add screenshot

        = 1.4.1 =
        * Clean code

        = 1.4.0 =
        * Rev UI
        * Change database
        * Fix upload path server bug

        = 1.3.2 =
        * Fix bug to sort order with Ajax call

        = 1.3.1 =
        * Update jQuery to last version

        = 1.3.0 =
        * New improve class object structure

        = 1.2.5 =
        * Remove a conflict with a new class object structure

        = 1.2 =
        * Done doc :)

        = 1.1 =
        * Rev, Fix and stable release

        = 1.0 =
        * First release
        `

        == Upgrade Notice ==

        = 2.2.0 =
        Add Widget support and fix for Wordpress 2.8.6. Upgrade immediately

        = 2.0.0 =
        Major release improve. Upgrade immediately.

        = 1.4.0 =
        Major release improve. Upgrade immediately.

        = 1.3.1 =
        Upgrade to last jQuery release. Upgrade immediately.

        = 1.0 =
        Please download :)

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