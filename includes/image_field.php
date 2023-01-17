<?php

/* Image Type Enum */
enum ImageType: string {
    case jpg = 'jpg';
    case png = 'png';
    case gif = 'gif';
    case svg = 'svg';
    case mp4 = 'mp4';

    static function array_from_raw(array $string_values): array {
        return array_map('ImageType::from', $string_values);
    }

    static function array_to_raw(array $type_values): array {
        $to_raw = function (ImageType $image_type): string {
            return $image_type->value;
        };
        return array_map($to_raw, $type_values);
    }

    static function raw_cases(): array {
        return ImageType::array_to_raw(ImageType::cases());
    }

    function valid_extensions(): array {
        switch ($this) {
            case ImageType::jpg:
                return ['jpg', 'jpeg'];
            case ImageType::mp4:
                return ['mp4', 'm4v'];
            default:
                return [$this->value];
        }
    }

    function display_name(): string {
        switch ($this) {
            case ImageType::jpg:
                return "JPEG Image";
            case ImageType::png:
                return "PNG Image";
            case ImageType::gif:
                return "GIF Image";
            case ImageType::svg:
                return "SVG Vector Image";
            case ImageType::mp4:
                return "MPEG-4 Video";
        }
    }
}


/* Option Defaults */
$image_field_option_defaults = array(
    'saveNewValues' => false,
    'showMediaLibrary' => false,
    'maxSizeKB' => 120,
    'allowed_types' => ImageType::raw_cases()
);



/* Instantiate the field */
add_filter('frm_pro_available_fields', 'add_image_field');
function add_image_field($fields) {
    $fields['pattern_image'] = array(
        'name' => 'Pattern Image' // the key for the field and the label
    );
    return $fields;
}

/* Set Field Name and Option Defaults */
add_filter('frm_before_field_created', 'set_image_field_defaults');
function set_image_field_defaults($field_data) {
    global $image_field_option_defaults;

    if ($field_data['type'] != 'pattern_image') {
        return $field_data;
    }

    foreach ($image_field_option_defaults as $option => $default) {
        $field_options[$option] = $default;
    }

    $field_data['name'] = 'Pattern Image';
    return $field_data;
}


/* Show the placeholder in form builder */
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

/* Show custom field options form */
add_action('frm_field_options_form', 'image_field_options', 10, 3);
function image_field_options($field, $display, $values) {
    global $image_field_option_defaults;

    if ($field['type'] != 'pattern_image') {
        return;
    }
    $id = $field['id'];

    foreach ($image_field_option_defaults as $option => $default) {
        if (!isset($field[$option])) {
            $field[$option] = $default;
        }
    }

    $maxSize = $field['maxSizeKB'];
    $allowed_types = ImageType::array_from_raw($field['allowed_types']);
?>
    <tr>
        <td>
            <label for="maxSizeKB_<?php echo esc_attr($id) ?>">Max File Size (KB)</label>
            <input type="number" id="maxSizeKB_<?php echo esc_attr($id) ?>" name="field_options[maxSizeKB_<?php echo esc_attr($id) ?>]" value="<?php echo esc_attr($field['maxSizeKB']) ?>" />
        </td>
        <td>
            <label for="mediaLibrarySettings_<?php echo esc_attr($id) ?>">Media Library</label>
            <div id="mediaLibrarySettings_<?php echo esc_attr($id) ?>">
                <label for="showMediaLibrary_<?php echo esc_attr($id) ?>">
                    <input type="checkbox" id="showMediaLibrary_<?php echo esc_attr($id) ?>" class="frm_checkbox" name="field_options[showMediaLibrary_<?php echo esc_attr($id) ?>]" value="1" <?php echo ($field['showMediaLibrary']) ? 'checked' : '' ?>>
                    Show library
                </label>
                <label for="saveNewValues_<?php echo esc_attr($id) ?>">
                    <input type="checkbox" id="saveNewValues_<?php echo esc_attr($id) ?>" class="frm_checkbox" name="field_options[saveNewValues_<?php echo esc_attr($id) ?>]" value="1" <?php echo ($field['saveNewValues']) ? 'checked' : '' ?>>
                    Save new images to library
                </label>
            </div>
        </td>
        <td>
            <label for="allowedImageTypes_<?php echo esc_attr($id) ?>">Allowed File Types</label>
            <div id="allowedImageTypes_<?php echo esc_attr($id) ?>">
                <?php foreach (ImageType::cases() as $image_type) : ?>
                    <div>
                        <input type="checkbox" id="allowedImageType_<?php echo esc_attr($image_type->value) ?>_<?php echo esc_attr($id) ?>" class="frm_checkbox" name="field_options[allowed_types_<?php echo esc_attr($image_type->value) ?>_<?php echo esc_attr($id) ?>]" <?php echo (in_array($image_type, $allowed_types)) ? 'checked' : '' ?>>
                        <label for="allowedImageType_<?php echo esc_attr($image_type->value) ?>_<?php echo esc_attr($id) ?>" class="frm_inline_label"><?php echo $image_type->display_name() ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </td>
    </tr>
<?php
}

/* Format and save custom field options */
add_filter('frm_update_field_options', 'update_image_field_options', 10, 3);
function update_image_field_options($field_options, $field, $values) {
    global $image_field_option_defaults;

    if ($field->type != 'pattern_image')
        return $field_options;

    $allowed_types = [];
    foreach (ImageType::cases() as $image_type) {
        $key_name = "allowed_types_" . $image_type->value . "_" . $field->id;
        if (isset($values['field_options'][$key_name])) {
            array_push($allowed_types, $image_type);
            unset($values['field_options'][$key_name]);
        }
    }
    $allowed_types = ImageType::array_to_raw($allowed_types);
    $values['field_options']["allowed_types_" . $field->id] = $allowed_types;

    foreach ($image_field_option_defaults as $option => $default) {
        $field_options[$option] = $values['field_options'][$option . '_' . $field->id] ?? $default;
    }

    return $field_options;
}



/* Show the field in your form */
add_action('frm_form_fields', 'show_image_field', 10, 2);
function show_image_field($field, $field_name) {
    $plugin_url = plugin_dir_url(__FILE__);
    if ($field['type'] != 'pattern_image') {
        return;
    }

    $field['value'] = stripslashes_deep($field['value']);
    $maxSizeBytes = $field['maxSizeKB'] * 1000;
    $existing_value = esc_attr($field['value']);
    $field_id = substr($field_name, 10, 3);

    $html = file_get_contents(WP_PLUGIN_DIR . '/PatternFileFields/html/image_field.html');
    $html = str_replace("UID", $field_id, $html);
    $html = str_replace("IMAGE_BYTE_LIMIT", "$maxSizeBytes", $html);
    $html = str_replace("EXISTING_IMAGE_DATA", $existing_value, $html);
    $html = str_replace("LIBRARY_CLASS", ($field['showMediaLibrary'] ? '' : 'hidden'), $html);
    echo $html;
}

/* Custom image preview shortcode */
add_shortcode('pattern_image_preview', 'image_preview');
function image_preview($attr) {
    $args = shortcode_atts(array(
        'data' => '',
    ), $attr);
    $image_data = $args['data'];

    if ($image_data == "") {
        $html = file_get_contents(WP_PLUGIN_DIR . '/PatternFileFields/html/image_preview_missing.html');
        return $html;
    } else {
        $preview_id = "preview_" . rand();
        $html = file_get_contents(WP_PLUGIN_DIR . '/PatternFileFields/html/image_preview.html');
        $html = str_replace("UID", $preview_id, $html);
        $html = str_replace("IMAGE_SOURCE", $image_data, $html);
        return $html;
    }
}
