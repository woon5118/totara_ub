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

admin_externalpage_setup('tool_sitepolicy');

$policyversionid = required_param('policyversionid', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);

$version = new policyversion($policyversionid);
$sitepolicy = $version->get_sitepolicy();

$currenturl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/versionpublish.php", ['policyversionid' => $policyversionid]);
$versionlisturl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/versionlist.php", ['sitepolicyid' => $sitepolicy->get_id()]);

$PAGE->set_context(context_system::instance());
$PAGE->set_url($currenturl);

$primarypolicy = localisedpolicy::from_version($version, ['isprimary' => localisedpolicy::STATUS_PRIMARY]);
$policytitle = $primarypolicy->get_title();

// Perform action.
if ($confirm) {
    $sitepolicy->switchversion($version);

    $strparams = [
        'title' => $policytitle,
        'version' => $version->get_versionnumber()
    ];
    redirect(
        $versionlisturl,
        get_string('publishsuccess', 'tool_sitepolicy', $strparams),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

// Output.
$heading = get_string('publishheading', 'tool_sitepolicy', $policytitle);

$PAGE->set_pagelayout('admin');
$PAGE->set_title($heading);
$PAGE->set_heading($heading);

/**
 * @var tool_sitepolicy_renderer $renderer
 */
$renderer = $PAGE->get_renderer('tool_sitepolicy');
echo $renderer->header();

// Show confirmations.
if ($version->is_complete()) {
    // Version is complete - good to go.
    $message = $renderer->heading(get_string('publishpolicytitle', 'tool_sitepolicy', $policytitle));
    $message .= get_string('publishlistheading', 'tool_sitepolicy');

    $message .= html_writer::alist([
        get_string('publishlist1', 'tool_sitepolicy'),
        get_string('publishlist2', 'tool_sitepolicy'),
        get_string('publishlist3', 'tool_sitepolicy'),
        get_string('publishlist4', 'tool_sitepolicy'),
    ]);
    $message .= get_string('publishlangheading', 'tool_sitepolicy');

    $langarray = [];
    $versionlang = $version->get_languages();
    // First entry is primary
    $primarylang = reset($versionlang)->language;

    foreach ($versionlang as $translation) {
        $langarray[] = get_string_manager()->get_list_of_languages($primarylang)[$translation->language];
    }
    $message .= html_writer::alist($langarray);

    $confirmurl = new moodle_url($currenturl, ['confirm' => "1"]);
    $continue = new single_button($confirmurl, get_string('publishpublish', 'tool_sitepolicy'));
}

$cancel = new single_button($versionlisturl, get_string('publishcancel', 'tool_sitepolicy'));
echo $renderer->action_confirm($heading, $message, $continue, $cancel);
echo $renderer->footer();