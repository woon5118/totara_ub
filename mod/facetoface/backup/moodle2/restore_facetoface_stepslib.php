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
        $paths[] = new restore_path_element('facetoface_sessions_dates', '/activity/facetoface/sessions/session/sessions_dates/sessions_date');
        $paths[] = new restore_path_element('facetoface_asset_dates', '/activity/facetoface/sessions/session/sessions_dates/asset_dates/asset_date');
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

    private function create_facetoface_room($data) {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/totara/customfield/field/location/define.class.php');
        $now = time();

        $customroom = new stdClass();
        $customroom->name = $data->room_name;
        $customroom->description = $data->room_description;
        $customroom->allowconflicts = $data->room_allowconflicts ? 1 : 0;
        $customroom->capacity = $data->room_capacity;
        $customroom->custom = (int)$data->room_custom;
        $customroom->hidden = (int)$data->room_hidden;
        $customroom->timecreated = isset($data->timecreated) ? $data->timecreated : $now;
        $customroom->timemodified = isset($data->timemodified) ? $data->timemodified : $now;
        $roomid = $DB->insert_record('facetoface_room', $customroom);

        $customroom->id = $roomid;
        // If the backup contains the custom fields for location and building.
        $customroom->customfield_building = $data->room_custom_building;
        $customroom->customfield_location = $data->room_custom_location;
        // But perhaps the backup is an old-style backup.
        if (isset($data->building)) {
            $customroom->customfield_building = $data->building;
        }
        if (isset($data->address)) {
            customfield_define_location::prepare_form_location_data_for_db($data, 'customfield_location');
            $customroom->customfield_location = $data->address;
        }
        customfield_save_data($customroom, 'facetofaceroom', 'facetoface_room');


        return $roomid;
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


    protected function process_facetoface_sessions_dates($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->sessionid = $this->get_new_parentid('facetoface_session');

        $data->timestart = $this->apply_date_offset($data->timestart);
        $data->timefinish = $this->apply_date_offset($data->timefinish);

        if (!isset($data->room_name)) {
            $data->room_name = '';
        }

        // Check if the session has any predefined or custom room.
        if ((int)$data->roomid > 0 && $data->room_name != '') {
            // If it is a custom room, create a new record.
            if (isset($data->room_custom) && (int)$data->room_custom == 1) {
                $data->roomid = $this->create_facetoface_room($data);
            } else {
                // Check if a predefined room exists.
                // First, standardise the building and address fields.
                $building = '';
                if (isset($data->room_building)) {
                    $building = $data->room_building;
                } else if (isset($data->room_custom_building)) {
                    $building = $data->room_custom_building;
                }

                $location = '';
                if (isset($data->room_address)) {
                    $location = $data->room_address;
                } else if (isset($data->room_custom_location)) {
                    $location = $data->room_custom_location;
                }

                $rooms = $DB->get_records_sql(
                    'SELECT r.id, r.name, r.custom,
                        (SELECT rid.data
                            FROM {facetoface_room_info_data} rid
                            LEFT JOIN {facetoface_room_info_field} rif ON rid.fieldid = rif.id
                            WHERE rif.shortname = \'location\' AND rid.facetofaceroomid = r.id) AS location,
                        (SELECT rid.data
                            FROM {facetoface_room_info_data} rid
                            LEFT JOIN {facetoface_room_info_field} rif ON rid.fieldid = rif.id
                            WHERE rif.shortname = \'building\' AND rid.facetofaceroomid = r.id) AS building
                        FROM {facetoface_room} r
                        WHERE name = :name AND custom = :custom
                        GROUP BY r.id, r.name, r.custom
                        HAVING (
                        (SELECT rid.data
                            FROM {facetoface_room_info_data} rid
                            LEFT JOIN {facetoface_room_info_field} rif ON rid.fieldid = rif.id
                            WHERE rif.shortname = \'building\' AND rid.facetofaceroomid = r.id) = :building
                        AND
                        (SELECT rid.data
                            FROM {facetoface_room_info_data} rid
                            LEFT JOIN {facetoface_room_info_field} rif ON rid.fieldid = rif.id
                            WHERE rif.shortname = \'location\' AND rid.facetofaceroomid = r.id) LIKE :location)',
                    array(
                        'name' => $data->room_name,
                        'building' => $building,
                        'location' => '%' . $location . '%',
                        'custom' => 0
                    )
                );
                if (count($rooms) > 0) {
                    if (count($rooms) > 1) {
                        debugging("Room [{$data->room_name}, {$building}, {$location}] matches more ".
                            "than one predefined room and we can't identify which - arbitrarily selecting one of them");
                    }
                    $data->roomid = reset($rooms)->id;
                } else {
                    // Create a new predefined room record.
                    debugging("Room [{$data->room_name}, {$building}, {$location}] ".
                        "in face to face session does not exist - creating as predefined room");
                    $data->roomid = $this->create_facetoface_room($data);
                }
            }
        } else {
            // F2F session has no room.
            $data->roomid = 0;
        }

        // insert the entry record
        $newitemid = $DB->insert_record('facetoface_sessions_dates', $data);
    }

    protected function process_facetoface_asset_dates($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->sessionsdateid = $this->get_new_parentid('facetoface_sessions_date');

        if ((int)$data->asset_custom == 1) {
            $data->assetid = $this->create_facetoface_asset($data);
        } else {
            // Search for existing asset.
            $assets = $DB->get_records('facetoface_asset', array('name' => $data->asset_name, 'custom' => $data->asset_custom,
                'allowconflicts' => $data->asset_allowconflicts));

            if (count($assets) > 0) {
                if (count($assets) > 1) {
                    debugging("Asset {$data->asset_name}, matches more than one asset - arbitrarily selecting one of them");
                }
                $data->assetid = reset($assets)->id;
            } else {
                $data->assetid = $this->create_facetoface_asset($data);
            }
        }
        // Insert the entry record.
        $DB->insert_record('facetoface_asset_dates', $data);
    }

    /**
     * Create custom asset
     * @param array $data
     * @return int asset id
     */
    private function create_facetoface_asset($data) {
        global $DB;

        $now = time();

        $customasset = new stdClass();
        $customasset->name = $data->asset_name;
        $customasset->custom = (int)$data->asset_custom;
        $customasset->allowconflicts = $data->asset_allowconflicts ? 1 : 0;
        $customasset->timecreated = isset($data->timecreated) ? $data->timecreated : $now;
        $customasset->timemodified = isset($data->timemodified) ? $data->timemodified : $now;
        $assetid = $DB->insert_record('facetoface_asset', $customasset);

        return $assetid;
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
