<?php


include_once('file_types.php');


/* Option Defaults */
$file_field_option_defaults = array(
    'saveNewValues' => false,
    'showMediaLibrary' => false,
    'enabledLibraryCollections' => [],
    'maxSizeKB' => 120,
    'allowed_types' => []
);


/* Instantiate the field */
add_filter('frm_pro_available_fields', 'add_file_field');
function add_file_field($fields) {
    $fields['pattern_file'] = array(
        'name' => 'Pattern File Upload' // the key for the field and the label
    );
    return $fields;
}

/* Set Field Name and Option Defaults */
add_filter('frm_before_field_created', 'set_file_field_defaults');
function set_file_field_defaults($field_data) {
    global $file_field_option_defaults;

    if ($field_data['type'] != 'pattern_file') {
        return $field_data;
    }

    foreach ($file_field_option_defaults as $option => $default) {
        $field_options[$option] = $default;
    }

    $field_data['name'] = 'Pattern File Upload';
    return $field_data;
}


/* Show the placeholder in form builder */
add_action('frm_display_added_fields', 'show_file_field_placeholder');
function show_file_field_placeholder($field) {
    if ($field['type'] != 'pattern_file') {
        return;
    }
    $field_name = 'item_meta[' . $field['id'] . ']';
?>
    <div class="frm_html_field_placeholder">
        <div class="howto button-secondary frm_html_field">This is a placeholder for your file field. <br />View your form to see it in action.</div>
    </div>
<?php
}

/* Show custom field options form */
add_action('frm_field_options_form', 'file_field_options', 10, 3);
function file_field_options($field, $display, $values) {
    global $file_field_option_defaults;

    if ($field['type'] != 'pattern_file') {
        return;
    }
    $id = $field['id'];

    foreach ($file_field_option_defaults as $option => $default) {
        if (!isset($field[$option])) {
            $field[$option] = $default;
        }
    }
    if (class_exists('Pattern_Media')) {
        $collections = Pattern_Media::getCollections();
    }

    $maxSize = $field['maxSizeKB'];
    $allowed_types = PatternFileType::array_from_raw($field['allowed_types']);
    $enabled_collections = $field['enabledLibraryCollections'];
?>
    <tr>
        <td>
            <label for="maxSizeKB_<?php echo esc_attr($id) ?>">Max File Size (KB)</label>
            <input type="number" id="maxSizeKB_<?php echo esc_attr($id) ?>" name="field_options[maxSizeKB_<?php echo esc_attr($id) ?>]" value="<?php echo esc_attr($field['maxSizeKB']) ?>" />
        </td>
        <td>
            <label for="allowedFileTypes_<?php echo esc_attr($id) ?>" style="margin-bottom: 10px; margin-top: 10px">Restrict Upload File Types</label>
            <div id="allowedFileTypes_<?php echo esc_attr($id) ?>" style="margin-left: 20px">
                <?php foreach (PatternFileCategory::cases() as $file_category) : ?>
                    <label><?php echo $file_category->display_name(); ?></label>
                    <div style="margin-left: 15px">
                        <?php foreach ($file_category->file_types() as $file_type) : ?>
                            <div>
                                <input type="checkbox" id="allowedImageType_<?php echo esc_attr($file_type->value) ?>_<?php echo esc_attr($id) ?>" class="frm_checkbox" name="field_options[allowed_types_<?php echo esc_attr($file_type->value) ?>_<?php echo esc_attr($id) ?>]" <?php echo (in_array($file_type, $allowed_types)) ? 'checked' : '' ?>>
                                <label for="allowedImageType_<?php echo esc_attr($file_type->value) ?>_<?php echo esc_attr($id) ?>" class="frm_inline_label"><?php echo $file_type->display_name() ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </td>
        <td>
            <label for="mediaLibrarySettings_<?php echo esc_attr($id) ?>" class="margin-bottom: 10px; margin-top: 10px">Media Library</label>
            <div id="mediaLibrarySettings_<?php echo esc_attr($id) ?>">
                <label for="saveNewValues_<?php echo esc_attr($id) ?>">
                    <input type="checkbox" id="saveNewValues_<?php echo esc_attr($id) ?>" class="frm_checkbox" name="field_options[saveNewValues_<?php echo esc_attr($id) ?>]" value="1" <?php echo ($field['saveNewValues']) ? 'checked' : '' ?>>
                    Save uploaded files to media library
                </label>
                <label for="showMediaLibrary_<?php echo esc_attr($id) ?>">
                    <input type="checkbox" id="showMediaLibrary_<?php echo esc_attr($id) ?>" class="frm_checkbox" name="field_options[showMediaLibrary_<?php echo esc_attr($id) ?>]" value="1" <?php echo ($field['showMediaLibrary']) ? 'checked' : '' ?> onclick="showHideCollections(<?php echo esc_attr($id) ?>);">
                    Allow selecting from library
                </label>
            </div>
        </td>
        <td id="collections_picker_<?php echo esc_attr($id) ?>" style="<?php echo ($field['showMediaLibrary']) ? '' : 'display: none;' ?>">
            <label for="allowed_collections_<?php echo esc_attr($id) ?>" style="margin-bottom: 10px">Allowed Collections</label>
            <div id="allowed_collections_<?php echo esc_attr($id) ?>">
                <?php foreach ($collections as $collection) : ?>
                    <div>
                        <input type="checkbox" id="enabledLibraryCollections_<?php echo esc_attr($collection->id) ?>_<?php echo esc_attr($id) ?>" class="frm_checkbox" name="field_options[enabledLibraryCollections_<?php echo esc_attr($collection->id) ?>_<?php echo esc_attr($id) ?>]" <?php echo (in_array($collection->id, $enabled_collections)) ? 'checked' : '' ?>>
                        <label for="enabledLibraryCollections_<?php echo esc_attr($collection->id) ?>_<?php echo esc_attr($id) ?>" class="frm_inline_label"><?php echo $collection->collection_name ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </td>
    </tr>
    <script>
        function showHideCollections(id) {
            console.log("Clicked");
            var checkbox = document.getElementById(`showMediaLibrary_${id}`);
            var picker = document.getElementById(`collections_picker_${id}`);
            if (checkbox.checked) {
                picker.style.display = "block";
            } else {
                picker.style.display = "none";
            }
        }
    </script>
<?php
}

/* Process custom field options */
add_filter('frm_update_field_options', 'process_file_field_options', 10, 3);
function process_file_field_options($field_options, $field, $values) {
    global $file_field_option_defaults;

    if ($field->type != 'pattern_file')
        return $field_options;

    $allowed_types = [];
    foreach (PatternFileType::cases() as $image_type) {
        $key_name = "allowed_types_" . $image_type->value . "_" . $field->id;
        if (isset($values['field_options'][$key_name])) {
            array_push($allowed_types, $image_type);
            unset($values['field_options'][$key_name]);
        }
    }
    $allowed_types = PatternFileType::array_to_raw($allowed_types);
    $values['field_options']["allowed_types_" . $field->id] = $allowed_types;

    $allowed_collections = [];
    if (class_exists('Pattern_Media')) {
        foreach (Pattern_Media::getCollections() as $collection) {
            $key_name = "enabledLibraryCollections_" . $collection->id . "_" . $field->id;
            if (isset($values['field_options'][$key_name])) {
                array_push($allowed_collections, $collection->id);
                unset($values['field_options'][$key_name]);
            }
        }
    }
    $values['field_options']["enabledLibraryCollections_" . $field->id] = $allowed_collections;

    foreach ($file_field_option_defaults as $option => $default) {
        $field_options[$option] = $values['field_options'][$option . '_' . $field->id] ?? $default;
    }

    return $field_options;
}



/* Show the field in your form */
add_action('frm_form_fields', 'show_file_field', 10, 2);
function show_file_field($field, $field_name) {
    $plugin_url = plugin_dir_url(__FILE__);
    if ($field['type'] != 'pattern_file') {
        return;
    }

    $field_id = $field['id'];
    $maxSizeBytes = $field['maxSizeKB'] * 1000;
    $raw_value = $field['value'];
    if (is_string($raw_value)) {
        $existing_value_json = htmlspecialchars_decode($raw_value);
        $existing_value = json_decode($existing_value_json, associative: true);
    } else {
        $existing_value = $raw_value;
        $existing_value_json = (empty($existing_value)) ? '' : json_encode($existing_value);
    }
    $file_data = (empty($existing_value)) ? '' : $existing_value['data'];
    $file_type = (empty($file_data)) ? '' : PatternFileType::from_data_string($file_data);
    $mime_type = (empty($file_type)) ? '' : $file_type?->mime_type();
    $file_category = (empty($file_type)) ? '' : $file_type->category();
    $allowed_types = PatternFileType::array_from_raw($field['allowed_types']);
    if (count($allowed_types) == 0) {
        $allowed_types = PatternFileType::cases();
    }
    $allowed_mime_types = implode(',', PatternFileType::array_to_mime_type($allowed_types));
    $showMediaLibrary = $field['showMediaLibrary'] ?? false;

    $library_fetch_url = get_rest_url() . "pattern-api/media?action=getlibraryitem&id=";

    if ($showMediaLibrary && class_exists('Pattern_Media')) {
        $collections = Pattern_Media::getItemList();
    }
?>
    <div id="pattern_file_field_wrapper_<?php echo esc_attr($field_id) ?>" data-byte-limit=<?php echo esc_attr($maxSizeBytes) ?> data-allowed-types="<?php echo esc_attr($allowed_mime_types) ?>">
        <div>
            <!-- Drop Container -->
            <input style="display: none;" type="text" id="value_input_<?php echo esc_attr($field_id) ?>" name="item_meta[<?php echo esc_attr($field_id) ?>]" value="<?php echo esc_attr($existing_value_json) ?>" />
            <div id="file_drop_container_<?php echo esc_attr($field_id) ?>" class="file_container drop_container <?php echo (empty($file_data) ? '' : 'hidden') ?>" ondrop="dropZoneDrop(event, <?php echo esc_attr($field_id) ?>);" ondragover="dropZoneDragOver(event, <?php echo esc_attr($field_id) ?>);" ondragleave="dropZoneDragLeave(event, <?php echo esc_attr($field_id) ?>);">
                <div class="inner_drop_block">
                    <p class="upload_text_<?php echo esc_attr($field_id) ?>">Drag file to upload</p>
                    <input type="file" accept="<?php echo esc_attr($allowed_mime_types) ?>" name="file_input_<?php echo esc_attr($field_id) ?>" id="file_input_<?php echo esc_attr($field_id) ?>" class="file_input" onchange="onUploadFile(<?php echo esc_attr($field_id) ?>);" />
                    <label for="file_input_<?php echo esc_attr($field_id) ?>" class="drop_button">Select File<br />to Upload</label>
                    <?php if ($showMediaLibrary) { ?>
                        <button type="button" class="drop_button" data-bs-toggle="modal" data-bs-target="#libraryModal_<?php echo esc_attr($field_id) ?>">Select From<br />Pattern Library</button>
                    <?php } ?>
                    <p id="upload_error_<?php echo esc_attr($field_id) ?>" class="error hidden"></p>
                </div>
            </div>

            <!-- Image Container -->
            <div id="image_container_<?php echo esc_attr($field_id) ?>" class="file_container content_container <?php echo ($file_category == PatternFileCategory::image) ? '' : 'hidden' ?>">
                <div class="file_inner_container">
                    <div class="center_content_wrapper">
                        <img id="pattern_image_<?php echo esc_attr($field_id) ?>" class="pattern_image" src="<?php echo ($file_category == PatternFileCategory::image) ? esc_attr($file_data) : '' ?>" alt="Dropped image" />
                    </div>
                    <button class="delete_button" type="button" onclick="clearFile(<?php echo esc_attr($field_id) ?>);">Remove</button>
                </div>
            </div>

            <!-- Video Container -->
            <div id="video_container_<?php echo esc_attr($field_id) ?>" class="file_container content_container <?php echo ($file_category == PatternFileCategory::video) ? '' : 'hidden' ?>">
                <div class="file_inner_container">
                    <div class="center_content_wrapper">
                        <div class="video_wrapper">
                            <video class="pattern_video" width="220" height="170" id="pattern_video_<?php echo esc_attr($field_id) ?>" disablepictureinpicture playsinline autoplay muted loop>
                                <source type="<?php echo ($file_category == PatternFileCategory::video) ? esc_attr($mime_type) : ''  ?>" id="pattern_video_src_<?php echo esc_attr($field_id) ?>" src="<?php echo ($file_category == PatternFileCategory::video) ? esc_attr($file_data) : '' ?>">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>
                    <button class="delete_button" type="button" onclick="clearFile(<?php echo esc_attr($field_id) ?>);">Remove</button>
                </div>
            </div>

            <!-- Audio Container -->
            <div id="audio_container_<?php echo esc_attr($field_id) ?>" class="file_container audio_container <?php echo ($file_category == PatternFileCategory::audio) ? '' : 'hidden' ?>">
                <div class="file_inner_container">
                    <audio id="pattern_audio_<?php echo esc_attr($field_id) ?>" class="pattern_audio" autobuffer="autobuffer">
                        <source id="pattern_audio_src_<?php echo esc_attr($field_id) ?>" class="pattern_audio_src" src="<? echo ($file_category == PatternFileCategory::audio) ? esc_attr($file_data) : '' ?>" />
                    </audio>
                    <!-- <div class="controls"> -->
                    <div class="button_wrapper">
                        <i id="audio_button_<?php echo esc_attr($field_id) ?>" class="audio_button material-icons md-120" onclick="playPause(<?php echo esc_attr($field_id) ?>);">play_circle_filled</i>
                    </div>
                    <div class="audio_progress_line">
                        <div id="audio_progress_<?php echo esc_attr($field_id) ?>" class="audio_progress"></div>
                    </div>
                    <!-- </div> -->
                    <button class="delete_button" type="button" onclick="clearFile(<?php echo esc_attr($field_id) ?>);">Remove</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <?php if ($showMediaLibrary) : ?>
        <div class="modal fade" id="libraryModal_<?php echo esc_attr($field_id) ?>" tabindex="-1" aria-labelledby="libraryModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="library_header modal-header">
                        <h1 class="fs-3" id="libraryModalLabel">Pattern Library</h1>
                    </div>
                    <div id="library_body_<?php echo esc_attr($field_id) ?>" class="library_body modal-body overflow-auto">
                        <?php foreach (array_keys($collections) as $collection_name) : ?>
                            <label for="collection_<?php echo esc_attr($collection_name) ?>_<?php echo esc_attr($field_id) ?>"><?php echo esc_attr($collection_name) ?></label>
                            <div id="collection_<?php echo esc_attr($collection_name) ?>_<?php echo esc_attr($field_id) ?>" class="collection_row">
                                <? foreach ($collections[$collection_name] as $entry) : ?>
                                    <div>
                                        <div id="library_preview_wrapper_<?php echo esc_attr($entry->id) ?>_<?php echo esc_attr($field_id) ?>" class="library_preview_wrapper" onclick="selectLibraryEntry(<?php echo esc_attr($entry->id) ?>,<?php echo esc_attr($field_id) ?>);" data-field-id="<?php echo esc_attr($field_id) ?>" data-entry-id="<?php echo esc_attr($entry->id) ?>" data-fetch-url="<?php echo esc_attr($library_fetch_url . $entry->id) ?>">
                                            <div class="library_preview_container" class="">
                                                <img id="image_preview_<?php echo esc_attr($entry->id) ?>_<?php echo esc_attr($field_id) ?>" class="image_preview" />
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="clearSelection(<?php echo esc_attr($field_id) ?>);" data-bs-dismiss="modal">Cancel</button>
                        <button id="library_apply_button_<?php echo esc_attr($field_id) ?>" type="button" class="btn btn-primary" onclick="applyFromLibrary(<?php echo esc_attr($field_id) ?>)" data-bs-dismiss="modal" disabled>Apply</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <script>
        window.onload = configureField(<?php echo esc_attr($field_id) ?>);
    </script>
<?php
}

/* Custom image preview shortcode */
add_shortcode('pattern_image_preview', 'file_preview');
function file_preview($attr) {
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
