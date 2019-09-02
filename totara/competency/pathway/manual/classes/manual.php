<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_pathway
 */

namespace pathway_manual;

use pathway_manual\entities\rating;
use totara_competency\base_achievement_detail;
use totara_competency\pathway;
use totara_competency\pathway_aggregator;
use totara_job\job_assignment;

class manual extends pathway {

    /**
     * Supported roles
     */
    const ROLE_MANAGER = 'manager';
    const ROLE_APPRAISER = 'appraiser';
    const ROLE_SELF = 'self';
    // Todo: manager's manager and whichever others we want.

    /**
     * Types of aggregation available in manual
     */
    const AGGREGATE_ALL = 1;
    const AGGREGATE_ANY_N = 2;
    const AGGREGATE_LAST = 3;

    /** @var int $classification  */
    protected $classification = self::PATHWAY_MULTI_VALUE;

    /** @var array $roles*/
    private $roles = [];



    /********************************************************************
     * Instantiation
     ********************************************************************/

    /**
     * Load the manual configuration from the database
     */
    protected function fetch_configuration() {
        global $DB;

        $this->set_roles(
            $DB->get_fieldset_select(
                'pathway_manual_role',
                'role',
                'path_manual_id = :path_manual_id',
                ['path_manual_id' => $this->get_path_instance_id()]
            )
        );

    }

    /****************************************************************************
     * Saving
     ****************************************************************************/

    /**
     * Save the configuration
     */
    protected function save_configuration() {
        global $DB;

        // Not checking is_dirty here as it is checked in parent::save()

        $main = new \stdClass();
        $main->aggregation_method = self::AGGREGATE_LAST; // Default in v1
        $main->status = static::PATHWAY_STATUS_ACTIVE;
        $main->timemodified = time();

        if (empty($this->get_path_instance_id())) {
            $this->set_path_instance_id($DB->insert_record('pathway_manual', $main));
        } else {
            // Updating the timemodified as some of the roles changed
            $main->id = $this->get_path_instance_id();
            $DB->update_record('pathway_manual', $main);
        }

        // We want the role to be first in the select statement so that arrays are keyed that way.
        $previous_role_records = $DB->get_records('pathway_manual_role',
            ['path_manual_id' => $this->get_path_instance_id()],
            '',
            'role, id, path_manual_id');

        // Create this object once and reuse it for inserting new records.
        $new_record = new \stdClass();
        $new_record->path_manual_id = $this->get_path_instance_id();
        foreach ($this->roles as $role) {
            if (!isset($previous_role_records[$role])) {
                $new_record->role = $role;
                $DB->insert_record('pathway_manual_role', $new_record);
            } else {
                unset($previous_role_records[$role]);
            }
        }

        // Remaining previous records are not meant to be there.
        if (!empty($previous_role_records)) {
            list($insql, $inparams) = $DB->get_in_or_equal(array_keys($previous_role_records), SQL_PARAMS_NAMED);

            $select =
                "path_manual_id = :path_manual_id
                 AND role $insql";

            $params = array_merge(['path_manual_id' => $this->get_path_instance_id()], $inparams);

            $DB->delete_records_select('pathway_manual_role', $select, $params);
        }
    }

    /**
     * Determine whether there are any difference between the instance
     * and the stored values
     *
     * @return bool
     */
    protected function configuration_is_dirty(): bool {
        global $DB;

        if (empty($this->get_path_instance_id())) {
            // New instance is considered dirty
            return true;
        }

        // We want the role to be first in the select statement so that arrays are keyed that way.
        $previous_role_records = $DB->get_records('pathway_manual_role',
            ['path_manual_id' => $this->get_path_instance_id()],
            '',
            'role, id, path_manual_id');

        $has_changes = count($previous_role_records) != count($this->roles);
        if (!$has_changes) {
            // Compare one by one
            foreach ($this->roles as $role) {
                if (!isset($previous_role_records[$role])) {
                    $has_changes = true;
                    break;
                } else {
                    unset($previous_role_records[$role]);
                }
            }

            $has_changes = $has_changes || count($previous_role_records) > 0;
        }

        return $has_changes;
    }


    /**
     * Delete the pathway specific detail
     */
    protected function delete_configuration() {
        global $DB;

        $DB->delete_records('pathway_manual_role', ['path_manual_id' => $this->get_path_instance_id()]);
        $DB->delete_records('pathway_manual', ['id' => $this->get_path_instance_id()]);
        $this->set_path_instance_id(0);
    }

    /****************************************************************************
     * Getters and setters
     ****************************************************************************/

    /**
     * Return an array of valid roles.
     * Index is numeric to simulate unique ids
     * Starting at 1 to simulate db
     *
     * @return array
     */
    public static function get_all_valid_roles(): array {
        return [
            1 => self::ROLE_MANAGER,
            2 => self::ROLE_APPRAISER,
            3 => self::ROLE_SELF,
        ];
    }

    /**
     * Set roles
     *
     * @param string[] $roles - Array of role names (value should be the role name, key will be ignored).
     * @return $this
     * @throws \coding_exception
     */
    public function set_roles(array $roles): pathway {
        $this->roles = [];

        $valid_roles = array_flip(static::get_all_valid_roles());
        foreach ($roles as $role) {
            if (!isset($valid_roles[$role])) {
                throw new \coding_exception('Invalid role');
            }
            $this->roles[$role] = $role;
        }

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function get_roles() {
        return $this->roles;
    }


    /**
     * Return a summary of the criteria associated with this pathway.
     * The returned set will always contain only a single element with the roles
     * that may give a manual rating returned as items
     *
     * @return array List of summarized criteria objects associated with the pathway
     */
    public function get_summarized_criteria_set(): array {
        $result = new \stdClass();
        $result->item_type = $this->get_title();
        $result->items = array_map(function ($role) { return ucfirst($role); }, $this->get_roles());

        return [$result];
    }


    /*******************************************************************************************************
     * Data exporting
     *******************************************************************************************************/

    /**
     * Return the name of the template to use for editing this pathway
     *
     * @return string Template name
     */
    public function get_edit_template(): string {
        return 'pathway_manual/pathway_manual_edit';
    }

    /**
     * Return the name of the template to use for viewing this pathway
     *
     * @return string Template name
     */
    public function get_view_template(): string {
        return 'pathway_manual/pathway_manual';
    }

    /**
     * Get a short description of the content of this pathway
     *
     * @return string Short description
     */
    public function get_short_description(): string {
        return implode(
            ', ',
            array_map(
                function ($role) {
                    return ucfirst($role);
                },
                $this->roles
            )
        );
    }

    /**
     * Export detail for editing the pathway
     * This contains detail information
     *
     * @return array
     */
    public function export_edit_detail(): array {
        $result = $this->export_edit_overview();
        $result['roles'] = $this->export_roles();

        return $result;
    }

    /**
     * Export roles
     *
     * @return array containing 'id', role and name for all linked roles
     */
    public function export_roles(): array {
        // Picker works on ids which we simulate through the index of items returned by get_all_valid_roles
        $roleids = array_flip(static::get_all_valid_roles());

        return array_map(
            function ($role) use ($roleids) {
                return [
                    'id' => $roleids[$role],
                    'role' => $role,
                    'name' => ucfirst($role),
                ];
            },
            $this->roles
        );
    }
    /**
     * Export detail for viewing this pathway
     * This contains translated information ready for display only pages
     *
     * @return array
     */
    public function export_view_detail(): array {
        $result = [
            'title' => $this->get_title(),
            'items' => [],
            'name' => get_string('anyscalevalue', 'totara_competency'),
        ];

        foreach ($this->roles as $role) {
            $result['items'][] = ['name' => ucfirst($role)];
        }

        return $result;
    }

    /**
     * Retrieve the current configuration from the database
     *
     * @param ?int $id Instance id
     * @return \stdClass | null
     */
    public static function dump_pathway_configuration(?int $id = null) {
        global $DB;

        if (!is_null($id) && $result = $DB->get_record('pathway_manual', ['id' => $id])) {
            $result->roles = $DB->get_records('pathway_manual_role', ['path_manual_id' => $id]);
            return $result;
        }

        return null;
    }



    /**************************************************************************
     * User specific
     **************************************************************************/

    /**
     * Record a manual rating given for a user and competency.
     *
     * This will immediately trigger aggregation, which may update the user's pathway achievement.
     *
     * @param int $subject_id Id of the user who this rating applies to.
     * @param int $rater_id Id of user who gave this rating (would be same as subject id in case of self-rating).
     * @param string $as_role Machine-readable representation of role that rater acts in when giving this rating,
     *     e.g. self::ROLE_MANAGER.
     * @param int|null $scale_value_id
     * @param string $comment An optional comment can be provided by the user with this rating.
     */
    public function set_manual_value($subject_id, $rater_id, $as_role, ?int $scale_value_id, string $comment = '') {
        $rating = new rating();
        $rating->comp_id = $this->get_competency()->id;
        $rating->user_id = $subject_id;
        $rating->scale_value_id = $scale_value_id;
        $rating->date_assigned = time();
        $rating->assigned_by = $rater_id;
        $rating->comment = $comment;

        $roles = $this->get_roles_that_apply_to_user($subject_id, $rater_id);

        if (!isset($roles[$as_role])) {
            throw new \coding_exception('No permissioms');
        }
        $rating->assigned_by_role = $as_role;
        $rating->save();

        (new pathway_aggregator($this))->aggregate([$subject_id]);
    }

    /**
     * @param int $user_id
     * @return achievement_detail
     */
    public function aggregate_current_value(int $user_id): base_achievement_detail {
        /** @var null|rating $rating */
        $rating = rating::repository()
            ->where('comp_id', $this->get_competency()->id)
            ->where('user_id', $user_id)
            ->where('assigned_by_role', $this->get_roles())
            ->order_by('date_assigned', 'desc')
            ->first();

        $achievement_detail = new achievement_detail();
        $achievement_detail->add_rating($rating);

        return $achievement_detail;
    }

    public function get_roles_that_apply_to_user($subject_id, $rater_id) {

        $roles_that_apply = [];

        foreach ($this->roles as $role) {
            switch ($role) {
                case self::ROLE_MANAGER:
                    if (job_assignment::is_managing($rater_id, $subject_id)) {
                        $roles_that_apply[self::ROLE_MANAGER] = self::ROLE_MANAGER;
                    }
                    break;
                case self::ROLE_APPRAISER:
                    $job_assignments = job_assignment::get_all($subject_id);
                    foreach ($job_assignments as $job_assignment) {
                        if ($job_assignment->appraiserid == $rater_id) {
                            $roles_that_apply[self::ROLE_APPRAISER] = self::ROLE_APPRAISER;
                        }
                    }
                    break;
                case self::ROLE_SELF:
                    if ($rater_id == $subject_id) {
                        $roles_that_apply[self::ROLE_SELF] = self::ROLE_SELF;
                    }
                    break;
                default:
                    throw new \coding_exception('Unrecognised role');
            }
        }

        return $roles_that_apply;
    }

    public function can_set_manual_value($subject_id, $rater_id) {
        return !empty($this->get_roles_that_apply_to_user($subject_id, $rater_id));
    }
}
