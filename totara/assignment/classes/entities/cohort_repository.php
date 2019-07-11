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

namespace totara_assignment\entities;


use totara_assignment\entities\traits\has_visible_filter;
use totara_assignment\filter\basket;
use totara_assignment\filter\visible;
use core\orm\entity\entity_repository;
use core\orm\query\field;
use core\orm\entity\filter\in;
use core\orm\entity\filter\like;

/**
 * @package tassign_competency\entities
 */
class cohort_repository extends entity_repository {

    use has_visible_filter;

    public function get_default_filters(): array {
        return [
            'basket' => new basket(),
            'visible' => new visible(),
            'text' => new like([
                new field('name', $this->builder),
                new field('description', $this->builder),
                new field('idnumber', $this->builder),
            ]),
            'ids' => new in('id')
        ];
    }

    /**
     * Select only limited subset of columns intended to be used with picker dialogue
     *
     * @return $this
     */
    public function select_only_fields_for_picker() {
        $this->add_select([
            'id',
            'name',
            'description',
            'idnumber',
        ]);

        return $this;
    }

}
