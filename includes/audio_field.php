<?php

/* Audio Type Enum */
enum AudioType: string {
    case mp3 = 'mp3';
    case aac = 'aac';
    case wav = 'wav';
    case flac = 'flac';

    static function array_from_raw(array $string_values): array {
        return array_map('AudioType::from', $string_values);
    }

    static function array_to_raw(array $type_values): array {
        $to_raw = function (AudioType $audio_type): string {
            return $audio_type->value;
        };
        return array_map($to_raw, $type_values);
    }

    static function raw_cases(): array {
        return AudioType::array_to_raw(AudioType::cases());
    }

    function valid_extensions(): array {
        switch ($this) {
            case AudioType::aac:
                return ['aac', 'm4a'];
            default:
                return [$this->value];
        }
    }

    function display_name(): string {
        switch ($this) {
            case AudioType::mp3:
                return "MPEG-3 Compressed Audio";
            case AudioType::aac:
                return "AAC Compressed Audio";
            case AudioType::wav:
                return "WAV Lossless Audio";
            case AudioType::flac:
                return "FLAC Lossless Audio";
        }
    }
}

/* Option Defaults */
$audio_field_option_defaults = array(
    'saveNewValues' => false,
    'showMediaLibrary' => false,
    'maxSizeKB' => 120,
    'allowed_types' => AudioType::raw_cases()
);

/* Show custom field options form */
add_action('frm_field_options_form', 'audio_field_options', 10, 3);
function audio_field_options($field, $display, $values) {
    global $audio_field_option_defaults;

    if ($field['type'] != 'pattern_audio') {
        return;
    }
    $id = $field['id'];

    foreach ($audio_field_option_defaults as $option => $default) {
        if (!isset($field[$option])) {
            $field[$option] = $default;
        }
    }

    $maxSize = $field['maxSizeKB'];
    $allowed_types = AudioType::array_from_raw($field['allowed_types']);
?>
    <tr>
        <td>
            <label for="maxSizeKB_<?php echo esc_attr($id) ?>">Max File Size (KB)</label>
            <input type="number" id="maxSizeKB_<?php echo esc_attr($id) ?>" name="field_options[maxSizeKB_<?php echo esc_attr($id) ?>]" value="<?php echo esc_attr($field['maxSizeKB']) ?>" />
        </td>
        <td>
        <td>
            <label for="mediaLibrarySettings_<?php echo esc_attr($id) ?>">Media Library</label>
            <div id="mediaLibrarySettings_<?php echo esc_attr($id) ?>">
                <label for="showMediaLibrary_<?php echo esc_attr($id) ?>">
                    <input type="checkbox" id="showMediaLibrary_<?php echo esc_attr($id) ?>" class="frm_checkbox" name="field_options[showMediaLibrary_<?php echo esc_attr($id) ?>]" value="1" <?php echo ($field['showMediaLibrary']) ? 'checked' : '' ?>>
                    Show library
                </label>
                <label for="saveNewValues_<?php echo esc_attr($id) ?>">
                    <input type="checkbox" id="saveNewValues_<?php echo esc_attr($id) ?>" class="frm_checkbox" name="field_options[saveNewValues_<?php echo esc_attr($id) ?>]" value="1" <?php echo ($field['saveNewValues']) ? 'checked' : '' ?>>
                    Save new audio files to library
                </label>
            </div>
        </td>
        <td>
            <label for="allowedImageTypes_<?php echo esc_attr($id) ?>">Allowed File Types</label>
            <div id="allowedImageTypes_<?php echo esc_attr($id) ?>">
                <?php foreach (AudioType::cases() as $image_type) : ?>
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
add_filter('frm_update_field_options', 'update_audio_field_options', 10, 3);
function update_audio_field_options($field_options, $field, $values) {
    global $audio_field_option_defaults;

    if ($field->type != 'pattern_audio')
        return $field_options;

    $allowed_types = [];
    foreach (AudioType::cases() as $audio_type) {
        $key_name = "allowed_types_" . $audio_type->value . "_" . $field->id;
        if (isset($values['field_options'][$key_name])) {
            array_push($allowed_types, $audio_type);
            unset($values['field_options'][$key_name]);
        }
    }
    $allowed_types = AudioType::array_to_raw($allowed_types);
    $values['field_options']["allowed_types_" . $field->id] = $allowed_types;

    foreach ($audio_field_option_defaults as $option => $default) {
        $field_options[$option] = $values['field_options'][$option . '_' . $field->id] ?? $default;
    }

    return $field_options;
}


/* Instantiate the field */
add_filter('frm_pro_available_fields', 'add_audio_field');
function add_audio_field($fields) {
    $fields['pattern_audio'] = array(
        'name' => 'Pattern Audio' // the key for the field and the label
    );
    return $fields;
}

/* Set field name and option defaults */
add_filter('frm_before_field_created', 'set_audio_field_defaults');
function set_audio_field_defaults($field_data) {
    global $audio_field_option_defaults;

    if ($field_data['type'] != 'pattern_audio') {
        return $field_data;
    }

    foreach ($audio_field_option_defaults as $option => $default) {
        $field_options[$option] = $default;
    }

    $field_data['name'] = 'Pattern Audio';
    return $field_data;
}


/* Show the field placeholder */
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

/* Show the field in form */
add_action('frm_form_fields', 'show_audio_field', 10, 2);
function show_audio_field($field, $field_name) {
    if ($field['type'] != 'pattern_audio') {
        return;
    }
    $field['value'] = stripslashes_deep($field['value']);
    $existing_value = esc_attr($field['value']);
    $field_id = substr($field_name, 10, 3);
    $html = file_get_contents(WP_PLUGIN_DIR . '/PatternFileFields/html/audio_field.html');
    $html = str_replace("UID", $field_id, $html);
    $html = str_replace("AUDIO_BYTE_LIMIT", "120000", $html);
    $html = str_replace("EXISTING_AUDIO_DATA", $existing_value, $html);
    $html = str_replace("LIBRARY_BUTTON_HIDDEN", (isset($field['showMediaLibrary']) ? '' : 'hidden'), $html);
    echo $html;
}


/* Custom audio preview shortcode */
add_shortcode('pattern_audio_preview', 'audio_preview');
function audio_preview($attr) {
    $args = shortcode_atts(array(
        'data' => '',
    ), $attr);
    $audio_data = $args['data'];

    if ($audio_data == "") {
        $html = file_get_contents(WP_PLUGIN_DIR . '/PatternFileFields/html/audio_preview_missing.html');
        return $html;
    } else {
        $preview_id = "preview_" . rand();
        $html = file_get_contents(WP_PLUGIN_DIR . '/PatternFileFields/html/audio_preview.html');
        $html = str_replace("UID", $preview_id, $html);
        $html = str_replace("AUDIO_SOURCE", $audio_data, $html);
        return $html;
    }
}
