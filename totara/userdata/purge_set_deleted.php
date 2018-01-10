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

$id = required_param('id', PARAM_INT);

$syscontext = context_system::instance();

$PAGE->set_context($syscontext);
$PAGE->set_url('/totara/userdata/purge_set_deleted.php', array('id' => $id));
$PAGE->set_pagelayout('noblocks'); // There is no need for navigation here.

require_login();
require_capability('totara/userdata:purgesetdeleted', $syscontext);

$user = $DB->get_record('user', array('id' => $id), '*', MUST_EXIST);

$returnurl = new moodle_url('/totara/userdata/user_info.php', array('id' => $user->id));

$extra = \totara_userdata\local\util::get_user_extras($user->id);

$currentdata = new stdClass();
$currentdata->id = $id;
$currentdata->deletedpurgetypeid = '';
if ($extra->deletedpurgetypeid) {
    $currentdata->deletedpurgetypeid = $extra->deletedpurgetypeid;
}

$form = new \totara_userdata\form\purge_set_deleted($currentdata);

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    $updates = array();
    if ($data->deletedpurgetypeid != $extra->deletedpurgetypeid) {
        $updates['deletedpurgetypeid'] = empty($data->deletedpurgetypeid) ? null : $data->deletedpurgetypeid;
        $updates['timedeletedpurged'] = null;
    }
    if ($updates) {
        $updates['id'] = $extra->id;
        $DB->update_record('totara_userdata_user', (object)$updates);
    }
    redirect($returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('purgesetdeleted', 'totara_userdata'));
echo $form->render();
echo $OUTPUT->footer();
