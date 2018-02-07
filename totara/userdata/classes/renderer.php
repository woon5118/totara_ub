<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Petr Skoda <petr.skoda@totaralearning.com>
 * @package totara_userdata
 */

defined('MOODLE_INTERNAL') || die();

class totara_userdata_renderer extends plugin_renderer_base {
    /**
     * UI widget for user identification purposes.
     *
     * @param stdClass $user
     * @param bool $userinfopage true when printed on user information page
     * @return string html fragment
     */
    public function user_id_card(\stdClass $user, $userinfopage = false) {
        $context = \context_user::instance($user->id, IGNORE_MISSING);
        if (!$context) {
            $context = \context_system::instance();
        }

        $html = '';
        $html .= '<dl class="dl-horizontal">';
        if ($user->deleted) {
            $html .= '<dt>' . get_string('userid', 'totara_reportbuilder') . '</dt>';
            $html .= '<dd>' . $user->id . '</dd>';
        }
        $fullname = fullname($user);
        if (!$userinfopage) {
            if (has_capability('totara/userdata:viewinfo', $context)) {
                $url = new \moodle_url('/totara/userdata/user_info.php', array('id' => $user->id));
                $fullname = html_writer::link($url, $fullname);
            }
        } else {
            if (!$user->deleted) {
                if (has_capability('moodle/user:viewdetails', $context)) {
                    $url = new \moodle_url('/user/profile.php', array('id' => $user->id));
                    $fullname = html_writer::link($url, $fullname);
                }
            }
        }
        $html .= '<dt>' . get_string('fullnameuser') . '</dt>';
        $html .= '<dd>' . $fullname . '</dd>'; // link for not deleted
        $html .= '<dt>' . get_string('userstatus', 'totara_reportbuilder') . '</dt>';
        $html .= '<dd>';
        if ($user->deleted) {
            $html .= get_string('deleteduser', 'totara_reportbuilder');
        } else if ($user->suspended) {
            $html .= get_string('suspendeduser', 'totara_reportbuilder');
        } else {
            $html .= get_string('activeuser', 'totara_reportbuilder');
        }
        $html .= '</dd>';
        $html .= '<dt>' . get_string('idnumber') . '</dt>';
        $html .= '<dd>' . (trim($user->idnumber) === '' ? '&nbsp;' : s($user->idnumber)) . '</dd>';
        $html .= '<dt>' . get_string('email') . '</dt>';
        $html .= '<dd>' . (trim($user->email) === '' ? '&nbsp;' : s($user->email)) . '</dd>';
        $html .= '<dt>' . get_string('username') . '</dt>';
        $html .= '<dd>' . (trim($user->username) === '' ? '&nbsp;' : s($user->username)) . '</dd>';
        $html .= '</dl>';

        return $html;
    }
}



