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

global $PAGE;

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url("/{$CFG->admin}/tool/sitepolicy/index.php");
$PAGE->set_pagelayout('admin');

$title = get_string('policiestitle', 'tool_sitepolicy');
$PAGE->set_title($title);
$PAGE->set_heading($title);

/**
 * @var tool_sitepolicy_renderer $renderer
 */
$renderer = $PAGE->get_renderer('tool_sitepolicy');

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

//Table
echo $renderer->manage_site_policy_table();
echo $OUTPUT->footer();
