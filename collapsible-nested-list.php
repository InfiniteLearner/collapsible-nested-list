<?php
/**
 * Plugin Name: Collapsible Nested List
 * Description: A plugin that creates a collapsible nested list.
 * Version: 1.0
 * License: GPL2
 */

function create_collapsible_nested_list($atts, $content = null) {
  $atts = shortcode_atts(
    array(
      'depth' => 3,
    ),
    $atts,
    'collapsible-nested-list'
  );

  // Use uniqid() to generate unique IDs for the collapsible list items.
  $list_id = uniqid('collapsible-list-');

  // Embed JavaScript to create collapsible list functionality.
  wp_enqueue_script('collapsible-nested-list', plugin_dir_url(__FILE__) . 'assets/collapsible-nested-list.js', array('jquery'), '1.0', true);

  wp_add_inline_script('collapsible-nested-list', "
    jQuery(document).ready(function($) {
      $('#$list_id .collapsible-item > .collapsible-toggle').click(function() {
        var parent = $(this).parent();
        var isCollapsed = parent.hasClass('collapsed');

        parent.toggleClass('collapsed', !isCollapsed);
        parent.toggleClass('expanded', isCollapsed);
      });
    });
  ");

  // Embed CSS to style the list.
  wp_enqueue_style('collapsible-nested-list', plugin_dir_url(__FILE__) . 'assets/collapsible-nested-list.css', array(), '1.0', 'all');

  wp_add_inline_style('collapsible-nested-list', "
    #$list_id .collapsible-item {
      margin-left: 20px;
    }
    #$list_id .collapsible-toggle {
      cursor: pointer;
    }
    #$list_id .collapsed > .collapsible-list {
      display: none;
    }
  ");

  $output = "<ul id=\"$list_id\">";
  $output .= do_shortcode($content);
  $output .= "</ul>";

  return $output;
}

function create_collapsible_nested_list_item($atts, $content = null) {
    extract(shortcode_atts(array(
        'item_id' => ''
    ), $atts));
    
    $item_id = $item_id ? $item_id : uniqid('collapsible-nested-list-item-');
    
    return "<li><span class='collapsible-nested-list-item' id='{$item_id}'>+</span><div class='collapsible-nested-list-item-content'>". do_shortcode($content) ."</div></li>";
}

add_shortcode('collapsible-nested-list', 'create_collapsible_nested_list');
add_shortcode('collapsible-nested-list-item', 'create_collapsible_nested_list_item');

/**
 * Activate the plugin.
 */
function collapsible_activate() { 
	// Clear the permalinks after the post type has been registered.
	flush_rewrite_rules(); 
}
register_activation_hook( __FILE__, 'pluginprefix_activate' );

/**
 * Deactivation hook.
 */
function collapsible_deactivate() {
	unregister_shortcode('collapsible-nested-list');
    unregister_shortcode('collapsible-nested-list-item');
	// Clear the permalinks to remove our post type's rules from the database.
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'pluginprefix_deactivate' );