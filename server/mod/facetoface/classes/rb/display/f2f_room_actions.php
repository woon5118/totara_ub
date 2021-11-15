<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Simon Player <simon.player@totaralearning.com>
 * @package mod_facetoface
 */

namespace mod_facetoface\rb\display;

use totara_reportbuilder\rb\display\base;
use core\output\flex_icon;
use moodle_url;

/**
 * Display class intended for room actions
 *
 * @author Simon Player <simon.player@totaralearning.com>
 * @package mod_facetoface
 */
class f2f_room_actions extends base {

    /**
     * Handles the display
     *
     * @param string $value
     * @param string $format
     * @param \stdClass $row
     * @param \rb_column $column
     * @param \reportbuilder $report
     * @return string
     */
    public static function display($value, $format, \stdClass $row, \rb_column $column, \reportbuilder $report) {
        global $OUTPUT;

        $isexport = ($format !== 'html');
        $extrafields = self::get_extrafields_row($row, $column);

        if ($isexport) {
            return '';
        }

        if ((int)$extrafields->custom > 0) {
            return '';
        }

        $output = array();
        $params = ['id' => $value];

        $output[] = $OUTPUT->action_icon(
            new moodle_url('/mod/facetoface/room/edit.php', $params),
            new flex_icon('edit', [
                'alt' => get_string('editroom', 'mod_facetoface'),
                'title' => get_string('editroom', 'mod_facetoface')
            ])
        );
        if ($extrafields->hidden && $report->src->get_embeddedurl()) {
            $urlparams = array_merge(array_merge($params, ['action' => 'show', 'sesskey' => sesskey()]), $report->src->get_urlparams());
            $output[] = $OUTPUT->action_icon(
                new moodle_url($report->src->get_embeddedurl(), $urlparams),
                new flex_icon('show', [
                    'alt' => get_string('roomshow', 'mod_facetoface'),
                    'title' => get_string('roomshow', 'mod_facetoface')
                ])
            );
        } else if ($report->src->get_embeddedurl()) {
            $urlparams = array_merge(array_merge($params, ['action' => 'hide', 'sesskey' => sesskey()]), $report->src->get_urlparams());
            $output[] = $OUTPUT->action_icon(
                new moodle_url($report->src->get_embeddedurl(), $urlparams),
                new flex_icon('hide', [
                    'alt' => get_string('roomhide', 'mod_facetoface'),
                    'title' => get_string('roomhide', 'mod_facetoface')
                ])
            );

        }
        if ($extrafields->cntdates) {
            $output[] = $OUTPUT->flex_icon('trash', [
                'classes' => 'ft-state-disabled',
                'alt' => get_string('currentlyassigned', 'mod_facetoface'),
                'title' => get_string('currentlyassigned', 'mod_facetoface')
            ]);
        } else {
            $output[] = $OUTPUT->action_icon(
                new moodle_url('/mod/facetoface/room/manage.php', array_merge($params, ['action' => 'delete'])),
                new flex_icon('trash', ['alt' => get_string('delete'), 'title' => get_string('delete')])
            );
        }
        return implode('', $output);
    }

    /**
     * Is this column graphable?
     *
     * @param \rb_column $column
     * @param \rb_column_option $option
     * @param \reportbuilder $report
     * @return bool
     */
    public static function is_graphable(\rb_column $column, \rb_column_option $option, \reportbuilder $report) {
        return false;
    }
}
