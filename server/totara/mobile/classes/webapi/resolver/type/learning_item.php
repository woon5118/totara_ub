<?php
/**
 *
 * This file is part of Totara LMS
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Chris Snyder <chris.snyder@totaralearning.com>
 * @package totara_mobile
 */

namespace totara_mobile\webapi\resolver\type;

use core\orm\query\builder;
use core\webapi\execution_context;
use core\webapi\type_resolver;
use totara_mobile\formatter\mobile_learning_item_formatter;
use totara_core\user_learning\item;
use totara_core\user_learning\item_base;
use totara_core\user_learning\item_has_dueinfo;
use totara_core\user_learning\item_has_progress;

class learning_item implements type_resolver {

    /**
     * Resolve program fields
     *
     * @param string $field
     * @param item|item_base $item
     * @param array $args
     * @param execution_context $ec
     * @return mixed
     */
    public static function resolve(string $field, $item, array $args, execution_context $ec) {
        global $CFG, $OUTPUT;

        if (!$item instanceof item) {
             throw new \coding_exception('Only learning_item objects are accepted: ' . gettype($item));
        }

        if ($item->get_type() == 'course') {
            $classpath = 'core\webapi\resolver\type\course';
        } else {
            $classpath = $item->get_component() . '\webapi\resolver\type\\' . $item->get_type();
        }

        $format = $args['format'] ?? null;
        $context = self::get_item_context($item);
        $authfield = ($field == 'description') ? 'summary' : $field;
        if (!$classpath::authorize($authfield, $format, $context)) {
            return null;
        }

        // Transform the format field from the constants to a core_format string.
        if ($field == 'description_format') {
            switch ($item->description_format) {
                case FORMAT_MOODLE:
                case FORMAT_HTML:
                    return 'HTML';
                    break;
                case FORMAT_PLAIN:
                    return 'PLAIN';
                    break;
                case FORMAT_RAW:
                    return 'RAW';
                    break;
                case FORMAT_MARKDOWN:
                    return 'MARKDOWN';
                    break;
                default:
                    // Note: There is also FORMAT_WIKI but it has been deprecated since 2005.
                    throw new \coding_exception("Unrecognised description format '{$item->description_format}'" );
                    break;
            }
        }

        if ($field == 'duedate') {
            // Make sure we have the due date, this is for programs and certifications, also courses inside learning plans.
            if ($item instanceof item_has_dueinfo) {
                $item->ensure_duedate_loaded();
                if (empty($item->duedate) || $item->duedate == -1) {
                    return null;
                }
            } else {
                return null;
            }
        }

        // Progress actually maps to progress_percentage
        if ($field == 'progress') {
            // Make sure we have the percentage in the progress.
            if ($item instanceof item_has_progress && $item->can_be_completed()) {
                return $item->get_progress_percentage();
            } else {
                return null;
            }
        }

        if ($field == 'duedate_state') {
            if (empty($item->duedate) || $item->duedate == -1) {
                $item->duedate_state = null; // For consistency.
            }
        }

        if ($field == 'url_view') {
            return $item->url_view->out();
        }

        if ($field == 'image_src') {
            if (empty($item->image_src)) {
                $item->image_src = null; // For consistency.
            }
        }

        if ($field == 'mobile_coursecompat') {
            if ($item->get_type() == 'course') {
                if (!empty($item->id)) {
                    $item->mobile_coursecompat = (bool) builder::table('totara_mobile_compatible_courses')
                        ->where('courseid', $item->id)
                        ->count();
                } else {
                    $item->mobile_coursecompat = false;
                }
            } else {
                $item->mobile_coursecompat = true;
            }
        }

        if ($field == 'mobile_image') {
            // Mobile image is blank if the item has default image. We have to kind of reverse-engineer this.
            if (empty($item->image_src)) {
                $item->image_src = "";
            }
            if ($item->image_src instanceof \moodle_url) {
                $item->image_src = $item->image_src->out();
            }
            switch ($item->get_type()) {
                case 'course':
                    $url = false;
                    if ($item->image_src == $OUTPUT->image_url('course_defaultimage', 'moodle')) {
                        $item->mobile_image = "";
                    } else {
                        $url = false;
                        if (get_config('course', 'defaultimage')) {
                            $fs = get_file_storage();
                            $files = $fs->get_area_files(
                                \context_system::instance()->id,
                                'course',
                                'defaultimage',
                                0,
                                "timemodified DESC",
                                false
                            );
                            if ($files) {
                                $file = reset($files);
                                $themerev = theme_get_revision();
                                $url = \moodle_url::make_pluginfile_url(
                                    \context_system::instance()->id,
                                    'course',
                                    'defaultimage',
                                    $themerev,
                                    '/',
                                    $file->get_filename(),
                                    false
                                );
                                $url = $url->out();
                            }
                        }
                        if ($url && $item->image_src == $url) {
                            $item->mobile_image = "";
                        } else {
                            $item->mobile_image = $item->image_src;
                        }
                    }
                    break;
                case 'program':
                case 'certification':
                    if ($item->get_type() == 'certification') {
                        $component = 'totara_certification';
                        $filearea = 'totara_certification_default_image';
                    } else {
                        $component = 'totara_program';
                        $filearea = 'totara_program_default_image';
                    }
                    if ($item->image_src == $OUTPUT->image_url('defaultimage', $component)) {
                        $item->mobile_image = "";
                    } else {
                        $url = false;
                        $fs = get_file_storage();
                        // check if same as custom default
                        $files = array_values($fs->get_area_files(
                            \context_system::instance()->id,
                            'totara_core',
                            $filearea,
                            0,
                            "timemodified DESC",
                            false
                        ));
                        if ($files) {
                            $file = \moodle_url::make_pluginfile_url(
                                $files[0]->get_contextid(),
                                $files[0]->get_component(),
                                $files[0]->get_filearea(),
                                $files[0]->get_itemid(),
                                $files[0]->get_filepath(),
                                $files[0]->get_filename()
                            );
                            $url = $file->out();
                        }
                        if ($url && $item->image_src == $url) {
                            $item->mobile_image = "";
                        } else {
                            $item->mobile_image = $item->image_src;
                        }
                    }
                    break;
            }
        }

        $formatter = new mobile_learning_item_formatter($item, $context);
        $formatted = $formatter->format($field, $format);

        // For mobile execution context, rewrite pluginfile urls in description and image_src fields.
        // This is clearly a hack, please suggest something more elegant.
        if (is_a($ec, 'totara_mobile\webapi\execution_context') && in_array($field, ['description', 'image_src', 'mobile_image'])) {
            $formatted = str_replace($CFG->wwwroot . '/pluginfile.php', $CFG->wwwroot . '/totara/mobile/pluginfile.php', $formatted);
        }
        return $formatted;
    }

    /**
     * @param item|item_base $item
     * @return \context_course|\context_program|false
     */
    private static function get_item_context(item $item) {
        switch ($item->get_type()) {
            case 'course':
                return \context_course::instance($item->id);
                break;
            case 'program':
            case 'certification':
                return \context_program::instance($item->id);
                break;
        }
    }
}
