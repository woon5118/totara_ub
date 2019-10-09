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

namespace totara_competency\entities;

use core\collection;
use core\orm\entity\relations\has_many;
use core\orm\entity\relations\has_one;
use totara_competency\achievement_configuration;
use totara_assignment\entities\hierarchy_item;

// Currently only required to re-use the constants
require_once($CFG->dirroot.'/totara/hierarchy/prefix/competency/lib.php');

/**
 * Todo: Combine with assignments implementation of this and move somewhere central.
 * THIS IS A COPY OF THE ASSIGNMENT COMPETENCY WITH ASSIGNMENT STUFF TAKEN OUT AND SCALES ADDED IN.
 *
 * Class competency
 *
 * @property int $aggregationmethod Aggregation method
 * @property int $proficiencyexpected Expected proficiency
 * @property int $evidencecount Evidence count
 *
 * @property-read int $id ID
 * @property string $shortname
 * @property string $description
 * @property string $idnumber
 * @property int $frameworkid
 * @property int $path
 * @property int $parentid
 * @property bool $visible
 * @property int $timecreated
 * @property int $timemodified
 * @property int $usermodified
 * @property string $fullname
 * @property int $depthlevel
 * @property int $typeid
 * @property string $sortthread
 * @property int $totarasync
 *
 *
 * @property-read scale $scale Scale associated with this competency
 * @property-read comp_type $comp_type Competency type
 * @property-read customfields $customfields Custom fields
 * @property-read string $scale_aggregation_type Scale aggregation type
 * @property-read int[] $assign_availability Assignment creation availabilities
 *
 * @property-read competency_achievement $achievement
 * @property-read collection $availability
 * @property-read pathway[] $pathways
 *
 * @package tassign_competency\resources
 */
class competency extends hierarchy_item {

    public const TABLE = 'comp';

    public const ASSIGNMENT_CREATE_SELF = \competency::ASSIGNMENT_CREATE_SELF;
    public const ASSIGNMENT_CREATE_OTHER = \competency::ASSIGNMENT_CREATE_OTHER;

    /**
     * @var scale $scale
     */
    private $scale;

    /** @var comp_type $comp_type*/
    private $comp_type;

    /** @var array $customfields */
    private $customfields;

    /** @var array $linkedcourses */
    private $linkedcourses;

    /** @var string $scale_aggregation_type */
    private $scale_aggregation_type;

    /**
     * Related achievement, meant to be used with a user filter
     *
     * @return has_one
     */
    public function achievement(): has_one {
        return $this->has_one(competency_achievement::class, 'comp_id')
            ->where_in('status', [competency_achievement::ACTIVE_ASSIGNMENT, competency_achievement::ARCHIVED_ASSIGNMENT]);
    }

    public function get_scale_attribute(): scale {
        global $DB;

        if (!isset($this->scale)) {
            $sql = "
                SELECT scale.*
                  FROM {comp_scale_assignments} sa,
                       {comp_scale} scale
                 WHERE sa.scaleid = scale.id
                   AND sa.frameworkid = :fwid";
            $record = $DB->get_record_sql($sql, ['fwid' => $this->frameworkid]);

            // Todo: Will have to centralise the scale_provider as well if doing this.
            // Could alternatively add a get_scale_id() or add to scale_provider a get_scale_for_competency method.
            $this->scale = new scale($record);
        }

        return $this->scale;
    }

    // Todo: take this away once competency has been moved. Overriding a method in hierarchy_item which doesn't work otherwise due to namespace.
    public static function get_framework_class(): ?string {
        return 'tassign_competency\entities\competency_framework';
    }

    public function get_comp_type_attribute(): ?comp_type {
        // Not caching for now - not needed during aggregation
        if (empty($this->comp_type) && !empty($this->typeid)) {
            $this->comp_type = new comp_type($this->typeid);
        }

        return $this->comp_type;
    }


    // TODO: Move to own resources???

    /**
     * Retrieve all custom field definitions and values for this competency
     *
     * @return array of customfield objects, the following is returned: type, title, value
     */
    public function get_custom_fields_attribute(): array {
        global $DB, $CFG;

        if (is_null($this->customfields)) {
            $this->customfields = [];

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

                    $cf_class = "customfield_{$cf->datatype}";
                    require_once($CFG->dirroot.'/totara/customfield/field/'.$cf->datatype.'/field.class.php');
                    $this->customfields[] = (object)[
                        'type' => $cf->datatype,
                        'title' => $cf->fullname,
                        'value' => call_user_func(
                            [$cf_class, 'display_item_data'],
                            $cf->data,
                            ['prefix' => 'comp', 'itemid' => $cf->id, 'extended' => true]
                        )
                    ];
                }
            }
        }

        return $this->customfields;
    }

    /**
     * Retrieve scale_aggregation
     *
     * @return ?string Scale aggregation type
     */
    public function get_scale_aggregation_type_attribute(): ?string {
        global $DB;

        if (is_null($this->scale_aggregation_type)) {
            $this->scale_aggregation_type = $DB->get_field('totara_competency_scale_aggregation',
                'type',
                ['comp_id' => $this->id]);
        }

        return $this->scale_aggregation_type;
    }

    /**
     * Assignment availability related model
     *
     * @return has_many
     */
    public function availability(): has_many {
        return $this->has_many(assignment_availability::class, 'comp_id');
    }

    /**
     * Retrieve assignment availability settings
     *
     * @return array Of assignment availability types
     */
    protected function get_assign_availability_attribute(): array {
        global $DB;

        $sql =
            'SELECT availability
               FROM {comp_assign_availability}
              WHERE comp_id = :compid';
        return $DB->get_fieldset_sql($sql, ['compid' => $this->id]);
    }

    /**
     * Configured pathways for this competency
     *
     * @return has_many
     */
    public function pathways(): has_many {
        return $this->has_many(pathway::class, 'comp_id');
    }

}
