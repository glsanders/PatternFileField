<?php
/**
* Plugin Name: Formidable Pro Add Color Picker Field
* Description: Adds a Color Picker Field type to the Advanced Fields in Formidable Pro.
* Version: 2.0.4
* Author: Ben Marshall
* Author URI: https://github.com/leavesoftea
* License: GPL2
*/

/*  Formidable Pro Add Color Picker Field - Adds a Color Picker Field type to the Advanced Fields in Formidable Pro.
    Copyright (C) 2022  Ben Marshall (email : sonic@speedymail.org)

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

/**
 * THIS PLUGIN MAKES USE OF 
 * Formidable Pro Add Color Picker Field - Copyright 2014  Darryl Erentzen  (email : darryl@erentzen.com)
 * This modified vesrion was changed on 2022-26-01 by Ben Marshall and changes are indicated on the diff file 
 * formidable-color-picker-diff.html found in the base-code.zip file.
 * 
 */

/**
 * THIS PLUGIN MAKES USE OF 
 * jscolor - JavaScript Color Picker
 *
 * @link    http://jscolor.com
 * @license For open source use: GPLv3
 *          For commercial use: JSColor Commercial License
 * @author  Jan Odvarko - East Desire
 * @version 2.4.6
 *
 * See usage examples at http://jscolor.com/examples/
 */



if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* Instantiate the fields */
add_filter('frm_pro_available_fields', 'add_color_picker_hex_field');
function add_color_picker_hex_field($fields){
  $fields['color_picker_hex'] = array(
      'name' => 'Color Picker (Hex)' // the key for the field and the label
  );
  return $fields;
}

add_filter('frm_pro_available_fields', 'add_color_picker_rgb_field');
function add_color_picker_rgb_field($fields){
  $fields['color_picker_rgb'] = array(
      'name' => 'Color Picker (RGB)' // the key for the field and the label  
  );
  return $fields;
}

/* Set default options */
/* When a new color_picker_field_hex is created, set default settings like the field name. */
add_filter('frm_before_field_created', 'set_color_picker_hex_defaults');
function set_color_picker_hex_defaults($field_data){
  if($field_data['type'] != 'color_picker_hex'){ //change to your chosen field key name
    return $field_data;
  }
  $field_data['name'] = 'Color Picker (Hex)';
  return $field_data;
}
/* When a new color_picker_field_rgb is created, set default settings like the field name. */
add_filter('frm_before_field_created', 'set_color_picker_rgb_defaults');
function set_color_picker_rgb_defaults($field_data){
  if($field_data['type'] != 'color_picker_rgb'){ //change to your chosen field key name
    return $field_data;
  }
  $field_data['name'] = 'Color Picker (RGB)';
  return $field_data;
}

//Show the color_picker_hex field in the builder page
add_action('frm_display_added_fields', 'show_color_picker_hex_admin_field');
function show_color_picker_hex_admin_field($field){
  if ( $field['type'] != 'color_picker_hex') {
    return;
  }
            
  $field_name = 'item_meta['. $field['id'] .']';
  ?>
<div style="width:100%;margin-bottom:10px;text-align:center;">
<div class="howto button-secondary frm_html_field">Color Picker (Hex)</div>   
</div>
<?php
}

//Show the color_picker_rgb field in the builder page
add_action('frm_display_added_fields', 'show_color_picker_rgb_admin_field');
function show_color_picker_rgb_admin_field($field){
  if ( $field['type'] != 'color_picker_rgb') {
    return;
  }
            
  $field_name = 'item_meta['. $field['id'] .']';
  ?>
<div style="width:100%;margin-bottom:10px;text-align:center;">
<div class="howto button-secondary frm_html_field">Color Picker (RGB)</div>   
</div>
<?php
}

/* Show the field in your form */
/* Control the output for color_picker_hex field in your form. */
add_action('frm_form_fields', 'show_my_color_picker_hex_field', 10, 2);
function show_my_color_picker_hex_field($field, $field_name){
  if ( $field['type'] != 'color_picker_hex' ) {
    return;
  }
  $field['value'] = stripslashes_deep($field['value']);
?>
<input type="text" data-jscolor="{format: 'hex', palette: '#000000 #7d7d7d #870014 #ec1c23 #ff7e26 #fef100 #22b14b #00a1e7 #3f47cc #a349a4 #ffffff #c3c3c3 #b87957 #feaec9 #ffc80d #eee3af #b5e61d #99d9ea #7092be #c8bfe7'}" id="field_<?php echo $field['field_key'] ?>" name="item_meta[<?php echo $field['id'] ?>]" value="<?php echo empty($field['value']) ? "3f48cc" : esc_attr($field['value']); ?>" <?php do_action('frm_field_input_html', $field) ?>/>
<?php
wp_enqueue_script('jscolor',plugins_url( 'jscolor/jscolor.min.js', __FILE__ ),'jquery');
}

/* Control the output for color_picker_rgb field in your form. */
add_action('frm_form_fields', 'show_my_color_picker_rgb_field', 10, 2);
function show_my_color_picker_rgb_field($field, $field_name){
  if ( $field['type'] != 'color_picker_rgb' ) {
    return;
  }
  $field['value'] = stripslashes_deep($field['value']);
?>
<input type="text" data-jscolor="{format: 'rgb', palette: '#000000 #7d7d7d #870014 #ec1c23 #ff7e26 #fef100 #22b14b #00a1e7 #3f47cc #a349a4 #ffffff #c3c3c3 #b87957 #feaec9 #ffc80d #eee3af #b5e61d #99d9ea #7092be #c8bfe7'}" id="field_<?php echo $field['field_key'] ?>" name="item_meta[<?php echo $field['id'] ?>]" value="<?php echo empty($field['value']) ? "rgb(63,72,204)" : (substr($field['value'], 0, 4) == "rgb(" ? esc_attr($field['value']) : "rgb(".esc_attr($field['value']).")"); ?>" <?php do_action('frm_field_input_html', $field) ?>/>
<?php
wp_enqueue_script('jscolor',plugins_url( 'jscolor/jscolor.js', __FILE__ ),'jquery');
}
?>