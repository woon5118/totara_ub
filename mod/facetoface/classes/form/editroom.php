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
 * @author Eugene Venter <eugene@catalyst.net.nz>
 * @package mod_facetoface
 */

namespace mod_facetoface\form;

defined('MOODLE_INTERNAL') || die();

use html_writer;
use mod_facetoface\user_helper;

global $CFG;
require_once($CFG->dirroot . '/lib/formslib.php');
require_once($CFG->dirroot . '/totara/customfield/field/location/define.class.php');

class editroom extends \moodleform {

    /**
     * Definition of the room form
     */
    public function definition() {
        global $TEXTAREA_OPTIONS;

        $mform = $this->_form;

        /** @var \mod_facetoface\room $room */
        $room = $this->_customdata['room'];
        /** @var \mod_facetoface\seminar $seminar */
        $seminar = empty($this->_customdata['seminar']) ? null : $this->_customdata['seminar'];
        /** @var \mod_facetoface\seminar_event $seminarevent */
        $seminarevent = empty($this->_customdata['seminarevent']) ? null : $this->_customdata['seminarevent'];
        /** @var string */
        $backurl = $this->_customdata['backurl'] ?? '';

        $roomnamelength = \mod_facetoface\room::ROOM_NAME_LENGTH;

        $mform->addElement('hidden', 'id', $room->get_id());
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'b', $backurl);
        $mform->setType('b', PARAM_URL);

        if (!empty($seminar)) {
            $mform->addElement('hidden', 'f', $seminar->get_id());
            $mform->setType('f', PARAM_INT);
        }

        if (!empty($seminarevent)) {
            $mform->addElement('hidden', 's', $seminarevent->get_id());
            $mform->setType('s', PARAM_INT);
        }

        // Room name.
        $mform->addElement('text', 'name', get_string('roomnameedit', 'mod_facetoface'), array('size' => '45'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('roomnameedittoolong', 'mod_facetoface', $roomnamelength), 'maxlength', $roomnamelength);

        // This form is loaded as ajax into page that has "capacity" so give it different name to avoid conflicts.
        $mform->addElement('text', 'roomcapacity', get_string('roomcapacity', 'mod_facetoface'));
        $mform->setType('roomcapacity', PARAM_INT);
        $mform->addRule('roomcapacity', null, 'required', null, 'client');
        $mform->addRule('roomcapacity', null, 'numeric', null, 'client');

        // Room link.
        $mform->addElement('text', 'url', get_string('roomurl', 'mod_facetoface'), ['maxlength' => '1024', 'size' => '45']);
        $mform->setType('url', PARAM_URL);

        // 'Allow room booking conflicts' checkbox
        $mform->addElement('advcheckbox', 'allowconflicts', get_string('allowroomconflicts', 'mod_facetoface'));
        $mform->addHelpButton('allowconflicts', 'allowroomconflicts', 'mod_facetoface');
        $mform->setType('allowconflicts', PARAM_INT);

        // We don't need autosave here
        $editoropts = $TEXTAREA_OPTIONS;
        $editoropts['autosave'] = false;
        // Room Description.
        $mform->addElement('editor', 'description_editor', get_string('roomdescriptionedit', 'mod_facetoface'), null, $editoropts);

        // Custom fields: Building and Location.
        customfield_definition($mform, (object)['id' => $room->get_id()], 'facetofaceroom', 0, 'facetoface_room');

        // Version control.
        if (!empty($room) && $room->exists()) {
            $mform->addElement('header', 'versions', get_string('versioncontrol', 'mod_facetoface'));

            $mform->addElement(
                'static',
                'versioncreated',
                get_string('created', 'mod_facetoface'),
                user_helper::get_timestamp_and_profile($room->get_timecreated(), $room->get_usercreated())
            );

            if (!empty($room->get_timemodified()) and $room->get_timemodified() != $room->get_timecreated()) {
                $mform->addElement(
                    'static',
                    'versionmodified',
                    get_string('modified'),
                    user_helper::get_timestamp_and_profile($room->get_timemodified(), $room->get_usermodified())
                );
            }
        }

        // Add to sitewide list.
        $capability = has_capability('mod/facetoface:managesitewiderooms', \context_system::instance());
        if ($capability and !empty($seminar) and $room->get_custom()) {
            $mform->addElement('advcheckbox', 'notcustom', get_string('addtositewidelist', 'mod_facetoface'));
        } else {
            $mform->addElement('hidden', 'notcustom');
        }
        $mform->setType('notcustom', PARAM_INT);
        $mform->closeHeaderBefore('notcustom');

        // Buttons.
        if (empty($seminar)) {
            $label = null;
            if (!$room->get_id()) {
                $label = get_string('addroom', 'facetoface');
            }
            $this->add_action_buttons(true, $label);
        }
        // Set default/existing data.
        $formdata = (object)[
            'id' => $room->get_id(),
            'name' => $room->get_name(),
            'roomcapacity' => $room->get_capacity(),
            'allowconflicts' => $room->get_allowconflicts(),
            'url' => $room->get_url(),
            'description_editor' => ['text' => $room->get_description()],
            'notcustom' => $room->get_custom() ? 0 : 1,
            'description' => $room->get_description(),
            'descriptionformat' => FORMAT_HTML,
        ];
        customfield_load_data($formdata, 'facetofaceroom', 'facetoface_room');
        $formdata = file_prepare_standard_editor(
            $formdata,
            'description',
            $editoropts,
            $editoropts['context'],
            'mod_facetoface',
            'room',
            $room->get_id()
        );
        $this->set_data($formdata);
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        /** @var \mod_facetoface\room $room */
        $room = $this->_customdata['room'];

        if ((int)$data['roomcapacity'] <= 0) {
            // Client side JS validation does not work much in the hacky dialog forms - do it on server side!
            $errors['roomcapacity'] = get_string('required');
        }

        if ($room->get_id() and $room->get_allowconflicts() and $data['allowconflicts'] == 0) {
            // Make sure there are no existing conflicts before we switch the setting!
            if ($room->has_conflicts()) {
                $errors['allowconflicts'] = get_string('error:roomconflicts', 'mod_facetoface');
            }
        }

        if (!empty($data->url)) {
            if (!filter_var($data->url, FILTER_VALIDATE_URL)) {
                $errors['url'] = get_string('error:urlformat', 'mod_facetoface');
            }
        }

        return $errors;
    }
}
