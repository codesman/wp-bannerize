<?php
/**
 * Widget support
 *
 * @author          =undo= <g.fazioli@saidmade.com>
 * @copyright       Copyright (C) 2010 Saidmade Srl
 *
 * @since	2.3.3
 * @version	1.0.1
 *
 */
class WP_BANNERIZE_WIDGET extends WP_Widget {

    // @since 2.2.1
    var $table_bannerize = WP_BANNERIZE_TABLE;

    function WP_BANNERIZE_WIDGET() {
        // @since 2.2.1
        global $wpdb;

        $this->table_bannerize = $wpdb->prefix . WP_BANNERIZE_TABLE;
        //
        $widget_ops = array('classname' => 'widget_wp_bannerize', 'description' => 'Amazing Banner Image Manager');
        $control_ops = array('width' => 400, 'height' => 350);
        $this->WP_Widget('wp_bannerize', 'WP Bannerize', $widget_ops, $control_ops);
    }

    function widget( $args, $instance ) {
        global $wpdb;

        // @since 2.3.9
        extract($args);
        extract($instance);

        /**
         * @sice 2.3.0
         * Check for categories
         */
        if( is_array($categories ) )  {
            if( ! is_category( $categories ) ) return;
        }

        $q = "SELECT * FROM `" . $this->table_bannerize . "` ";

        if( $group != "") $q .= " WHERE `group` = '" . $group. "'";

        /**
         * @since 2.0.2
         * Add random option
         */
        $q .= ($random == '0') ? " ORDER BY `sorter` ASC" : "ORDER BY RAND()";

        /**
         * @since 2.0.0
         * Limit rows number
         */
        if( $limit != "") $q .= " LIMIT 0," . $limit ;

        $rows = $wpdb->get_results( $q );


        echo $before_widget;
        echo $container_before;

        // @since 2.3.2
        $even_before = $odd_before = $alternate_class = "";
        $index = 0;

        $odd_before = str_replace("%alt%", "", $before);
        if($alt_class != "") {
            $alternate_class = 'class="' . $alt_class . '"';
            $even_before = str_replace("%alt%", $alternate_class, $before);
        }
        $new_link_class = ($link_class != "") ? 'class="'.$link_class.'"' : "";

        foreach( $rows as $row ) {
            $target = ( $row->target != "" ) ? 'target="' . $row->target . '"' : "";

            $o .= ( ($index%2 == 0) ? $odd_before : $even_before ) . '<a ' . $new_link_class . ' ' . $target . ' href="' . $row->url . '"><img alt="'.$row->description.'" border="0" src="' . $row->filename . '" /></a>' . $after;
            $index++;
        }

        echo $o;

        echo $container_after;
        echo $after_widget;
    }

    function update( $new_instance, $old_instance ) {
        $instance                       = $old_instance;
        $instance['title'] 		= strip_tags($new_instance['title']);
        $instance['group']              = strip_tags($new_instance['group']);
        $instance['random'] 		= strip_tags($new_instance['random']);
        $instance['limit'] 		= strip_tags($new_instance['limit']);
        $instance['categories'] 	= ($new_instance['categories']);

        $instance['container_before'] 	= ($new_instance['container_before']);
        $instance['container_after'] 	= ($new_instance['container_after']);
        $instance['before'] 		= ($new_instance['before']);
        $instance['after'] 		= ($new_instance['after']);

        $instance['alt_class'] 		= ($new_instance['alt_class']);
        $instance['link_class']		= ($new_instance['link_class']);

        return $instance;
    }

    function form( $instance ) {
        $instance	= wp_parse_args( (array) $instance,
            array( 'title' 	=> '',
            'random'		=> '0',
            'limit'		=> '10',
            'container_before'  => '<ul>',
            'container_after'	=> '</ul>',
            'before'		=> '<li %alt%>',
            'after'		=> '</li>',
            'categories'        => array(),
            'alt_class'         => 'alt',
            'link_class'        => '' )
        );
        $title                  = strip_tags($instance['title']);
        $group                  = strip_tags($instance['group']);
        $random                 = ($instance['random']);
        $limit                  = strip_tags($instance['limit']);
        $categories             = ($instance['categories']);

        $container_before	= ($instance['container_before']);
        $container_after	= ($instance['container_after']);
        $before			= ($instance['before']);
        $after			= ($instance['after']);

        $alt_class              = strip_tags($instance['alt_class']);
        $link_class             = strip_tags($instance['link_class']);

        ?>
<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
<p><label for="<?php echo $this->get_field_id('group'); ?>"><?php _e('Key:'); ?></label>
        <?php echo $this->get_group( $group ) ?></p>
<p><label for="<?php echo $this->get_field_id('random'); ?>"><?php _e('Random:'); ?></label>
    <input <?php echo ($random == '1') ? 'checked="chekced"' : '' ?> value="1" type="checkbox" name="<?php echo $this->get_field_name('random'); ?>" id="<?php echo $this->get_field_id('random'); ?>" /></p>

<p><label for="<?php echo $this->get_field_id('categories'); ?>"><?php _e('Show only for these Categories:'); ?></label></p>
<p><?php echo $this->get_categories_checkboxes($categories) ?></p>

<p><label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Max:'); ?></label>
    <input type="text" value="<?php echo $limit ?>" name="<?php echo $this->get_field_name('limit'); ?>" id="<?php echo $this->get_field_id('limit'); ?>" /></p>
<p><strong>HTML Markup:</strong></p>
<p><label for="<?php echo $this->get_field_id('container_before'); ?>"><?php _e('container_before:'); ?></label>
    <input size="8" type="text" value="<?php echo $container_before ?>" name="<?php echo $this->get_field_name('container_before'); ?>" id="<?php echo $this->get_field_id('container_before'); ?>" /></p>

<p><label for="<?php echo $this->get_field_id('before'); ?>"><?php _e('before:'); ?></label>
    <input size="8" type="text" value="<?php echo $before ?>" name="<?php echo $this->get_field_name('before'); ?>" id="<?php echo $this->get_field_id('before'); ?>" />
    alt class: <input size="8" type="text" value="<?php echo $alt_class ?>" name="<?php echo $this->get_field_name('alt_class'); ?>" id="<?php echo $this->get_field_id('alt_class'); ?>" />
    (Es. &lt;li class="alt"&gt; ...)
</p>

<p><label for="<?php echo $this->get_field_id('link_class'); ?>"><?php _e('link_class:'); ?></label>
    <input size="8" type="text" value="<?php echo $link_class ?>" name="<?php echo $this->get_field_name('link_class'); ?>" id="<?php echo $this->get_field_id('link_class'); ?>" /></p>


<p><label for="<?php echo $this->get_field_id('after'); ?>"><?php _e('after:'); ?></label>
    <input size="8" type="text" value="<?php echo $after ?>" name="<?php echo $this->get_field_name('after'); ?>" id="<?php echo $this->get_field_id('after'); ?>" /></p>


<p><label for="<?php echo $this->get_field_id('container_after'); ?>"><?php _e('container_after:'); ?></label>
    <input size="8" type="text" value="<?php echo $container_after ?>" name="<?php echo $this->get_field_name('container_after'); ?>" id="<?php echo $this->get_field_id('container_after'); ?>" /></p>

    <?php
    }

    // Widget Interface
    function get_group($group = '' ) {
        global $wpdb;
        $o = '<select rel="'.$group.'" id="' . $this->get_field_id('group') . '" name="' . $this->get_field_name('group')  . '">' .
            '<option value=""></option>';
        $q = "SELECT `group` FROM `" . $this->table_bannerize . "` GROUP BY `group` ORDER BY `group` ";
        $rows = $wpdb->get_results( $q );
        foreach( $rows as $row ) {
            $sel = ($group == $row->group) ? 'selected="selected"' : "" ;
            $o .= '<option ' . $sel . ' value="' . $row->group . '">' . $row->group . '</option>';
        }
        $o .= '</select>';
        return $o;
    }

    /**
     * Get Select Checked Categories
     */
    function get_categories_checkboxes( $selected_cats = null ) {

        $all_categories = get_categories();
        $o = '<ul style="margin-left:12px">';

        foreach($all_categories as $key => $cat) {
            if($cat->parent == "0") $o .= $this->_i_show_category($cat, $selected_cats);
        }
        return $o . '</ul>';
    }

    function _i_show_category($cat_object, $selected_cats = null) {
       $checked = "";
       if(!is_null($selected_cats) && is_array($selected_cats)) {
           $checked = (in_array($cat_object->cat_ID, $selected_cats)) ? 'checked="checked"' : "";
       }
       $ou = '<li><label><input ' . $checked .' type="checkbox" name="' . $this->get_field_name('categories').'[]" value="'. $cat_object->cat_ID .'" /> ' . $cat_object->cat_name . '</label>';

       $childs = get_categories('parent=' . $cat_object->cat_ID);
       foreach($childs as $key => $cat) {
           $ou .= '<ul style="margin-left:12px">' . $this->_i_show_category($cat, $selected_cats) . '</ul>';
       }
       $ou .= '</li>';
       return $ou;
    }
}

?>