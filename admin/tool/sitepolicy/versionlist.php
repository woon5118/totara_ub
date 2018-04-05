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

use \tool_sitepolicy\policyversion,
    \tool_sitepolicy\sitepolicy,
    \tool_sitepolicy\localisedpolicy;

admin_externalpage_setup('tool_sitepolicy-managerpolicies');

$sitepolicyid = required_param('sitepolicyid', PARAM_INT);
$sitepolicy = new sitepolicy($sitepolicyid);

$redirect = new moodle_url("/{$CFG->admin}/tool/sitepolicy/index.php");
$currenturl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/versionlist.php", ['sitepolicyid' => $sitepolicyid]);

$PAGE->set_url($currenturl);
$PAGE->set_context(context_system::instance());

// Perform actions.
$action = optional_param('action', "", PARAM_ALPHANUMEXT);
if ($action == 'newdraft') {
    $time = time();
    $latestversion = policyversion::from_policy_latest($sitepolicy);

    $trans = $DB->start_delegated_transaction();
    $draft = policyversion::new_policy_draft($sitepolicy);
    $draft->save();
    $draft->clone_content($latestversion);
    $trans->allow_commit();

    // Redirect to new version primary localised policy edit form
    $primarypolicy = localisedpolicy::from_version($draft, ['isprimary' => localisedpolicy::STATUS_PRIMARY]);
    $localisedpolicyformurl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/versionform.php",
        ['localisedpolicy' => $primarypolicy->get_id(), 'newpolicy' => 1, 'ret' => 'versions']);

    redirect($localisedpolicyformurl);
}

$version = policyversion::from_policy_latest($sitepolicy);
$primarypolicy = localisedpolicy::from_version($version, ['isprimary' => localisedpolicy::STATUS_PRIMARY]);
$title = $primarypolicy->get_title();

$heading = get_string('versionstitle', 'tool_sitepolicy');
$context = context_system::instance();
$PAGE->set_title($heading);

/**
 * @var tool_sitepolicy_renderer $renderer
 */
$renderer = $PAGE->get_renderer('tool_sitepolicy');
$output = $PAGE->get_renderer('tool_sitepolicy');
echo $renderer->header();

if ($version->get_status() == policyversion::STATUS_DRAFT) {
    $versionsummary = $version->get_summary();

    $incompletelanguages = [];
    foreach ($versionsummary as $entries => $entry) {
        if ($entry->incomplete) {
            $incompletelanguages[] = get_string_manager()->get_list_of_languages($entry->primarylanguage)[$entry->language];
        }
    }

    if (!empty($incompletelanguages)) {
        $message = get_string('publishincompletedesc', 'tool_sitepolicy');
        $message .= html_writer::alist($incompletelanguages);
        $message .= get_string('publishincompleteaction', 'tool_sitepolicy');
        echo $renderer->notification($message, \core\output\notification::NOTIFY_WARNING);
    }
}
echo $renderer->heading(get_string('versionsheading', 'tool_sitepolicy', $title));

// Show create new draft when latest version is published.
if ($version->get_status() != policyversion::STATUS_DRAFT) {
    $newdrafturl = new moodle_url($currenturl, ['action' => 'newdraft']);
    echo $renderer->single_button($newdrafturl, get_string('versionscreatenew', 'tool_sitepolicy'));
}

echo $renderer->manage_version_policy_table($sitepolicyid);
echo $renderer->footer();
