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
  if ($field['type'] != 'pattern_image') {
    return;
  }
  $field['value'] = stripslashes_deep($field['value']);
  include('include/image-field.html');
?>
  <script>
    let imageByteLimit = 120000;
    let fieldName = "<?php echo esc_attr($field_name) ?>";
    let existingValue = "<?php echo esc_attr($field['value']) ?>";
    let valueInput = document.getElementById("value_input");
    valueInput.name = fieldName;
    if (existingValue != null && existingValue != "") {
      showImage(existingValue);
    }
  </script>
<?php
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


/* Show the field in your form */
/* Control the output for file_string field in your form. */
add_action('frm_form_fields', 'show_audio_field', 10, 2);
function show_audio_field($field, $field_name) {
  if ($field['type'] != 'pattern_audio') {
    return;
  }
  $field['value'] = stripslashes_deep($field['value']);
  include('include/audio-field.html');
?>
  <script>
    let audioByteLimit = 120000;
    let audioFieldName = "<?php echo esc_attr($field_name) ?>";
    console.log("Audio field name");
    console.log(audioFieldName);
    let existingAudioValue = "<?php echo esc_attr($field['value']) ?>";
    let audioValueInput = document.getElementById("audio_value_input");
    audioValueInput.name = audioFieldName;
    if (existingAudioValue != null && existingAudioValue != "") {
      showAudio(existingAudioValue);
    }
  </script>
<?php
}
