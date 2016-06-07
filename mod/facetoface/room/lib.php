<?php
/*
* This file is part of Totara LMS
*
* Copyright (C) 2016 onwards Totara Learning Solutions LTD
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
* @package totara_facetoface
*/

defined('MOODLE_INTERNAL') || die();

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/totara/customfield/fieldlib.php');
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/lib/filelib.php');
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/totara/core/totara.php');
require_once($CFG->dirroot . '/mod/facetoface/room/room_form.php');

/**
 * Creates a new room, or updates an existing one depending on whether $data->id is set
 * @stdClass $data
 * @dataobject $todb
 */
function create_or_update_room($data, $todb) {
    global $DB, $TEXTAREA_OPTIONS;
    $iscreation = ($data->id == 0);
    if ($iscreation) {
        $data->id = $DB->insert_record('facetoface_room', $todb);
        $todb->id = $data->id;
    } else {
        $todb->id = $data->id;
        $DB->update_record('facetoface_room', $todb);
    }

    customfield_save_data($data, 'facetofaceroom', 'facetoface_room');

    // Update description.
    $descriptiondata = file_postupdate_standard_editor(
      $data,
      'description',
      $TEXTAREA_OPTIONS,
      $TEXTAREA_OPTIONS['context'],
      'mod_facetoface',
      'room',
      $data->id
    );

    $DB->set_field('facetoface_room', 'description', $descriptiondata->description, array('id' => $data->id));
}

/**
 * Delete room and related information
 *
 * @param int $id
 */
function room_delete($id) {
    global $DB;

    $sqldelparam = "
        DELETE FROM {facetoface_room_info_data_param}
        WHERE dataid IN
            (SELECT id FROM {facetoface_room_info_data} WHERE facetofaceroomid = :id)
        ";
    $DB->execute($sqldelparam, array('id' => $id));
    $DB->delete_records('facetoface_room_info_data', array('facetofaceroomid' => $id));
    $DB->delete_records('facetoface_room', array('id' => $id));
}

/**
 * Process room edit form and call related handlers
 * @param int $roomid
 * @param callable $successhandler function($id) where $id is roomid
 * @param callable $cancelhandler
 * @param array $customdata additional form customdata
 * @return \mod_facetoface_room_form
 */
function process_room_form($roomid, callable $successhandler, callable $cancelhandler = null, array $customdata = array()) {
    global $DB, $TEXTAREA_OPTIONS, $USER;

    if (empty($customdata['userid'])) {
        $userid = $USER->id;
    } else {
        $userid = $customdata['userid'];
    }

    if ($roomid == 0) {
        $room = new stdClass();
        $room->id = 0;
        $room->description = '';
        $room->status = 1;
        $room->custom=0;
        if (!empty($customdata['custom']) || !empty($customdata['customforce'])) {
            // Pre set custom for new rooms and when it's enforced.
            $room->custom=1;
        }
    } else {
        $room = $DB->get_record('facetoface_room', array('id' => $roomid), '*', MUST_EXIST);
        $room->roomcapacity = $room->capacity;
        customfield_load_data($room, 'facetofaceroom', 'facetoface_room');
    }

    $room->descriptionformat = FORMAT_HTML;
    $room = file_prepare_standard_editor($room, 'description', $TEXTAREA_OPTIONS, $TEXTAREA_OPTIONS['context'], 'mod_facetoface', 'room', $room->id);

    $customdata['room'] = $room;
    $customdata['editoroptions'] = $TEXTAREA_OPTIONS;
    if (empty($room->id)) {
        // This kills the auto-save for when creating new rooms. We do this as the same description
        // will keep coming up if creating several rooms in a row.
        $customdata['editorattributes'] = array('id' => rand());
    } else {
        $customdata['editorattributes'] = array('id' => $room->id);
    }

    $form = new mod_facetoface_room_form(null, $customdata, 'post', '', array('class' => 'dialog-nobind'));
    $form->set_data($room);

    if ($form->is_cancelled()) {
        if (is_callable($cancelhandler)) {
            $cancelhandler();
        }
    }

    if ($data = $form->get_data()) {
        $todb = new stdClass();
        $todb->name = $data->name;
        $todb->capacity = $data->roomcapacity;
        $todb->allowconflicts = empty($data->allowconflicts) ? 0 : 1;
        $todb->custom = 0;
        if (!empty($customdata['custom']) && empty($data->notcustom)) {
            $todb->custom = 1;
        }
        if (!empty($customdata['customforce'])) {
            // Force custom to stay as is.
            $todb->custom = $room->custom;
        }
        $new = false;
        if (empty($data->id)) {
            $todb->timecreated = time();
            $todb->usercreated = $userid;
        } else {
            $todb->timemodified = time();
            $todb->usermodified = $userid;
            $new = true;
        }

        /**
         * Need to combine the location data here since the preprocess isn't called enough before the save and fails.
         * But first check to see if the location custom field is present.
         * $_customlocationfieldname added in @see customfield_location::edit_field_add()
         */
        if (property_exists($form->_form, '_customlocationfieldname')) {
            customfield_define_location::prepare_form_location_data_for_db($data, $form->_form->_customlocationfieldname);
        }

        create_or_update_room($data, $todb);
        $successhandler($todb);
    }
    return $form;
}

/**
 * Return true if user has capability to edit room.
 * @param int $userid
 * @param int $roomid
 * @param int $facetofaceid
 * @return bool
 */
function can_user_edit_room($userid, $roomid, $facetofaceid) {
    global $DB;
    $system_context = context_system::instance();
    if (has_capability('totara/core:modconfig', $system_context, $userid)) {
        return true;
    }
    // Check if user have 'mod/facetoface:editevents' capability to edit events in current facetoface.
    if (!$facetoface = $DB->get_record('facetoface', array('id' => $facetofaceid))) {
        print_error('error:incorrectfacetofaceid', 'facetoface');
    }
    if (!$course = $DB->get_record('course', array('id' => $facetoface->course))) {
        print_error('error:coursemisconfigured', 'facetoface');
    }
    if (!$cm = get_coursemodule_from_instance('facetoface', $facetoface->id, $course->id)) {
        print_error('error:incorrectcoursemodule', 'facetoface');
    }

    $context = context_module::instance($cm->id);
    if (has_capability('mod/facetoface:editevents', $context, $userid)) {
        if (!$roomid) {
            // New room creation.
            return true;
        }

        $roomdata = $DB->get_record('facetoface_room', array('id' => $roomid));
        // Only custom rooms can be edited.
        if ($roomdata->custom) {
            // Determine event it is assigned to and check that event is the same as current.
            $roomsess = $DB->get_records_sql("
                SELECT fs.facetoface FROM {facetoface_sessions_dates} fsd
                JOIN {facetoface_sessions} fs ON (fsd.sessionid = fs.id)
                WHERE fsd.roomid = :roomid
            ", array('roomid' => $roomid));

            // Unassigned (orphaned) custom rooms can be edited
            // (they will became normal custom room exclusively owned by event after save).
            if (empty($roomsess)) {
                return true;
            }

            // If room assigned to current facetoface, user can edit it.
            foreach ($roomsess as $roomses) {
                if ($roomses->facetoface == $facetofaceid) {
                    return true;
                }
            }
        }
    }
    return false;
}