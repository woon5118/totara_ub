<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 * @package mod_facetoface
 */

/**
 * Structure step to restore one facetoface activity
 */
class restore_facetoface_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('facetoface', '/activity/facetoface');
        $paths[] = new restore_path_element('facetoface_notification', '/activity/facetoface/notifications/notification');
        $paths[] = new restore_path_element('facetoface_session', '/activity/facetoface/sessions/session');
        $paths[] = new restore_path_element('facetoface_sessions_date', '/activity/facetoface/sessions/session/sessions_dates/sessions_date');
        $paths[] = new restore_path_element('facetoface_room', '/activity/facetoface/sessions/session/sessions_dates/sessions_date/room');
        $paths[] = new restore_path_element('facetoface_asset', '/activity/facetoface/sessions/session/sessions_dates/sessions_date/assets/asset');
        $paths[] = new restore_path_element('facetoface_session_custom_fields', '/activity/facetoface/sessions/session/custom_fields/custom_field');
        if ($userinfo) {
            $paths[] = new restore_path_element('facetoface_signup', '/activity/facetoface/sessions/session/signups/signup');
            $paths[] = new restore_path_element('facetoface_signups_status', '/activity/facetoface/sessions/session/signups/signup/signups_status/signup_status');
            $paths[] = new restore_path_element('facetoface_signup_custom_fields', '/activity/facetoface/sessions/session/signups/signup/signups_status/signup_status/signup_fields/signup_field');
            $paths[] = new restore_path_element('facetoface_cancellation_custom_fields', '/activity/facetoface/sessions/session/signups/signup/signups_status/signup_status/cancellation_fields/cancellation_field');
            $paths[] = new restore_path_element('facetoface_session_roles', '/activity/facetoface/sessions/session/session_roles/session_role');
            $paths[] = new restore_path_element('facetoface_interest', '/activity/facetoface/interests/interest');
        }

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_facetoface($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // insert the facetoface record
        $newitemid = $DB->insert_record('facetoface', $data);
        $this->apply_activity_instance($newitemid);
    }


    protected function process_facetoface_notification($data) {
        global $DB, $USER;

        $data = (object)$data;
        $oldid = $data->id;

        $data->facetofaceid = $this->get_new_parentid('facetoface');
        $data->course = $this->get_courseid();

        $data->timemodified = $this->apply_date_offset($data->timemodified);
        $data->usermodified = isset($USER->id) ? $USER->id : get_admin()->id;

        // Insert the notification record.
        $newitemid = $DB->insert_record('facetoface_notification', $data);
    }


    protected function process_facetoface_session($data) {
        global $DB, $USER;

        $data = (object)$data;
        $oldid = $data->id;

        $data->facetoface = $this->get_new_parentid('facetoface');

        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);
        $data->usermodified = isset($USER->id) ? $USER->id : get_admin()->id;

        // insert the entry record
        $newitemid = $DB->insert_record('facetoface_sessions', $data);
        $this->set_mapping('facetoface_session', $oldid, $newitemid, true); // childs and files by itemname
    }

    protected function process_facetoface_signup($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->sessionid = $this->get_new_parentid('facetoface_session');
        $data->userid = $this->get_mappingid('user', $data->userid);
        if (!empty($data->bookedby)) {
            $data->bookedby = $this->get_mappingid('user', $data->bookedby);
        }

        // insert the entry record
        $newitemid = $DB->insert_record('facetoface_signups', $data);
        $this->set_mapping('facetoface_signup', $oldid, $newitemid, true); // childs and files by itemname
    }


    protected function process_facetoface_signups_status($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->signupid = $this->get_new_parentid('facetoface_signup');

        $data->timecreated = $this->apply_date_offset($data->timecreated);

        // insert the entry record
        $newitemid = $DB->insert_record('facetoface_signups_status', $data);
        $this->set_mapping('facetoface_signups_status', $oldid, $newitemid, true); // Childs and files by itemname.
    }


    protected function process_facetoface_session_roles($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->sessionid = $this->get_new_parentid('facetoface_session');
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->roleid = $this->get_mappingid('role', $data->roleid);

        // insert the entry record
        $newitemid = $DB->insert_record('facetoface_session_roles', $data);
    }


    protected function process_facetoface_session_custom_fields($data) {
        global $DB;

        $data = (object)$data;

        if ($data->field_data) {
            if (!$field = $DB->get_record('facetoface_session_info_field', array('shortname' => $data->field_name))) {
                debugging("Custom field [{$data->field_name}] in face to face session cannot be restored " .
                        "because it doesn't exist in the target database");
            } else if ($field->datatype != $data->field_type) {
                debugging("Custom field [{$data->field_name}] in face to face session cannot be restored " .
                        "because there is a data type mismatch - " .
                        "target type = [{$field->datatype}] <> restore type = [{$data->field_type}]");
            } else {
                if ($customfield = $DB->get_record('facetoface_session_info_data',
                        array('fieldid' => $field->id, 'facetofacesessionid' => $this->get_new_parentid('facetoface_session')))) {
                    $customfield->data = $data->field_data;
                    $DB->update_record('facetoface_session_info_data', $customfield);
                    // Insert params if exist.
                    if (!empty($data->paramdatavalue)) {
                        $param = new stdClass();
                        $param->dataid = $customfield->id;
                        $param->value  = $data->paramdatavalue;
                        $params = array('dataid' => $customfield->id, 'value' => $data->paramdatavalue);
                        if (!$DB->get_record('facetoface_session_info_data_param', $params)) {
                            $DB->insert_record('facetoface_session_info_data_param', $param);
                        }
                    }
                } else {
                    $customfield = new stdClass();
                    $customfield->facetofacesessionid = $this->get_new_parentid('facetoface_session');
                    $customfield->fieldid = $field->id;
                    $customfield->data    = $data->field_data;
                    $dataid = $DB->insert_record('facetoface_session_info_data', $customfield);

                    // Insert params if exist.
                    if (!empty($data->paramdatavalue)) {
                        $param = new stdClass();
                        $param->dataid = $dataid;
                        $param->value  = $data->paramdatavalue;
                        $DB->insert_record('facetoface_session_info_data_param', $param);
                    }
                }
            }
        }
    }

    protected function process_facetoface_signup_custom_fields($data) {
        global $DB;

        $data = (object)$data;

        if ($data->field_data) {
            if (!$field = $DB->get_record('facetoface_signup_info_field', array('shortname' => $data->field_name))) {
                debugging("Custom field [{$data->field_name}] in face to face signup cannot be restored " .
                    "because it doesn't exist in the target database");
            } else if ($field->datatype != $data->field_type) {
                debugging("Custom field [{$data->field_name}] in face to face signup cannot be restored " .
                    "because there is a data type mismatch - " .
                    "target type = [{$field->datatype}] <> restore type = [{$data->field_type}]");
            } else {
                if ($customfield = $DB->get_record('facetoface_signup_info_data',
                    array('fieldid' => $field->id, 'facetofacesignupid' => $this->get_new_parentid('facetoface_signups_status')))) {
                    $customfield->data = $data->field_data;
                    $DB->update_record('facetoface_signup_info_data', $customfield);
                    // Insert params if exist.
                    if (!empty($data->paramdatavalue)) {
                        $param = new stdClass();
                        $param->dataid = $customfield->id;
                        $param->value  = $data->paramdatavalue;
                        $params = array('dataid' => $customfield->id, 'value' => $data->paramdatavalue);
                        if (!$DB->get_record('facetoface_signup_info_data_param', $params)) {
                            $DB->insert_record('facetoface_signup_info_data_param', $param);
                        }
                    }
                } else {
                    $customfield = new stdClass();
                    $customfield->facetofacesignupid = $this->get_new_parentid('facetoface_signups_status');
                    $customfield->fieldid = $field->id;
                    $customfield->data    = $data->field_data;
                    $dataid = $DB->insert_record('facetoface_signup_info_data', $customfield);

                    // Insert params if exist.
                    if (!empty($data->paramdatavalue)) {
                        $param = new stdClass();
                        $param->dataid = $dataid;
                        $param->value  = $data->paramdatavalue;
                        $DB->insert_record('facetoface_signup_info_data_param', $param);
                    }
                }
            }
        }
    }

    protected function process_facetoface_cancellation_custom_fields($data) {
        global $DB;

        $data = (object)$data;

        if ($data->field_data) {
            if (!$field = $DB->get_record('facetoface_cancellation_info_field', array('shortname' => $data->field_name))) {
                debugging("Custom field [{$data->field_name}] in face to face cancellation cannot be restored " .
                    "because it doesn't exist in the target database");
            } else if ($field->datatype != $data->field_type) {
                debugging("Custom field [{$data->field_name}] in face to face cancellation cannot be restored " .
                    "because there is a data type mismatch - " .
                    "target type = [{$field->datatype}] <> restore type = [{$data->field_type}]");
            } else {
                if ($customfield = $DB->get_record('facetoface_cancellation_info_data',
                    array('fieldid' => $field->id, 'facetofacecancellationid' => $this->get_new_parentid('facetoface_signups_status')))) {
                    $customfield->data = $data->field_data;
                    $DB->update_record('facetoface_cancellation_info_data', $customfield);
                    // Insert params if exist.
                    if (!empty($data->paramdatavalue)) {
                        $param = new stdClass();
                        $param->dataid = $customfield->id;
                        $param->value  = $data->paramdatavalue;
                        $params = array('dataid' => $customfield->id, 'value' => $data->paramdatavalue);
                        if (!$DB->get_record('facetoface_cancellation_info_data_param', $params)) {
                            $DB->insert_record('facetoface_cancellation_info_data_param', $param);
                        }
                    }
                } else {
                    $customfield = new stdClass();
                    $customfield->facetofacecancellationid = $this->get_new_parentid('facetoface_signups_status');
                    $customfield->fieldid = $field->id;
                    $customfield->data    = $data->field_data;
                    $dataid = $DB->insert_record('facetoface_cancellation_info_data', $customfield);

                    // Insert params if exist.
                    if (!empty($data->paramdatavalue)) {
                        $param = new stdClass();
                        $param->dataid = $dataid;
                        $param->value  = $data->paramdatavalue;
                        $DB->insert_record('facetoface_cancellation_info_data_param', $param);
                    }
                }
            }
        }
    }


    protected function process_facetoface_session_field($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // insert the entry record
        $newitemid = $DB->insert_record('facetoface_session_info_field', $data);
    }


    protected function process_facetoface_sessions_date($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->sessionid = $this->get_new_parentid('facetoface_session');

        $data->roomid = 0;
        $data->timestart = $this->apply_date_offset($data->timestart);
        $data->timefinish = $this->apply_date_offset($data->timefinish);

        // insert the entry record
        $newitemid = $DB->insert_record('facetoface_sessions_dates', $data);

        $this->set_mapping('facetoface_sessions_date', $oldid, $newitemid);
    }

    protected function process_facetoface_room($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $sessionsdateid = $this->get_new_parentid('facetoface_sessions_date');
        $sessiondate = $DB->get_record('facetoface_sessions_dates', array('id' => $sessionsdateid), '*', MUST_EXIST);

        if ((int)$data->custom == 1) {
            // Custom rooms are easy, we just add a new one as exact copy.
            $newid = $this->create_facetoface_room($data);
            $DB->set_field('facetoface_sessions_dates', 'roomid', $newid, array('id' => $sessionsdateid));
            return;
        }

        if (!$this->get_task()->is_samesite()) {
            // We cannot restore site rooms from inside courses, sorry!
            $this->log('shared seminar room from other site cannot be restored', backup::LOG_WARNING);
            return;
        }

        // Ok, we are on the same site, let's see if the room still exists and use it if there are no conflicts.
        $room = $DB->get_record('facetoface_room', array('id' => $oldid));
        if (!$room) {
            $this->log('original seminar room not found', backup::LOG_WARNING);
            return;
        }
        if ($room->custom != 0) {
            // This should not ever happen, somebody hacked DB or backup file.
            return;
        }
        if ($room->allowconflicts == 0) {
            $available = facetoface_get_available_rooms(array(array($sessiondate->timestart, $sessiondate->timefinish)), 'id');
            if (!isset($available[$room->id])) {
                $this->log('seminar room collision detected, room not added', backup::LOG_WARNING);
                return;
            }
        }
        // It should be fine to add the room to the session.
        $DB->set_field('facetoface_sessions_dates', 'roomid', $room->id, array('id' => $sessionsdateid));
    }

    /**
     * Create a new room.
     *
     * @param stdClass $room
     * @return int new room id
     */
    private function create_facetoface_room(stdClass $room) {
        global $DB, $USER;
        $now = time();
        unset($room->id);
        $room->timecreated = $now;
        $room->timemodified = $now;
        $room->usercreated = $USER->id; // This is a NEW room, do not use old user id!
        $room->usermodified = null;
        return $DB->insert_record('facetoface_room', $room);
    }

    protected function process_facetoface_asset($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $sessionsdateid = $this->get_new_parentid('facetoface_sessions_date');
        $sessiondate = $DB->get_record('facetoface_sessions_dates', array('id' => $sessionsdateid), '*', MUST_EXIST);

        if ((int)$data->custom == 1) {
            // Custom assets are easy, we just add a new one as exact copy.
            $newid = $this->create_facetoface_asset($data);
            $DB->insert_record('facetoface_asset_dates', (object)array('assetid' => $newid, 'sessionsdateid' => $sessionsdateid));
            return;
        }

        if (!$this->get_task()->is_samesite()) {
            // We cannot restore site assets from inside courses, sorry!
            $this->log('shared seminar asset from other site cannot be restored', backup::LOG_WARNING);
            return;
        }

        // Ok, we are on the same site, let's see if the asset still exists and use it if there are no conflicts.
        $asset = $DB->get_record('facetoface_asset', array('id' => $oldid));
        if (!$asset) {
            $this->log('original seminar asset not found', backup::LOG_WARNING);
            return;
        }
        if ($asset->custom != 0) {
            // This should not ever happen, somebody hacked DB or backup file.
            return;
        }
        if ($asset->allowconflicts == 0) {
            $available = facetoface_get_available_assets(array(array($sessiondate->timestart, $sessiondate->timefinish)), 'id');
            if (!isset($available[$asset->id])) {
                $this->log('seminar asset collision detected, asset not added', backup::LOG_WARNING);
                return;
            }
        }
        // It should be fine to add the asset to the session.
        $DB->insert_record('facetoface_asset_dates', (object)array('assetid' => $asset->id, 'sessionsdateid' => $sessionsdateid));
    }

    /**
     * Create a new asset.
     *
     * @param stdClass $asset
     * @return int new asset id
     */
    private function create_facetoface_asset(stdClass $asset) {
        global $DB, $USER;
        $now = time();
        unset($asset->id);
        $asset->timecreated = $now;
        $asset->timemodified = $now;
        $asset->usercreated = $USER->id; // This is a NEW asset, do not use old user id!
        $asset->usermodified = null;
        return $DB->insert_record('facetoface_asset', $asset);
    }

    protected function process_facetoface_interest($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->facetoface = $this->get_new_parentid('facetoface');
        $data->userid = $this->get_mappingid('user', $data->userid);

        // Insert the entry record.
        $newitemid = $DB->insert_record('facetoface_interest', $data);
    }

    protected function after_execute() {
        // Face-to-face doesn't have any related files
        //
        // Add facetoface related files, no need to match by itemname (just internally handled context)
    }
}
