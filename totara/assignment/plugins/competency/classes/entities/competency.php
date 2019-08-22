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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package tassign_competency
 */

namespace tassign_competency\entities;


use totara_assignment\entities\hierarchy_item;
use totara_assignment\user_groups;

/**
 * Class competency
 *
 * @property string $shortname Short name
 * @property string $description Competency description
 * @property string $idnumber External systems ID number
 * @property int $frameworkid Framework ID
 * @property string $path Competency path in the hierarchy
 * @property int $parentid Parent competency ID
 * @property bool $visible Visible flag
 * @property int $timecreated Time created
 * @property int $timemodified Time modified
 * @property int $usermodified User modified
 * @property string $fullname Full competency name
 * @property int $depthlevel Depth level in the hierarchy
 * @property int $typeid Competency type ID
 * @property string $sortthread Sortorder
 * @property bool $totarasync Totara sync flag
 *
 * @property int $aggregationmethod Aggregation method
 * @property int $proficiencyexpected Expected proficiency
 * @property int $evidencecount Evidence count
 *
 * @property-read array $assigned_user_groups
 * @property-read int $children_count
 * @property-read int $assignments_count
 * @property-read int $assign_availability
 * @property-read array $custom_fields
 *
 * @method static competency_repository repository()
 *
 * @package tassign_competency\entities
 */
class competency extends hierarchy_item {

    public const TABLE = 'comp';

    /**
     * If this is called this item will have a assigned_user_groups attribute loaded when to_array() is called
     *
     * @return $this
     */
    public function with_assigned_user_groups(): competency {
        return $this->add_extra_attribute('assigned_user_groups');
    }

    /**
     * @return array
     */
    public function get_assigned_user_groups_attribute(): array {
        $assignments = assignment::repository()
            ->where('competency_id', $this->id)
            ->select('*')
            ->with_user_group_name()
            ->get();

        $user_group_names = [];
        foreach ($assignments as $assignment) {
            $name = $assignment->user_group_name;
            if ($assignment->user_group_type == user_groups::USER) {
                $user_name_fields = totara_get_all_user_name_fields();
                $user = new \stdClass();
                foreach ($user_name_fields as $field) {
                    $user->$field = isset($assignment->$field) ? $assignment->$field : '';
                }
                $name = fullname($user);
            }
            $user_group_names[] = $name;
        };

        return $user_group_names;
    }

    /**
     * Retrieve assignment availability settings
     *
     * @return array Of assignment availability types
     */
    public function get_assign_availability_attribute(): array {
        // This is copied from totara/competency/classes/entities/competency.php
        // TODO sort out way to not have this duplicated
        global $DB;

        $sql =
            'SELECT availability
               FROM {comp_assign_availability}
              WHERE comp_id = :compid';
        return $DB->get_fieldset_sql($sql, ['compid' => $this->id]);
    }

    /**
     * Retrieve all custom field definitions and values for this competency
     *
     * @return array of customfield objects, the following is returned: type, title, value
     */
    public function get_custom_fields_attribute(): array {
        // This is copied from totara/competency/classes/entities/competency.php
        // TODO sort out way to not have this duplicated
        global $DB, $CFG;

        $customfields = [];

        $sql = "
            SELECT c.*, f.datatype, f.hidden, f.fullname, f.shortname
              FROM {comp_type_info_data} c
        INNER JOIN {comp_type_info_field} f
                ON c.fieldid = f.id
             WHERE c.competencyid = :compid
          ORDER BY f.sortorder";

        $cflds = $DB->get_records_sql($sql, ['compid' => $this->id]);

        // Now get the values
        if ($cflds) {
            foreach ($cflds as $cf) {
                // Don't show hidden custom fields.
                if ($cf->hidden) {
                    continue;
                }

                /** @var \customfield_base $cf_class */
                $cf_class = "customfield_{$cf->datatype}";
                require_once($CFG->dirroot.'/totara/customfield/field/'.$cf->datatype.'/field.class.php');

                $customfields[] = (object) [
                    'type' => $cf->datatype,
                    'title' => $cf->fullname,
                    'value' => $cf_class::display_item_data($cf->data, [
                        'prefix' => 'competency',
                        'itemid' => $cf->id,
                        'extended' => true,
                    ])
                ];
            }
        }

        return $customfields;
    }

}
