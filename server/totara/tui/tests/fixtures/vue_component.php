<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
 * @package totara_core
 */

require(__DIR__ . '/../../../../config.php');

$displaydebugging = false;
if (!defined('BEHAT_SITE_RUNNING') || !BEHAT_SITE_RUNNING) {
    if (debugging()) {
        $displaydebugging = true;
    } else {
        throw new coding_exception('Invalid access detected.');
    }
}
$title = 'Vue component rendering check';

require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);
$PAGE->set_context($context);
$PAGE->set_url('/totara/core/tests/fixtures/vue_component.php');
$PAGE->set_pagelayout('noblocks');
$PAGE->set_title($title);

$PAGE->requires->tui_bundle('totara_core');

echo $OUTPUT->header();
if ($displaydebugging) {
    // This is intentionally hard coded - this page is not in the navigation and should only ever be used by developers.
    $msg = 'This page only exists to facilitate acceptance testing, if you are here for any other reason please file an improvement request.';
    echo $OUTPUT->notification($msg, \core\output\notification::NOTIFY_SUCCESS);
    // We display a developer debug message as well to ensure that this doesn't not get shown during behat testing.
    debugging('This is a developer resource, please contact your system admin if you have arrived here by mistake.', DEBUG_DEVELOPER);
}
echo $OUTPUT->heading($title);
echo $OUTPUT->render(new \totara_tui\output\component('totara_core/pages/VueComponent'));
echo $OUTPUT->footer();
