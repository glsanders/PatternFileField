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

include_once("includes/image_field.php");
include_once("includes/audio_field.php");
include_once("includes/file_field.php");

/*
  STYLE SHEET
 */
add_action('wp_enqueue_scripts', 'add_supporting_files');
function add_supporting_files() {
  $plugin_url = plugin_dir_url(__FILE__);
  // wp_enqueue_script('pattern_fields_audio_scripts', $plugin_url . 'javascript/audio_scripts.js');
  // wp_enqueue_script('pattern_fields_image_scripts', $plugin_url . 'javascript/image_scripts.js');
  wp_enqueue_script('pattern_fields_image_scripts', $plugin_url . 'javascript/scripts.js');
  wp_enqueue_style('pattern_fields_common_styles', $plugin_url . 'css/common_styles.css');
  wp_enqueue_style('pattern_fields_audio_styles', $plugin_url . 'css/audio_styles.css');
  wp_enqueue_style('pattern_fields_image_styles', $plugin_url . 'css/image_styles.css');
  wp_enqueue_style('pattern_fields_library_styles', $plugin_url . 'css/library_styles.css');
  wp_enqueue_style('pattern_material_icons', 'https://fonts.googleapis.com/icon?family=Material+Icons');
  wp_enqueue_style('bootstrap_styles', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css');
  wp_enqueue_script('bootstramp_scripts', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js');
}
