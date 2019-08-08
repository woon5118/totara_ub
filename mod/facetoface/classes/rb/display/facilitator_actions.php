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
* @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
* @package mod_facetoface
*/

namespace mod_facetoface\rb\display;

defined('MOODLE_INTERNAL') || die();

use totara_reportbuilder\rb\display\base;
use mod_facetoface\facilitator_user;

/**
 * Display class intended for assets/rooms/facilitators actions
 */
class facilitator_actions extends base {

    /**
     * Handles the display
     * @param string $value
     * @param string $format
     * @param \stdClass $row
     * @param \rb_column $column
     * @param \reportbuilder $report
     * @return string
     */
    public static function display($value, $format, \stdClass $row, \rb_column $column, \reportbuilder $report): string {
        global $OUTPUT;

        $isexport = ($format !== 'html');
        $extrafields = self::get_extrafields_row($row, $column);

        if ($isexport) {
            return null;
        }
        if ((int)$extrafields->custom > 0) {
            return '';
        }

        $useractive = true;
        if (isset($extrafields->userid) && (int)$extrafields->userid > 0) {
            $useractive = facilitator_user::is_userid_active((int)$extrafields->userid);
        }

        $output = array();
        $output[] = $OUTPUT->action_icon(
            new \moodle_url('/mod/facetoface/reports/facilitators.php', array('facilitatorid' => $value)),
            new \pix_icon('t/calendar', get_string('details', 'mod_facetoface'))
        );

        $output[] = $OUTPUT->action_icon(
            new \moodle_url('/mod/facetoface/facilitator/edit.php', array('id' => $value)),
            new \pix_icon('t/edit', get_string('edit'))
        );

        if ($extrafields->hidden && $report->src->get_embeddedurl()) {
            if ($useractive) {
                $params = array_merge($report->src->get_urlparams(), array('action' => 'show', 'id' => $value, 'sesskey' => sesskey()));
                $output[] = $OUTPUT->action_icon(
                    new \moodle_url($report->src->get_embeddedurl(), $params),
                    new \pix_icon('t/show', get_string('show'))
                );
            } else {
                $output[] = $OUTPUT->flex_icon('show', ['classes' => 'ft-size-100 ft-state-disabled', 'alt' => get_string('facilitatoruserdeleted', 'mod_facetoface')]);
            }
        } else if ($report->src->get_embeddedurl()) {
            $params = array_merge($report->src->get_urlparams(), array('action' => 'hide', 'id' => $value, 'sesskey' => sesskey()));
            $output[] = $OUTPUT->action_icon(
                new \moodle_url($report->src->get_embeddedurl(), $params),
                new \pix_icon('t/hide', get_string('hide'))
            );
        }
        if ($extrafields->cntdates) {
            $output[] = $OUTPUT->pix_icon('t/delete_gray', get_string('currentlyassigned', 'mod_facetoface'), 'moodle', array('class' => 'disabled iconsmall'));
        } else {
            if ($report->src->get_embeddedurl()) {
                $params = array_merge($report->src->get_urlparams(), array('action' => 'delete', 'id' => $value, 'sesskey' => sesskey()));
                $output[] = $OUTPUT->action_icon(
                    new \moodle_url($report->src->get_embeddedurl(), $params),
                    new \pix_icon('t/delete', get_string('delete'))
                );
            }
        }

        return implode('', $output);
    }

    /**
     * Is this column graphable?
     * @param \rb_column $column
     * @param \rb_column_option $option
     * @param \reportbuilder $report
     * @return bool
     */
    public static function is_graphable(\rb_column $column, \rb_column_option $option, \reportbuilder $report): bool {
        return false;
    }
}