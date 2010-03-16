<?php
/**
 * Class for Manage Admin (back-end)
 *
 * @package         wp-bannerize
 * @subpackage      wp-bannerize_client
 * @author          =undo= <g.fazioli@saidmade.com>
 * @copyright       Copyright (C) 2010 Saidmade Srl
 * 
 */
class WPBANNERIZE_ADMIN extends WPBANNERIZE_CLASS {

    function WPBANNERIZE_ADMIN() {
        $this->WPBANNERIZE_CLASS();	// super

        /**
         * Load localizations if available
         *
         * @since 2.4.0
         */
		load_plugin_textdomain ( 'wp-bannerize' , false, 'wp-bannerize/localization'  );

        $this->initDefaultOption();
    }

    /**
     * Init the default plugin options and re-load from WP
     *
     * @since 2.2.2
     */
    function initDefaultOption() {
        /**
         * Add version control in options.
         * In questa maniera dalle release successive quando si chiama il
         * metodo checkTable() � possibile verificare la versione del plugin
         * precedente; senza impazzire con le DESC sulle tabelle.
         *
         */
        $this->options = array('wp_bannerize_version' => $this->version );
        add_option( $this->options_key, $this->options, $this->options_title );

        parent::getOptions();

        /**
         * Check table for new field
         *
         * @since 1.4+
         */
        $this->update = $this->checkTable();

        /**
         * Add option menu in Wordpress backend
         */
        add_action('admin_menu', 	array( $this, 'add_menus') );


        /**
         * Add wp_enqueue_script for jquery library
         *
         * @since 2.3.6
         */
        wp_enqueue_script('jquery-ui-sortable');

        /**
         * Add thickbox standard Wordpress support
         *
         * @since 2.1.0
         */
        wp_enqueue_script('thickbox');
        wp_enqueue_style('thickbox');

        /**
         * Add queue for style sheet
         *
         * @since 2.3.6
         */
        wp_register_style('wp-bannerize-style-css', $this->uri . "/css/style.css");
        wp_enqueue_style('wp-bannerize-style-css');

        /**
         * Add main admin javascript
         *
         * @since 2.4.0
         */
		wp_enqueue_script ( 'wp-bannerize-main-js' , $this->uri . '/js/main.js' , array ( 'jquery' ) , '1.2.0' , true );
		wp_localize_script ( 'wp-bannerize-main-js' , 'wpBannerizeMainL10n' , array (
                                                    'ajaxURL' => $this->ajax_url,
													'messageConfirm' => __( 'WARINING!! Do you want delete this banner?'  , 'wp-bannerize' )
													) );
        /**
         * Update version control in options
         *
         * @since 2.2.2
         */
        update_option( $this->options_key, $this->options);
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
            $menus['main'] = add_object_page('WP Bannerize', 'WP Bannerize', 8, $this->directory.'-settings' );
        } else
            $menus['main'] = add_menu_page('WP Bannerize', 'WP Bannerize', 8, $this->directory.'-settings', array(&$this,'set_options_subpanel') );

        $menus['settings'] = add_submenu_page($this->directory.'-settings', __('Settings', 'wp-bannerize'), __('Settings', 'wp-bannerize'), 8, $this->directory.'-settings', array(&$this,'set_options_subpanel') );

        /**
         * Add contextual Help
         */
        if (function_exists('add_contextual_help')) {
            add_contextual_help($menus['main'],'<p><strong>'.__('Use', 'wp-bannerize').':</strong></p>' .
                '<pre>wp_bannerize();</pre>or<br/>' .
                '<pre>wp_bannerize( \'group=a&limit=10\' );</pre>or<br/>' .
                '<pre>wp_bannerize( \'group=a&limit=10&random=1\' );</pre>or<br/>' .
                '<pre>wp_bannerize( \'group=a&limit=10&random=1&before=&lt;li %alt%>&alt_class=pair\' );</pre><br/>' .
                '<pre>
* group               If \'\' show all groups, else show the selected group code (default \'\')
* container_before    Main tag container open (default &lt;ul&gt;)
* container_after     Main tag container close (default &lt;/ul&gt;)
* before              Before tag banner open (default &lt;li %alt% &gt;) see alt_class below
* after               After tag banner close (default &lt;/li&gt;) 
* random              Show random banner sequence (default \'\')
* categories          Category ID separated by commas (defualt \'\')
* alt_class           class alternate for "before" TAG (use before param)
* link_class          Additional class for link TAG A
* limit               Limit rows number (default \'\' - show all rows)</pre>' 			
            );
        }
    }

    /**
     * Draw Options Panel
     */
    function set_options_subpanel() {
        global $wpdb, $_POST;

        if( $this->update ) {
            $this->showUpdate();
            return;
        }

        /**
         * Any error flag
         */
        $any_error = "";

        if( isset( $_POST['command_action'] ) ) {
            $any_error = __('Your settings have been saved.', 'wp-bannerize');

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
        if( $any_error != '') {
            echo '<div id="message" class="updated fade"><p>' . $any_error . '</p></div>';
        }
        ?>

<div class="wrap">
    <div class="icon32" id="icon-options-general"><br/></div>
    <h2><?=$this->options_title?> ver. <?=$this->version?></h2>

    <div id="poststuff" class="metabox-holder has-right-sidebar">
        <div class="inner-sidebar">
            <div id="side-sortables" class="meta-box-sortabless ui-sortable" style="position: relative;">

                <div id="sm_pnres" class="postbox">
                    <h3 class="hndle"><span>Links</span></h3>
                    <div class="inside">
                       <div style="text-align:center;margin-bottom:12px"><?php include_once('adv.php') ?></div>
                       <p style="text-align:center"><a href="http://www.saidmade.com">Saidmade Srl</a></p>
                       <p style="text-align:center"><a href="http://www.undolog.com">Research &amp; Development Blog</a></p>
                    </div>
                </div>

                <div id="sm_pnres" class="postbox">
                    <h3 class="hndle"><span>Donate</span></h3>
                    <div class="inside">
                        <p style="text-align:center;font-family:Tahoma;font-size:10px">Developed by <a target="_blank" href="http://www.saidmade.com"><img alt="Saidmade" align="absmiddle" src="http://labs.saidmade.com/images/sm-a-80x15.png" border="0" /></a>
                            <br/>
                            more Wordpress plugins on <a target="_blank" href="http://labs.saidmade.com">labs.saidmade.com</a> and <a target="_blank" href="http://www.undolog.com">Undolog.com</a>
                            <br/>
                        </p>
                        <div>
                            <form style="text-align:center;width:auto;margin:0 auto" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                                <input type="hidden" name="cmd" value="_s-xclick">
                                <input type="hidden" name="hosted_button_id" value="3499468">
                                <input type="image" src="https://www.paypal.com/it_IT/IT/i/btn/btn_donateCC_LG.gif" name="submit" alt="PayPal - Il sistema di pagamento online più facile e sicuro!">
                                <img alt="" border="0" src="https://www.paypal.com/it_IT/i/scr/pixel.gif" width="1" height="1">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="has-sidebar sm-padded">
            <div id="post-body-content" class="has-sidebar-content">
                <div class="meta-box-sortabless">

                    <div id="sm_rebuild" class="postbox">
                        <h3 class="hndle"><span><?php  _e('Insert new Banner', 'wp-bannerize')?></span></h3>
                        <div class="inside">
                            <form class="form_box" name="insert_bannerize" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="command_action" id="command_action" value="mysql_insert" />
                                <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />

                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><label for="group"><?php _e('Image', 'wp-bannerize')?>:</label></th>
                                        <td><input type="file" name="filename" id="filename" size="40" /></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label for="group"><?php _e('Key', 'wp-bannerize')?>:</label></th>
                                        <td><input type="text" maxlength="128" name="group" id="group" value="A" size="32" style="text-align:right" /> <?php echo $this->get_combo_group() ?> (<?php _e('Insert a key max 128 chars', 'wp-bannerize')?>)</td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label for="description"><?php _e('Description', 'wp-bannerize')?>:</label></th>
                                        <td><input type="text" name="description" id="description" value="" size="32" /></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label for="url">URL:</label></th>
                                        <td><input type="text" name="url" id="url" value="" size="32" /> <label for="url"><?php _e('Target', 'wp-bannerize')?>:</label> <?php echo $this->get_target_combo() ?></td>
                                    </tr>
                                </table>
                                <p class="submit"><input class="button-primary" type="submit" value="<?php _e('Insert', 'wp-bannerize')?>" /></p
                            </form>

                            <form style="display:none" name="delete_bannerize" method="post" action="">
                                <input type="hidden" name="command_action" id="command_action" value="mysql_delete" />
                                <input type="hidden" name="id" id="id" value="" />
                            </form>
                        </div>
                    </div>

                    <div style="float:left;width:100%">

                        <div class="tablenav">
                            <div class="alignleft actions">
                                <form class="form_box" name="filter_bannerize" method="post" action="">
                                    <?php $this->combo_group(); ?> <input class="button-secondary" type="submit" value="<?php _e('Filter', 'wp-bannerize')?>"/> | <?php _e('Use', 'wp-bannerize')?> <img align="absmiddle" alt="Drag and Drop" border="0" src="<?php echo $this->uri ?>/css/images/arrow_ns.png" /> <?php _e('for drag and drop to change order', 'wp-bannerize')?>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <?php
    }

    /**
     * Update previous WP Bannerize version to 1.4
     *
     * @return
     */
    function showUpdate() {
        global $wpdb, $_POST;
        ?>
<div class="wrap">
    <div class="icon32" id="icon-options-general"><br/></div>
    <h2><?=$this->options_title?> ver. <?=$this->version?></h2>
            <?php
            if( isset( $_POST['toupdate'])) {
                $this->alterTable();
                ?>
    <div class="updated">
        <p><?php echo __('Update succefully') ?></p>
    </div>
    <form method="post" action="">
        <div class="submit"><input type="submit"  value="<?php echo __('Reload') ?>"/></div>
    </form>
            <?php
            } else {
                ?>

    <p><?php echo __('Please, re-insert your banners.') ?></p>
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
     * Get Select Checked Categories
     */
    function get_categories_checkboxes( $cats = null ) {
        if(!is_null($cats)) $cat_array = explode(",", $cats);
        $res = get_categories();
        $o = "";
        foreach($res as $key => $cat) {
            $checked = "";
            if(!is_null($cats)) {
                if( in_array( $cat->cat_ID, $cat_array) )
                    $checked = 'checked="checked"';
            }
            $o .= '<label><input ' . $checked .' type="checkbox" name="categories[]" id="categories[]" value="'. $cat->cat_ID .'" /> ' . $cat->cat_name . '</label> ';
       }
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
     * Esegue l'upload e lo store nel database
     *
     * Array ( [name] 		=> test.pdf
     * 		   [type]		=> application/pdf
     * 		   [tmp_name] 	=> /tmp/phpcXS1lh
     *    	   [error] 		=> 0
     *    	   [size] 		=> 277304 )
     *
     * @return
     */
    function mysql_insert() {
        global $wpdb, $_POST, $_FILES;

        // verifica eventuali errori
        if( $_FILES['filename']['error'] == 0 ) {
            $size           = floor( $_FILES['filename']['size'] / (1024*1024) );
            $mime           = $_FILES['filename']['type'];
            $name           = $_FILES['filename']['name'];
            $temp           = $_FILES['filename']['tmp_name'];

            $group          = $_POST['group'];
            $description 	= $_POST['description'];
            $url            = $_POST['url'];
            $target 	 	= $_POST['target'];

            $uploads		= wp_upload_bits( strtolower($name), '', '' );

            if ( move_uploaded_file( $_FILES['filename']['tmp_name'], $uploads['file'] )) {

                $q = "INSERT INTO `" . $this->table_bannerize . "`" .
                    " ( `group`, `description`, `url`, `filename`, `target`, `realpath` )" .
                    " VALUES ('" . $group . "', '" . $description . "', '" . 
                                   $url . "', '" . $uploads['url'] . "', '" . $target . "', '" . $uploads['file'] . "')";
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
     * Update a banner
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
        add_action( 'plugin_action_links_' . basename( dirname( $pluginfile ) ) . '/' . basename( $pluginfile ), array( &$this, 'plugin_settings' ), 10, 4 );
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
         * Da questa release è possibile controllare la versione del
         * plugin in modo da sapere se effettuare modifiche oppure no.
         *
         * @since 2.2.2
         */
        if( !isset($this->options['wp_bannerize_version']) ) {
            $this->options = array('wp_bannerize_version' => $this->version );
            update_option( $this->options_key, $this->options);
        }

        /**
         * Prelevo la versione attuale
         *
         * version_array[0] = release
         * version_array[1] = minor
         * version_array[2] = revision
         */
        $version_array = explode(".", $this->options['wp_bannerize_version'] );

        /**
         * Check old table name and rename it
         * RENAME TABLE `bannerize`  TO `wp_bannerize` ;
         *
         * @since 2.2.1
         */
        $desc = $wpdb->get_results( 'DESC `' . $this->_old_table_bannerize . '`' );

        if(count($desc) > 0) {
            $wpdb->query( 'RENAME TABLE `' . $this->_old_table_bannerize . '` TO `' . $this->table_bannerize . '`' );
        }

        /**
         * Check group field for alter table varchar(128)
         *
         * @since 2.1.0
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
     * group field is varchar(128)
     *
     * @since 2.1.0
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