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

use core\json_editor\helper\document_helper;
use core\orm\query\builder;
use core\webapi\execution_context;
use core\webapi\type_resolver;
use totara_mobile\local\duedate_state as mobile_duedate_state;
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
                    if (document_helper::is_valid_json_document($item->description)) {
                        return 'JSON_EDITOR';
                    } else {
                        return 'HTML';
                    }
                    break;
                case FORMAT_PLAIN:
                    return 'PLAIN';
                    break;
                case FORMAT_MARKDOWN:
                    return 'MARKDOWN';
                    break;
                case FORMAT_JSON_EDITOR:
                    return 'JSON_EDITOR';
                    break;
                default:
                    // Note: There is also FORMAT_WIKI but it has been deprecated since 2005.
                    throw new \coding_exception("Unrecognised description format '{$item->description_format}'" );
                    break;
            }
        }

        // concatenate type and id to form unique identifiers for mobile
        if ($field == 'id') {
            return $item->get_type() . '_' . $item->id;
        }

        if ($field == 'duedate' || $field == 'duedate_state') {
            // Make sure we have the due date, this is for programs and certifications, also courses inside learning plans.
            if ($item instanceof item_has_dueinfo) {
                $item->ensure_duedate_loaded();
            } else {
                return null;
            }

            if ($field == 'duedate') {
                if (empty($item->duedate) || $item->duedate == -1) {
                    return null;
                }
            }

            // Mobile - override duedate state to make them consistent.
            if ($field == 'duedate_state') {
                if (!empty($item->duedate) && $item->duedate != -1) {
                    $item->duedate_state = mobile_duedate_state::calculate($item->duedate);
                } else {
                    return null;
                }
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
            $item->mobile_image = $item->image_src;
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
