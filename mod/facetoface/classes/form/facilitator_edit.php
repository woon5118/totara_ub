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

namespace mod_facetoface\form;

use html_writer;
use mod_facetoface\facilitator;
use mod_facetoface\facilitator_user;
use mod_facetoface\customfield_area\facetofacefacilitator as facilitatorcustomfield;
use mod_facetoface\seminar_event;

defined('MOODLE_INTERNAL') || die();

class facilitator_edit extends \moodleform {
    /**
     * Definition of the facilitator form
     */
    public function definition() {
        global $OUTPUT, $TEXTAREA_OPTIONS;

        $mform = $this->_form;

        /** @var \mod_facetoface\facilitator_user $facilitator */
        $facilitator = $this->_customdata['facilitator'];
        /** @var \mod_facetoface\seminar $seminar */
        $seminar = empty($this->_customdata['seminar']) ? null : $this->_customdata['seminar'];
        /** @var \mod_facetoface\seminar_event $seminarevent */
        $seminarevent = empty($this->_customdata['seminarevent']) ? null : $this->_customdata['seminarevent'];

        $context = empty($this->_customdata['context']) ? \context_system::instance() : $this->_customdata['context'];
        $capability = has_capability('mod/facetoface:managesitewidefacilitators', $context);
        $adhoc = empty($this->_customdata['context']) ? true : $this->_customdata['adhoc'];

        $prefix = $filearea = facilitatorcustomfield::get_area_name();
        $tblprefix = facilitatorcustomfield::get_prefix();
        $component = facilitatorcustomfield::get_component();
        $syscontext = facilitatorcustomfield::get_context();

        $mform->addElement('hidden', 'id', $facilitator->get_id());
        $mform->setType('id', PARAM_INT);

        if (!empty($seminar)) {
            $mform->addElement('hidden', 'f', $seminar->get_id());
            $mform->setType('f', PARAM_INT);
        }
        if (!empty($seminarevent)) {
            $mform->addElement('hidden', 's', $seminarevent->get_id());
            $mform->setType('s', PARAM_INT);
        }

        $mform->addElement('text', 'name', get_string('facilitatorname', 'facetoface'), ['size' => '45']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        if (!$adhoc && !defined('AJAX_SCRIPT') || !AJAX_SCRIPT) {
            $class = '';
            if ((bool)$facilitator->get_userid()) {
                $class = 'nonempty';
            }
            $span = html_writer::span($facilitator->get_fullname(), $class, ['id' => 'facilitatortitle']);
            $username = [];
            $username[] =& $mform->createElement('hidden', 'userid');
            $typeoptions = [
                '0' => get_string('facilitatorinternal', 'mod_facetoface'),
                '1' => get_string('facilitatorexternal', 'mod_facetoface'),
            ];
            $username[] =& $mform->createElement('select', 'facilitatortype', null, $typeoptions);
            $username[] =& $mform->createElement('button', 'facilitatorselector', get_string('selectuserwithdot', 'mod_facetoface'),
                ['id' => 'show-facilitator-dialog']);
            $username[] =& $mform->createElement('static', 'facilitatortitle', null, $span);
            $mform->addGroup($username, 'labeltype', get_string('facilitatortype', 'mod_facetoface'), null, false);
            $mform->setType('userid', PARAM_INT);
            $mform->addHelpButton('labeltype', 'facilitatortype', 'mod_facetoface');
        } else {
            $mform->addElement('hidden', 'userid', 0);
            $mform->setType('userid', PARAM_INT);
        }

        $mform->addElement('advcheckbox', 'allowconflicts', get_string('allowfacilitatorconflicts', 'mod_facetoface'));
        $mform->addHelpButton('allowconflicts', 'allowfacilitatorconflicts', 'mod_facetoface');
        $mform->setType('allowconflicts', PARAM_INT);

        if ($capability and !empty($seminar) and $facilitator->get_custom()) {
            $mform->addElement('advcheckbox', 'notcustom', get_string('addfacilitatortositewidelist', 'mod_facetoface'));
            $mform->addHelpButton('notcustom', 'addfacilitatortositewidelist', 'mod_facetoface');
        } else {
            $mform->addElement('hidden', 'notcustom');
        }
        $mform->setType('notcustom', PARAM_INT);

        $mform->addElement('editor', 'description_editor', get_string('descriptionlabel', 'mod_facetoface'), null, $TEXTAREA_OPTIONS);

        customfield_definition($mform, (object)['id' => $facilitator->get_id()], $prefix, 0, $tblprefix);

        if ($facilitator->exists()) {
            $mform->addElement('header', 'versions', get_string('versioncontrol', 'mod_facetoface'));

            $created = new \stdClass();
            $created->user = get_string('unknownuser');
            $usercreated = $facilitator->get_usercreated();
            if (!empty($usercreated)) {
                $user = \mod_facetoface\facetoface_user::get_user($usercreated);
                $created->user = \html_writer::link(
                    new \moodle_url('/user/view.php', array('id' => $facilitator->get_usercreated())),
                    fullname($user)
                );
            }
            $created->time = empty($facilitator->get_timecreated()) ? '' : userdate($facilitator->get_timecreated());
            $mform->addElement(
                'static',
                'versioncreated',
                get_string('created', 'mod_facetoface'),
                get_string('timestampbyuser', 'mod_facetoface', $created)
            );

            if (!empty($facilitator->get_timemodified()) && $facilitator->get_timemodified() != $facilitator->get_timecreated()) {
                $modified = new \stdClass();
                $modified->user = get_string('unknownuser');
                $usermodified = $facilitator->get_usermodified();
                if (!empty($usermodified)) {
                    $user = \mod_facetoface\facetoface_user::get_user($usermodified);
                    $modified->user = \html_writer::link(
                        new \moodle_url('/user/view.php', array('id' => $usermodified)),
                        fullname($user)
                    );
                }
                $modified->time = empty($facilitator->get_timemodified()) ? '' : userdate($facilitator->get_timemodified());
                $mform->addElement(
                    'static',
                    'versionmodified',
                    get_string('modified'),
                    get_string('timestampbyuser', 'mod_facetoface', $modified)
                );
            }
        }

        if (empty($seminar)) {
            $label = null;
            if (!$facilitator->exists()) {
                $label = get_string('addfacilitator', 'mod_facetoface');
            }
            $this->add_action_buttons(true, $label);
        }

        $formdata = (object)[
            'id' => $facilitator->get_id(),
            'userid' => $facilitator->get_userid(),
            'facilitatortype' => (bool)$facilitator->get_userid() ? '0' : '1',
            'name' => $facilitator->get_name(),
            'allowconflicts' => $facilitator->get_allowconflicts(),
            'description_editor' => ['text' => $facilitator->get_description()],
            'notcustom' => $facilitator->get_custom() ? 0 : 1,
            'description' => $facilitator->get_description(),
            'descriptionformat' => FORMAT_HTML,
        ];
        customfield_load_data($formdata, $prefix, $tblprefix);
        $formdata = file_prepare_standard_editor(
            $formdata,
            'description',
            $TEXTAREA_OPTIONS,
            $syscontext,
            $component,
            $filearea,
            $facilitator->get_id()
        );
        $this->set_data($formdata);
    }

    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);

        /** @var \mod_facetoface\facilitator $facilitator */
        $facilitator = $this->_customdata['facilitator'];

        if ($facilitator->exists() && $facilitator->get_allowconflicts() && $data['allowconflicts'] == 0) {
            // Make sure there are no existing conflicts before we switch the setting!
            if ($facilitator->has_conflicts()) {
                $errors['allowconflicts'] = get_string('error:facilitatorconflicts', 'mod_facetoface');
            }
        }

        return $errors;
    }

    public function save($data) {
        global $DB;
        // New one, no problem.
        if ((int)$data->id == 0 || (int)$data->userid == 0) {
            return \mod_facetoface\facilitator_helper::save($data);
        }
        // Update one, lets load old record.
        $oldfacilitator = new facilitator($data->id);
        if ($oldfacilitator->get_userid() == (int)$data->userid) {
            // Nothing change for user record, still same user.
            // Lets check if user active or not.
            if (facilitator_user::is_userid_active((int)$data->userid)) {
                // All good, just lets save the record.
                return \mod_facetoface\facilitator_helper::save($data);
            }
        }
        // Right, new user, we must load all session records
        // It's probably doesn't matter if the user is in the trouble or not
        // We will update all upcoming sessions and skip the past ones for history records
        // Lets check a new user: for case he/she is active.
        if (!facilitator_user::is_userid_active((int)$data->userid)) {
            // This should not happened, but somehow did.
            // \core\notification::warning(get_string('facilitatoruserwarning', 'mod_facetoface', $facilitator->get_fullname()));
            return false;
        }

        $transaction = $DB->start_delegated_transaction();

        $data->id = 0;
        $newfacilitator = \mod_facetoface\facilitator_helper::save($data);

        /**
         * TODO:
         * NOTE: keep this for case if we want to return to facilitator calendar
        // Find and update calendar entries for upcoming events.
        $sql = "SELECT fsd.timestart, fs.id, fs.facetoface
                  FROM {facetoface_sessions} fs
                  JOIN {facetoface_sessions_dates} fsd ON fsd.sessionid = fs.id
                  JOIN {facetoface_facilitator_dates} ffd ON ffd.sessionsdateid = fsd.id
                 WHERE ffd.facilitatorid = :oldfacilitatorid
                   AND fsd.timestart > :timenow";
        $params = ['facilitatorid' => $oldfacilitator->get_id(), 'timenow' => time()];
        $records = $DB->get_records_sql($sql, $params);
        $updatesql = "UPDATE {event}
                       SET userid = :newfacilitatorid
                     WHERE userid = :userid AND uuid = :uuid
                       AND instance = :instance AND modulename = :modulename
                       AND eventtype = :eventtype AND timestart = :timestart";
        $eventtype = 'facetoface' . facilitator::EVENTTYPE;
        foreach ($records as $rec) {
            $params = [
                'newfacilitatorid' => $newfacilitator->get_userid(),
                'userid' => $oldfacilitator->get_userid(),
                'uuid' => $rec->id,
                'instance' => $rec->facetoface,
                'modulename' => 'facetoface',
                'eventtype' => $eventtype,
                'timestart' => $rec->timestart
            ];
            $DB->execute($updatesql, $params);
        }
        */
        $sql = "SELECT DISTINCT ffd.*
                  FROM {facetoface_facilitator_dates} ffd
                  JOIN {facetoface_sessions_dates} fsd ON fsd.id = ffd.sessionsdateid
                  JOIN {facetoface_sessions} fs ON fs.id = fsd.sessionid
                 WHERE ffd.facilitatorid = :facilitatorid
                   AND fsd.timestart > :timenow";
        $params = ['facilitatorid' => $oldfacilitator->get_id(), 'timenow' => time()];

        $sessiondates = $DB->get_records_sql($sql, $params);
        foreach ($sessiondates as $sessiondate) {
            $sql = "UPDATE {facetoface_facilitator_dates}
                       SET facilitatorid = :newfacilitatorid
                     WHERE facilitatorid = :oldfacilitatorid
                       AND id = :sessiondateid";
            $params = [
                'newfacilitatorid' => $newfacilitator->get_id(),
                'oldfacilitatorid' => $oldfacilitator->get_id(),
                'sessiondateid' => $sessiondate->id,
            ];
            $DB->execute($sql, $params);
        }

        $transaction->allow_commit();

        return $newfacilitator;
    }
}