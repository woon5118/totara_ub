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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency\models\activity_log;

use core\orm\entity\entity;
use tassign_competency\entities\competency_assignment_user_log;
use totara_assignment\entities\cohort;
use totara_assignment\entities\organisation;
use totara_assignment\entities\position;
use totara_assignment\entities\user;
use totara_competency\models\activity_log;
use tassign_competency\entities;

class assignment extends activity_log {

    /**
     * Load an instance of this model using data from the entity passed in.
     *
     * @param entity $entity
     * @return activity_log
     */
    public static function load_by_entity(entity $entity): activity_log {
        if (!($entity instanceof competency_assignment_user_log)) {
            throw new \coding_exception('Invalid entity', 'Entity must be instance of competency_assignment_user_log');
        }

        return (new assignment())->set_entity($entity);
    }

    /**
     * Timestamp of the date corresponding to this data.
     *
     * @return int
     */
    public function get_date(): int {
        return $this->get_entity()->created_at;
    }

    /**
     * @return string
     */
    public function get_assignment_action(): string {
        return $this->get_entity()->action_name;
    }

    /**
     * Gets the human-readable description for an assignment log type instance.
     *
     * @return string
     */
    public function get_description(): string {
        $assignment = new entities\assignment($this->get_assignment()->get_id());
        switch ($this->get_entity()->action) {
            case competency_assignment_user_log::ACTION_ASSIGNED:
                switch ($assignment->type) {
                    case entities\assignment::TYPE_SYSTEM:
                        return get_string('activitylog_assignedcontinuous', 'totara_competency');
                    case entities\assignment::TYPE_LEGACY:
                        return get_string('assignment_type:legacy', 'tassign_competency');
                    case entities\assignment::TYPE_SELF:
                        return get_string('activitylog_assignedself', 'totara_competency');
                    case entities\assignment::TYPE_ADMIN:
                        switch ($assignment->user_group_type) {
                            // Todo: Use methods available in assignment model when branches have been merged.
                            case 'user':
                                $assigner = new user($assignment->created_by);
                                $a = new \stdClass();
                                $a->assigner_name = fullname((object)$assigner->to_array());
                                return get_string('activitylog_assignedadmin', 'totara_competency', $a);
                            case 'cohort':
                                $user_group = new cohort($assignment->user_group_id);
                                $a = new \stdClass();
                                $a->audience_name = $user_group->name;
                                return get_string('activitylog_assignedaudience', 'totara_competency', $a);
                            case 'position':
                                $user_group = new position($assignment->user_group_id);
                                $a = new \stdClass();
                                $a->position_name = $user_group->fullname;
                                return get_string('activitylog_assignedposition', 'totara_competency', $a);
                            case 'organisation':
                                $user_group = new organisation($assignment->user_group_id);
                                $a = new \stdClass();
                                $a->organisation_name = $user_group->fullname;
                                return get_string('activitylog_assignedorganisation', 'totara_competency', $a);
                            default:
                                throw new \coding_exception(
                                    'Invalid type',
                                    'Assignment group type not found: '. $assignment->user_group_type
                                );
                        }
                        break;
                    case entities\assignment::TYPE_OTHER:
                        $assigner = new user($assignment->created_by);
                        $a = new \stdClass();
                        $a->assigner_name = fullname((object)$assigner->to_array());
                        // Todo: How to get role...
                        $a->assigner_role = 'Todo: Get Role';
                        return get_string('activitylog_assignedother', 'totara_competency', $a);
                    default:
                        throw new \coding_exception(
                            'Invalid type',
                            'Invalid assignment type: ' . $this->get_assignment()->get_type_name()
                        );
                }
                break;
            case competency_assignment_user_log::ACTION_UNASSIGNED_USER_GROUP:
            case competency_assignment_user_log::ACTION_UNASSIGNED_ARCHIVED:
                switch ($assignment->type) {
                    case entities\assignment::TYPE_SYSTEM:
                        return get_string('activitylog_unassignedcontinuous', 'totara_competency');
                    case entities\assignment::TYPE_SELF:
                        return get_string('activitylog_unassignedself', 'totara_competency');
                    case entities\assignment::TYPE_ADMIN:
                        switch ($assignment->user_group_type) {
                            case 'user':
                                $assigner = new user($assignment->created_by);
                                $a = new \stdClass();
                                $a->assigner_name = fullname((object)$assigner->to_array());
                                return get_string('activitylog_unassignedadmin', 'totara_competency', $a);
                            case 'cohort':
                                $user_group = new cohort($assignment->user_group_id);
                                $a = new \stdClass();
                                $a->audience_name = $user_group->name;
                                return get_string('activitylog_unassignedaudience', 'totara_competency', $a);
                            case 'position':
                                $user_group = new position($assignment->user_group_id);
                                $a = new \stdClass();
                                $a->position_name = $user_group->fullname;
                                return get_string('activitylog_unassignedposition', 'totara_competency', $a);
                            case 'organisation':
                                $user_group = new organisation($assignment->user_group_id);
                                $a = new \stdClass();
                                $a->organisation_name = $user_group->fullname;
                                return get_string('activitylog_unassignedorganisation', 'totara_competency', $a);
                            default:
                                throw new \coding_exception(
                                    'Invalid type',
                                    'Assignment group type not found: '. $assignment->user_group_type
                                );
                        }
                        break;
                    case entities\assignment::TYPE_OTHER:
                        $assigner = new user($assignment->created_by);
                        $a = new \stdClass();
                        $a->assigner_name = fullname((object)$assigner->to_array());
                        // Todo: How to get role...
                        $a->assigner_role = 'Todo: Get Role';
                        return get_string('activitylog_unassignedother', 'totara_competency', $a);
                    default:
                        throw new \coding_exception(
                            'Invalid type',
                            'Invalid assignment type: ' . $this->get_assignment()->type
                        );
                }
                break;
            case competency_assignment_user_log::ACTION_TRACKING_START:
                return get_string('activitylog_trackingstarted', 'totara_competency');
            case competency_assignment_user_log::ACTION_TRACKING_END:
                return get_string('activitylog_trackingstopped', 'totara_competency');
            default:
                throw new \coding_exception('Invalid action', 'Invalid action provided: ' . $this->get_entity()->action);
        }
    }
}