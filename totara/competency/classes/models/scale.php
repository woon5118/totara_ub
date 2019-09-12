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

use coding_exception;
use core\orm\collection;
use core\orm\entity\repository;
use core\orm\query\builder;
use core\orm\query\subquery;
use totara_competency\entities\scale as scale_entity;
use totara_competency\entities\scale_value;

class scale {

    /**
     * @var scale_entity
     */
    protected $entity;

    /**
     * @var collection
     */
    protected $values = null;

    public function __construct(scale_entity $entity) {
        if (!$entity->exists()) {
            throw new coding_exception('Can load only existing entities');
        }

        $this->entity = $entity;
    }

    public static function find_by_id(array $id, $with_values = true): self {
        $model = new static(static::scale_repository()->find_or_fail($id));

        if ($with_values) {
            $model->load_values();
        }

        return $model;
    }

    public static function find_by_ids(array $ids, $with_values = true): collection {

        $ids = static::sanitize_ids($ids);

        $scales = static::scale_repository()->where('id', $ids)->get();

        $values = $with_values ? static::preload_values($scales) : new collection();

        $models = $scales->map(function(scale_entity $scale) use ($with_values, $values) {
            $model = new static($scale);

            if ($with_values) {
                static::assign_preloaded_values($model, $values);
            }

            return $model;
        });

        return $models;
    }

    public static function find_by_competency_ids(array $ids): collection {
        // We'll need to fetch required scales


        $subquery = builder::table('comp')
            ->select_raw('distinct frameworkid')
            ->where('id', static::sanitize_ids($ids));

        $scale_ids = static::scale_repository()
            ->select('id')
            ->join('comp_scale_assignments', 'id', 'scaleid')
            ->where('comp_scale_assignments.frameworkid', 'in', $subquery)
            ->get()
            ->pluck('id');

        return static::find_by_ids($scale_ids);
    }

    public static function find_by_competency_id(int $id, $with_values = true): self {
        $model = new static(static::scale_repository()
            ->join('comp_scale_assignments', 'id', 'scaleid')
            ->join('comp', 'comp_scale_assignments.frameworkid', 'frameworkid')
            ->where('comp.id', $id)
            ->one(true));

        if ($with_values) {
            $model->load_values();
        }

        return $model;
    }

    protected static function scale_repository(): repository {
        return scale_entity::repository()
            ->select('*')
            ->add_select((new subquery(function(builder $builder) {
                $builder->from('comp_scale_values')
                    ->select('id')
                    ->where_field('scaleid', 'comp_scale.id')
                    ->where('proficient', 1)
                    ->when(true, function (repository $repository) {
                        $subquery = builder::table('comp_scale_values')
                            ->select('max(sortorder)')
                            ->where_field('scaleid', 'comp_scale.id')
                            ->where('proficient', 1);

                        $repository->where('sortorder', $subquery);
                    });
            }))->as('min_proficient_value_id'));
    }

    protected function load_values() {
        $this->values = scale_value::repository()
            ->where('scaleid', $this->get_id())
            ->order_by('sortorder', 'desc')
            ->get();

        return $this;
    }

    protected static function preload_values(collection $scales) {
        return scale_value::repository()
            ->where('scaleid', $scales->pluck('id'))
            ->order_by('sortorder', 'desc')
            ->get();
    }

    protected static function assign_preloaded_values(self $scale, collection $values) {
        $scale->values = $values->filter('scaleid', $scale->get_id());

        return $scale;
    }

    public function get_id(): int {
        return $this->entity->id;
    }

    public function to_array(): array {
        return $this->entity->to_array();
    }

    public function __get($name) {

        if ($name == 'values') {
            return $this->values ?? new collection();
        }

        return $this->entity->get_attribute($name);
    }

    public function has_attribute($name) {

        if ($name == 'values') {
            return !! ($this->values ?? false);
        }

        return $this->entity()->has_attribute($name);
    }

    public function entity(): scale_entity {
        return $this->entity;
    }

    protected static function sanitize_ids(array $ids): array {
        return array_unique(array_filter(array_map('intval', $ids), function ($id) {
            return $id > 0;
        }));
    }
}