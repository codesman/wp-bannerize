<?php
/**
 * Client (front-end)
 */
class WPBANNERIZE_CLIENT extends WPBANNERIZE_CLASS {
	
	function WPBANNERIZE_CLIENT() {
		$this->WPBANNERIZE_CLASS();							// super
		
		parent::getOptions();								// retrive options from database
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
	function bannerize( $args = '' ) {
		global $wpdb;
		
		$default = array(
						 'group' 				=> '',
						 'container_before'		=> '<ul>',
						 'container_after'		=> '</ul>',
						 'before'				=> '<li>',
						 'after'				=> '</li>'
						);
		
		$new_args = wp_parse_args( $args, $default );
		
		$q = "SELECT * FROM `" . $this->table_bannerize . "` ";
		
		if( $new_args['group'] != "") {
			$q .= " WHERE `group` = '" . $new_args['group'] . "'";
		}
		
		$q .= " ORDER BY `sorter` ASC";
		
		$rows = $wpdb->get_results( $q );	
	
		$o = $new_args['container_before'];
		
		foreach( $rows as $row ) {
			$target = ( $row->target != "" ) ? 'target="' . $row->target . '"' : "";
			$o .= $new_args['before'] . 
				  '<a ' . $target . ' href="' . $row->url . '"><img border="0" src="' . $row->filename . '" /></a>' .
				  $new_args['after'];	
		}
		$o .= $new_args['container_after'];
		
		echo $o;
	}	
} // end of class

?>