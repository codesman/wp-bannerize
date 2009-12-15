<?php
/**
 * Wrap standard function
 * 
 * @return 
 * @param object $args[optional]
 */
function wp_bannerize( $args = '' ) {
	global $wp_bannerize_client;
	$wp_bannerize_client->bannerize( $args );
}

?>