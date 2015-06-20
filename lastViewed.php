<?php
/*
Plugin Name: DD Last Viewed
Version: 2.5
Plugin URI: http://dijkstradesign.com
Description: A plug-in to add a last viewed/visited widget
Author: Wouter Dijkstra
Author URI: http://dijkstradesign.com
*/


/*  Copyright 2014  WOUTER DIJKSTRA  (email : info@dijkstradesign.nl)

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

require_once('inc/widget.php');
require_once('inc/shortcode.php');

function dd_lastviewed_add_front()
{
    wp_register_style( 'dd_lastviewed_css', plugins_url('/css/style.css', __FILE__) );
    wp_enqueue_style( 'dd_lastviewed_css' );

    if (is_singular()) {
        wp_enqueue_script('jquery');
        wp_enqueue_script( 'jquery-cookie', plugins_url( '/js/jquery.cookie.js', __FILE__ ) , array( 'jquery' ), '' );
        wp_enqueue_script( 'dd_js_lastviewed', plugins_url( '/js/lastviewed.js', __FILE__ ) , array( 'jquery','jquery-cookie' ), '' );
    }
}
add_action( 'wp_enqueue_scripts', 'dd_lastviewed_add_front' );

function dd_lastviewed_admin()
{
    wp_register_style( 'dd_lastviewed_admin_styles', plugins_url('/css/admin-style.css', __FILE__) );
    wp_enqueue_style( 'dd_lastviewed_admin_styles' );

    wp_enqueue_script('jquery');
    wp_enqueue_script( 'dd_js_admin-lastviewed', plugins_url( '/js/admin-lastviewed.js', __FILE__ ) , array( 'jquery' ), '' );

}
add_action( 'admin_init', 'dd_lastviewed_admin' );


function add_lastviewed_id() {

    if (is_singular()) {
        $post_type = get_post_type();
        $lastviewed_widgets =get_option('widget_lastviewed');

        foreach($lastviewed_widgets as $id => $lastviewed_widget){
            $types = $lastviewed_widget["selected_posttypes"];
            $posts_per_widget = $lastviewed_widget["lastViewed_total"];

            if (in_array($post_type, $types)){
                //Set a hidden input to get always the id of the single or page.
                echo'<input id="cookie_data_lastviewed_widget_'.$id.'" class="lastviewed_data" type="hidden" data-post-per-widget="'.$posts_per_widget.'" data-post-id="' . get_the_id() . '">';
            }
        }
    }
}
add_action('wp_footer', 'add_lastviewed_id');