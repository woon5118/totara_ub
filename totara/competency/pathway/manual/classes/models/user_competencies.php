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

use context;
use core\entities\user;
use pathway_manual\data_providers\rateable_competencies;
use pathway_manual\manual;

/**
 * Class user_competencies
 *
 * @package pathway_manual\models
 */
class user_competencies {

    /**
     * @var rateable_competency[]
     */
    protected $competencies;

    /**
     * @var int
     */
    protected $count;

    /**
     * @var user
     */
    protected $user;

    /**
     * @var string
     */
    protected $role;

    /**
     * @param user $user
     * @param string $role
     * @param rateable_competency[] $competencies
     */
    public function __construct(user $user, string $role, array $competencies) {
        manual::check_is_valid_role($role, true);

        $this->user = $user;
        $this->role = $role;
        $this->competencies = $competencies;
    }

    /**
     * Get the scale groups.
     *
     * @return scale_group[]
     */
    public function get_scale_groups(): array {
        return scale_group::build_from_competencies($this->competencies);
    }

    /**
     * Get the total number of competencies available.
     *
     * @return int
     */
    public function get_count(): int {
        return count($this->competencies);
    }

    /**
     * Return the user that these are for if it is not for the current user.
     *
     * @return user
     */
    public function get_user_for(): user {
        return $this->user;
    }

    /**
     * Can the current user rate competencies for the specified user?
     *
     * @param user $for_user
     * @param context $context
     * @return bool
     */
    public static function can_rate_competencies(user $for_user, context $context) {
        if ($for_user->is_logged_in()) {
            $has_capability = has_capability('totara/competency:rate_own_competencies', $context);
        } else {
            $has_capability = has_capability('totara/competency:rate_other_competencies', $context);
        }

        return $has_capability && rateable_competencies::for_user($for_user)
                ->add_current_user_roles_filter()
                ->count();
    }

}
