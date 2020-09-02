<?php
/**
* Plugin Name: Unique User ID (Amanda O. King)
* Plugin URI: https://www.amandaking.us
* Description: Display the unique user ID of a registered and logged in user. Use shortcode [aok-id] or use php code <? echo do_shortcode( '[aok-id]' ); ?> in your theme files, wherever you'd like the unique ID to display.
* Version: 1.0
* Author: Amanda King
* Author URI: https://www.amandaking.us
**/

function aok_display_id($theID) {
  return $theID .= 'Unique ID: ' . get_current_user_id();
}
add_filter( 'the_unique_id', 'aok_display_id' );
add_shortcode('aok-id', 'aok_display_id');