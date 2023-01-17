<?php

enum PatternFileCategory: string {
    case image = 'image';
    case video = 'video';
    case audio = 'audio';
    // case application = 'application';
    // case other = 'other';

    function file_types(): array {
        $file_types = array_filter(PatternFileType::cases(), function ($type) {
            return $type->category() == $this;
        });
        return $file_types;
    }

    function file_extensions(): array {
        $types = $this->file_types();
        $extension_lists = array_map(function ($type) {
            return $type->valid_extensions();
        }, $types);
        return array_reduce($extension_lists, function ($complete_list, $ext_list) {
            return array_merge($complete_list, $ext_list);
        }, []);
    }

    function display_name(): string {
        switch ($this) {
            case PatternFileCategory::image:
                return "Image File Types";
            case PatternFileCategory::video:
                return "Video File Types";
            case PatternFileCategory::audio:
                return "Audio File Types";
                // default:
                //     return "Other File Types";
        }
    }
}

/* Image Type Enum */
enum PatternFileType: string {
    case jpg = 'jpeg';
    case png = 'png';
    case gif = 'gif';
    case svg = 'svg+xml';
    case mp4 = 'mp4';
    case mp3 = 'mpeg';
    case aac = 'aac';
    case wav = 'wav';
    case flac = 'flac';
    // case pdf = 'pdf';

    static function from_data_string(string $data): PatternFileType | null {
        $test_string = substr($data, 0, 40);
        foreach (PatternFileType::cases() as $type) {
            $search_string = $type->mime_type();
            if (str_contains($test_string, $search_string)) {
                return $type;
            }
        };
        return null;
    }

    static function array_from_raw(array $string_values): array {
        return array_map('PatternFileType::from', $string_values);
    }

    static function array_to_raw(array $type_values): array {
        $to_raw = function (PatternFileType $file_type): string {
            return $file_type->value;
        };
        return array_map($to_raw, $type_values);
    }

    static function array_to_mime_type(array $type_values): array {
        $to_type_string = function (PatternFileType $file_type): string {
            return $file_type->mime_type();
        };
        return array_map($to_type_string, $type_values);
    }

    static function raw_cases(): array {
        return PatternFileType::array_to_raw(PatternFileType::cases());
    }

    static function valid_extensions_list($types): array {
        $extension_lists = array_map(function ($type) {
            return $type->valid_extensions();
        }, $types);
        return array_reduce($extension_lists, function ($complete_list, $ext_list) {
            return array_merge($complete_list, $ext_list);
        }, []);
    }

    function valid_extensions(): array {
        switch ($this) {
            case PatternFileType::jpg:
                return [$this->value, 'jpg'];
            case PatternFileType::mp4:
                return [$this->value, 'm4v'];
            case PatternFileType::aac:
                return [$this->value, 'm4a'];
                // default:
                //     return [$this->value];
        }
    }

    function display_name(): string {
        switch ($this) {
            case PatternFileType::jpg:
                return "JPEG Image";
            case PatternFileType::png:
                return "PNG Image";
            case PatternFileType::gif:
                return "GIF Image";
            case PatternFileType::svg:
                return "SVG Vector Image";
            case PatternFileType::mp4:
                return "MPEG-4 Video";
            case PatternFileType::mp3:
                return "MPEG-3 Compressed Audio";
            case PatternFileType::aac:
                return "AAC Compressed Audio";
            case PatternFileType::wav:
                return "WAV Lossless Audio";
            case PatternFileType::flac:
                return "FLAC Lossless Audio";
                // case PatternFileType::pdf:
                //     return 'PDF Document';
        }
    }

    function category(): PatternFileCategory {
        switch ($this) {
            case PatternFileType::jpg:
            case PatternFileType::png:
            case PatternFileType::gif:
            case PatternFileType::svg:
                return PatternFileCategory::image;
            case PatternFileType::mp4:
                return PatternFileCategory::video;
            case PatternFileType::mp3:
            case PatternFileType::aac:
            case PatternFileType::wav:
            case PatternFileType::flac:
                return PatternFileCategory::audio;
                // case PatternFileType::pdf:
                //     return PatternFileCategory::application;
        }
    }

    function mime_type(): String {
        return $this->category()->value . '/' . $this->value;
    }
}
