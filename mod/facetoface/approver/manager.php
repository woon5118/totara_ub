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
 * @package mod_facetoface
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once($CFG->dirroot.'/totara/core/dialogs/dialog_content_hierarchy.class.php');

$userid = required_param('userid', PARAM_INT);

$PAGE->set_context(context_system::instance());
require_login(null, false, null, false, true);

// First check that the user really does exist and that they're not a guest.
$userexists = !isguestuser($userid) && $DB->record_exists('user', array('id' => $userid, 'deleted' => 0));

// Prepare an array of managers. If they can't see other users this will remain empty and they'll just get
// an empty request.
$managers = array();

$guest = guest_user();
// Load potential managers for this user.
$usernamefields = get_all_user_name_fields(true, 'u');
$sql = "SELECT u.id, u.email, {$usernamefields}
          FROM {user} u
         WHERE u.deleted = 0
           AND u.suspended = 0
           AND u.id != :guestid
           AND u.id != :userid
      ORDER BY {$usernamefields}";
$params = array(
    'guestid' => $guest->id,
    'userid' => $userid
);
// Limit results to 1 more than the maximum number that might be displayed
// there is no point returning any more as we will never show them.
$managers = $DB->get_records_sql($sql, $params, 0, TOTARA_DIALOG_MAXITEMS + 1);

foreach ($managers as $manager) {
    $manager->fullname = fullname($manager);
}

$dialog = new totara_dialog_content();
$dialog->searchtype = 'user';
$dialog->items = $managers;
$dialog->customdata['current_user'] = $userid;
$dialog->urlparams['userid'] = $userid;

echo $dialog->generate_markup();
