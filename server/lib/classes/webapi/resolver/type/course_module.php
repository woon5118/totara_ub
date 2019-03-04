<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author David Curry <david.curry@totaralearning.com>
 * @package core
 */

namespace core\webapi\resolver\type;

use core\webapi\execution_context;
use core_course\formatter\course_module_formatter;
use core_availability\info as info;
use core\format;
use core\webapi\type_resolver;
use context_course;
use coursecat;

class course_module implements type_resolver {

    public static function resolve(string $field, $cminfo, array $args, execution_context $ec) {
        global $DB, $USER, $CFG;

        require_once($CFG->libdir . '/grade/grade_grade.php');
        require_once($CFG->libdir . '/grade/grade_item.php');

        if (!$cminfo instanceof \cm_info) {
            throw new \coding_exception('Only cm_info objects are accepted: ' . gettype($cminfo));
        }

        // Take a lot of the basic information out of the wrapper, and use it as the base object.
        $info = $cminfo->get_course_module_record(true);

        if (!$mod_context = \context_module::instance($info->id, IGNORE_MISSING)) {
            // If there is no matching context we have a bad object, ignore missing so we can do our own error.
            throw new \coding_exception('Only valid module objects are accepted');
        }

        $format = $args['format'] ?? null;
        $available = $cminfo->available;
        if ($field == 'available') {
            return $available;
        }

        if ($field == 'instanceid') {
            $info->instanceid = $info->instance;
        }

        if ($field == 'modtype') {
            $info->modtype = $info->modname;
        }

        $course = $cminfo->get_course();
        if ($field == 'availablereason') {
            $info->availablereason = [];
            if (!$available) {
                if (!empty($cminfo->availableinfo)) {
                    if (is_string($cminfo->availableinfo)) {
                        $info->availablereason = [$cminfo->availableinfo];
                    } else {
                        $modinfo = get_fast_modinfo($course->id, $USER->id);
                        $coursecontext = \context_course::instance($course->id);

                        // Mimic half of core_availability::format_info() to get the cm names.
                        foreach ($cminfo->availableinfo->items as $item) {
                            // Don't waste time if there are no special tags.
                            if (strpos($item, '<AVAILABILITY_') === false) {
                                $cminfo->availablereason[] = $item;
                                continue;
                            }

                            $reason = preg_replace_callback('~<AVAILABILITY_CMNAME_([0-9]+)/>~',
                                        function($matches) use($modinfo, $coursecontext) {
                                            $cm = $modinfo->get_cm($matches[1]);
                                            if ($cm->has_view() and $cm->uservisible) {
                                                // Help student by providing a link to the module which is preventing availability.
                                                return \html_writer::link($cm->url, format_string($cm->name, true, array('context' => $coursecontext)));
                                            } else {
                                                return format_string($cm->name, true, array('context' => $coursecontext));
                                            }
                                        }, $item
                                    );
                            $info->availablereason[] = $reason;
                        }
                    }
                }
            }
        }

        /**
         * Note: This is a constant defined in lib/completionlib.php
         *       translated into string constants for mobile
         */
        if ($field == 'completion') {
            switch ($info->completion) {
                case COMPLETION_TRACKING_NONE :
                    return 'tracking_none';
                    break;
                case COMPLETION_TRACKING_MANUAL :
                    return 'tracking_manual';
                    break;
                case COMPLETION_TRACKING_AUTOMATIC :
                    return 'tracking_automatic';
                    break;
                default :
                    return 'unknown';
                    break;
            }
        }

        /**
         * Note: This is a constant defined in lib/completionlib.php
         *       translated into string constants for mobile
         */
        if ($field == 'completionstatus') {
            if ($available) {
                $completioninfo = new \completion_info($course);
                $completiondata = $completioninfo->get_data($cminfo);
                switch ($completiondata->completionstate) {
                    case COMPLETION_INCOMPLETE :
                        return 'incomplete';
                        break;
                    case COMPLETION_COMPLETE :
                        return 'complete';
                        break;
                    case COMPLETION_COMPLETE_PASS :
                        return 'complete_pass';
                        break;
                    case COMPLETION_COMPLETE_FAIL :
                        return 'complete_fail';
                        break;
                    default :
                        return 'unknown';
                        break;
                }
            } else {
                return 'unknown';
            }
        }

        $gradefields = ['gradefinal', 'grademax', 'gradepercentage'];
        if (in_array($field, $gradefields)) {
            $item = \grade_item::fetch([
                'itemtype' => 'mod',
                'itemmodule' => $info->modname,
                'iteminstance' => $info->instance,
            ]);

            // No grade item found?
            if (empty($item)) {
                return 0;
            }

            $grade = new \grade_grade(array('itemid' => $item->id, 'userid' => $USER->id));

            if ($field == 'gradefinal') {
                return $grade->finalgrade;
            }

            if ($field == 'grademax') {
                return $grade->rawgrademax;
            }

            if ($field == 'gradepercentage') {
               return ((float)$grade->finalgrade / (float)$grade->rawgrademax) * 100;
            }
        }

        if ($field == 'showdescription') {
            if ($available) {
                return $info->showdescription;
            } else {
                return false;
            }
        }

        $modvaluefields = ['description', 'descriptionformat'];
        if (in_array($field, $modvaluefields)) {
            // Note: The get_coursemodule_info functions do too much pre-formatting, thisi s the easiest way to handle it.
            $modvalues = $DB->get_record($cminfo->modname, ['id' => $cminfo->instance], 'name, intro, introformat');

            if ($field == 'description') {
                $info->description = $modvalues->intro;
            }

            // Transform the format field from the constants to a core_format string.
            if ($field == 'descriptionformat') {
                switch ($modvalues->introformat) {
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
                        throw new \coding_exception("Unrecognised description format '{$modvalues->introformat}'" );
                        break;
                }
            }
        }

        if ($field == 'viewurl') {
            if ($available) {
                return $cminfo->url;
            } else {
                return '';
            }
        }

        $formatter = new course_module_formatter($info, $mod_context);
        $formatted = $formatter->format($field, $format);

        // For mobile execution context, rewrite pluginfile urls in description and image_src fields.
        // This is clearly a hack, please suggest something more elegant.
        if (is_a($ec, 'totara_mobile\webapi\execution_context') && in_array($field, ['description'])) {
            $formatted = str_replace($CFG->wwwroot . '/pluginfile.php', $CFG->wwwroot . '/totara/mobile/pluginfile.php', $formatted);
        }

        return $formatted;
    }
}
