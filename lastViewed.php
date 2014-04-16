<?php
/*
Plugin Name: DD Last Viewed
Version: 1.3
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

require_once('inc/widget.php');
require_once('inc/shortcode.php');

/**
 *   Enqueue plugin style-file (CSS) and js file for frontend
 */

function dd_lastviewed_add_front()
{
    wp_register_style( 'dd_lastviewed_css', plugins_url('/css/style.css', __FILE__) );
    wp_enqueue_style( 'dd_lastviewed_css' );

    if (is_singular()) {
        wp_enqueue_script('jquery');
        wp_enqueue_script( 'jquery-cookie', plugins_url( '/js/jquery.cookie.js', __FILE__ ) , array( 'jquery' ), '' );
        wp_enqueue_script( 'dd_js_lastviewed', plugins_url( '/js/lastViewed.js', __FILE__ ) , array( 'jquery','jquery-cookie' ), '' );
    }
}
add_action( 'wp_enqueue_scripts', 'dd_lastviewed_add_front' );


function dd_lastviewed_admin()
{
    wp_register_style( 'dd_lastviewed_admin_styles', plugins_url('/css/admin-style.css', __FILE__) );
    wp_enqueue_style( 'dd_lastviewed_admin_styles' );
}
add_action( 'admin_init', 'dd_lastviewed_admin' );


function add_lastviewed_id() {
    if (is_singular()) {
        //Set a hidden input to get always the id of the single or page.
        echo'<input id="LastViewed_ID" type="hidden" data-id="' . get_the_id() . '">';
    }
}
add_action('wp_footer', 'add_lastviewed_id');