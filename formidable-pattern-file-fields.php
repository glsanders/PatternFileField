<?php

/**
 * Plugin Name: Formidable Pro Pattern File Fields
 * Description: Adds multiple field-upload fields that store their values as a Base64 string.
 * Version: 1.0.0
 * Author: Matt Maddux
 * Author URI: https://github.com/mattmaddux
 * License: GPL2
 */

/*  Formidable Pro Pattern File Fields - Adds multiple field-upload fields that store their values as a Base64 string.
    Copyright (C) 2022  Matt Maddux (email : matt@lumberstack.org)

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


if (!defined('ABSPATH')) exit; // Exit if accessed directly


/*
  STYLE SHEET
 */
add_action('wp_enqueue_scripts', 'add_supporting_files');
function add_supporting_files() {
  $plugin_url = plugin_dir_url(__FILE__);
  wp_enqueue_script('pattern_fields_audio_scripts', $plugin_url . 'javascript/audio_scripts.js');
  wp_enqueue_script('pattern_fields_image_scripts', $plugin_url . 'javascript/image_scripts.js');
  wp_enqueue_style('pattern_fields_common_styles', $plugin_url . 'css/common_styles.css');
  wp_enqueue_style('pattern_fields_audio_styles', $plugin_url . 'css/audio_styles.css');
  wp_enqueue_style('pattern_fields_image_styles', $plugin_url . 'css/image_styles.css');
  wp_enqueue_style('pattern_material_icons', 'https://fonts.googleapis.com/icon?family=Material+Icons');
}


/*
  IMAGE FIELD
*/

/* Instantiate the field */
add_filter('frm_pro_available_fields', 'add_image_field');
function add_image_field($fields) {
  $fields['pattern_image'] = array(
    'name' => 'Pattern Image' // the key for the field and the label
  );
  return $fields;
}

/* Set default options */
add_filter('frm_before_field_created', 'set_image_field_defaults');
function set_image_field_defaults($field_data) {
  if ($field_data['type'] != 'pattern_image') {
    return $field_data;
  }
  $field_data['name'] = 'Pattern Image';
  return $field_data;
}


/* Show the color_picker_hex field in the builder page */
add_action('frm_display_added_fields', 'show_image_admin_field');
function show_image_admin_field($field) {
  if ($field['type'] != 'pattern_image') {
    return;
  }
  $field_name = 'item_meta[' . $field['id'] . ']';
?>
  <div class="frm_html_field_placeholder">
    <div class="howto button-secondary frm_html_field">This is a placeholder for your image field. <br />View your form to see it in action.</div>
  </div>
<?php
}


/* Show the field in your form */
/* Control the output for file_string field in your form. */
add_action('frm_form_fields', 'show_image_field', 10, 2);
function show_image_field($field, $field_name) {
  $plugin_url = plugin_dir_url(__FILE__);
  if ($field['type'] != 'pattern_image') {
    return;
  }
  $field['value'] = stripslashes_deep($field['value']);
  $existing_value = esc_attr($field['value']);
  $field_id = substr($field_name, 10, 3);

  $html = file_get_contents(plugin_dir_path(__FILE__) . 'html/image_field.html');
  $html = str_replace("UID", $field_id, $html);
  $html = str_replace("IMAGE_BYTE_LIMIT", "120000", $html);
  $html = str_replace("EXISTING_IMAGE_DATA", $existing_value, $html);
  echo $html;
}

/* Show the field in your form */
/* Control the output for file_string field in your form. */
add_action('frm_form_fields', 'show_audio_field', 10, 2);
function show_audio_field($field, $field_name) {
  if ($field['type'] != 'pattern_audio') {
    return;
  }
  $field['value'] = stripslashes_deep($field['value']);
  $existing_value = esc_attr($field['value']);
  $field_id = substr($field_name, 10, 3);

  $html = file_get_contents(plugin_dir_path(__FILE__) . 'html/audio_field.html');
  $html = str_replace("UID", $field_id, $html);
  $html = str_replace("AUDIO_BYTE_LIMIT", "120000", $html);
  $html = str_replace("EXISTING_AUDIO_DATA", $existing_value, $html);
  echo $html;
}



/*
  AUDIO FIELD
*/

/* Instantiate the field */
add_filter('frm_pro_available_fields', 'add_audio_field');
function add_audio_field($fields) {
  $fields['pattern_audio'] = array(
    'name' => 'Pattern Audio' // the key for the field and the label
  );
  return $fields;
}

/* Set default options */
add_filter('frm_before_field_created', 'set_audio_field_defaults');
function set_audio_field_defaults($field_data) {
  if ($field_data['type'] != 'pattern_audio') {
    return $field_data;
  }
  $field_data['name'] = 'Pattern Audio';
  return $field_data;
}


/* Show the color_picker_hex field in the builder page */
add_action('frm_display_added_fields', 'show_audio_admin_field');
function show_audio_admin_field($field) {
  if ($field['type'] != 'pattern_audio') {
    return;
  }
  $field_name = 'item_meta[' . $field['id'] . ']';
?>
  <div class="frm_html_field_placeholder">
    <div class="howto button-secondary frm_html_field">This is a placeholder for your audio field. <br />View your form to see it in action.</div>
  </div>
<?php
}




add_shortcode('pattern_image_preview', 'image_preview');
function image_preview($attr) {
  $args = shortcode_atts(array(
    'data' => '',
  ), $attr);
  $image_data = $args['data'];

  if ($image_data == "") {
    $html = file_get_contents(plugin_dir_path(__FILE__) . 'html/image_preview_missing.html');
    return $html;
  } else {
    $preview_id = "preview_" . rand();
    $html = file_get_contents(plugin_dir_path(__FILE__) . 'html/image_preview.html');
    $html = str_replace("UID", $preview_id, $html);
    $html = str_replace("IMAGE_SOURCE", $image_data, $html);
    return $html;
  }
}

add_shortcode('pattern_audio_preview', 'audio_preview');
function audio_preview($attr) {
  $args = shortcode_atts(array(
    'data' => '',
  ), $attr);
  $audio_data = $args['data'];

  if ($audio_data == "") {
    $html = file_get_contents(plugin_dir_path(__FILE__) . 'html/audio_preview_missing.html');
    return $html;
  } else {
    $preview_id = "preview_" . rand();
    $html = file_get_contents(plugin_dir_path(__FILE__) . 'html/audio_preview.html');
    $html = str_replace("UID", $preview_id, $html);
    $html = str_replace("AUDIO_SOURCE", $audio_data, $html);
    return $html;
  }
}
