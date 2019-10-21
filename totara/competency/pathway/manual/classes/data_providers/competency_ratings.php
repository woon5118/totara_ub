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

namespace pathway_manual\data_providers;

use core\orm\query\builder;
use pathway_manual\manual;
use pathway_manual\models\role_rating;
use tassign_competency\entities\assignment;
use totara_competency\entities\pathway;

defined('MOODLE_INTERNAL') || die();

class competency_ratings {

    /**
     * @var int
     */
    protected $comp_id;

    /**
     * @var int
     */
    protected $user_id;

    public function __construct(int $competency_id, int $user_id) {
        $this->comp_id = $competency_id;
        $this->user_id = $user_id;
    }

    /**
     * Get the competency from the competency assignment.
     *
     * @param int $assignment_id
     * @param int $user_id
     * @return competency_ratings
     */
    public static function for_assignment(int $assignment_id, int $user_id): self {
        return new static((new assignment($assignment_id))->competency_id, $user_id);
    }

    /**
     * Get the roles available for the competency.
     *
     * @return string[]
     */
    protected function get_roles(): array {
        $role_order = [
            manual::ROLE_SELF,
            manual::ROLE_MANAGER,
            manual::ROLE_APPRAISER,
        ];

        $roles = builder::table('pathway_manual_role')
            ->select_raw('DISTINCT role')
            ->join([pathway::TABLE, 'pathway'], 'path_manual_id', 'pathway.path_instance_id')
            ->where('pathway.comp_id', $this->comp_id)
            ->get()
            ->pluck('role');

        // Sorts the roles
        return array_intersect($role_order, array_values($roles));
    }

    /**
     * Get the most recent ratings made for the roles applicable for this competency and user.
     *
     * @return role_rating[]
     */
    public function fetch_role_ratings(): array {
        $role_ratings = [];
        foreach ($this->get_roles() as $role) {
            $role_ratings[] = new role_rating($this->comp_id, $this->user_id, $role);
        }
        return $role_ratings;
    }

}
