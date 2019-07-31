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
 * @author Qingyang Liu <qingyang.liu@totaralearning.com>
 * @package engage_survey
 */

use engage_survey\event\survey_viewed;
use engage_survey\totara_engage\resource\survey;
use totara_core\advanced_feature;
use totara_playlist\totara_engage\link\nav_helper;
use totara_tui\output\component;
use totara_engage\access\access_manager;
use core\notification;

require_once(__DIR__ . "/../../../../config.php");
global $OUTPUT, $PAGE, $USER;
require_login();
advanced_feature::require('engage_resources');

$id = required_param("id", PARAM_INT);
$source = optional_param("source", '', PARAM_TEXT);
$source_url = optional_param("source_url", '', PARAM_URL);

/** @var survey $survey */
$survey = survey::from_resource_id($id);
$survey->redirect_edit_page((int)$USER->id, $source, $source_url);

$url = new \moodle_url("/totara/engage/resources/survey/survey_vote.php", ['id' => $id]);
$context = $survey->get_context();

$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title($survey->get_name());
$PAGE->set_pagelayout('legacynolayout');

$tui = null;
if (access_manager::can_access($survey, $USER->id)) {
    // Build the back button
    [$back_button, $navigation_buttons] = nav_helper::build_resource_nav_buttons($survey->get_id(), $survey->get_userid(), $source);

    $tui = new component(
        'engage_survey/pages/SurveyVoteView',
        [
            'resource-id' => $id,
            'back-button' => $back_button,
            'navigation-buttons' => $navigation_buttons,
        ]
    );
    $tui->register($PAGE);

    $event = survey_viewed::from_survey($survey);
    $event->trigger();
}

echo $OUTPUT->header();

if (null !== $tui) {
    echo $OUTPUT->render($tui);
} else {
    notification::error(get_string('cannot_view_survey', 'engage_survey'));
}
echo $OUTPUT->footer();