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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com
 * @package totara_competency
 */

namespace totara_competency\models;

use core\orm\collection;
use core\orm\entity\model;
use core\orm\entity\repository;
use totara_competency\entities\competency;
use totara_competency\entities\competency_achievement;
use totara_competency\entities\scale as scale_entity;
use totara_competency\entities\scale_assignment;
use totara_core\advanced_feature;

/**
 * Class scale
 * This is a model that represent a scale
 *
 * @property-read int $id
 * @property-read string $name
 * @property-read string $description
 * @property-read int $timemodified
 * @property-read int $usermodified
 * @property-read int $defaultid
 * @property-read int $minproficiencyid
 * @property-read collection $values
 * @property scale_entity $entity
 *
 * @package totara_competency\models
 */
class scale extends model {

    /**
     * Scale constructor. It's here for the purpose of type-hint
     *
     * @param scale_entity $entity
     */
    public function __construct(scale_entity $entity) {
        parent::__construct($entity);
    }

    public static function get_entity_class(): string {
        return scale_entity::class;
    }

    /**
     * Load scale by ID including values
     *
     * @param int $id Ids to load scales by
     * @return scale
     */
    public static function load_by_id_with_values(int $id): self {
        return static::load_by_ids([$id], true)->first();
    }

    /**
     * Load scales by IDs
     *
     * @param int[] $ids Ids to load scales by
     * @param bool $with_values A flag to load scale values
     * @return collection
     */
    public static function load_by_ids(array $ids, bool $with_values = true): collection {
        return scale_entity::repository()
            ->where('id', 'in', static::sanitize_ids($ids))
            ->when($with_values, function (repository $repository) {
                $repository->with('values');
            })
            ->get()
            ->transform_to(static::class);
    }

    /**
     * Load scales by competency ids
     *
     * @param int[] $ids Competency IDs
     * @param bool $with_values A flag to load scale values
     * @return collection|array
     */
    public static function find_by_competency_ids(array $ids, bool $with_values = false): collection {
        $scales = competency::repository()
            ->where('id', 'in', static::sanitize_ids($ids))
            ->with('scale')
            ->get()
            ->pluck('scale');

        $scales = new collection($scales);
        return static::load_by_ids($scales->pluck('id'), $with_values);
    }

    /**
     * Load scales by competency id
     *
     * @param int $id Competency ID
     * @param bool $with_values A flag to load scale values
     * @return scale
     */
    public static function find_by_competency_id(int $id, bool $with_values = true): ?self {
        return static::find_by_competency_ids([$id], $with_values)->first();
    }

    /**
     * Checks if a scale is used in the system. A scale is used if there are any
     * achievement records or it's been given a value in a learning plan
     *
     * @return bool
     */
    public function is_in_use(): bool {
        $has_achievement = competency_achievement::repository()
            ->where_not_null('scale_value_id')
            ->join(['comp', 'c'], 'competency_id', 'id')
            ->join(['comp_scale_assignments', 'sca'], 'c.frameworkid', 'sca.frameworkid')
            ->where('sca.scaleid', $this->id)
            ->exists();

        $used_in_lps = false;
        if (advanced_feature::is_enabled('learningplans')) {
            global $CFG;
            require_once($CFG->dirroot.'/totara/plan/components/competency/competency.class.php');

            $used_in_lps = \dp_competency_component::is_competency_scale_used($this->id);
        }

        return $has_achievement || $used_in_lps;
    }

    /**
     * Checks if a scale is assigned to any framework
     *
     * @return bool
     */
    public function is_assigned(): bool {
        return scale_assignment::repository()
            ->where('scaleid', $this->id)
            ->exists();
    }

    /**
     * Filter out bad ids if any
     *
     * @param array $ids
     * @return array
     */
    protected static function sanitize_ids(array $ids): array {
        return array_unique(array_filter(array_map('intval', $ids), function ($id) {
            return $id > 0;
        }));
    }

    /**
     * Get all entity properties
     *
     * @return array
     */
    public function to_array(): array {
        return $this->entity->to_array();
    }
}
