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
        parent::WP_Widget(false, 'DD Last Viewed', array('description' => __('A list of the recently viewed posts, pages or custom posttypes.', 'text_domain'),));
    }

    function form($instance)
    {
        $lastviewedTitle = isset($instance['lastviewedTitle']) ? $instance['lastviewedTitle'] : "Last Viewed";
        $widgetID = str_replace('lastviewed-', '', $this->id);
        $fieldID = $this->get_field_id('lastviewedTitle');
        $fieldName = $this->get_field_name('lastviewedTitle');
        $args = array('public' => true,'_builtin' => false);//grab the post_types active in theme
        $output = 'names'; // names or objects, note names is the default
        $operator = 'and'; // 'and' or 'or'
        $custom_post_types = get_post_types($args, $output, $operator);
        $default_post_types = get_post_types('', 'names');
        $post_types = array_merge($custom_post_types, $default_post_types);
        $lastViewed_total = isset($instance['lastViewed_total']) ? $instance['lastViewed_total'] : 5;
        $lastViewed_total = esc_attr($lastViewed_total);
        $lastViewed_truncate = isset($instance['lastViewed_truncate']) ? $instance['lastViewed_truncate'] : 78;
        $lastViewed_truncate = esc_attr($lastViewed_truncate);
        $lastViewed_linkname = isset($instance['lastViewed_linkname']) ? $instance['lastViewed_linkname'] : "More";
        $lastViewed_linkname = esc_attr($lastViewed_linkname);

        $lastViewed_showPostTitle = isset( $instance['lastViewed_showPostTitle'] ) ? (bool) $instance['lastViewed_showPostTitle'] : false;
        $lastViewed_showThumb = isset( $instance['lastViewed_showThumb'] ) ? (bool) $instance['lastViewed_showThumb'] : false;
        $lastViewed_showExcerpt = isset( $instance['lastViewed_showExcerpt'] ) ? (bool) $instance['lastViewed_showExcerpt'] : false;

        $lastViewed_showMore = isset( $instance['lastViewed_showMore'] ) ? (bool) $instance['lastViewed_showMore'] : false;


        echo '<p>';
        echo '<label for="'.$fieldID.'">Titel:</label>';
        echo '<input id="'.$fieldID.'" class=" widefat textWrite_Title" type="text" value="'.esc_attr($lastviewedTitle).'"name="'.$fieldName.'">';
        echo '</p>';
        echo '<p><label>Number of items to show: <label>
            <input type="number" name="' . $this->get_field_name('lastViewed_total') . '" min="1" max="10" value="' . $lastViewed_total . '"></p>';
        echo '<hr>';
        echo '<p class="typeholder">Select the types:<br/>';

        foreach ($post_types as $post_type) {

            $obj = get_post_type_object( $post_type );
            $RealName = $obj->labels->name;
            $selected_posttypes = isset($instance['selected_posttypes']) ? $instance['selected_posttypes'] : "";

            if (in_array($post_type, array('attachment','revision','nav_menu_item'))) {
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
        echo '</p>';


        echo '<hr>';

        $checked = $lastViewed_showPostTitle == true ? 'checked="checked"' : '';
        $showTitle = '<div class="showTitle LV_setting_row"> ';
        $showTitle .= inputSwitch($lastViewed_showPostTitle);
        $showTitle .= '<input id="lastViewed_showPostTitle" name="' .$this->get_field_name('lastViewed_showPostTitle').'" type="checkbox" '.$checked.'/>';
        $showTitle .= __('Display Item Title');
        $showTitle .= '</div>';

        echo $showTitle;


        $checked = $lastViewed_showThumb == true ? 'checked="checked"' : '';
        $showThumb = '<div class="showThumb LV_setting_row"> ';
        $showThumb .= inputSwitch($lastViewed_showThumb);
        $showThumb .= '<input id="lastViewed_showThumb" name="' .$this->get_field_name('lastViewed_showThumb').'" type="checkbox" '.$checked.'/>';
        $showThumb .= __('Display Thumbnail (if excist)');
        $showThumb .= '</div>';

        echo $showThumb;

        $checked = $lastViewed_showExcerpt == true ? 'checked="checked"' : '';
        $showExcerpt = '<div class="showExcerpt LV_setting_row"> ';
        $showExcerpt .= inputSwitch($lastViewed_showExcerpt);
        $showExcerpt .= '<input id="lastViewed_showExcerpt" name="' .$this->get_field_name('lastViewed_showExcerpt').'" type="checkbox" '.$checked.'/>';
        $showExcerpt .= __('Display Excerpt').'  ';
        $showExcerpt .= '<input type="number" name="' . $this->get_field_name('lastViewed_truncate') . '" min="1" max="10" value="' . $lastViewed_truncate . '">';
        $showExcerpt .= '  '.__('Characters');
        $showExcerpt .= '</div>';

        echo $showExcerpt;

        $checked = $lastViewed_showMore == true ? 'checked="checked"' : '';
        $showMore = '<div class="showMore LV_setting_row"> ';
        $showMore .= inputSwitch($lastViewed_showMore);
        $showMore .= '<input id="lastViewed_showMore" name="' .$this->get_field_name('lastViewed_showMore').'" type="checkbox" '.$checked.'/>';
        $showMore .= __('Display Breaklink').'   ';
        $showMore .= '<input id="'. $this->get_field_id('lastViewed_linkname').'" class="textWrite_Title" type="text" value="'.esc_attr($lastViewed_linkname).'"name="'. $this->get_field_name('lastViewed_linkname').'">';
        $showMore .= '</div>';

        echo $showMore;

        echo '<hr>';

        if (is_numeric($widgetID)){

            echo '<p style="font-size: 11px; opacity:0.6">';
            echo '<span class="shortcodeTtitle">Shortcode:</span>';
            echo '<span class="shortcode">[dd_lastviewed widget_id="'.$widgetID.'"]</span>';
            echo '</p>';

        }
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

        $instance['lastViewed_showPostTitle'] = (bool) $new_instance['lastViewed_showPostTitle'];
        $instance['lastViewed_showThumb'] = (bool) $new_instance['lastViewed_showThumb'];
        $instance['lastViewed_showExcerpt'] = (bool) $new_instance['lastViewed_showExcerpt'];
        $instance['lastViewed_showMore'] = (bool) $new_instance['lastViewed_showMore'];

        return $instance;
    }

    function widget($args , $instance)
    {
        $widgetID = $args['widget_id'];
        $widgetID = str_replace('lastviewed-', '', $widgetID);
        $widgetOptions = get_option($this->option_name);
        $lastviewedTitle = $widgetOptions[$widgetID]['lastviewedTitle'];
        $lastViewed_thumb = $widgetOptions[$widgetID]['lastViewed_thumb'];
        $lastViewed_total = $widgetOptions[$widgetID]['lastViewed_total'];
        $lastViewed_truncate = $widgetOptions[$widgetID]['lastViewed_truncate'] ? $widgetOptions[$widgetID]['lastViewed_truncate'] : 78;
        $lastViewed_linkname = $widgetOptions[$widgetID]['lastViewed_linkname'];

        $lastViewed_showPostTitle = $widgetOptions[$widgetID]['lastViewed_showPostTitle'];
        $lastViewed_showThumb = $widgetOptions[$widgetID]['lastViewed_showThumb'];
        $lastViewed_showExcerpt = $widgetOptions[$widgetID]['lastViewed_showExcerpt'];

        $lastViewed_showMore = $widgetOptions[$widgetID]['lastViewed_showMore'];

        $lastlist = ($_COOKIE['lastViewed']);
        $idList = explode(",", $lastlist);
        $idList = array_reverse($idList);
        $count = 0;
        $currentVisitPostId = get_the_ID();
        //get also the selected types
        $selected_posttypes = get_option('widget_lastViewed');
        $selected_posttypes = isset($selected_posttypes[$widgetID]["selected_posttypes"]) ? $selected_posttypes[$widgetID]["selected_posttypes"] : false;

        extract($args, EXTR_SKIP);

        if ($selected_posttypes && isset($_COOKIE["lastViewed"]) && $lastlist !== "") {

            echo $before_widget;
            echo $before_title.$lastviewedTitle.$after_title;
            echo '<ul class="lastViewedList">';
                foreach ($idList as $id) {

                    $the_post = get_post($id); //Gets post ID

                    $the_content = strip_shortcodes( $the_post->post_content );
                    $the_content = wp_strip_all_tags( $the_content, $remove_breaks );
                    $the_excerpt = ($the_post->post_excerpt) ? $the_post->post_excerpt : $the_content; //get_the_excerpt($the_post);
                    $viewType = get_post_type($the_post);



                    if ($the_post && $id != $currentVisitPostId ) {

                        //Do not show types which aren't allowed
                        foreach ($selected_posttypes as $selected_type) {

                            if ($selected_type == $viewType && $count < $lastViewed_total ) {
                                $count++;

                                $hasThumb = $lastViewed_showThumb && has_post_thumbnail($id) ? $lastViewed_showThumb : false;
                                $clearfix = $hasThumb ? "class='clearfix'" : "";

                                echo '<li '.$clearfix.'>';

                                if ($hasThumb && $lastViewed_showPostTitle | $lastViewed_showExcerpt) {
                                    echo '<div class="lastViewedThumb">'.get_the_post_thumbnail($id).'</div>';
                                }
                                elseif ($hasThumb && !$lastViewed_showPostTitle && !$lastViewed_showExcerpt) {
                                    echo '<a class="lastViewedThumb" href="' . get_permalink($id) . '">'.get_the_post_thumbnail($id).'</a>';
                                }

                                echo '<div class="lastViewedcontent">';

                                if($lastViewed_showPostTitle){
                                    echo '<a class="lastViewedTitle" href="' . get_permalink($id) . '">' . get_the_title($id) . '</a>';
                                }

                                $the_excerpt =  substr($the_excerpt, 0, strrpos(substr($the_excerpt, 0, $lastViewed_truncate), ' '));

                                if(!$lastViewed_showPostTitle && $lastViewed_showExcerpt){

                                    echo '<a href="' . get_permalink($id) . '" class="lastViewedExcerpt">'.$the_excerpt;
                                        if($lastViewed_showMore){
                                            echo '<span class="more">'.$lastViewed_linkname.'</span>';
                                        }
                                     echo '</a>';

                                }
                                elseif($lastViewed_showPostTitle && $lastViewed_showExcerpt){
                                    echo "<p class='lastViewedExcerpt'>" .$the_excerpt;
                                        if($lastViewed_showMore){
                                            echo '<a href="' . get_permalink($id) . '" class="more">'.$lastViewed_linkname.'</a>';
                                        }
                                    echo '</p>'; //stop afterfull word
                                }


                                echo '</div>';
                                echo '</li>';
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


function inputSwitch($value){

    $status = $value == '1' ? 'on' : '';

    return '
                <div class="dd-switch '.$status.'">
                    <div class="switchHolder">
                        <div class="onSquare button-primary"></div>
                        <div class="buttonSwitch"></div>
                        <div class="offSquare"></div>
                    </div>
                </div>';

}

