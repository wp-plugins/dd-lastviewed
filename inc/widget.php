<?php
/**
 * Created by PhpStorm.
 * User: dijkstradesign
 * Date: 04-01-14
 * Time: 13:02
 */

class lastviewed extends WP_Widget
{
    function lastviewed()
    {
        parent::WP_Widget(false, 'Last viewed', array('description' => __('Shows the last viewed of a post or custom posttype.', 'text_domain'),));
    }

    function form($instance)
    {
        $lastviewedTitle = isset($instance['lastviewedTitle']) ? $instance['lastviewedTitle'] : "Last Viewed";
        $widgetID = str_replace('lastviewed-', '', $this->id);

        $fieldID = $this->get_field_id('lastviewedTitle');
        $fieldName = $this->get_field_name('lastviewedTitle');

        echo '<p>';
        echo '<label for="'.$fieldID.'">Titel:</label>';
        echo '<input id="'.$fieldID.'" class=" widefat textWrite_Title" type="text" value="'.esc_attr($lastviewedTitle).'"name="'.$fieldName.'">';
        echo '</p>';
        echo '<p class="typeholder">Select the types:<br/>';

        $args = array(
            'public' => true,
            '_builtin' => false
        );

        //grab the post_types active in theme

        $output = 'names'; // names or objects, note names is the default
        $operator = 'and'; // 'and' or 'or'
        $custom_post_types = get_post_types($args, $output, $operator);
        $default_post_types= $post_types = get_post_types('', 'names');
        $post_types = array_merge($custom_post_types, $default_post_types);
        $lastViewed_thumb = isset($instance['lastViewed_thumb']) ? $instance['lastViewed_thumb'] : "";
        $lastViewed_thumb = esc_attr($lastViewed_thumb);
        $yesSelect = ($lastViewed_thumb == 'yes') ? "checked" : "";
        $noSelect = ($lastViewed_thumb != 'yes') ? "checked" : "";
        $lastViewed_total = isset($instance['lastViewed_total']) ? $instance['lastViewed_total'] : 5;
        $lastViewed_total = esc_attr($lastViewed_total);
        $lastViewed_truncate = isset($instance['lastViewed_truncate']) ? $instance['lastViewed_truncate'] : 78;
        $lastViewed_truncate = esc_attr($lastViewed_truncate);
        $lastViewed_linkname = isset($instance['lastViewed_linkname']) ? $instance['lastViewed_linkname'] : "More";
        $lastViewed_linkname = esc_attr($lastViewed_linkname);

        foreach ($post_types as $post_type) {

            $obj = get_post_type_object( $post_type );
            $RealName = $obj->labels->name;
            $selected_posttypes = isset($instance['selected_posttypes']) ? $instance['selected_posttypes'] : "";

            if (in_array($post_type, array('page','attachment','revision','nav_menu_item'))) {
                break;
            }
            $option = '<label>';
            $option .= '<input type="checkbox" class="checkbox posttypeCheckbox customPostCheck"id="LV_checkbox_' . $post_type . '" name="' . $this->get_field_name('selected_posttypes') . '[]"';
            if (is_array($selected_posttypes)) {
                foreach ($selected_posttypes as $selected_type) {
                    if ($selected_type == $post_type) {
                        $option .= ' checked="checked"';
                    }
                }
            }
            $option .= ' value="' . $post_type . '" />';
            $option .= $RealName;
            $option .= '</label><br/>';
            echo $option;
        }
        echo '
            </p>
            <p><label>Show thumbnails if excist:</label><br>
            <label class="set_thumb"><input type="radio" name="' . $this->get_field_name('lastViewed_thumb') . '" value="no" ' . $noSelect . ' />No</label><br/>
            <label class="set_thumb"><input type="radio" name="' . $this->get_field_name('lastViewed_thumb') . '" value="yes" ' . $yesSelect . '/>Yes</label><br/>
            </p>
            <p><label>Number to show:<label>
            <input type="number" name="' . $this->get_field_name('lastViewed_total') . '" min="1" max="10" value="' . $lastViewed_total . '"></p>
            <p><label>Truncate excerpt:<label>
            <input type="number" name="' . $this->get_field_name('lastViewed_truncate') . '" min="1" max="10" value="' . $lastViewed_truncate . '"></p>
            <p><label>Link name:<label>
             <input id="'. $this->get_field_id('lastViewed_linkname').'" class="textWrite_Title" type="text" value="'.esc_attr($lastViewed_linkname).'"name="'. $this->get_field_name('lastViewed_linkname').'"></p>
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
        $by_shortcode = isset($args['by_shortcode'])? $args['by_shortcode'] : "";
        $widgetID = str_replace('lastviewed-', '', $widgetID);
        $widgetOptions = get_option($this->option_name);
        $lastviewedTitle = $widgetOptions[$widgetID]['lastviewedTitle'];
        $lastViewed_thumb = $widgetOptions[$widgetID]['lastViewed_thumb'];
        $lastViewed_total = $widgetOptions[$widgetID]['lastViewed_total'];
        $lastViewed_truncate = $widgetOptions[$widgetID]['lastViewed_truncate'] ? $widgetOptions[$widgetID]['lastViewed_truncate'] : 78;
        $lastViewed_linkname = $widgetOptions[$widgetID]['lastViewed_linkname'];
        $lastlist = ($_COOKIE['lastViewed']);
        $idList = explode(",", $lastlist);
        $idList = array_reverse($idList);

        extract($args, EXTR_SKIP);

        if (isset($_COOKIE["lastViewed"]) && $lastlist !== "") {

            $count = 0;
            $currentVisitPostId = get_the_ID();

            echo $before_widget;

            echo '<h3>'.$lastviewedTitle.'</h3>';
            echo '<ul class="lastViewedList">';
                foreach ($idList as $id) {

                    global $wpdb;
                    $post_exists = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE id = '" . $id . "'", 'ARRAY_A');

                    if ($post_exists && $id != $currentVisitPostId ) {

                        $the_post = get_post($id); //Gets post ID
                        $the_excerpt = ($the_post->post_excerpt) ? $the_post->post_excerpt : $the_post->post_content;
                        $viewType = get_post_type($the_post);

                        //get also the selected types
                        $selected_posttypes = get_option('widget_lastViewed');
                        $selected_posttypes = isset($selected_posttypes[$widgetID]["selected_posttypes"]) ? $selected_posttypes[$widgetID]["selected_posttypes"] : "";

                        echo $visitPostId;

                        //Do not show types which aren't allowed
                        foreach ($selected_posttypes as $selected_type) {

                            if ($selected_type == $viewType) {


                                if ($count < $lastViewed_total) {

                                    $count++;
                                    echo '<li class="clearfix">';

                                    if ($lastViewed_thumb == 'yes' && has_post_thumbnail($id)) {
                                        echo '<div class="lastViewedThumb">'.get_the_post_thumbnail($id).'</div>';
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
                }
            echo '</ul>';
            echo $after_widget;
        }
    }
}
add_action('widgets_init', create_function('', 'return register_widget("lastviewed");'));