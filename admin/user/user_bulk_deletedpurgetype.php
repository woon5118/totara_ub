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

require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once(__DIR__ . '/user_bulk_deletedpurgetype_form.php');

$confirmhash = optional_param('confirmhash', '', PARAM_ALPHANUM);

admin_externalpage_setup('userbulk');
require_capability('totara/userdata:purgesetdeleted', context_system::instance());

$returnurl = new moodle_url('/admin/user/user_bulk.php');

$usersids = $SESSION->bulk_users;
if (empty($usersids)) {
    redirect($returnurl, get_string('error'), null, \core\output\notification::NOTIFY_ERROR);
}
$currenthash = sha1(serialize($usersids));

if (!$confirmhash) {
    $confirmhash = $currenthash;
}

$currentdata = new stdClass();
$currentdata->confirmhash = $confirmhash;
$form = new user_bulk_deletedpurgetype_form($currentdata);

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    if ($data->confirmhash !== $currenthash) {
        // Somebody modified list of users!
        redirect($returnurl, get_string('error'), null, \core\output\notification::NOTIFY_ERROR);
    }

    if ($data->deletedpurgetypeid == -1) {
        $deletedpurgetypeid = null;
    } else {
        $deletedpurgetypeid = (string)$data->deletedpurgetypeid;
    }
    list($in, $params) = $DB->get_in_or_equal($usersids);
    $rs = $DB->get_recordset_select('user', "id $in", $params);
    foreach ($rs as $user) {
        $extra = \totara_userdata\local\util::get_user_extras($user->id);
        if ($extra->deletedpurgetypeid === $deletedpurgetypeid) {
            continue;
        }
        $DB->set_field('totara_userdata_user', 'deletedpurgetypeid', $deletedpurgetypeid, array('id' => $extra->id));
    }
    $rs->close();
    redirect($returnurl, get_string('changessaved'));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('deletedpurgetype', 'totara_userdata'));

echo $form->render();

echo $OUTPUT->footer();
