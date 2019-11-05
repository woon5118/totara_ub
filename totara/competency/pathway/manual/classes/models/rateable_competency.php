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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package pathway_manual
 */

namespace pathway_manual\models;

use core\entities\user;
use pathway_manual\manual;
use totara_competency\entities\assignment;
use totara_competency\entities\competency;

/**
 * Class rateable_competency
 *
 * @package pathway_manual\models
 */
class rateable_competency {

    /**
     * @var competency
     */
    protected $entity;

    /**
     * @var user
     */
    protected $user;

    /**
     * @var string
     */
    protected $role;

    /**
     * @param competency $entity
     * @param user $user
     * @param string|null $role Can optionally specify the role that will rate this competency.
     */
    public function __construct(competency $entity, user $user, string $role = null) {
        $this->entity = $entity;
        $this->user = $user;

        if (isset($role)) {
            manual::check_is_valid_role($role, true);
            $this->role = $role;
        }
    }

    /**
     * Get the competency entity.
     *
     * @return competency
     */
    public function get_entity(): competency {
        return $this->entity;
    }

    /**
     * Get the competency from the competency assignment.
     *
     * @param assignment $assignment
     * @param user $user
     * @return rateable_competency
     */
    public static function for_assignment(assignment $assignment, user $user): self {
        return new static($assignment->competency, $user);
    }

    /**
     * Get the ratings by each role for this competency and user.
     *
     * @return role_rating[]
     */
    public function get_all_role_ratings(): array {
        $roles = manual::get_roles_for_competency($this->entity);

        $role_ratings = [];
        foreach ($roles as $role) {
            $role_ratings[] = new role_rating($this->entity, $this->user, $role);
        }
        return $role_ratings;
    }

}
