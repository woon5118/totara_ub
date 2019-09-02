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
 * @author David Curry <david.curry@totaralms.com>
 * @author Alastair Munro <alastair.munro@totaralms.com>
 * @package totara_plan
 */

defined('MOODLE_INTERNAL') || die();

use totara_competency\event\competency_achievement_updated;


class totara_plan_observer {

    /**
     * Clears relevant user data when the user is deleted
     *  - Evidence records
     *
     * @deprecated since Totara 13
     *
     * @param \core\event\user_deleted $event
     *
     */
    public static function user_deleted(\core\event\user_deleted $event) {
        debugging('\totara_plan_observer::user_deleted has been deprecated and is no longer used, please use totara_evidence\observer::user_deleted instead.', DEBUG_DEVELOPER);
        totara_evidence\observer::user_deleted($event);
    }

    /*
     * This function is to clean up any references to courses within
     * programs when they are deleted. Any coursesets that become empty
     * due to this are also deleted as programs does not allow empty
     * coursesets.
     *
     * @param \core\event\course_deleted $event
     * @return boolean True if all references to the course are deleted correctly
     */
    public static function course_deleted(\core\event\course_deleted $event) {
        global $DB;

        $courseid = $event->objectid;

        $transaction = $DB->start_delegated_transaction();

        // Remove relations.
        $sql = "DELETE FROM {dp_plan_component_relation} WHERE
                    (component1 = 'course' AND itemid1 IN (SELECT id FROM {dp_plan_course_assign} WHERE courseid = :courseid1))
                OR
                    (component2 = 'course' AND itemid2 IN (SELECT id FROM {dp_plan_course_assign} WHERE courseid = :courseid2))";

        $params = array('courseid1' => $courseid, 'courseid2' => $courseid);
        $DB->execute($sql, $params);

        // Remove records of courses assigned to plans.
        $DB->delete_records('dp_plan_course_assign', array('courseid' => $courseid));

        $transaction->allow_commit();

        return true;
    }

    public static function competency_record_updated(competency_achievement_updated $event) {
        global $DB;

        // TODO: See the later part of this method where alerts are sent if the user has just become proficient
        // and has a learning plan. This may not need to be done in an observer any more. It might only be done
        // when a learning plan value is changed. Otherwise, this method will need to be fixed up to work with
        // current code.
        return;

        if (empty($event->other->achieved_via_ids)) {
            return;
        }

        list($insql, $params) = $DB->get_in_or_equal($event->other->achieved_via_ids);

        $params[] = 'learning_plan';

        $has_learning_plan = $DB->record_exists_sql(
            'SELECT 1 
                 FROM {pathway} p 
                 JOIN {comp_user_pathway_achieved} cupa 
                 ON p.id = cupa.pathway_id
                 JOIN {comp_record_via} crv
                 ON cupa.id = crv.id 
                 WHERE crv.id ' . $insql .' AND path_type = ?',
            $params
        );

        if (!$has_learning_plan) {
            return;
        }

        $plan_value_record = $DB->get_record(
            'dp_plan_competency_value',
            ['competency_id' => $event->other->competency_id, 'user_id' => $event->relateduserid]
        );

        if (!$plan_value_record) {
            // Not expected, as there was an achieved_via record. Still, nothing to do if it's missing.
            return;
        }

        // if (newly completed) {  Todo: Can probably add a property to the event to represent this. That will replace all up til // Load the component.

        // Todo: The lines using block_totara_stats and proficient db checks are very much temporary and copied from totara_stats
        // to get things going.
        $count = $DB->count_records(
            'block_totara_stats',
            ['userid' => $event->relateduserid, 'eventtype' => STATS_EVENT_COMP_ACHIEVED, 'data2' => $event->other->competency_id]
        );
        if (isset($scale_value)) {
            $isproficient = $DB->get_field('comp_scale_values', 'proficient', array('id' => $scale_value->get_id()));
        } else {
            $isproficient = 0;
        }
        if ($isproficient && $count == 0) {

            $plans = dp_get_plans($event->relateduserid);
            foreach ($plans as $plan) {
                if (!$DB->record_exists(
                    'dp_plan_competency_assign',
                    ['planid' => $plan->id, 'competencyid' => $event->other->competency_id]
                )) {
                    continue;
                }

                $competency_component = null;
                $development_plan = new development_plan($plan->id);
                foreach ($development_plan->get_components() as $component) {
                    if ($component instanceof dp_competency_component) {
                        $competency_component = $component;
                    }
                }
                if (is_null($competency_component)) {
                    continue;
                }

                // Send Alert.
                $alert_detail = new \stdClass();
                $alert_detail->itemname = $DB->get_field('comp', 'fullname', array('id' => $event->other->competency_id));
                $alert_detail->text = get_string('competencycompleted', 'totara_plan');
                $competency_component->send_component_complete_alert($alert_detail);
            }
        }
    }
}
