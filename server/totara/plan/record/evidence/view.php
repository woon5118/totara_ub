<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 * @author Eugene Venter <eugene@catalyst.net.nz>
 * @author Peter Bulmer <peterb@catalyst.net.nz>
 * @author Aaron Wells <aaronw@catalyst.net.nz>
 * @author Russell England <russell.england@totaralms.com>
 * @package totara
 * @subpackage plan
 */

use totara_core\advanced_feature;
use totara_evidence\models\evidence_item;
use totara_evidence\output\view_item;

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->dirroot . '/totara/plan/lib.php');
require_once($CFG->dirroot . '/totara/core/js/lib/setup.php');
require_once('edit_form.php');
require_once('lib.php');

require_login();

if (advanced_feature::is_disabled('recordoflearning')) {
    print_error('error:recordoflearningdisabled', 'totara_plan');
}

$evidenceid = required_param('id', PARAM_INT); // evidence assignment id

$evidence = evidence_item::load_by_id($evidenceid);
$userid = $evidence->user_id;

if (!$user = $DB->get_record('user', array('id' => $userid))) {
    print_error('error:usernotfound', 'totara_plan');
}

$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);
$PAGE->set_pagelayout('report');
$PAGE->set_url('/totara/plan/record/evidence/view.php', array('id' => $evidenceid));

$view_evidence = view_item::create($evidence);

if ($USER->id != $userid && !(\totara_job\job_assignment::is_managing($USER->id, $userid)) && !has_capability('totara/plan:accessanyplan', context_system::instance())) {
    print_error('error:cannotviewpage', 'totara_plan');
}

if ($USER->id != $userid) {
    $strheading = get_string('recordoflearningforname', 'totara_core', fullname($user, true));
    $usertype = 'manager';
} else {
    $strheading = get_string('recordoflearning', 'totara_core');
    $usertype = 'learner';
}

// Get subheading name for display.
if ($usertype == 'manager') {
    if (advanced_feature::is_enabled('myteam')) {
        $menuitem = 'myteam';
        $url = new moodle_url('/my/teammembers.php');
        $PAGE->navbar->add(get_string('team', 'totara_core'), $url);
    } else {
        $menuitem = null;
        $url = null;
    }
} else {
    $menuitem = null;
    $url = null;
}
$indexurl = new moodle_url('/totara/plan/record/evidence/index.php', array('userid' => $userid));
$PAGE->navbar->add($strheading, $indexurl);
$PAGE->navbar->add(get_string('allevidence', 'totara_plan'), new moodle_url('/totara/plan/record/evidence/index.php', array('userid' => $userid)));
$PAGE->navbar->add(get_string('evidenceview', 'totara_plan'));

$PAGE->set_title($strheading);
$PAGE->set_heading($SITE->fullname);
dp_display_plans_menu($userid, 0, $usertype, 'evidence/index', 'none', false);
echo $OUTPUT->header();

echo $OUTPUT->container_start('', 'dp-plan-content');

echo $OUTPUT->heading($strheading);

dp_print_rol_tabs(null, 'evidence', $userid);

echo $OUTPUT->render($view_evidence);

echo $OUTPUT->container_end();

echo $OUTPUT->footer();
