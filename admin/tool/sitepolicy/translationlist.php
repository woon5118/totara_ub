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

$policyversionid = required_param('policyversionid', PARAM_INT);
$translationlisturl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/translationlist.php", ['policyversionid' => $policyversionid]);

$version = new \tool_sitepolicy\policyversion($policyversionid);
$sitepolicy = $version->get_sitepolicy();
$primarypolicy = \tool_sitepolicy\localisedpolicy::from_version($version,
    ['isprimary' => \tool_sitepolicy\localisedpolicy::STATUS_PRIMARY]);
$title = $primarypolicy->get_title();

$heading = get_string('translationsheading', 'tool_sitepolicy', $title);
$context = context_system::instance();
$PAGE->set_url($translationlisturl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($heading);
$PAGE->set_heading($heading);
$PAGE->set_context($context);

/**
 * @var tool_sitepolicy_renderer $renderer
 */
$renderer = $PAGE->get_renderer('tool_sitepolicy');
echo $renderer->header();
echo $OUTPUT->heading($heading);

// Actions.
$versionlisturl = new moodle_url("/{$CFG->admin}/tool/sitepolicy/versionlist.php", ['sitepolicyid' => $sitepolicy->get_id()]);
echo html_writer::link($versionlisturl, get_string('translationsbacktoversions', 'tool_sitepolicy'));

if ($version->is_draft()) {
    echo $renderer->add_translation_single_select($version);
}

// Table.
echo $renderer->manage_translation_table($version);
echo $renderer->footer();
