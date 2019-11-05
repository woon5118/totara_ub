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
use core\orm\entity\entity;
use moodle_url;
use pathway_manual\entities\rating;
use pathway_manual\manual;
use totara_competency\entities\competency;
use user_picture;

defined('MOODLE_INTERNAL') || die();

/**
 * Class role_rating
 *
 * @package pathway_manual\models
 */
class role_rating {

    /**
     * @var competency
     */
    protected $competency;

    /**
     * @var user
     */
    protected $user;

    /**
     * @var string
     */
    protected $role;

    public function __construct(competency $competency, user $user, string $role) {
        manual::check_is_valid_role($role, true);

        $this->user = $user;
        $this->role = $role;
        $this->competency = $competency;
    }

    /**
     * @return string
     */
    public function get_role(): string {
        return $this->role;
    }

    /**
     * Does the logged in user have this role?
     *
     * @return bool
     */
    public function current_user_has_role(): bool {
        global $USER;
        return manual::user_has_role($this->user->id, $USER->id, $this->role);
    }

    /**
     * @return string
     */
    public function get_role_display_name(): string {
        if ($this->role == manual::ROLE_SELF) {
            if ($this->user->is_logged_in()) {
                return get_string('your_rating', 'pathway_manual');
            } else {
                return $this->user->fullname;
            }
        }

        return get_string('role_' . $this->role, 'pathway_manual');
    }

    /**
     * Get the default picture to display for this role.
     * Useful if there is no rating and therefore no user picture to show, so load a placeholder.
     *
     * @return moodle_url
     */
    public function get_default_picture(): moodle_url {
        global $PAGE, $OUTPUT;
        if ($this->role == manual::ROLE_SELF) {
            $user_picture = new user_picture((object) $this->user->to_array());
            $user_picture->size = 1; // Size f1.
            return $user_picture->get_url($PAGE);
        }

        return $OUTPUT->image_url('u/f1');
    }

    /**
     * Get the most recent rating made by a user with this role.
     *
     * @return rating|entity|null
     */
    public function get_latest_rating(): ?rating {
        return rating::repository()
            ->where('comp_id', $this->competency->id)
            ->where('user_id', $this->user->id)
            ->where('assigned_by_role', $this->role)
            ->order_by('date_assigned', 'desc')
            ->first();
    }

}
