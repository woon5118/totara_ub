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
 * @author David Curry <david.curry@totaralms.com>
 * @package modules
 * @subpackage facetoface
 */

require_once "$CFG->dirroot/lib/formslib.php";
require_once "$CFG->dirroot/mod/facetoface/lib.php";

class mod_facetoface_signup_form extends moodleform {
    function definition() {
        global $CFG;

        $mform =& $this->_form;
        $showdiscountcode = $this->_customdata['showdiscountcode'];
        $enableattendeenote = $this->_customdata['enableattendeenote'];
        $approvaltype = $this->_customdata['approvaltype'];
        $approvaladmins = $this->_customdata['approvaladmins'];
        $managerid = $this->_customdata['managerid'];
        $manager = core_user::get_user($managerid);

        $mform->addElement('hidden', 's', $this->_customdata['s']);
        $mform->setType('s', PARAM_INT);
        $mform->addElement('hidden', 'backtoallsessions', $this->_customdata['backtoallsessions']);
        $mform->setType('backtoallsessions', PARAM_INT);

        $mform->addElement('hidden', 'managerid');
        $mform->setType('managerid', PARAM_INT);
        $mform->setDefault('managerid', $managerid);

        // Do nothing if approval is set to none or role.
        if ($approvaltype == APPROVAL_SELF) {
            global $PAGE;

            $url = new moodle_url('/mod/facetoface/signup_tsandcs.php', array('s' => $this->_customdata['s']));
            $tandcurl = html_writer::link($url, get_string('approvalterms', 'mod_facetoface'), array("class"=>"tsandcs ajax-action"));

            $PAGE->requires->strings_for_js(array('approvalterms', 'close'), 'mod_facetoface');
            $PAGE->requires->yui_module('moodle-mod_facetoface-signupform', 'M.mod_facetoface.signupform.init');

            $mform->addElement('checkbox', 'authorisation', get_string('selfauthorisation', 'mod_facetoface'),
                               get_string('selfauthorisationdesc', 'mod_facetoface', $tandcurl));
            $mform->addRule('authorisation', get_string('required'), 'required', null, 'client', true);
            $mform->addHelpButton('authorisation', 'selfauthorisation', 'facetoface');
        } else if ($approvaltype == APPROVAL_MANAGER) {
            $mform->addElement('hidden', 'managerid');
            $mform->setType('managerid', PARAM_INT);
            $mform->setDefault('managerid', $managerid);

            $select = get_config(null, 'facetoface_managerselect');
            if ($select) {
                $manager_title = fullname($manager);
                $manager_class = strlen($manager_title) ? 'nonempty' : '';
                $mform->addElement(
                    'static',
                    'managerselector',
                    get_string('manager', 'totara_hierarchy'),
                    html_writer::tag('span', format_string($manager_title), array('class' => $manager_class, 'id' => 'managertitle'))
                    . html_writer::empty_tag('input', array('type' => 'button', 'value' => get_string('choosemanager', 'totara_hierarchy'), 'id' => 'show-manager-dialog'))
                );

                $mform->addHelpButton('managerselector', 'choosemanager', 'totara_hierarchy');
            } else {
                // Display the average manager approval string.
                $mform->addElement('static', 'managername', get_string('managername', 'mod_facetoface'), fullname($manager));
                $mform->setType('managername', PARAM_TEXT);
                $mform->addHelpButton('managername', 'managername', 'facetoface');
            }
        } else if ($approvaltype == APPROVAL_ADMIN) {
            $select = get_config(null, 'facetoface_managerselect');
            if ($select) {
                $manager_title = fullname($manager);
                $manager_class = strlen($manager_title) ? 'nonempty' : '';
                $mform->addElement(
                    'static',
                    'managerselector',
                    get_string('manager', 'totara_hierarchy'),
                    html_writer::tag('span', format_string($manager_title), array('class' => $manager_class, 'id' => 'managertitle'))
                    . html_writer::empty_tag('input', array('type' => 'button', 'value' => get_string('choosemanager', 'totara_hierarchy'), 'id' => 'show-manager-dialog'))
                );

                $mform->addHelpButton('managerselector', 'choosemanager', 'totara_hierarchy');
            } else {
                // Display the average manager&admin approval string.
                $mform->addElement('static', 'managername', get_string('managername', 'mod_facetoface'), fullname($manager));
                $mform->setType('managername', PARAM_TEXT);
                $mform->addHelpButton('managername', 'managername', 'facetoface');
            }

            // Display a list of approval administrators.
            $approvallist = html_writer::start_tag('ul', array('class' => 'approvallist'));

            // System approvers.
            $sysapps = get_users_from_config(get_config(null, 'facetoface_adminapprovers'), 'mod/facetoface:approveanyrequest');
            foreach ($sysapps as $approver) {
                if (!empty($approver)) {
                    $approvallist .= html_writer::tag('li', fullname($approver));
                    $approvers = get_users_from_config(get_config(null, 'facetoface_adminapprovers'), 'mod/facetoface:approveanyrequest');
                }
            }

            // Activity approvers.
            $actapps = explode(',', $approvaladmins);
            foreach ($actapps as $approverid) {
                if (!empty($approverid)) {
                     $approver = core_user::get_user($approverid);
                     $approvallist .= html_writer::tag('li', fullname($approver));
                }
            }
            $approvallist .= html_writer::end_tag('ul');

            $mform->addElement('static', 'approvalusers', get_string('approvalusers', 'mod_facetoface'), $approvallist);
            $mform->setType('approvalusers', PARAM_TEXT);
            $mform->addHelpButton('approvalusers', 'approvalusers', 'facetoface');
        }

        $showdiscountcode = true;
        if ($showdiscountcode) {
            $mform->addElement('text', 'discountcode', get_string('discountcode', 'facetoface'), 'size="6"');
            $mform->addHelpButton('discountcode', 'discountcodelearner', 'facetoface');
        } else {
            $mform->addElement('hidden', 'discountcode', '');
        }
        $mform->setType('discountcode', PARAM_TEXT);

        if ($enableattendeenote) {
            $signup = new stdClass();
            $signup->id = 0;
            customfield_definition($mform, $signup, 'facetofacesignup', 0, 'facetoface_signup');
            $mform->removeElement('customfields');
        }

        if (empty($CFG->facetoface_notificationdisable)) {
            $options = array(MDL_F2F_BOTH => get_string('notificationboth', 'facetoface'),
                             MDL_F2F_TEXT => get_string('notificationemail', 'facetoface'),
                             MDL_F2F_NONE => get_string('notificationnone', 'facetoface'),
                             );
            $mform->addElement('select', 'notificationtype', get_string('notificationtype', 'facetoface'), $options);
            $mform->addHelpButton('notificationtype', 'notificationtype', 'facetoface');
            $mform->addRule('notificationtype', null, 'required', null, 'client');
            $mform->setDefault('notificationtype', MDL_F2F_BOTH);
        } else {
            $mform->addElement('hidden', 'notificationtype', MDL_F2F_NONE);
        }
        $mform->setType('notificationtype', PARAM_INT);

        self::add_position_selection_formelem($mform, $this->_customdata['f2fid'], $this->_customdata['s']);

        if ($this->_customdata['waitlisteveryone']) {
            $mform->addElement(
                'static',
                'youwillbeaddedtothewaitinglist',
                get_string('youwillbeaddedtothewaitinglist', 'facetoface'),
                ''
            );
        }

        if ($approvaltype == APPROVAL_NONE) {
            $signupstr = 'signup';
        } else if ($approvaltype == APPROVAL_SELF) {
            $signupstr = 'signupandaccept';
        } else {
            $signupstr = 'signupandrequest';
        }

        $strsignup = $this->_customdata['signupbywaitlist'] ? 'joinwaitlist' : 'signup';
        $this->add_action_buttons(true, get_string($signupstr, 'facetoface'));
    }

    public static function add_position_selection_formelem ($mform, $f2fid, $sessionid) {
        global $DB;

        $selectpositiononsignupglobal = get_config(null, 'facetoface_selectpositiononsignupglobal');
        if (empty($selectpositiononsignupglobal)) {
            return;
        }

        $facetoface = $DB->get_record('facetoface', array('id' => $f2fid));
        $session = $DB->get_record('facetoface_sessions', array('id' => $sessionid));

        if (empty($facetoface->selectpositiononsignup)) {
            return;
        }

        $controlname = 'selectedposition_'.$f2fid;

        $managerrequired = facetoface_manager_needed($facetoface);
        $applicablepositions = get_position_assignments($managerrequired);

        if (count($applicablepositions) > 1) {
            $posselectelement = $mform->addElement('select', $controlname, get_string('selectposition', 'mod_facetoface'));
            $mform->addHelpButton($controlname, 'selectedposition', 'mod_facetoface');
            $mform->setType($controlname, PARAM_INT);

            foreach ($applicablepositions as $posassignment) {
                $label = position::position_label($posassignment);
                $posselectelement->addOption($label, $posassignment->id);
            }
        }
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $approvaltype = $this->_customdata['approvaltype'];

        // Manager validation if approval type requires it.
        if ($approvaltype == APPROVAL_MANAGER || $approvaltype == APPROVAL_ADMIN) {
            $manager = isset($data['managerid']) ? $data['managerid'] : null;
            if (empty($manager)) {
                $select = get_config(null, 'facetoface_managerselect');
                if ($select) {
                    $errors['managerselector'] = get_string('error:missingselectedmanager', 'mod_facetoface');
                } else {
                    $errors['managername'] = get_string('error:missingrequiredmanager', 'mod_facetoface');
                }
            }
        }

        return $errors;
    }
}
