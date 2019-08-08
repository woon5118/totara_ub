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

namespace mod_facetoface\output;

defined('MOODLE_INTERNAL') || die();

use html_writer;
use moodle_url;
use stdClass;
use \core\output\template;
use \mod_facetoface\facilitator;
use \mod_facetoface\facilitator_user;
use \mod_facetoface\customfield_area\facetofacefacilitator as facilitatorcustomfield;

/**
 * Class facilitator_details
 */
class facilitator_details extends template {

    /**
     * Instantiate facilitator details
     * @param facilitator_user $facilitator
     * @return facilitator_details
     */
    public static function create(facilitator_user $facilitator): facilitator_details {

        $namevalue = $facilitator->get_name() . $facilitator->get_fullname_link();

        $data = [
            'namestr' => get_string('facilitatorname', 'mod_facetoface'),
            'namevalue' => $namevalue,
            'customfields' => self::set_customfields($facilitator),
            'allowconflictstr' => get_string('allowfacilitatorconflicts', 'mod_facetoface'),
            'allowconflictvalue' => self::get_allowconflicts($facilitator),
            'isdescription' => empty($facilitator->get_description()),
            'descriptionstr' => get_string('descriptionlabel', 'mod_facetoface'),
            'descriptionvalue' => self::set_description($facilitator),
            'createdstr' => get_string('created', 'mod_facetoface'),
            'createdvalue' => self::set_created($facilitator),
            'modifiedstr' => get_string('modified'),
            'modifiedvalue' => self::set_modified($facilitator),
        ];

        return new static($data);
    }

    /**
     * Load facilitator custom fields data
     * @param facilitator $facilitator
     * @return array
     */
    private static function set_customfields(facilitator_user $facilitator): array {
        $filearea = facilitatorcustomfield::get_area_name();
        $tblprefix = facilitatorcustomfield::get_prefix();
        $options = array('prefix' => $filearea, 'extended' => true);
        $cfdata = (object)[
            'id' => $facilitator->get_id(),
            'fullname' => $facilitator->get_name(),
            'custom' => $facilitator->get_custom(),
        ];
        $fields = customfield_get_data($cfdata, $tblprefix, $filearea, true, $options);
        $customfields = [];
        if (!empty($fields)) {
            foreach ($fields as $field => $value) {
                $customfields[] = [
                    'field' => $field,
                    'value' => $value,
                ];
            }
        }
        return $customfields;
    }

    /**
     * load facilitator description.
     * @param facilitator $facilitator
     * @return string
     */
    private static function set_description(facilitator_user $facilitator): string {
        $context = facilitatorcustomfield::get_context();
        $component = facilitatorcustomfield::get_component();
        $filearea = facilitatorcustomfield::get_area_name();

        $descriptionhtml = '';
        if (!empty($facilitator->get_description())) {
            $descriptionhtml = file_rewrite_pluginfile_urls(
                $facilitator->get_description(),
                'pluginfile.php',
                $context->id,
                $component,
                $filearea,
                $facilitator->get_id()
            );
            $descriptionhtml = format_text($descriptionhtml, FORMAT_HTML);
        }
        return $descriptionhtml;
    }

    /**
     * Set created by user.
     * @param facilitator $facilitator
     * @return string
     */
    private static function set_created(facilitator_user $facilitator): string {
        // Created.
        $created = new stdClass();
        $created->user = get_string('unknownuser');
        $usercreated = $facilitator->get_usercreated();
        if (!empty($usercreated)) {
            $user = \mod_facetoface\facetoface_user::get_user($usercreated);
            $created->user = html_writer::link(
                new moodle_url('/user/view.php', array('id' => $usercreated)),
                fullname($user)
            );
        }
        $created->time = userdate($facilitator->get_timecreated());
        return get_string('timestampbyuser', 'mod_facetoface', $created);
    }

    /**
     * Set modified by user.
     * @param facilitator $facilitator
     * @return string
     */
    private static function set_modified(facilitator_user $facilitator): string {
        // Modified.
        $modified = new stdClass();
        $modified->user = get_string('unknownuser');
        $usermodified = $facilitator->get_usermodified();
        if (!empty($usermodified)) {
            $user = \mod_facetoface\facetoface_user::get_user($usermodified);
            $modified->user = html_writer::link(
                new moodle_url('/user/view.php', array('id' => $usermodified)),
                fullname($user)
            );
        }
        $modified->time = userdate($facilitator->get_timemodified());
        return get_string('timestampbyuser', 'mod_facetoface', $modified);
    }

    /**
     * Return yes/no string if facilitator allows conflicts
     * @param facilitator $facilitator
     * @return string
     */
    private static function get_allowconflicts(facilitator_user $facilitator): string {
        return $facilitator->get_allowconflicts() ? get_string('yes') : get_string('no');
    }
}