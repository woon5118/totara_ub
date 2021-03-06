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

/**
 * Edit course completion settings - the form definition.
 *
 * @package     core_completion
 * @category    completion
 * @copyright   2009 Catalyst IT Ltd
 * @author      Aaron Barnes <aaronb@catalyst.net.nz>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir.'/completionlib.php');

/**
 * Defines the course completion settings form.
 */
class course_completion_form extends moodleform {

    /**
     * Defines the form fields.
     */
    public function definition() {
        global $USER, $CFG, $DB;

        $courseconfig = get_config('moodlecourse');
        $mform    =& $this->_form;

        $unlockdelete = $this->_customdata['unlockdelete'];
        $unlockonly = $this->_customdata['unlockonly'];
        $unlocked = $unlockdelete || $unlockonly;
        $course   = $this->_customdata['course'];
        $completion = new completion_info($course);

        $params = array(
            'course'  => $course->id
        );


/// form definition
//--------------------------------------------------------------------------------

        // Check if there are existing non-RPL criteria completions.
        if ($completion->is_course_locked(false) && !$unlocked) {
            $mform->addElement('header', '', get_string('completionsettingslocked', 'completion'));

            if (completion_can_unlock_data($course->id)) {
                $mform->addElement('static', '', '', get_string('err_settingsunlockable', 'completion'));

                $buttonarray = array();
                $buttonarray[] = &$mform->createElement('submit', 'settingsunlockdelete', get_string('unlockcompletiondelete', 'completion'));
                $buttonarray[] = &$mform->createElement('submit', 'settingsunlock', get_string('unlockcompletionwithoutdelete', 'completion'));
                $mform->addGroup($buttonarray, 'settingsunlockgroup', '', array(' '), false);

            } else {
                $mform->addElement('static', '', '', get_string('err_settingslocked', 'completion'));
            }
        }

        // Get array of all available aggregation methods.
        $aggregation_methods = $completion->get_aggregation_methods();

        // Overall criteria aggregation.
        $mform->addElement('header', 'overallcriteria', get_string('general', 'core_form'));
        // Map aggregation methods to context-sensitive human readable dropdown menu.
        $overallaggregationmenu = array();
        foreach ($aggregation_methods as $methodcode => $methodname) {
            if ($methodcode === COMPLETION_AGGREGATION_ALL) {
                $overallaggregationmenu[COMPLETION_AGGREGATION_ALL] = get_string('overallaggregation_all', 'core_completion');
            } else if ($methodcode === COMPLETION_AGGREGATION_ANY) {
                $overallaggregationmenu[COMPLETION_AGGREGATION_ANY] = get_string('overallaggregation_any', 'core_completion');
            } else {
                $overallaggregationmenu[$methodcode] = $methodname;
            }
        }
        $mform->addElement('select', 'overall_aggregation', get_string('overallaggregation', 'core_completion'), $overallaggregationmenu);
        $mform->setDefault('overall_aggregation', $completion->get_aggregation_method());

        // Activity completion criteria
        $label = get_string('coursecompletioncondition', 'core_completion', get_string('activitiescompleted', 'core_completion'));
        $mform->addElement('header', 'activitiescompleted', $label);
        // Get the list of currently specified conditions and expand the section if some are found.
        $current = $completion->get_criteria(COMPLETION_CRITERIA_TYPE_ACTIVITY);
        if (!empty($current)) {
            $mform->setExpanded('activitiescompleted');
        }

        $activities = $completion->get_activities();
        if (!empty($activities)) {

            foreach ($activities as $activity) {
                $params_a = array('moduleinstance' => $activity->id);
                $criteria = new completion_criteria_activity(array_merge($params, $params_a));
                $criteria->config_form_display($mform, $activity);
            }
            // Totara: tell users how we deal with failed activity completions in course completions.
            if (!empty($CFG->completionexcludefailures)) {
                $note2 = get_string('completionexcludefailureson','totara_core');
            } else {
                $note2 = get_string('completionexcludefailuresoff','totara_core');
            }
            $mform->addElement('static', 'criteria_role_note', '', get_string('activitiescompletednote', 'core_completion') . ' ' . $note2);

            if (count($activities) > 1) {
                // Map aggregation methods to context-sensitive human readable dropdown menu.
                $activityaggregationmenu = array();
                foreach ($aggregation_methods as $methodcode => $methodname) {
                    if ($methodcode === COMPLETION_AGGREGATION_ALL) {
                        $activityaggregationmenu[COMPLETION_AGGREGATION_ALL] = get_string('activityaggregation_all', 'core_completion');
                    } else if ($methodcode === COMPLETION_AGGREGATION_ANY) {
                        $activityaggregationmenu[COMPLETION_AGGREGATION_ANY] = get_string('activityaggregation_any', 'core_completion');
                    } else {
                        $activityaggregationmenu[$methodcode] = $methodname;
                    }
                }
                $mform->addElement('select', 'activity_aggregation', get_string('activityaggregation', 'core_completion'), $activityaggregationmenu);
                $mform->setDefault('activity_aggregation', $completion->get_aggregation_method(COMPLETION_CRITERIA_TYPE_ACTIVITY));
            }

        } else {
            $mform->addElement('static', 'noactivities', '', get_string('err_noactivities', 'completion'));
        }

        // Course prerequisite completion criteria.
        $label = get_string('coursecompletioncondition', 'core_completion', get_string('dependenciescompleted', 'core_completion'));
        $mform->addElement('header', 'courseprerequisites', $label);
        // Get the list of currently specified conditions and expand the section if some are found.
        $current = $completion->get_criteria(COMPLETION_CRITERIA_TYPE_COURSE);
        if (!empty($current)) {
            $mform->setExpanded('courseprerequisites');
        }

        // Get applicable courses (prerequisites).
        $courses = $DB->get_records_sql("
                SELECT DISTINCT c.id, c.category, c.sortorder, c.fullname, cc.id AS selected
                  FROM {course} c
             LEFT JOIN {course_completion_criteria} cc ON cc.courseinstance = c.id AND cc.course = {$course->id}
            INNER JOIN {course_completion_criteria} ccc ON ccc.course = c.id
                 WHERE c.enablecompletion = ".COMPLETION_ENABLED."
                       AND c.id <> {$course->id}
              ORDER BY c.sortorder");

        // Totara: enforce tenant separation rules.
        foreach ($courses as $key => $c) {
            if (context_coursecat::instance($c->category)->is_user_access_prevented()) {
                unset($courses[$key]);
            }
        }

        if (!empty($courses)) {
            // Get category list.
            require_once($CFG->libdir. '/coursecatlib.php');
            $list = coursecat::make_categories_list();

            // Get course list for select box.
            $selectbox = array();
            $selected = array();
            foreach ($courses as $c) {
                $selectbox[$c->id] = $list[$c->category] . ' / ' . format_string($c->fullname, true,
                    array('context' => context_course::instance($c->id)));

                // If already selected ...
                if ($c->selected) {
                    $selected[] = $c->id;
                }
            }

            // Show multiselect box.
            $mform->addElement('select', 'criteria_course_value', get_string('coursesavailable', 'completion'), $selectbox,
                    array('multiple' => 'multiple', 'size' => 6, 'class' => 'criteria_course_value'));
            $mform->disabledIf('criteria_course_value', 'criteria_course_none', 'eq', 1);
            // Select current criteria.
            if (isset($selected)) {
                $mform->setDefault('criteria_course', $selected);
            }

            // Explain list.
            $mform->addElement('static', 'criteria_courses_explaination', '', get_string('coursesavailableexplaination', 'completion'));

            // Show select none checkbox
            $mform->addElement('checkbox', 'criteria_course_none', get_string('selectnone', 'completion'));
            $mform->setType('checkbox', PARAM_BOOL);

            if (count($courses) > 1) {
                // Map aggregation methods to context-sensitive human readable dropdown menu.
                $courseaggregationmenu = array();
                foreach ($aggregation_methods as $methodcode => $methodname) {
                    if ($methodcode === COMPLETION_AGGREGATION_ALL) {
                        $courseaggregationmenu[COMPLETION_AGGREGATION_ALL] = get_string('courseaggregation_all', 'core_completion');
                    } else if ($methodcode === COMPLETION_AGGREGATION_ANY) {
                        $courseaggregationmenu[COMPLETION_AGGREGATION_ANY] = get_string('courseaggregation_any', 'core_completion');
                    } else {
                        $courseaggregationmenu[$methodcode] = $methodname;
                    }
                }
                $mform->addElement('select', 'course_aggregation', get_string('courseaggregation', 'core_completion'), $courseaggregationmenu);
                $mform->setDefault('course_aggregation', $completion->get_aggregation_method(COMPLETION_CRITERIA_TYPE_COURSE));
            }

        } else {
            $mform->addElement('static', 'nocourses', '', get_string('err_nocourses', 'completion'));
        }

        // Completion on date
        $label = get_string('coursecompletioncondition', 'core_completion', get_string('completionondate', 'core_completion'));
        $mform->addElement('header', 'date', $label);
        // Expand the condition section if it is currently enabled.
        $current = $completion->get_criteria(COMPLETION_CRITERIA_TYPE_DATE);
        if (!empty($current)) {
            $mform->setExpanded('date');
        }
        $criteria = new completion_criteria_date($params);
        $criteria->config_form_display($mform);

        // Completion after enrolment duration
        $label = get_string('coursecompletioncondition', 'core_completion', get_string('enrolmentduration', 'core_completion'));
        $mform->addElement('header', 'duration', $label);
        // Expand the condition section if it is currently enabled.
        $current = $completion->get_criteria(COMPLETION_CRITERIA_TYPE_DURATION);
        if (!empty($current)) {
            $mform->setExpanded('duration');
        }
        $criteria = new completion_criteria_duration($params);
        $criteria->config_form_display($mform);

        // Completion on course grade
        $label = get_string('coursecompletioncondition', 'core_completion', get_string('coursegrade', 'core_completion'));
        $mform->addElement('header', 'grade', $label);
        // Expand the condition section if it is currently enabled.
        $current = $completion->get_criteria(COMPLETION_CRITERIA_TYPE_GRADE);
        if (!empty($current)) {
            $mform->setExpanded('grade');
        }
        $course_grade = $DB->get_field('grade_items', 'gradepass', array('courseid' => $course->id, 'itemtype' => 'course'));
        if (!$course_grade) {
            $course_grade = '0.00000';
        }
        $criteria = new completion_criteria_grade($params);
        $criteria->config_form_display($mform, $course_grade);

        // Manual self completion
        $label = get_string('coursecompletioncondition', 'core_completion', get_string('manualselfcompletion', 'core_completion'));
        $mform->addElement('header', 'manualselfcompletion', $label);
        // Expand the condition section if it is currently enabled.
        $current = $completion->get_criteria(COMPLETION_CRITERIA_TYPE_SELF);
        if (!empty($current)) {
            $mform->setExpanded('manualselfcompletion');
        }
        $criteria = new completion_criteria_self($params);
        $criteria->config_form_display($mform);
        $mform->addElement('static', 'criteria_self_note', '', get_string('manualselfcompletionnote', 'core_completion'));

        // Role completion criteria
        $label = get_string('coursecompletioncondition', 'core_completion', get_string('manualcompletionby', 'core_completion'));
        $mform->addElement('header', 'roles', $label);
        // Expand the condition section if it is currently enabled.
        $current = $completion->get_criteria(COMPLETION_CRITERIA_TYPE_ROLE);
        if (!empty($current)) {
            $mform->setExpanded('roles');
        }
        $roles = get_roles_with_capability('moodle/course:markcomplete', CAP_ALLOW, context_course::instance($course->id, IGNORE_MISSING));

        if (!empty($roles)) {
            foreach ($roles as $role) {
                $params_a = array('role' => $role->id);
                $criteria = new completion_criteria_role(array_merge($params, $params_a));
                $criteria->config_form_display($mform, $role);
            }
            $mform->addElement('static', 'criteria_role_note', '', get_string('manualcompletionbynote', 'core_completion'));
            // Map aggregation methods to context-sensitive human readable dropdown menu.
            $roleaggregationmenu = array();
            foreach ($aggregation_methods as $methodcode => $methodname) {
                if ($methodcode === COMPLETION_AGGREGATION_ALL) {
                    $roleaggregationmenu[COMPLETION_AGGREGATION_ALL] = get_string('roleaggregation_all', 'core_completion');
                } else if ($methodcode === COMPLETION_AGGREGATION_ANY) {
                    $roleaggregationmenu[COMPLETION_AGGREGATION_ANY] = get_string('roleaggregation_any', 'core_completion');
                } else {
                    $roleaggregationmenu[$methodcode] = $methodname;
                }
            }
            $mform->addElement('select', 'role_aggregation', get_string('roleaggregation', 'core_completion'), $roleaggregationmenu);
            $mform->setDefault('role_aggregation', $completion->get_aggregation_method(COMPLETION_CRITERIA_TYPE_ROLE));

        } else {
            $mform->addElement('static', 'noroles', '', get_string('err_noroles', 'completion'));
        }

        if ($unlockdelete) {
            $mform->addElement('header', 'warningunlocked', get_string('completionsettingsunlocked', 'completion'));
            $mform->setExpanded('warningunlocked', true, true);
            $mform->addElement('static', '', '', get_string('completedunlockedtext', 'completion'));
        }

        // Add common action buttons.
        $this->add_action_buttons();

        // Add hidden fields.
        $mform->addElement('hidden', 'id', $course->id);
        $mform->addElement('hidden', 'unlockdelete', $unlockdelete);
        $mform->addElement('hidden', 'unlockonly', $unlockonly);
        $mform->setType('id', PARAM_INT);
        $mform->setType('unlockdelete', PARAM_INT);
        $mform->setType('unlockonly', PARAM_INT);

        // If the criteria are locked, freeze values and submit button.
        if ($completion->is_course_locked(false) && !$unlocked) {
            $except = array('settingsunlockgroup');
            $mform->hardFreezeAllVisibleExcept($except);
            $mform->addElement('cancel');
        }
    }
}
