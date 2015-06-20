<?php

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
        $lastViewed_thumbSize = isset($instance['lastViewed_thumbSize']) ? $instance['lastViewed_thumbSize'] : "thumbnail";
        $lastViewed_thumbSize = esc_attr($lastViewed_thumbSize);
        $lastViewed_showExcerpt = isset( $instance['lastViewed_showExcerpt'] ) ? (bool) $instance['lastViewed_showExcerpt'] : false;
        $lastViewed_excerpt_type = isset( $instance['lastViewed_excerpt_type'] ) ? $instance['lastViewed_excerpt_type'] : false;
        $lastViewed_excerpt_type = esc_attr($lastViewed_excerpt_type);
        $lastViewed_content_rich = isset( $instance['lastViewed_content_rich'] ) ? $instance['lastViewed_content_rich'] : false;
        $lastViewed_content_rich = esc_attr($lastViewed_content_rich);
        $lastViewed_showTruncate = isset( $instance['lastViewed_showTruncate'] ) ? (bool) $instance['lastViewed_showTruncate'] : false;
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
        $showThumb .= __('Display Thumbnail');
            $all_sizes = get_intermediate_image_sizes();
            $dropdown = '<select name="'.$this->get_field_name('lastViewed_thumbSize').'">';
                foreach($all_sizes as $size){
                    $selected = $lastViewed_thumbSize == $size ? 'selected' : '';
                    $dropdown .= '<option value="'.$size.'" '.$selected.'>'.$size.'</option>';
                }
            $dropdown .= '</select>';
        $showThumb .= $dropdown;
        $showThumb .= '</div>';

        echo $showThumb;

        $checked = $lastViewed_showExcerpt == true ? 'checked="checked"' : '';
        $excerpt_type = $lastViewed_excerpt_type;
        $type_c_checked = $excerpt_type == 'content' ? 'checked' : '';
        $type_e_checked = $excerpt_type == 'excerpt' ? 'checked' : '';
        $rich_r_checked = $lastViewed_content_rich == 'rich' ? 'checked' : '';
        $rich_p_checked = $lastViewed_content_rich == 'plain' ? 'checked' : '';
        $showExcerpt = '<div class="showExcerpt LV_setting_row"> ';
        $showExcerpt .= inputSwitch($lastViewed_showExcerpt);
        $showExcerpt .= '<input id="lastViewed_showExcerpt" name="' .$this->get_field_name('lastViewed_showExcerpt').'" type="checkbox" '.$checked.'/>';
        $showExcerpt .= __('Display').'  ';
        $showExcerpt .= '<label><input type="radio" name="' .$this->get_field_name('lastViewed_excerpt_type').'" '.$type_c_checked.' value="content">Content </label>';
        $showExcerpt .= '<div class="content_rich">(<label><input type="radio" name="' .$this->get_field_name('lastViewed_content_rich').'" '.$rich_r_checked.'  value="rich">Rich</label>';
        $showExcerpt .= '<label><input type="radio" name="' .$this->get_field_name('lastViewed_content_rich').'"  '.$rich_p_checked.' value="plain">Plain </label>)</div>';
        $showExcerpt .= '<label><input type="radio" name="' .$this->get_field_name('lastViewed_excerpt_type').'" '.$type_e_checked.' value="excerpt">Excerpt</label>';
        $showExcerpt .= '</div>';

        echo $showExcerpt;

        $checked = $lastViewed_showTruncate == true ? 'checked="checked"' : '';
        $showTruncate = '<div class="showTruncate LV_setting_row"> ';
        $showTruncate .= inputSwitch($lastViewed_showTruncate);
        $showTruncate .= '<input id="lastViewed_showTruncate" name="' .$this->get_field_name('lastViewed_showTruncate').'" type="checkbox" '.$checked.'/>';
        $showTruncate .= __('Truncate').'  ';
        $showTruncate .= '<input type="number" name="' . $this->get_field_name('lastViewed_truncate') . '" min="1" max="10" value="' . $lastViewed_truncate . '">';
        $showTruncate .= '  '.__('Characters');
        $showTruncate .= '</div>';

        echo $showTruncate;

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
        $instance['lastViewed_thumbSize'] = strip_tags($new_instance['lastViewed_thumbSize']);
        $instance['lastViewed_showExcerpt'] = (bool) $new_instance['lastViewed_showExcerpt'];
        $instance['lastViewed_excerpt_type'] = strip_tags($new_instance['lastViewed_excerpt_type']);
        $instance['lastViewed_content_rich'] = strip_tags($new_instance['lastViewed_content_rich']);
        $instance['lastViewed_showTruncate'] = (bool) $new_instance['lastViewed_showTruncate'];
        $instance['lastViewed_showMore'] = (bool) $new_instance['lastViewed_showMore'];

        return $instance;
    }

    function widget($args , $instance)
    {
        $widgetID = $args['widget_id'];
        $widgetID = str_replace('lastviewed-', '', $widgetID);
        $widgetOptions = get_option($this->option_name);
        $lastviewedTitle = $widgetOptions[$widgetID]['lastviewedTitle'];
        $lastViewed_total = $widgetOptions[$widgetID]['lastViewed_total'];
        $lastViewed_truncate = $widgetOptions[$widgetID]['lastViewed_truncate'] ? $widgetOptions[$widgetID]['lastViewed_truncate'] : false;
        $lastViewed_linkname = $widgetOptions[$widgetID]['lastViewed_linkname'];
        $lastViewed_showPostTitle = $widgetOptions[$widgetID]['lastViewed_showPostTitle'];
        $lastViewed_showThumb = $widgetOptions[$widgetID]['lastViewed_showThumb'];
        $lastViewed_thumbSize = $widgetOptions[$widgetID]['lastViewed_thumbSize'];
        $lastViewed_showExcerpt = $widgetOptions[$widgetID]['lastViewed_showExcerpt'];
        $lastViewed_excerpt_type = $widgetOptions[$widgetID]['lastViewed_excerpt_type'];
        $lastViewed_content_rich = $widgetOptions[$widgetID]['lastViewed_content_rich'];
        $lastViewed_showTruncate = $widgetOptions[$widgetID]['lastViewed_showTruncate'];
        $lastViewed_showMore = $widgetOptions[$widgetID]['lastViewed_showMore'];

        $cookie_name = 'cookie_data_lastviewed_widget_'.$widgetID;

        $lastlist = ($_COOKIE[$cookie_name]);

        $idList = explode(",", $lastlist);
        $idList = array_reverse($idList);
        $selected_posttypes = get_option('widget_lastViewed');
        $selected_posttypes = isset($selected_posttypes[$widgetID]["selected_posttypes"]) ? $selected_posttypes[$widgetID]["selected_posttypes"] : false;

        extract($args, EXTR_SKIP);

        if(is_singular()){
            $currentVisitPostId = get_the_ID();
            $idList = array_diff($idList, array($currentVisitPostId)); // strip this id from idlist
        }

        $args=array('post__in'=> $idList, 'post_type' => $selected_posttypes, 'orderby'=>'post__in', 'posts_per_page'=> $lastViewed_total);
        $my_query = new WP_Query($args);
        $hasThumb = $lastViewed_showThumb ? $lastViewed_showThumb : false;

        if( $my_query->have_posts() && $selected_posttypes ) {

            echo $before_widget;
                echo $before_title.$lastviewedTitle.$after_title;

                if(!$lastViewed_showPostTitle && !$lastViewed_showExcerpt &&  !$hasThumb ){
                    echo'<p>No options set yet! Set the options in the <a href="'.esc_url( home_url( '/wp-admin/widgets.php' ) ).'">widget</a>.</p>';
                }
                echo '<ul class="lastViewedList">';

                    while ($my_query->have_posts()) : $my_query->the_post();

                        $id = get_the_ID();
                        $title = get_the_title();
                        $strip_content = $lastViewed_content_rich == 'plain' && $lastViewed_excerpt_type == 'content'; // 1/0
                        $content = get_the_content();

                        $regex = '/\[dd_lastviewed(.*?)\]/'; //avoid shortcode '[lastviewed] in order to prevent a loop
                        $content = preg_replace($regex, '', $content);

                        $content = apply_filters( 'the_content', $content );
                        $content = $strip_content ? strip_shortcodes( $content ) : $content;
                        $content = $strip_content ? wp_strip_all_tags( $content, $remove_breaks ) : $content;
                        $content = $lastViewed_excerpt_type == 'content' ? $content : get_the_excerpt();
                        $content = $lastViewed_showTruncate ? substr($content, 0, strrpos(substr($content, 0, $lastViewed_truncate), ' ')) : $content;

                        $thumb = get_the_post_thumbnail($id,$lastViewed_thumbSize);
                        $hasThumb = $lastViewed_showThumb && has_post_thumbnail() ? $lastViewed_showThumb : false;
                        $perma = get_permalink();
                        $clearfix = $hasThumb ? "clearfix" : "";

                        echo '<li class="'.$clearfix.'">';

                            if ($hasThumb && $lastViewed_showPostTitle | $lastViewed_showExcerpt) {
                                echo '<div class="lastViewedThumb">'.$thumb.'</div>';
                            }
                            elseif ($hasThumb && !$lastViewed_showPostTitle && !$lastViewed_showExcerpt) {
                                echo '<a class="lastViewedThumb" href="' . $perma . '">'.$thumb.'</a>';
                            }

                            echo '<div class="lastViewedcontent">';

                                if($lastViewed_showPostTitle){
                                    echo '<a class="lastViewedTitle" href="' . $perma . '">' . $title . '</a>';
                                }

                                if(!$lastViewed_showPostTitle && $lastViewed_showExcerpt){

                                    echo '<a href="' . $perma . '" class="lastViewedExcerpt">'.$content;
                                        if($lastViewed_showMore){
                                            echo '<span class="more">'.$lastViewed_linkname.'</span>';
                                        }
                                    echo '</a>';
                                }

                                elseif($lastViewed_showPostTitle && $lastViewed_showExcerpt){
                                    echo "<p class='lastViewedExcerpt'>" .$content;
                                        if($lastViewed_showMore){
                                            echo '<a href="' . $perma . '" class="more">'.$lastViewed_linkname.'</a>';
                                        }
                                    echo '</p>';
                                }
                            echo '</div>';
                        echo '</li>';
                    endwhile;
                echo '</ul>';
            echo $after_widget;
        }
        wp_reset_query();
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