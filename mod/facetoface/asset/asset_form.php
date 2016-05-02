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
 * @author Valerii Kuznetsov <valerii.kuznetsov@totaralms.com>
 * @package mod_facetoface
 */

defined('MOODLE_INTERNAL') || die();

require_once("{$CFG->dirroot}/lib/formslib.php");

class mod_facetoface_asset_form extends moodleform {

    /**
     * Definition of the room form
     */
    public function definition() {
        global $DB;

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'custom');
        $mform->setType('custom', PARAM_INT);

        $mform->addElement('text', 'name', get_string('assetname', 'facetoface'), array('size' => '45'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('checkbox', 'allowconflicts', get_string('allowconflicts', 'mod_facetoface'));
        $mform->addHelpButton('allowconflicts', 'assettype', 'facetoface');

        $mform->addElement('editor', 'description_editor', get_string('assetdescription', 'facetoface'), $this->_customdata['editorattributes'], $this->_customdata['editoroptions']);

        if ($this->_customdata['asset']) {
            $asset = $this->_customdata['asset'];
        } else {
            $asset = new stdClass();
        }

        customfield_definition($mform, $asset, 'facetofaceasset', 0, 'facetoface_asset');

        if (!empty($asset->custom) || !isset($asset->custom) && !empty($this->_customdata['custom'])) {
            $mform->addElement('checkbox', 'notcustom', get_string('publishreuse', 'mod_facetoface'));
        }

        if ($asset->id) {
            $mform->addElement('header', 'versions', get_string('versioncontrol', 'mod_facetoface'));

            $created = new stdClass();
            $created->user = get_string('unknownuser');
            if (!empty($asset->usercreated)) {
                $created->user = html_writer::link(
                    new moodle_url('/user/view.php', array('id' => $asset->usercreated)),
                    fullname($DB->get_record('user', array('id' => $asset->usercreated)))
                );
            }
            $created->time = empty($asset->timecreated) ? '' : userdate($asset->timecreated);
            $mform->addElement(
                    'static',
                    'versioncreated',
                    get_string('created', 'mod_facetoface'),
                    get_string('timestampbyuser', 'mod_facetoface', $created)
            );

            if (!empty($asset->timemodified)) {
                $modified = new stdClass();
                $modified->user = get_string('unknownuser');
                if (!empty($asset->usermodified)) {
                    $modified->user = html_writer::link(
                        new moodle_url('/user/view.php', array('id' => $asset->usermodified)),
                        fullname($DB->get_record('user', array('id' => $asset->usermodified)))
                    );
                }
                $modified->time = userdate($asset->timemodified);
                $mform->addElement(
                        'static',
                        'versionmodified',
                        get_string('modified'),
                        get_string('timestampbyuser', 'mod_facetoface', $modified)
                );
            }
        }
        if (empty($this->_customdata['noactionbuttons'])) {
            $label = null;
            if (!$asset->id) {
                $label = get_string('addasset', 'facetoface');
            }
            $this->add_action_buttons(true, $label);
        }
    }
}
