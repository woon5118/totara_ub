<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2017 onwards Totara Learning Solutions LTD
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
 * @author Rob Tyler <rob.tyler@totaralearning.com>
 * @package totara_reportbuilder
 */

namespace totara_reportbuilder\rb\display;

/**
 * Class describing column display formatting.
 *
 * @author Rob Tyler <rob.tyler@totaralearning.com>
 * @package totara_reportbuilder
 */
class user_actions extends base {

    public static function display($userid, $format, \stdClass $row, \rb_column $column, \reportbuilder $report) {
        global $CFG, $OUTPUT, $PAGE, $USER;

        if ($format !== 'html') {
            // Only applicable to the HTML format.
            return '';
        }

        $extrafields = self::get_extrafields_row($row, $column);

        if (isguestuser($userid)) {
            // No actions for the guest user.
            return '';
        }

        $buttons = array();
        // We don't want to support Mnet in this report, bu we're going to check
        // if the user is one so we don't display any actions that might cause problems.
        $mnetuser = is_mnet_remote_user((object) array('id' => $userid, 'mnethostid' => $extrafields->mnethostid));

        // Define the text used for alt and title parameters on the icons.
        $text = array (
            'edit' => get_string('editrecord', 'totara_reportbuilder', $extrafields->fullname),
            'confirm' => get_string('confirmrecord', 'totara_reportbuilder', $extrafields->fullname),
            'delete' => get_string('deleterecord', 'totara_reportbuilder', $extrafields->fullname),
            'suspend' => get_string('suspendrecord', 'totara_reportbuilder', $extrafields->fullname),
            'undelete' => get_string('undeleterecord', 'totara_reportbuilder', $extrafields->fullname),
            'unsuspend' => get_string('unsuspendrecord', 'totara_reportbuilder', $extrafields->fullname),
            'unlock' => get_string('unlockrecord', 'totara_reportbuilder', $extrafields->fullname),
        );

        // Define a URL that can be used to return the user to the page they started from.
        $page = optional_param('spage', '0', PARAM_INT);
        $reportparams = array(
            'spage'=> $page
        );
        $returnurl = (string) new \moodle_url($PAGE->url, $reportparams);

        // If we're on the admin/user.php page we don't need to add a return url (to return
        // the user to the report) as the actions will be completed on the report page.
        if ($PAGE->url->get_path() == '/admin/user.php') {
            $actionurl = (string) new \moodle_url('/admin/user.php', $reportparams + ['sesskey' => sesskey()]);
        } else {
            $actionurl = (string) new \moodle_url('/admin/user.php', array('returnurl' => $returnurl, 'sesskey' => sesskey()));
        }

        // If it's an Mnet user no actions are supported.
        if ($mnetuser) {

            $buttons[] = \html_writer::span(
                get_string('mnetuser', 'totara_reportbuilder'),
                'label label-info',
                array('title' => get_string('mnetnotsupported', 'totara_reportbuilder'))
            );

        } else if ($extrafields->deleted && $extrafields->can_delete) {

            // If the record has been marked as deleted, don't show any edit, suspend etc icons
            $preg_emailhash = '/^[0-9a-f]{32}$/i';

            $buttons[] = \html_writer::link(
                new \moodle_url($actionurl, array('undelete' => $userid, 'returnurl' => $returnurl)),
                $OUTPUT->flex_icon('recycle', array('alt' => $text['undelete'])),
                array('title' => $text['undelete'])
            );

            if ($CFG->authdeleteusers !== 'partial' && !preg_match($preg_emailhash, $extrafields->email)) {
                $buttons[] = \html_writer::link(
                    new \moodle_url($actionurl, array('delete' => $userid, 'returnurl' => $returnurl)),
                    $OUTPUT->flex_icon('delete', array('alt' => $text['delete'])),
                    array('title' => $text['delete'])
                );
            }

        } else {
            // Here we want the icons to appear in a logical, useful order, and for their
            // positions to be as consistent as possible to improve usability. We want
            // the order to be: edit, suspend, delete; as these three are the three main
            // action icons, followed by: unlock and confirm.

            $issiteadmin = is_siteadmin($userid);
            $iscurrentuser = ($userid != $USER->id);

            // Add edit action icon but prevent editing of admins by non-admin users.
            if ($extrafields->can_update && (is_siteadmin($USER) || !$issiteadmin)) {
                $buttons[] = \html_writer::link(
                    new \moodle_url('/user/editadvanced.php', array('id'=>$userid, 'course'=>SITEID, 'returnurl' => $returnurl)),
                    $OUTPUT->flex_icon('settings', array('alt' => $text['edit'])),
                    array('title' => $text['edit'])
                );
            }

            // Add suspend and unsuspend icons.
            if ($extrafields->can_update && $iscurrentuser && !$issiteadmin) {
                if ($extrafields->suspended) {
                    $buttons[] = \html_writer::link(
                        new \moodle_url($actionurl, array('unsuspend' => $userid, 'returnurl' => $returnurl)),
                        $OUTPUT->flex_icon('show', array('alt' => $text['unsuspend'])),
                        array('title' => $text['unsuspend'])
                    );
                } else {
                    $buttons[] = \html_writer::link(
                        new \moodle_url($actionurl, array('suspend' => $userid, 'returnurl' => $returnurl)),
                        $OUTPUT->flex_icon('hide', array('alt' => $text['suspend'])),
                        array('title' => $text['suspend'])
                    );
                }
            }

            // Add delete action icon.
            if ($extrafields->can_delete && $iscurrentuser && !$issiteadmin) {
                $buttons[] = \html_writer::link(
                    new \moodle_url($actionurl, array('delete'=> $userid, 'returnurl' => $returnurl)),
                    $OUTPUT->flex_icon('delete', array('alt' => $text['delete'])),
                    array('title' => $text['delete'])
                );
            }

            // Add an unlock icon for when the user has locked their account.
            if ($extrafields->can_update && login_is_lockedout((object)array('id' => $userid, 'mnethostid' => $extrafields->mnethostid))) {
                $buttons[] = \html_writer::link(
                    new \moodle_url($actionurl, array('unlock' => $userid)),
                    $OUTPUT->flex_icon('unlock', array('alt' => $text['unlock'])),
                    array('title' => $text['unlock'])
                );
            }

            // If a user is self-registered allow the user to confirm the user.
            if ($extrafields->can_update && empty($extrafields->confirmed)) {
                $buttons[] = \html_writer::link(
                    new \moodle_url($actionurl, array('confirmuser' => $userid)),
                    $OUTPUT->flex_icon('check', array('alt' => $text['confirm'])),
                    array('title' => $text['confirm'])
                );
            }
        }

        if ($buttons) {
            return implode ('', $buttons);
        } else {
            return '';
        }
    }

    public static function is_graphable(\rb_column $column, \rb_column_option $option, \reportbuilder $report) {
        return false;
    }
}
