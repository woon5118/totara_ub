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

use \tool_sitepolicy\localisedpolicy;

admin_externalpage_setup('tool_sitepolicy-managerpolicies');

$PAGE->set_context(context_system::instance());

$newpolicy = optional_param('newpolicy', "", PARAM_INT);
$localisedpolicyid = required_param('localisedpolicy', PARAM_INT);
$sitepolicyid = optional_param('sitepolicyid', 0, PARAM_INT);
$returnpage = optional_param('ret', 'policies', PARAM_ALPHANUMEXT);
$PAGE->set_url(new moodle_url("/{$CFG->admin}/tool/sitepolicy/versionform.php", ['localisedpolicyid' => $localisedpolicyid]));

$primarypolicy = new localisedpolicy($localisedpolicyid);

if (!$primarypolicy->is_primary()) {
    throw new coding_exception('Cannot edit non primary version as primary');
}

$version = $primarypolicy->get_policyversion();
$title = $primarypolicy->get_title();

switch ($returnpage) {
    case 'versions':
        $redirect = new moodle_url("/{$CFG->admin}/tool/sitepolicy/versionlist.php", ['sitepolicyid' => $version->get_sitepolicy()->get_id()]);
        break;

    case 'translations':
        $redirect = new moodle_url("/{$CFG->admin}/tool/sitepolicy/translationlist.php", ['policyversionid' => $version->get_id()]);
        break;

    default:
        $redirect = new moodle_url("/{$CFG->admin}/tool/sitepolicy/index.php");
        break;
}

// Prepare current data
$languages = get_string_manager()->get_list_of_translations();
if (!array_key_exists($primarypolicy->get_language(), $languages)) {
    $primarypolicy->set_language('en');
}

$statements = $primarypolicy->get_statements(false);

$currentdata = [
    'localisedpolicy' => $primarypolicy->get_id(),
    'versionnumber' => $version->get_versionnumber(),
    'language' => $primarypolicy->get_language(),
    'policyversionid' => $version->get_id(),
    'title' => $primarypolicy->get_title(),
    'policytext' => $primarypolicy->get_policytext(),
    'whatsnew' => $primarypolicy->get_whatsnew(),
    'statements' => $statements,
    'sitepolicyid' => $version->get_sitepolicy()->get_id(),
    'newpolicy' => $newpolicy,
    'ret' => $returnpage,
];
$form = new \tool_sitepolicy\form\versionform($currentdata);

if ($form->is_cancelled()) {
    if ($newpolicy) {
        $version->delete();
    }
    redirect($redirect);

} elseif ($formdata = $form->get_data()) {
    if (!empty($version->get_timepublished())) {
        throw new coding_exception('Cannot edit published version.');
    }

    $time = time();

    $primarypolicy->set_authorid($USER->id);
    $primarypolicy->set_title($formdata->title);
    $primarypolicy->set_policytext($formdata->policytext);
    if (isset($formdata->whatsnew)) {
        $primarypolicy->set_whatsnew($formdata->whatsnew);
    }
    $primarypolicy->set_statements($formdata->statements);
    $primarypolicy->save();

    $returnpage = !empty($returnpage) ? $returnpage : $formdata->ret;
    switch ($returnpage) {
        case 'versions':
            $successmsg = get_string('versionupdated', 'tool_sitepolicy', $version->get_versionnumber());
            break;

        case 'translations':
            $successmsg = get_string('versionupdated', 'tool_sitepolicy', $version->get_versionnumber());
            break;

        default:
            $successmsg = get_string('policyupdated', 'tool_sitepolicy', $formdata->title);
            break;
    }

    redirect($redirect, $successmsg,
        null,
        \core\output\notification::NOTIFY_SUCCESS);
}

$PAGE->set_pagelayout('admin');
$PAGE->set_title($title);
echo $OUTPUT->header($title);

$params = ['title' => $title];
if ($newpolicy) {
    $heading = get_string('versionformheadernew', 'tool_sitepolicy', $params);
} else {
    $params['versionnumber'] = $version->get_versionnumber();
    $heading = get_string('versionformheader', 'tool_sitepolicy', $params);
}

echo $OUTPUT->heading($heading);
echo $form->render();
echo $OUTPUT->footer();

