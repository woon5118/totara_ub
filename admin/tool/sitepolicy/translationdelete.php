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

$localisedpolicyid = required_param('localisedpolicy', PARAM_INT);
$localisedpolicy = new \tool_sitepolicy\localisedpolicy($localisedpolicyid);
$version = $localisedpolicy->get_policyversion();

$redirecturl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/translationlist.php", ['policyversionid' => $version->get_id()]);
$pageurl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/translationdelete.php", ['localisedpolicy' => $localisedpolicyid]);

$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());

// Validate.
if ($localisedpolicy->is_primary()) {
    throw new coding_exception('Cannot delete primary policy version translation.');
}

if ($version->get_timepublished()) {
    throw new coding_exception('Cannot delete translation of published version.');
}

$languagestr = get_string_manager()->get_list_of_languages()[$localisedpolicy->get_language()];

// Perform action.
$confirm = optional_param('confirm', 0, PARAM_INT);
if ($confirm) {
    $localisedpolicy->delete();

    redirect(
        $redirecturl,
        get_string('translationdeleted', 'tool_sitepolicy', $languagestr),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

// Output
$PAGE->set_pagelayout('admin');
$heading = get_string('deletetranslationheading', 'tool_sitepolicy');
$PAGE->set_title($heading);
$PAGE->set_heading($heading);

/**
 * @var tool_sitepolicy_renderer $renderer
 */
$renderer = $PAGE->get_renderer('tool_sitepolicy');
echo $renderer->header();

$policytitle = $localisedpolicy->get_primary_title();

// Show confirmation.
$strparams = ['title' => $policytitle, 'language' => $languagestr];
$message = $renderer->heading(get_string('deletetranslationtitle', 'tool_sitepolicy', $strparams));
$message .= get_string('deletetranslationmessage', 'tool_sitepolicy');
$deleteurl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/translationdelete.php", ['confirm' => 1, 'localisedpolicy' => $localisedpolicyid]);
$delete = new single_button($deleteurl, get_string('deletetranslationdelete', 'tool_sitepolicy'));
$cancel = new single_button($redirecturl, get_string('deletetranslationcancel', 'tool_sitepolicy'));

echo $renderer->action_confirm($heading, $message, $delete, $cancel);
echo $renderer->footer();
