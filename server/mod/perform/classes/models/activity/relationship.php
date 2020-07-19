<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\activity;

use core\orm\entity\model;
use totara_core\entities\relationship as core_relationship_entity;
use totara_core\relationship\relationship as core_relationship_model;
use totara_core\relationship\resolvers\subject;

/**
 * A thin wrapper around core relationships, which includes mod_perform specific fields such as is_subject.
 */
class relationship extends model {

    /**
     * @var core_relationship_entity
     */
    protected $entity;

    protected $model_accessor_whitelist = [
        'core_relationship',
        'is_subject',
    ];

    /**
     * @param core_relationship_entity $core_relationship_entity
     */
    public function __construct(core_relationship_entity $core_relationship_entity) {
        parent::__construct($core_relationship_entity);
    }

    /**
     * @inheritDoc
     */
    protected static function get_entity_class(): string {
        return core_relationship_entity::class;
    }

    /**
     * Get the core relationship model instance.
     *
     * @return core_relationship_model
     */
    public function get_core_relationship(): core_relationship_model {
        return new core_relationship_model($this->entity);
    }

    /**
     * Is this relationship a "subject".
     *
     * @return bool
     */
    public function get_is_subject(): bool {
        $resolvers = $this->get_core_relationship()->get_resolvers();

        // Exclude potential combination relationships (subject/appraiser).
        if (count($resolvers) !== 1) {
            return false;
        }

        return reset($resolvers) === subject::class;
    }

}