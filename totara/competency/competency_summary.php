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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_competency
 */

require_once(__DIR__ . '/../../config.php');

use totara_competency\entities\competency;

//(new totara_competency\controllers\competency_controller())->process('summary');

$comp_id = required_param('id', PARAM_INT);

require_login();
//require_capability('moodle/site:config', $systemcontext);

$url = new moodle_url('/totara/competency/competency_summary.php', ['id' => $comp_id]);

$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);

$competency = new competency($comp_id);
$heading = get_string('competencytitle',
    'totara_hierarchy',
    (object)['framework' => $competency->framework->fullname, 'fullname' => $competency->fullname]);

echo $OUTPUT->header();
echo $OUTPUT->heading($heading);
echo $OUTPUT->tui_component('totara_competency/views/CompetencySummary');
echo $OUTPUT->footer();
