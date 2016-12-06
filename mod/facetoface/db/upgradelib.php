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
 * @author  Valerii Kuznetsov <valerii.kuznetsov@totaralearning.com>
 * @package mod_facetoface
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Update 9.0 manager prefix strings to new with added "Below is the message that was sent to learner:" suffix.
 * If affects only unchanged original 9.0 strings in facetoface notifications and their templates
 */
function facetoface_upgradelib_managerprefix_clarification() {
    global $DB;

    $upgradestrings = [
        "setting:defaultconfirmationinstrmngrdefault" => "setting:defaultconfirmationinstrmngrdefault_v92",
        "setting:defaultcancellationinstrmngrdefault" => "setting:defaultcancellationinstrmngrdefault_v92",
        "setting:defaultreminderinstrmngrdefault" => "setting:defaultreminderinstrmngrdefault_v92",
        "setting:defaultrequestinstrmngrdefault" => "setting:defaultrequestinstrmngrdefault_v92",
        "setting:defaultrolerequestinstrmngrdefault" => "setting:defaultrolerequestinstrmngrdefault_v92",
        "setting:defaultadminrequestinstrmngrdefault" => "setting:defaultadminrequestinstrmngrdefault_v92",
        "setting:defaultdeclineinstrmngrdefault" => "setting:defaultdeclineinstrmngrdefault_v92",
        "setting:defaultregistrationexpiredinstrmngr" => "setting:defaultregistrationexpiredinstrmngr_v92",
        "setting:defaultpendingreqclosureinstrmngrcopybelow" => "setting:defaultpendingreqclosureinstrmngrcopybelow_v92"
    ];
    // Get all notifications templates.
    $notificationtables = ['facetoface_notification_tpl', 'facetoface_notification'];
    foreach ($notificationtables as $table) {
        $templates = $DB->get_records($table);
        foreach ($templates as $template) {
            foreach ($upgradestrings as $original => $new) {
                // Conditionaly update strings according content.
                if (strcmp($template->managerprefix, text_to_html(get_string($original, 'facetoface'))) === 0) {
                    $template->managerprefix = text_to_html(get_string($new, 'facetoface'));
                    $DB->update_record($table, $template);
                }
            }
        }
    }
}