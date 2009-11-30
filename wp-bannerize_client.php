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
     * group			If '' show all group, else code of group (default '')
     * container_before		Main tag container open (default <ul>)
     * container_after		Main tag container close (default </ul>)
     * before			Before tag banner open (default <li %alt%>)
     * after			After tag banner close (default </li>)
     * random                   Show random banner sequence (default '')
     * categories               Category ID separated by commas. (default '')
     * limit			Limit rows number (default '' - show all rows)
     *
     */
    function bannerize( $args = '' ) {
        global $wpdb;

        $default = array(
            'group' 			=> '',
            'container_before'		=> '<ul>',
            'container_after'		=> '</ul>',
            'before'			=> '<li %alt%>',
            'after'			=> '</li>',
            'random'                    => '',
            'categories'                => '',
            'alt_class'                 => 'alt',
            'link_class'                => '',
            'limit'			=> ''
        );

        $new_args = wp_parse_args( $args, $default );

        /**
         * @sice 2.3.0
         * Check for categories
         */
         if( $new_args['categories'] != "")  {
            $cat_ids = explode(",", $new_args['categories']);
            if( ! is_category( $cat_ids ) ) return;
        }

        $q = "SELECT * FROM `" . $this->table_bannerize . "` ";

        if( $new_args['group'] != "") $q .= " WHERE `group` = '" . $new_args['group'] . "'";

        /**
         * @since 2.0.2
         * Add random option
         */
        $q .= ($new_args['random'] == '') ? " ORDER BY `sorter` ASC" : "ORDER BY RAND()";

        /**
         * @since 2.0.0
         * Limit rows number
         */
        if( $new_args['limit'] != "") $q .= " LIMIT 0," . $new_args['limit'] ;

        $rows = $wpdb->get_results( $q );

        $o = $new_args['container_before'];

        // @since 2.3.2
        $even_before = $odd_before = $alternate_class = "";
        $index = 0;

        $odd_before = str_replace("%alt%", "", $new_args['before']);
        if( $new_args['alt_class'] != "" ) {
            $alternate_class = 'class="' . $new_args['alt_class'] . '"';
            $even_before = str_replace("%alt%", $alternate_class, $new_args['before']);
        }
        $new_link_class = ($new_args['link_class'] != "") ? 'class="' . $new_args['link_class'] . '"' : "";

        foreach( $rows as $row ) {
            $target = ( $row->target != "" ) ? 'target="' . $row->target . '"' : "";
            $o .= ( ($index%2 == 0 ) ? $odd_before : $even_before ) .
                '<a ' . $new_link_class . ' ' . $target . ' href="' . $row->url . '"><img alt="'.$row->description.'" border="0" src="' . $row->filename . '" /></a>' .
                $new_args['after'];
            $index++;
        }
        $o .= $new_args['container_after'];

        echo $o;
    }
} // end of class

?>