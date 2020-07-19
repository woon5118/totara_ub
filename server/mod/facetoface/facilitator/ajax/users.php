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

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->dirroot . '/mod/facetoface/lib.php');
require_once($CFG->dirroot . '/mod/facetoface/dialogs/seminar_dialog_content.php');

$userid = optional_param('userid', 0, PARAM_INT);

require_login(null, false, null, false, true);

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/mod/facetoface/facilitator/ajax/users.php');

// Get guest user for exclusion purposes
$guest = guest_user();
// Load potential managers for this user.
$usernamefields = get_all_user_name_fields(true, 'u');
$sql = "SELECT u.id, {$usernamefields}
          FROM {user} u
         WHERE u.deleted = 0
           AND u.suspended = 0
           AND u.id != :guestid
           AND u.id NOT IN (
                SELECT ff.userid FROM {facetoface_facilitator} ff
           )
      ORDER BY {$usernamefields}";
$params = ['guestid' => $guest->id];
// Limit results to 1 more than the maximum number that might be displayed
// there is no point returning any more as we will never show them.
$users = $DB->get_records_sql($sql, $params, 0, TOTARA_DIALOG_MAXITEMS + 1);
foreach ($users as $user) {
    $user->fullname = fullname($user);
}

// Display page.
$dialog = new \seminar_dialog_content();
$dialog->baseurl = '/mod/facetoface/facilitator/ajax/users.php';
$dialog->proxy_dom_data(array('id', 'fullname'));
$dialog->type = \totara_dialog_content::TYPE_CHOICE_SINGLE;
$dialog->manageadhoc = false;
$dialog->items = $users;
$dialog->disabled_items = [$userid => $userid];
$dialog->selected_title = 'selected';
$dialog->lang_file = 'mod_facetoface';
$dialog->customdata = ['userid' => $userid];
$dialog->search_code = '/mod/facetoface/dialogs/search.php';
$dialog->searchtype = 'facetoface_facilitator';
$dialog->string_nothingtodisplay = 'error:nopredefinedfacilitators';
// Additional url parameters needed for pagination in the search tab.
$dialog->urlparams = $params;

echo $dialog->generate_markup();
