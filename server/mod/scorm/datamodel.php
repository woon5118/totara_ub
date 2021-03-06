<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

// Totara: no random error output here, instead problems are written into error log,
// this normalises behaviour of production sites incorrectly configured to display errors.
define('NO_DEBUG_DISPLAY', true);

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/scorm/locallib.php');

// Totara: Make sure users cannot abort this page request in the middle,
// unfortunately crazy IIS with FastCGI ignores this completely.
ignore_user_abort(true);
set_time_limit(60 * 5);

// Totara: keep params (+ sesskey) in sync with list in request.js

$id = optional_param('id', '', PARAM_INT);       // Course Module ID, or
$a = optional_param('a', '', PARAM_INT);         // scorm ID
$scoid = required_param('scoid', PARAM_INT);  // sco ID
$attempt = required_param('attempt', PARAM_INT);  // attempt number.

// Totara: allow submitting as JSON
$json_data = optional_param('json_data', null, PARAM_RAW); // data

if (!empty($id)) {
    if (! $cm = get_coursemodule_from_id('scorm', $id)) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
        print_error('coursemisconf');
    }
    if (! $scorm = $DB->get_record("scorm", array("id" => $cm->instance))) {
        print_error('invalidcoursemodule');
    }
} else if (!empty($a)) {
    if (! $scorm = $DB->get_record("scorm", array("id" => $a))) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record("course", array("id" => $scorm->course))) {
        print_error('coursemisconf');
    }
    if (! $cm = get_coursemodule_from_instance("scorm", $scorm->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
} else {
    print_error('missingparameter');
}

$PAGE->set_url('/mod/scorm/datamodel.php', array('scoid' => $scoid, 'attempt' => $attempt, 'id' => $cm->id));

require_login($course, false, $cm, false, true); // Totara: no redirects here.

// Totara: respect view and launch permissions.
require_capability('mod/scorm:view', context_module::instance($cm->id));
require_capability('mod/scorm:launch', context_module::instance($cm->id));

scorm_send_headers_totara();

if (confirm_sesskey() && (!empty($scoid))) {
    $result = true;
    $request = null;
    if (has_capability('mod/scorm:savetrack', context_module::instance($cm->id))) {
        // Preload all current tracking data.
        $trackdata = $DB->get_records('scorm_scoes_track', array('userid' => $USER->id, 'scormid' => $scorm->id, 'scoid' => $scoid,
                                                                 'attempt' => $attempt), '', 'element, id, value, timemodified');
        // Totara: allow submitting as JSON
        if ($json_data) {
            $data = json_decode($json_data, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $message = json_last_error_msg();
                throw new \coding_exception("Invalid JSON: '{$json_data}'");
            }
        } else {
            $data = data_submitted();
        }
        foreach ($data as $element => $value) {
            $element = str_replace('__', '.', $element);
            if (substr($element, 0, 3) == 'cmi') {
                $netelement = preg_replace('/\.N(\d+)\./', "\.\$1\.", $element);
                $result = scorm_insert_track($USER->id, $scorm->id, $scoid, $attempt, $element, $value, $scorm->forcecompleted,
                                             $trackdata) && $result;
            }
            if (substr($element, 0, 15) == 'adl.nav.request') {
                // SCORM 2004 Sequencing Request.
                require_once($CFG->dirroot.'/mod/scorm/datamodels/scorm_13lib.php');

                $search = array('@continue@', '@previous@', '@\{target=(\S+)\}choice@', '@exit@',
                                    '@exitAll@', '@abandon@', '@abandonAll@');
                $replace = array('continue_', 'previous_', '\1', 'exit_', 'exitall_', 'abandon_', 'abandonall');
                $action = preg_replace($search, $replace, $value);

                if ($action != $value) {
                    // Evaluating navigation request.
                    $valid = scorm_seq_overall ($scoid, $USER->id, $action, $attempt);
                    $valid = 'true';

                    // Set valid request.
                    $search = array('@continue@', '@previous@', '@\{target=(\S+)\}choice@');
                    $replace = array('true', 'true', 'true');
                    $matched = preg_replace($search, $replace, $value);
                    if ($matched == 'true') {
                        $request = 'adl.nav.request_valid["'.$action.'"] = "'.$valid.'";';
                    }
                }
            }
        }
    }
    if ($result) {
        echo "true\n0";
    } else {
        echo "false\n101";
    }
    if ($request != null) {
        echo "\n".$request;
    }
}
