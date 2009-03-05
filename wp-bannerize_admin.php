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
		$this->checkTable();
		
		add_action('admin_menu', 	array( $this, 'set_options_page') );
	}
	
	/**
	 * ADD OPTION PAGE TO WORDPRESS ENVIRORMENT
	 *
	 * Add callback for adding options panel
	 *
	 */
	function set_options_page() {
		if ( function_exists('add_options_page') ) {
	 		$plugin_page = add_options_page( $this->options_title, $this->options_title, 8, basename(__FILE__), array( $this, 'set_options_subpanel') );
			add_action( 'admin_head-'. $plugin_page, array( $this, 'set_admin_head' ) );
		}
	}
	
	/**
	 * Draw Options Panel
	 */
	function set_options_subpanel() {
		global $wpp_options, $wpdb, $_POST;
	
		$any_error = "";										// any error flag
	
		if( isset( $_POST['command_action'] ) ) {				// have to save options	
			$any_error = 'Your settings have been saved.';
	
			switch( $_POST['command_action'] ) {
				case "mysql_insert":
					$any_error = $this->mysql_insert();
					break;
				case "mysql_delete":
					$any_error = $this->mysql_delete();
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
	    <h2><?=$this->options_title?> ver. <?=$this->version?></h2>
	
		<h3>Insert a new banner</h3>
		<form class="form_box" name="insert_bannerize" method="post" action="" enctype="multipart/form-data">
			<input type="hidden" name="command_action" id="command_action" value="mysql_insert" />
			<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
			
			<p>
				<label for="group">Group: </label><input type="text" name="group" id="group" value="A" size="8" style="text-align:right" />
				<label for="description">Description: </label><input type="text" name="description" id="description" value="" size="32" /> 
				<label for="url">URL: </label><input type="text" name="url" id="url" value="" size="32" />
			    <label for="group">Image: </label><input type="file" name="filename" id="filename" size="40" />
			</p>
			<div class="submit"><input type="submit" value="Insert" /></div>
		</form>
		
		<form style="display:none" name="delete_bannerize" method="post" action="">
			<input type="hidden" name="command_action" id="command_action" value="mysql_delete" />
			<input type="hidden" name="id" id="id" value="" />
		</form>
	
		<h3>Banners List</h3>
		<form class="form_box" name="filter_bannerize" method="post" action="">
			<p><label for="group_filter">Show group: </label><?php $this->combo_group(); ?></p>
		</form>
		<?php
		
			$q = "SELECT * FROM `" . $this->table_bannerize . "`";
			
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
					  '<td>' . $row->description . '</td>' .
					  '<td align="center"><a target="_blank" href="' . $row->url . '"><img height="55" border="0" src="' . $this->uploads_url . $row->filename . '" /></a></td>' .
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
				<input type="image" src="https://www.paypal.com/it_IT/IT/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - Il sistema di pagamento online pi� facile e sicuro!">
				<img alt="" border="0" src="https://www.paypal.com/it_IT/i/scr/pixel.gif" width="1" height="1">
			</form>
		</p>	
	
		</div>
		
		<?php
		
	}
	
	/**
	 * Build the select/option filter group
	 *
	 * @return 
	 */
	function combo_group() {
		global $wpdb, $_POST;
		$o = '<select onchange="document.forms[\'filter_bannerize\'].submit()" id="group_filter" name="group_filter">' .
		     '<option value="">All</option>';
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
	
	/**
	 * Hook the admin/plugin head
	 * 
	 * @return 
	 */
	function set_admin_head() {
	?>
	<style type="text/css">
		table#list_bannerize {
			
		}
		table#list_bannerize thead th {
			background:#ccc;
			border-bottom:1px solid;
			padding:6px;
		}
		table#list_bannerize tbody tr {
			background:#f1f1f1;
			border:3px solid #000;
		}
		table#list_bannerize tbody td {
			border:1px dotted;
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
							url: "<?=$this->_AJAX_URL?>",
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
			$pathname = $this->uploads_path . $basepath;
			@mkdir( $pathname, 0777, true  );
			
			$filename = $pathname . strtolower($name);
			$urlname  = $basepath . strtolower($name);
			
			if ( move_uploaded_file( $_FILES['filename']['tmp_name'], $filename )) {
				
				$q = "INSERT INTO `" . $this->table_bannerize . "`" .
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
		$filename = $wpdb->get_var( "SELECT filename FROM `" . $this->table_bannerize . "` WHERE `id` = " . $_POST['id'] );
		@unlink( $this->uploads_path . $filename );
		
		$q = "DELETE FROM `" . $this->table_bannerize . "` WHERE `id` = " . $_POST['id'];
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
		
		$q = 'CREATE TABLE IF NOT EXISTS `' . $this->table_bannerize . '` (
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
	
} // end of class

?>