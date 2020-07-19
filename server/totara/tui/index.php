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
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @package totara_tui
 *
 * @var moodle_page $PAGE
 * @var core_renderer $OUTPUT
 */

require(__DIR__ . '/../../config.php');

$tc = optional_param('tc', null, PARAM_RAW);
$component = optional_param('component', null, PARAM_RAW);

$params = [];
if ($tc !== null) {
    $params['tc'] = $tc;
}
if ($component !== null) {
    $params['component'] = $component;
}

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/totara/tui/index.php', $params);

require_login();
require_capability('moodle/site:config', $context);

$PAGE->set_pagelayout('noblocks');
$PAGE->set_title(get_string('samples', 'totara_tui'));

// Get data for the page.
$output = $PAGE->get_renderer('totara_tui');
$component = \totara_tui\output\framework::vue('samples/pages/Samples');

echo $output->header();
echo $output->render($component);
echo $output->footer();
