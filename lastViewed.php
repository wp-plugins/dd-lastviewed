<?php
/*
Plugin Name: DD Last Viewed
Version: 0.9.1
Plugin URI: http://dijkstradesign.com
Description: A plug-in to add a last viewed widget
Author: Wouter Dijkstra
Author URI: http://dijkstradesign.com
*/


/*  Copyright 2013  WOUTER DIJKSTRA  (email : info@dijkstradesign.nl)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


class lastviewed extends WP_Widget
{
    function lastviewed()
    {
        parent::WP_Widget(false, 'Last viewed', array('description' => __('Shows the last viewed of a post or custom posttype.', 'text_domain'),));
    }

    function form($instance)
    {
        if (isset($instance['lastviewedTitle']))
        {
            $lastviewedTitle = $instance['lastviewedTitle'];
        }
        else{
            $lastviewedTitle = "Last Viewed";
        }

        $widgetID = str_replace('lastviewed-', '', $this->id);

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('lastviewedTitle'); ?> ">Titel:</label>
            <input id="<?php echo $this->get_field_id('lastviewedTitle'); ?>" class=" widefat textWrite_Title" type="text" value="<?php echo esc_attr($lastviewedTitle); ?>"name="<?php echo $this->get_field_name('lastviewedTitle'); ?>">
        </p>
        <p class="typeholder">Select the types:<br/>

        <?php
        $args = array(
            'public' => true,
            '_builtin' => false
        );

        //grab the custom_pos_types active in theme
        $output = 'names'; // names or objects, note names is the default
        $operator = 'and'; // 'and' or 'or'
        $post_types = get_post_types($args, $output, $operator);
        foreach ($post_types as $post_type) {

            $obj = get_post_type_object( $post_type );
            $RealName = $obj->labels->name;

            $option = '<label for="LV_checkbox_' . $post_type . '">';
            $option .= '<input type="checkbox" class="checkbox posttypeCheckbox customPostCheck"id="LV_checkbox_' . $post_type . '" name="' . $this->get_field_name('selected_posttypes') . '[]"';

            if (isset($instance['selected_posttypes']))
            {
                $selected_posttypes = $instance['selected_posttypes'];
            }
            else{
                $selected_posttypes = "";
            }

            if (is_array($selected_posttypes)) {
                foreach ($selected_posttypes as $selected_type) {
                    if ($selected_type == $post_type) {
                        $option = $option . ' checked="checked"';
                    }
                }
            }
            $option .= ' value="' . $post_type . '" />';
            $option .= $RealName;
            $option .= '</label><br/>';
            echo $option;
        }

        $post_types = get_post_types('', 'names');

        foreach ($post_types as $post_type) {

            $obj = get_post_type_object( $post_type );
            $RealName = $obj->labels->name;

            //Removes those posttypes from list
            if ($post_type == 'page' || $post_type == 'attachment' || $post_type == 'revision' || $post_type == 'nav_menu_item') {
                break;
            }

            $option = '<label for="LV_checkbox_' . $post_type . '">';
            $option .= '<input type="checkbox" class="checkbox posttypeCheckbox postCheck" id="LV_checkbox_' . $post_type . '" name="' . $this->get_field_name('selected_posttypes') . '[]"';

            if (isset($instance['selected_posttypes']))
            {
                $selected_posttypes = $instance['selected_posttypes'];
            }
            else{
                $selected_posttypes = "";
            }

            if (is_array($selected_posttypes)) {
                foreach ($selected_posttypes as $selected_type) {
                    if ($selected_type == $post_type) {
                        $option = $option . ' checked="checked"';
                    }
                }
            }
            $option .= ' value="' . $post_type . '" />';
            $option .= $RealName;
            $option .= '</label><br/>';
            echo $option;
        }
        echo '</p>';

        if (isset($instance['lastViewed_thumb']))
        {
            $lastViewed_thumb = $instance['lastViewed_thumb'];
        }
        else{
            $lastViewed_thumb = "";
        }

        $lastViewed_thumb = esc_attr($lastViewed_thumb);
        if ($lastViewed_thumb == 'yes') {
            $noSelect = "";
            $yesSelect = "checked";
        } else {
            $noSelect = "checked";
            $yesSelect = "";
        }


        echo '
            <p><label>Show thumbnails if excist:</label><br>
            <label class="set_thumb"><input type="radio" name="' . $this->get_field_name('lastViewed_thumb') . '" value="no" ' . $noSelect . ' />No</label><br/>
            <label class="set_thumb"><input type="radio" name="' . $this->get_field_name('lastViewed_thumb') . '" value="yes" ' . $yesSelect . '/>Yes</label><br/>
            </p>
        ';

        if (isset($instance['lastViewed_total']))
        {
            $lastViewed_total = $instance['lastViewed_total'];
        }
        else{
            $lastViewed_total = "";
        }
        $lastViewed_total = esc_attr($lastViewed_total);

        if ($lastViewed_total == "") {
            $lastViewed_total = 5;
        }
        echo '
            <p><label>Number to show:<label>
            <input type="number" name="' . $this->get_field_name('lastViewed_total') . '" min="1" max="10" value="' . $lastViewed_total . '"></p>
        ';

        if (isset($instance['lastViewed_truncate']))
        {
            $lastViewed_truncate = $instance['lastViewed_truncate'];
        }
        else{
            $lastViewed_truncate = "";
        }

        $lastViewed_truncate = esc_attr($lastViewed_truncate);
        if ($lastViewed_truncate == "") {
            $lastViewed_truncate = 78;
        }
        echo'
            <p><label>Truncate excerpt:<label>
            <input type="number" name="' . $this->get_field_name('lastViewed_truncate') . '" min="1" max="10" value="' . $lastViewed_truncate . '"></p>
        ';

        if (isset($instance['lastViewed_linkname']))
        {
            $lastViewed_linkname = $instance['lastViewed_linkname'];
        }
        else{
            $lastViewed_linkname = "More";
        }

        $lastViewed_linkname = esc_attr($lastViewed_linkname);

        echo'
            <p><label>Link name:<label>
             <input id="'. $this->get_field_id('lastViewed_linkname').'" class="textWrite_Title" type="text" value="'.esc_attr($lastViewed_linkname).'"name="'. $this->get_field_name('lastViewed_linkname').'"></p>';

        echo'
             <p style="font-size: 11px; opacity:0.6">
            <span class="shortcodeTtitle">Shortcode:</span>
            <span class="shortcode">[dd_lastviewed widget_id="'.$widgetID.'"]</span>
        </p>
        ';
    }

    function update($new_instance, $old_instance)
    {
        // processes widget options to be saved
        $instance = $old_instance;
        $instance['lastviewedTitle'] = strip_tags($new_instance['lastviewedTitle']);
        $instance['selected_posttypes'] = $new_instance['selected_posttypes'];
        $instance['lastViewed_thumb'] = strip_tags($new_instance['lastViewed_thumb']);
        $instance['lastViewed_total'] = strip_tags($new_instance['lastViewed_total']);
        $instance['lastViewed_truncate'] = strip_tags($new_instance['lastViewed_truncate']);
        $instance['lastViewed_linkname'] = strip_tags($new_instance['lastViewed_linkname']);
        return $instance;
        return $new_instance;
    }

    function widget($args)
    {
        $widgetID = $args['widget_id'];

        if (isset($args['by_shortcode']))
        {
            $by_shortcode = $args['by_shortcode'];
        }
        else{
            $by_shortcode = '';
        }

        $widgetID = str_replace('lastviewed-', '', $widgetID);
        $widgetOptions = get_option($this->option_name);

        $lastviewedTitle = $widgetOptions[$widgetID]['lastviewedTitle'];
        $lastViewed_thumb = $widgetOptions[$widgetID]['lastViewed_thumb'];
        $lastViewed_total = $widgetOptions[$widgetID]['lastViewed_total'];
        $lastViewed_truncate = $widgetOptions[$widgetID]['lastViewed_truncate'];
        if ($lastViewed_truncate == ''){
            $lastViewed_truncate = 78;
        }
        $lastViewed_linkname = $widgetOptions[$widgetID]['lastViewed_linkname'];

        $widgetId = $args['widget_id'];
        preg_match_all('!\d+!', $widgetId, $widgetId);
        $widgetId = implode(' ', $widgetId[0]);

        $before_widget = '<div id="'.$by_shortcode.'lastViewed-' . $widgetId . '" class="widget widget_lastViewed">';
        $after_widget = '</div>';

        $lastlist = ($_COOKIE['lastViewed']);
        $idList = explode(",", $lastlist);
        $idList = array_reverse($idList);


        if (isset($_COOKIE["lastViewed"]) && $lastlist !== "") {
            ?>
            <?php echo $before_widget; ?>
            <h3><?php echo $lastviewedTitle ?></h3>
            <ul class="lastViewedList">
                <?php
                foreach ($idList as $id) {

                    global $wpdb;
                    $post_exists = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE id = '" . $id . "'", 'ARRAY_A');

                    if ($post_exists) {

                        $the_post = get_post($id); //Gets post ID
                        $the_excerpt = $the_post->post_excerpt;
                        $the_content = $the_post->post_content;
                        if ($the_excerpt == '') {
                            $the_excerpt = $the_content;
                        }
                        $viewType = get_post_type($the_post);

                        //get also the selected types
                        $selected_posttypes = get_option('widget_lastViewed');

                        $widgetId = $args['widget_id'];
                        preg_match_all('!\d+!', $widgetId, $widgetId);
                        $widgetId = implode(' ', $widgetId[0]);



                        if (isset($selected_posttypes[$widgetId]["selected_posttypes"]))
                        {
                            //the number is the widgetnumber inside the admin
                            $selected_posttypes = $selected_posttypes[$widgetId]["selected_posttypes"];
                        };
                        $count ='';
                        //Do not show types which aren't allowed
                        foreach ($selected_posttypes as $selected_type) {

                            if ($selected_type == $viewType) {

                                $count++;
                                if ($count > $lastViewed_total) {
                                    break;
                                }

                                echo '<li class="clearfix">';

                                if ($lastViewed_thumb == 'yes') {
                                    if (has_post_thumbnail($id)) {

                                        echo '<div class="lastViewedThumb">';
                                        echo get_the_post_thumbnail($id);
                                        echo '</div>';
                                    }
                                }
                                echo '<div class="lastViewedcontent">';
                                echo '<a class="lastViewedTitle" href="' . get_permalink($id) . '">' . get_the_title($id) . '</a>';
                                echo "<p class='lastViewedExcerpt'>" . substr($the_excerpt, 0, strrpos(substr($the_excerpt, 0, $lastViewed_truncate), ' ')) . '...<a href="' . get_permalink($id) . '" class="more">'.$lastViewed_linkname.'</a></p>'; //stop afterfull word
                                echo '</div>';
                                echo '</li>';
                            }
                        }
                    }
                }
                ?>
            </ul>
            <?php

            echo $after_widget;
        }
    }
}
add_action('widgets_init', create_function('', 'return register_widget("lastviewed");'));

function addToFooter()
{
    if (is_singular()) {

        //Set a hidden input to get always the id of the single or page.
        echo'<input id="LastViewed_ID" type="hidden" data-id="' . get_the_id() . '">';
        $plugin_url_path = WP_PLUGIN_URL;
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-cookie', $plugin_url_path . '/lastViewed/js/jquery.cookie.js', array(), '0');
        wp_enqueue_script('lastViewed', $plugin_url_path . '/lastViewed/js/lastViewed.js', array(), '0');
    }
}
add_action('wp_footer', 'addToFooter');

function addToHeader()
{
    $plugin_url_path = WP_PLUGIN_URL;
    $stylesheet = '<link rel="stylesheet" href="' . $plugin_url_path . '/lastViewed/css/style.css"/>';
    echo $stylesheet;
}
add_action('wp_head', 'addToHeader');


function addToAdminFooter()
{
    $plugin_url_path = WP_PLUGIN_URL;
    wp_enqueue_script('jquery');
    wp_enqueue_script('lastViewed', $plugin_url_path . '/lastViewed/js/admin.js', array(), '0');
}
add_action( 'admin_footer', 'addToAdminFooter' );


function shortcode_lastviewed( $atts ){
    // Configure defaults and extract the attributes into variables

    $args = array(
        'widget_id' => $atts['widget_id'],
        'by_shortcode' => 'shortcode_',
    );

    ob_start();
    the_widget( 'lastviewed', '', $args);
    $output = ob_get_clean();
    return $output;
}
add_shortcode( 'dd_lastviewed', 'shortcode_lastviewed' );

?>