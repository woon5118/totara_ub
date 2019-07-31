<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package totara_engage
 */
use core\notification;

require_once(__DIR__ . "/../../config.php");
require_login();

// Please note that this page is for development only.
global $CFG, $OUTPUT, $PAGE, $DB;

$PAGE->set_context(\context_system::instance());
$PAGE->set_url(new \moodle_url("/totara/engage/dev_generator.php"));
$PAGE->set_title(get_string('engagegenerator', 'totara_engage'));
$PAGE->set_pagelayout('vue');

if (!isset($CFG->sitetype) || 'development' !== $CFG->sitetype) {
    echo $OUTPUT->header();
    notification::error(
        get_string('error:generatoraccess', 'totara_engage')
    );
    echo $OUTPUT->footer();
    exit;
}

require_once("{$CFG->dirroot}/totara/engage/tests/fixtures/engage_generator_helper.php");

$selected_component = optional_param('component', null, PARAM_COMPONENT);
$selected_number = optional_param('number', 0, PARAM_INT);

$generators = engage_generator_helper::get_generators();

if (null !== $selected_component && 0 !== $selected_component) {
    require_sesskey();

    $selected_generators = $generators;
    if ('' !== $selected_component && isset($generators[$selected_component])) {
        $selected_generators = [
            $selected_component => $generators[$selected_component]
        ];
    }

    $transaction = $DB->start_delegated_transaction();
    foreach ($selected_generators as $component => $generator) {
        try {
            for ($i = 0; $i < $selected_number; $i++) {
                $generator->generate_random();
            }

            $a = new \stdClass();
            $a->number = $selected_number;
            $a->component = get_string('pluginname', $component);

            notification::success(get_string('generatedxcomponent', 'totara_engage', $a));
        } catch (Throwable $e) {
            notification::error($e->getMessage());
        }
    }

    $transaction->allow_commit();
}

$parameters = [
    'components' => [],
    'session-key' => sesskey(), // CSRF protection (;¬_¬)
];

// Build up the parameters array.
foreach ($generators as $component => $gen) {
    $parameters['components'][] = [
        'label' => get_string('pluginname', $component),
        'component' => $component
    ];
}

if (null !== $selected_component) {
    $parameters['selected-component'] = $selected_component;
}

if (0 != $selected_number) {
    $parameters['selected-number'] = $selected_number;
}

$tui = new \totara_tui\output\component(
    'totara_engage/pages/EngageGeneratorPage',
    $parameters
);
$tui->register($PAGE);

echo $OUTPUT->header();
echo $OUTPUT->render($tui);
echo $OUTPUT->footer();