<?php

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