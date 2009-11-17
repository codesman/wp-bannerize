<?php
/**
 * Admin (back-end)
 */
class WPBANNERIZE_ADMIN extends WPBANNERIZE_CLASS {
	
	function WPBANNERIZE_ADMIN() {
		$this->WPBANNERIZE_CLASS();							// super
		
		$this->initDefaultOption();
	}
	
	/**
	 * Init the default plugin options and re-load from WP
	 * 
	 * @return 
	 */
	function initDefaultOption() {
		$this->options 						= array();
		add_option( $this->options_key, $this->options, $this->options_title );
		
		parent::getOptions();
		
		/**
		 * @since 1.4+
		 * Check table for new field
		 * 
		 * @since 2.1.0
		 * Alter table group filed to varchar(128)
		 */
		$this->update = $this->checkTable(); 
		
		add_action('admin_menu', 	array( $this, 'add_menus') );
		
		/**
		 * @since 2.1.0
		 * Add thickbox standard Wordpress support
		 */
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');
	}
	
	/**
	 * ADD OPTION PAGE TO WORDPRESS ENVIRORMENT
	 *
	 * Add callback for adding options panel
	 *
	 */
	function add_menus() {
		$menus = array();
		
		if (function_exists('add_object_page')) {
                    // Remove from 2.2.0 for fix Wordpress 2.8.6
                    // $menus['main'] = add_object_page('WP Bannerize', 'WP Bannerize', 8, $this->directory.'-settings', array( &$this, 'set_options_subpanel') );
                    $menus['main'] = add_object_page('WP Bannerize', 'WP Bannerize', 8, $this->directory.'-settings' );
		} else
                    $menus['main'] = add_menu_page('WP Bannerize', 'WP Bannerize', 8, $this->directory.'-settings', array(&$this,'set_options_subpanel') );

		$menus['settings'] = add_submenu_page($this->directory.'-settings', __('Settings'), __('Settings'), 8, $this->directory.'-settings', array(&$this,'set_options_subpanel') );
		
		add_action( 'admin_head-' . $menus['settings'], array( &$this, 'set_admin_head' ) );
		
		/**
		 * Add contextual Help
		 */
		if (function_exists('add_contextual_help')) {
			add_contextual_help($menus['main'],'<p><strong>'.__('Use').':</strong></p>' .
			'<pre>wp_bannerize();</pre> or<br/>' .
			'<pre>wp_bannerize( \'group=a&limit=10\' );</pre> or<br/>' .
			'<pre>wp_bannerize( \'group=a&limit=10&random=1\' );</pre><br/>' .
			'<pre>
* group               If \'\' show all groups, else show the selected group code (default \'\')
* container_before    Main tag container open (default &lt;ul&gt;)
* container_after     Main tag container close (default &lt;/ul&gt;)
* before              Before tag banner open (default &lt;li&gt;)
* after               After tag banner close (default &lt;/li&gt;) 
* random              Show random banner sequence (default \'\')
* limit               Limit rows number (default \'\' - show all rows)</pre>' 			
			);
		}
	}
	
	/**
	 * Draw Options Panel
	 */
	function set_options_subpanel() {
		global $wpp_options, $wpdb, $_POST;
		
		if( $this->update ) {
			$this->showUpdate();
			return;
		}
	
		$any_error = "";										// any error flag
	
		if( isset( $_POST['command_action'] ) ) {				// have to save options	
			$any_error = __('Your settings have been saved.');
	
			switch( $_POST['command_action'] ) {
				case "mysql_insert":
					$any_error = $this->mysql_insert();
					break;
				case "mysql_delete":
					$any_error = $this->mysql_delete();
					break;		
				case "mysql_update":
					$any_error = $this->mysql_update();
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
			<div class="icon32" id="icon-options-general"><br/></div>
		    <h2><?=$this->options_title?> ver. <?=$this->version?></h2>
		
			<h2><?php echo __('Insert new Banner')?></h2>
			<form class="form_box" name="insert_bannerize" method="post" action="" enctype="multipart/form-data">
				<input type="hidden" name="command_action" id="command_action" value="mysql_insert" />
				<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
				
				<table class="form-table">
					<tr>
						<th scope="row"><label for="group"><?php echo __('Image')?>:</label></th>
						<td><input type="file" name="filename" id="filename" size="40" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="group"><?php echo __('Key')?>:</label></th>
						<td><input type="text" maxlength="128" name="group" id="group" value="A" size="32" style="text-align:right" /> <?php echo $this->get_combo_group() ?> (<?php echo __('Insert a key max 128 char')?>)</td>
					</tr>
					<tr>
						<th scope="row"><label for="description"><?php echo __('Description')?>:</label></th>
						<td><input type="text" name="description" id="description" value="" size="32" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="url">URL:</label></th>
						<td><input type="text" name="url" id="url" value="" size="32" /> <label for="url"><?php echo __('Target')?>:</label> <?php echo $this->get_target_combo() ?></td>
					</tr>
				</table>
				<div class="submit"><input class="button-primary" type="submit" value="<?php echo __('Insert')?>" /></div>
			</form>
			
			<form style="display:none" name="delete_bannerize" method="post" action="">
				<input type="hidden" name="command_action" id="command_action" value="mysql_delete" />
				<input type="hidden" name="id" id="id" value="" />
			</form>
	
			<div class="icon32" id="icon-edit"><br/></div><h2><?php echo __('Banners list')?></h2>
			<div class="tablenav">
				<div class="alignleft actions">
					<form class="form_box" name="filter_bannerize" method="post" action="">
						<?php $this->combo_group(); ?> <input class="button-secondary" type="submit" value="<?php echo __('Filter')?>"/>
						| <?php echo __('Use')?> <img align="absmiddle" alt="Drag and Drop" border="0" src="<?php echo $this->uri ?>/css/images/arrow_ns.png" /> <?php echo __('for drag and drop to change order')?>
					</form>
				</div>
			</div>		
			<?php
			
				$q = "SELECT * FROM `" . $this->table_bannerize . "`";
				
				if( isset( $_POST['group_filter']) ) {
					if( $_POST['group_filter'] != "" ) $q .= " WHERE `group` = '".$_POST['group_filter']."'";
				}
				
				$q .= " ORDER BY `sorter`, `group` ASC ";
				
				$rows = $wpdb->get_results( $q );
				
				$o = '<table class="widefat" id="list_bannerize" width="100%" cellpadding="4" cellspacing="0">
				       <thead>
					    <tr>
						 <th class="manage-column" scope="col"></th>
						 <th width="40" scope="col">'.__('Image').'</th>
						 <th scope="col">'.__('Key').'</th>
						 <th width="100%" scope="col">'.__('Description').'</th>
						 <th scope="col">'.__('URL').'</th>
						 <th scope="col">'.__('Target').'</th>
						</tr>
					   </thead>
					   <tfoot>
					    <tr>
						 <th class="manage-column" scope="col"></th>
						 <th width="40" scope="col">'.__('Image').'</th>
						 <th scope="col">'.__('Key').'</th>
						 <th width="100%" scope="col">'.__('Description').'</th>
						 <th scope="col">'.__('URL').'</th>
						 <th scope="col">'.__('Target').'</th>
						</tr>					   
					   </tfoot>
					   <tbody>';	
				
				$i = 0;	
				
				foreach( $rows as $row ) {
					$class = ($i%2 == 0) ? 'class="alternate"' : ''; $i++;
					$e = '<div class="inline-edit" id="edit_'.$row->id.'" style="display:none">' .
						  '<form method="post" name="form_edit_'.$row->id.'">' .
						  '<input type="hidden" name="command_action" value="mysql_update" />' .
						  '<input type="hidden" name="id" value="'.$row->id.'" />' .
						  '<label for="group">' . __('Key') . ':</label> <input size="8" type="text" name="group" value="' . $row->group . '" /> ' . $this->get_combo_group("form_edit_".$row->id) .
						  '<label for="description">' . __('Description') . ':</label> <input size="32" type="text" name="description" value="' . $row->description . '" /> (image alt)<br/>' .
						  '<label for="url">' . __('URL') . ':</label> <input type="text" name="url" size="32" value="' . $row->url . '" /> ' .
						  '<label for="target">' . __('Target') . ':</label> ' . $this->get_target_combo( $row->target ) . 
						  '<p class="submit inline-edit-save">' .
						  '<a onclick="jQuery(\'div#edit_'.$row->id.'\').hide();return false;" class="button-secondary cancel alignleft" title="'.__('Cancel').'" href="#" accesskey="c">'.__('Cancel').'</a>' .
						  '<a onclick="document.forms[\'form_edit_'.$row->id.'\'].submit();" class="button-primary save alignright" title="' . __('Update') . '" href="#" accesskey="s">' . __('Update') . '</a>' .
						  '</p>' .
						  '</form>' .
						  '</div>';
					
					$o .= '<tr ' . $class . ' id="item_' . $row->id . '">' .
						  '<th scope="row"><div class="arrow"></div></th> ' .
						  '<td width="40" align="left"><img class="wp-bannerize-thumbnail" height="32" width="32" border="1" src="' . $row->filename . '" /></td>' .
					      '<td>' . $row->group . '</td>' .
						  '<td width"100%">' . $e . "<br/>" . $row->description .
						  '<div class="row-actions">' .
						  '<span class="edit"><a class="edit_'.$row->id.'" title="Edit" href="#">'.__('Edit').'</a> | </span>' .
						  '<span class="delete"><a onclick="delete_banner('.$row->id.');return false;" href="#" title="'.__('Delete').'" class="submitdelete">'.__('Delete').'</a> | </span>' .
						  '<span class="view"><a target="_blank" class="thickbox" rel="wp-bannerize-gallery" href="' . $row->filename . '" title="'.__('View').'">'.__('View').'</a></span>' .
						  '</div>' .
						  '</td>' .
						  '<td>' . $row->url . '</td>' .
						  '<td>' . $row->target . '</td>' .
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
					<input type="image" src="https://www.paypal.com/it_IT/IT/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - Il sistema di pagamento online pi� facile e sicuro!">
					<img alt="" border="0" src="https://www.paypal.com/it_IT/i/scr/pixel.gif" width="1" height="1">
				</form>
			</p>	
	
		</div>
		
		<?php
	}
	
	/**
	 * Update previous WP Bannerize version to 1.4
	 * 
	 * @return 
	 */
	function showUpdate() {
		global $wpp_options, $wpdb, $_POST;
		?>
		<div class="wrap">
			<div class="icon32" id="icon-options-general"><br/></div>
		    <h2><?=$this->options_title?> ver. <?=$this->version?></h2>
			<?php
				if( isset( $_POST['toupdate'])) {
					$this->alterTable();
					?>
					<p>Update succefully!</p>
					<form method="post" action="">
						<div class="submit"><input type="submit"  value="Reload"/></div>
					</form>										
					<?php
				} else {
				?>			
			
			<p>This version use a different Database Table.</p>
			<p>You have to re-insert your banner.</p>
			<form method="post" action="">
				<input type="hidden" name="toupdate" />
				<div class="submit"><input type="submit"  value="Update"/></div>
			</form>
		</div>	
	<?php	
		}
	}
	
	/**
	 * Build the select/option filter group
	 *
	 * @return 
	 */
	function combo_group() {
		global $wpdb, $_POST;
		$o = '<select onchange="document.forms[\'filter_bannerize\'].submit()" id="group_filter" name="group_filter">' .
		     '<option value="">'.__('All').'</option>';
		$q = "SELECT `group` FROM `" . $this->table_bannerize . "` GROUP BY `group` ORDER BY `group` ";
		$rows = $wpdb->get_results( $q );
		$sel = "";
		foreach( $rows as $row ) {
			if( $_POST['group_filter'] == $row->group ) $sel = 'selected="selected"'; else $sel = "";
			$o .= '<option ' . $sel . 'value="' . $row->group . '">' . $row->group . '</option>';
		}
		$o .= '</select>';
		echo $o;
	}	

	function get_combo_group($name="insert_bannerize") {
		global $wpdb, $_POST;
		$o = '<select onchange="document.forms[\''.$name.'\'].group.value=this.options[this.selectedIndex].value" id="group_filter">' .
		     '<option value=""></option>';
		$q = "SELECT `group` FROM `" . $this->table_bannerize . "` GROUP BY `group` ORDER BY `group` ";
		$rows = $wpdb->get_results( $q );
		$sel = "";
		foreach( $rows as $row ) {
			$o .= '<option value="' . $row->group . '">' . $row->group . '</option>';
		}
		$o .= '</select>';
		return $o;
	}
	
	/**
	 * Build combo menu for target
	 * 
	 * @return 
	 */
	function get_target_combo($sel="") {
		$o = '
		<select name="target" id="target">
			<option></option>
			<option '. ( ($sel=='_blank')?'selected="selected"':'' ) . '>_blank</option>
			<option '. ( ($sel=='_parent')?'selected="selected"':'' ) . '>_parent</option>
			<option '. ( ($sel=='_self')?'selected="selected"':'' ) . '>_self</option>
			<option '. ( ($sel=='_top')?'selected="selected"':'' ) . '>_top</option>
		</select>
		';
		return $o;
	}
	
	/**
	 * Hook the admin/plugin head
	 * 
	 * @return 
	 */
	function set_admin_head() {
		$aba = $this->ajax_url;
	?>
	<link rel="stylesheet" href="<?php echo $this->uri?>/css/style.css" type="text/css" media="screen, projection" />

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		<?php require_once( $this->path . '/js/main.php'); ?>
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
			$size 			= floor( $_FILES['filename']['size'] / (1024*1024) );
			$mime 			= $_FILES['filename']['type'];
			$name 			= $_FILES['filename']['name'];
			$temp 			= $_FILES['filename']['tmp_name'];
			
			$group 		 	= $_POST['group'];
			$description 	= $_POST['description'];
			$url 		 	= $_POST['url'];
			$target 	 	= $_POST['target'];
			
			$uploads		= wp_upload_bits( strtolower($name), '', '' );

			if ( move_uploaded_file( $_FILES['filename']['tmp_name'], $uploads['file'] )) {
				
				$q = "INSERT INTO `" . $this->table_bannerize . "`" .
				     " ( `group`, `description`, `url`, `filename`, `target`, `realpath` )" .
					 " VALUES ('" . $group . "', '" . $description . "', '" . $url . "', '" . $uploads['url'] . "', '" . $target . "', '" . $uploads['file'] . "')";
				$wpdb->query($q);	 
				return( '' );
			} else {
				return ( '<div id="result">Impossibile spostare e posizionare il file ' . $_FILES['filename']['name'] .
				         ' (' . $_FILES['filename']['size'] . ' bytes). Errore ' . $_FILES['filename']['error'] . '</div>' );
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
		$filename = $wpdb->get_var( "SELECT realpath FROM `" . $this->table_bannerize . "` WHERE `id` = " . $_POST['id'] );
		@unlink( $filename );
		
		$q = "DELETE FROM `" . $this->table_bannerize . "` WHERE `id` = " . $_POST['id'];
		$wpdb->query($q);
		return('');
	}
	
	/**
	 * Uodate a banner
	 * 
	 * @return 		(not used)
	 */
	function mysql_update() {
		global $wpdb, $_POST, $_FILES;
		
		$q = "UPDATE `" . $this->table_bannerize . "`" .
			 "set `group` = '{$_POST['group']}', " .
			 "`description` = '{$_POST['description']}', " .
			 "`url` = '{$_POST['url']}', " .
			 "`target` = '{$_POST['target']}' " .
		 	 " WHERE `id` = " . $_POST['id'];
		$wpdb->query($q);
		return('');
	}
	
	/**
	 * Attach settings in Wordpress Plugins list
	 */
	function register_plugin_settings( $pluginfile ) {
		add_action( 'plugin_action_links_'.basename( dirname( $pluginfile ) ) . '/' . basename( $pluginfile ), array( &$this, 'plugin_settings' ), 10, 4 );
	}
	
	function plugin_settings( $links ) {
		$settings_link = '<a href="admin.php?page=wp-bannerize-settings">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}	

	/**
	 * Check if 'bannerize' table exists on the database
	 * if not exists then create it
	 * 
	 * @return 
	 */
	function checkTable() {
		global $wpdb;
		
		/**
		 * @since 2.1.0
		 * Check group field for alter table varchar(128)
		 */
		$desc = $wpdb->get_results( 'DESC `' . $this->table_bannerize . '`' );
		
		foreach( $desc as $field ) {
			if(  $field->Field == "group" )
				if(  $field->Type == "varchar(8)" ) {
					$wpdb->query( 'ALTER TABLE `' . $this->table_bannerize . '` CHANGE `group` `group` VARCHAR(128)' );
					break;
				}
		}
		
		/**
		 * Check old wp-bannerize version
		 */
		$q = 'DESC `' . $this->table_bannerize . '`';
		$rows = $wpdb->get_results( $q );
		if( count( $rows ) > 0 && count( $rows ) < 8 ) {
			// previou version
			return true;
		} else {
			$this->createTable();
		}
		return false;
	}	
	
	/**
	 * Create WP Bannerize table for store banner data
	 * 
	 * @since 2.1.0
	 * group field is varchar(128)
	 * 
	 * @return 
	 */
	function createTable() {
		global $wpdb;
		$q = 'CREATE TABLE IF NOT EXISTS `' . $this->table_bannerize . '` (
			  `id` int(11) NOT NULL auto_increment,
			  `sorter` int(11) NOT NULL,
			  `group` varchar(128) NOT NULL,
			  `description` varchar(255) NOT NULL,
			  `url` varchar(255) NOT NULL,
			  `target` varchar(32) NOT NULL,
			  `filename` varchar(255) NOT NULL,
			  `realpath` varchar(255) NOT NULL,
			  PRIMARY KEY  (`id`)
			)';
		$wpdb->query($q);		
	}
	
	/**
	 * Drop WP Bannerize table
	 * 
	 * @return 
	 */
	function dropTable() {
		global $wpdb;
		$q = 'DROP TABLE `' . $this->table_bannerize . '`';
		$wpdb->query($q);		
	}
	
	/**
	 * Alter WP Bannerize table
	 * 
	 * ALTER TABLE `bannerize` ADD `realpath` VARCHAR( 255 ) NOT NULL 
	 * 
	 * @return 
	 */
	function alterTable() {
		global $wpdb;
		$q = 'ALTER TABLE `' . $this->table_bannerize . '` ADD 
			  `target` varchar(32) NOT NULL, ADD
			  `realpath` varchar(255) NOT NULL
			 ';
		$wpdb->query($q);		
	}
	
} // end of class

?>