<?php
/**
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Courteney Brownie <courteney.brownie@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package tool_sitepolicy
 */

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('tool_sitepolicy-managerpolicies');

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url("/{$CFG->admin}/tool/sitepolicy/versionform.php"));

$redirect = new moodle_url("/{$CFG->admin}/tool/sitepolicy/index.php");
$pagetitle = get_string('policyformheader', 'tool_sitepolicy');

$PAGE->set_pagelayout('admin');
$PAGE->set_title($pagetitle);

$currentdata = [
    'versionnumber' => 1,
];

$form = new tool_sitepolicy\form\versionform($currentdata);

if ($form->is_cancelled()) {
    redirect($redirect);
}

if ($formdata = $form->get_data()) {
    $trans = $DB->start_delegated_transaction();

    $time = time();
    $sitepolicy = new tool_sitepolicy\sitepolicy();
    $sitepolicy->set_timecreated($time);
    $sitepolicy->save();

    $version = tool_sitepolicy\policyversion::new_policy_draft($sitepolicy, $time);
    $version->save();

    $primarypolicy = tool_sitepolicy\localisedpolicy::from_data($version, $formdata->language, true);
    $primarypolicy->set_authorid($USER->id);
    $primarypolicy->set_timecreated($time);
    $primarypolicy->set_title($formdata->title);
    $primarypolicy->set_policytext($formdata->policytext);
    $primarypolicy->set_statements($formdata->statements);

    $primarypolicy->save();
    $trans->allow_commit();

    redirect($redirect, get_string('policynewsaved', 'tool_sitepolicy', $formdata->title),
        null,
        \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('policycreatenew', 'tool_sitepolicy'));
echo $form->render();
echo $OUTPUT->footer();

