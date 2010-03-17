<?php
/**
 * Ajax gateway
 *
 * @package         wp-bannerize
 * @subpackage      ajax.php
 * @author          =undo= <g.fazioli@saidmade.com>
 * @copyright       Copyright (C) 2010 Saidmade Srl
 * 
 */
if ( isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) {
    require_once('../../../wp-config.php');
    $_db = @mysql_connect ( DB_HOST, DB_USER, DB_PASSWORD ); mysql_select_db( DB_NAME );
    foreach($_POST["item"] as $key => $value){
        $sql = "UPDATE `" . $wpdb->prefix ."bannerize` SET `sorter` = {$key} WHERE id = {$value}";
        $result = mysql_query($sql);
    }  
}
?>