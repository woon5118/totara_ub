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
use core\orm\entity\repository;
use totara_competency\entities\competency;
use totara_competency\entities\scale as scale_entity;

class scale extends entity_model {

    /**
     * Scale constructor. It's here for the purpose of type-hint
     *
     * @param scale_entity $entity
     */
    public function __construct(scale_entity $entity) {
        parent::__construct($entity);
    }

    /**
     * Load scale by ID
     *
     * @param int $id Ids to load scales by
     * @param bool $with_values A flag to load scale values
     * @return scale
     */
    public static function find_by_id(int $id, $with_values = true): self {
        return static::find_by_ids([$id], $with_values)->first();
    }

    /**
     * Load scales by IDs
     *
     * @param int[] $ids Ids to load scales by
     * @param bool $with_values A flag to load scale values
     * @return collection
     */
    public static function find_by_ids(array $ids, bool $with_values = true): collection {
        return scale_entity::repository()
            ->where('id', 'in', static::sanitize_ids($ids))
            ->when($with_values, function(repository $repository) {
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
     * @return collection
     */
    public static function find_by_competency_ids(array $ids, bool $with_values = false): collection {
       return static::find_by_ids((new collection(competency::repository()
            ->where('id', 'in', static::sanitize_ids($ids))
            ->with('scale')
            ->get()
            ->pluck('scale')))->pluck('id'), $with_values);
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
}