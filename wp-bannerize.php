<?php
/*
Plugin Name: WP-BANNERIZE
Plugin URI: http://wordpress.org/extend/plugins/wp-bannerize/
Description: WP_BANNERIZE is a image banner manager. See <a href="options-general.php?page=wp-bannerize.php">configuration panel</a> for more settings. For more info and plugins visit <a href="http://labs.saidmade.com">Labs Saidmade</a>.
Version: 1.2
Author: Giovambattista Fazioli
Author URI: http://labs.saidmade.com
Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
 
	Copyright 2009 Saidmade Srl (email : g.fazioli@undolog.com)

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
	
	
	CHANGE LOG
	
	* 1.2		Re-write doc and readme.txt
	* 1.1		Rev, Fix and stable release
	* 1.0		First release

*/

// ________________________________________________________________________________________ MAIN

/**
 * DEFINE CONSTANT
 *
 * All constant are defined here
 */
define( 'PLUGINNAME',			'wp-bannerize' );
define( 'WPB_OPTIONSKEY',		'wp-bannerize' );
define( 'WPBZ_OPTIONSTITLE',	'wp-bannerize' );
define( 'WPBZ_VERSION',			'1.2' );

define( 'WPBZ_UPLOADS_URL', 	get_option('siteurl') . '/wp-content/uploads/' );
define( 'WPBZ_UPLOADS_PATH',	realpath( dirname(__FILE__) . '/../../uploads/' ) . '/' );
define( 'WPBZ_AJAX_URL',	 	get_option('siteurl') .  "/wp-content/plugins/wp-bannerize/ajax.php" );

define( 'WPBZ_TABLE_BANNERIZE',	'bannerize');

/**
 * INIT OPTIONS
 *
 * The default options are stored in this array ( key => default value )
 * The global plugin variables have a $wpp_ prefix (wordpress plugin)
 */
$wpp_options = array();

/**
 * Add to Wordpress options database
 */
add_option( WPBZ_OPTIONSKEY, $wpp_options, WPBZ_OPTIONSTITLE );

/**
 * re-Load options
 */
$wpp_options = get_option( WPBZ_OPTIONSKEY );

// ________________________________________________________________________________________ OPTIONS

/**
 * ADD OPTION PAGE TO WORDPRESS ENVIRORMENT
 *
 * Add callback for adding options panel
 *
 */
function wpp_options_page() {
	if ( function_exists('add_options_page') ) {
 		$plugin_page = add_options_page( WPBZ_OPTIONSTITLE, WPBZ_OPTIONSTITLE, 8, basename(__FILE__), 'wpp_options_subpanel');
		add_action( 'admin_head-'. $plugin_page, 'wpp_admin_head' );
	}
}

/**
 * Draw Options Panel
 */
function wpp_options_subpanel() {
	global $wpp_options, $wpdb, $_POST;

	$any_error = "";										// any error flag


	if( isset( $_POST['command_action'] ) ) {				// have to save options	
		$any_error = 'Your settings have been saved.';

		switch( $_POST['command_action'] ) {
			case "mysql_insert":
				$any_error = mysql_insert();
				break;
			case "mysql_delete":
				$any_error = mysql_delete();
				break;		
		}
	}
	
	/**
	 * Show error or OK
	 */
	if( $any_error != '') echo '<div id="message" class="updated fade"><p>' . $any_error . '</p></div>';
	
	/**
	 * INSERT OPTION
	 *
	 * You can include a separate file: include ('options.php');
	 *
	 */
	?>
	
	<div class="wrap">
    <h2>WP-BANNERIZE</h2>

	<h3>Inserisci un nuovo banner</h3>
	<form class="form_box" name="insert_bannerize" method="post" action="" enctype="multipart/form-data">
		<input type="hidden" name="command_action" id="command_action" value="mysql_insert" />
		<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
		
		<p>
			<label for="group">Group: </label><input type="text" name="group" id="group" value="A" size="8" style="text-align:right" />
			<label for="description">Description: </label><input type="text" name="description" id="description" value="" size="32" /> 
			<label for="url">URL: </label><input type="text" name="url" id="url" value="" size="32" />
		    <label for="group">Image: </label><input type="file" name="filename" id="filename" size="40" />
		</p>
		<div class="submit"><input type="submit" value="+ Add" /></div>
	</form>
	
	<form style="display:none" name="delete_bannerize" method="post" action="">
		<input type="hidden" name="command_action" id="command_action" value="mysql_delete" />
		<input type="hidden" name="id" id="id" value="" />
	</form>

	<h3>Lista banner</h3>
	<form class="form_box" name="filter_bannerize" method="post" action="">
		<p><label for="group_filter">Show group: </label><?php combo_group(); ?></p>
	</form>
	<?php
	
		$q = "SELECT * FROM `" . WPBZ_TABLE_BANNERIZE . "`";
		
		if( isset( $_POST['group_filter']) ) {
			if( $_POST['group_filter'] != "" ) $q .= " WHERE `group` = '".$_POST['group_filter']."'";
		}
		
		$q .= " ORDER BY `sorter`, `group` ASC ";
		
		$rows = $wpdb->get_results( $q );
		
		$o = '<table id="list_bannerize" width="100%" cellpadding="4" cellspacing="0">
		       <thead>
			    <tr>
				 <th>Group</th>
				 <th>Description</th>
				 <th>Banner</th>
				 <th>!</th>
				</tr>
			   </thead>
			   <tbody>';	
			
		foreach( $rows as $row ) {
			$o .= '<tr id="item_' . $row->id . '">' .
			      '<td>' . $row->group . '</td>' .
				  '<td width="100%">' . $row->description . '</td>' .
				  '<td align="center"><a target="_blank" href="' . $row->url . '"><img height="55" border="0" src="' . WPBZ_UPLOADS_URL . $row->filename . '" /></a></td>' .
				  '<td align="center"><button class="button" onclick="delete_banner('.$row->id.')">Delete</button></td>' .
				  '</tr>';
		}
		$o .= '</tbody>
		       </table>';
	
		echo $o;
	?>
	
	<p style="text-align:center;font-family:Tahoma;font-size:10px">Developed by <a target="_blank" href="http://www.saidmade.com"><img align="absmiddle" src="http://labs.saidmade.com/images/sm-a-80x15.png" border="0" /></a>
		<br/>
		more Wordpress plugins on <a target="_blank" href="http://labs.saidmade.com">labs.saidmade.com</a> and <a target="_blank" href="http://www.undolog.com">Undolog.com</a>
		<br/>
		<form style="text-align:center;width:300px;margin:0 auto" action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="3499468">
			<input type="image" src="https://www.paypal.com/it_IT/IT/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - Il sistema di pagamento online più facile e sicuro!">
			<img alt="" border="0" src="https://www.paypal.com/it_IT/i/scr/pixel.gif" width="1" height="1">
		</form>
	</p>	

	</div>
	
	<?php
	
}

// ________________________________________________________________________________________ FUNCTIONS

/**
 * Build the select/option filter group
 *
 * @return 
 */
function combo_group() {
	global $wpdb, $_POST;
	$o = '<select onchange="document.forms[\'filter_bannerize\'].submit()" id="group_filter" name="group_filter">' .
	     '<option value="">All</option>';
	$q = "SELECT `group` FROM `" . WPBZ_TABLE_BANNERIZE . "` GROUP BY `group` ORDER BY `group` ";
	$rows = $wpdb->get_results( $q );
	$sel = "";
	foreach( $rows as $row ) {
		if( $_POST['group_filter'] == $row->group ) $sel = 'selected="selected"'; else $sel = "";
		$o .= '<option '.$sel.'value="'.$row->group.'">'.$row->group.'</option>';
	}
	$o .= '</select>';
	echo $o;
}

/**
 * Hook the admin/plugin head
 * 
 * @return 
 */
function wpp_admin_head() {
?>
<style type="text/css">
	table#list_bannerize {
		
	}
	table#list_bannerize thead th {
		background:#ccc;
		border-bottom:1px solid;
		padding:6px;
	}
	table#list_bannerize tbody td {
		border-left:1px dotted;
		border-bottom:1px dotted;
		white-space:nowrap;
		padding:2px;
	}
	table#list_bannerize tbody td img {
		border:1px solid;
	}
	form.form_box {
		background:#f1f1f1;
		border:1px solid #aaa;
		padding:12px;
	}
</style>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.5.3/jquery-ui.min.js" type="text/javascript"></script>
<script type="text/javascript">
	
	jQuery(document).ready(function(){
		jQuery('table#list_bannerize tbody tr').css('width',jQuery('table#list_bannerize').width() );
		jQuery('table#list_bannerize tbody').sortable({
					axis:"y",
					cursor:"n-resize",
					stop:function() {
						jQuery.ajax({
						type: "POST",
						url: "<?=WPBZ_AJAX_URL?>",
						data: jQuery("table#list_bannerize tbody").sortable("serialize")	})
					}
		});
	});
	
	function delete_banner( id ) {
		if( confirm('WARINING!!\n\nDo you want delete this banner?') ) {
			var f = document.forms['delete_bannerize'];
			f.id.value = id;
			f.submit();
		}
	}
</script>
<?php
}

/**
 * Esegue l'upload e lo store nel database
 * 
 * Array ( [name] 			=> test.pdf 
 * 		   [type]			=> application/pdf 
 * 		   [tmp_name] 		=> /tmp/phpcXS1lh 
 *    	   [error] 			=> 0 
 *    	   [size] 			=> 277304 ) 
 * 
 * @return 
 */
function mysql_insert() {
	global $wpp_options, $wpdb, $_POST, $_FILES;
	// verifica eventuali errori
	if( $_FILES['filename']['error'] == 0 ) {
		$size = floor( $_FILES['filename']['size'] / (1024*1024) );
		$mime = $_FILES['filename']['type'];
		$name = $_FILES['filename']['name'];
		$temp = $_FILES['filename']['tmp_name'];
		
		$group 		 = $_POST['group'];
		$description = $_POST['description'];
		$url 		 = $_POST['url'];
		
		$basepath = 'banners/' . date('Y') . '/' . date('m') . '/' . date('d') . "/";
		$pathname = WPBZ_UPLOADS_PATH . $basepath;
		@mkdir( $pathname, 0777, true  );
		
		$filename = $pathname . strtolower($name);
		$urlname  = $basepath . strtolower($name);
		
		if ( move_uploaded_file( $_FILES['filename']['tmp_name'], $filename )) {
			
			$q = "INSERT INTO `" . WPBZ_TABLE_BANNERIZE . "`" .
			     " ( `group`, `description`, `url`, `filename` )" .
				 " VALUES ('" . $group . "', '" . $description . "', '" . $url . "', '" . $urlname . "')";
			$wpdb->query($q);	 
			return( '' );
		} else {
			return ( '<div id="result">Impossibile spostare e posizionare il file ' . $_FILES['filename']['name'] .
			         ' (' . $_FILES['filename']['size'] . ' bytes). Errore ('. $pathname .') ' . $_FILES['filename']['error'] . '</div>' );
		}
	} else {
		return( '<div id="result">Impossibile trasferire il file ' . $_FILES['filename']['name'] .
		        ' (' . $_FILES['filename']['size'] . ' bytes). Errore ' . $_FILES['filename']['error'] . '</div>' );
	}
}

/**
 * Delete a banner
 * 
 * @return 
 */
function mysql_delete() {
	global $wpdb, $_POST, $_FILES;
	//
	$filename = $wpdb->get_var( "SELECT filename FROM `" . WPBZ_TABLE_BANNERIZE . "` WHERE `id` = " . $_POST['id'] );
	@unlink( WPBZ_UPLOADS_PATH . $filename );
	
	$q = "DELETE FROM `" . WPBZ_TABLE_BANNERIZE . "` WHERE `id` = " . $_POST['id'];
	$wpdb->query($q);
	return('');
}


/**
 * Check if 'bannerize' table exists on the database
 * if not exists then create it
 * 
 * @return 
 */
function checkTable() {
	global $wpdb;
	
	$q = 'CREATE TABLE IF NOT EXISTS `' . WPBZ_TABLE_BANNERIZE . '` (
			  `id` int(11) NOT NULL auto_increment,
			  `sorter` int(11) NOT NULL,
			  `group` varchar(8) NOT NULL,
			  `description` varchar(255) NOT NULL,
			  `url` varchar(256) NOT NULL,
			  `filename` varchar(255) NOT NULL,
			  PRIMARY KEY  (`id`)
			)';
	$wpdb->query($q);
}

/**
 * Show banner
 * 
 * @return 
 * @param object $args
 * 
 * group				If '' show all group, else code of group (default '')
 * container_before		Main tag container open (default <ul>)
 * container_after		Main tag container close (default </ul>)
 * before				Before tag banner open (default <li>) 
 * after				After tag banner close (default </li>) 
 * 
 */
function wp_bannerize( $args = '' ) {
	global $wpdb;
	
	$default = array(
					 'group' 				=> '',
					 'container_before'		=> '<ul>',
					 'container_after'		=> '</ul>',
					 'before'				=> '<li>',
					 'after'				=> '</li>'
					);
	
	$new_args = wp_parse_args( $args, $default );
	
	$q = "SELECT * FROM `" . WPBZ_TABLE_BANNERIZE . "` ";
	
	if( $new_args['group'] != "") {
		$q .= " WHERE `group` = '" . $new_args['group'] . "'";
	}
	
	$q .= " ORDER BY `sorter` ASC";
	
	$rows = $wpdb->get_results( $q );	

	$o = $new_args['container_before'];
	
	foreach( $rows as $row ) {
		$o .= $new_args['before'] . 
			  '<a target="_blank" href="' . $row->url . '"><img border="0" src="' . WPBZ_UPLOADS_URL . $row->filename . '" /></a>' .
			  $new_args['after'];	
	}
	$o .= $new_args['container_after'];
	
	echo $o;
}

// ________________________________________________________________________________________ HOOK

/**
 * Link my custom option to admin menu
 *
 */ 
add_action('admin_menu', 	'wpp_options_page');
checkTable();

?>