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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package pathway_manual
 */

namespace pathway_manual\models;

use context_user;
use core\entities\user;
use core\orm\query\builder;
use pathway_manual\entities\rating as rating_entity;
use pathway_manual\entities\role;
use pathway_manual\manual;
use totara_competency\aggregation_users_table;
use totara_competency\entities\competency;
use totara_competency\entities\pathway;

defined('MOODLE_INTERNAL') || die();

/**
 * Class rating
 *
 * @package pathway_manual\models
 */
class rating {

    /**
     * @var int
     */
    protected $subject_user_id;

    /**
     * @var string
     */
    protected $role;

    /**
     * @param int $subject_user_id
     * @param string $role
     */
    public function __construct(int $subject_user_id, string $role) {
        $this->subject_user_id = $subject_user_id;
        $this->role = $role;
        manual::check_is_valid_role($role, true);
    }

    /**
     * @param int $subject_user_id
     * @param string $role
     * @return rating
     */
    public static function for_user_and_role(int $subject_user_id, string $role): self {
        return new static($subject_user_id, $role);
    }

    /**
     * Create a new manual rating made by the logged in user.
     *
     * @param int $competency_id
     * @param int|null $scale_value_id
     * @param string $comment
     */
    public function create(
        int $competency_id,
        ?int $scale_value_id,
        string $comment = ''
    ) {
        $this->create_multiple([
            ['comp_id' => $competency_id, 'scale_value_id' => $scale_value_id, 'comment' => $comment]
        ]);
    }

    /**
     * Validate and create multiple ratings made by the logged in user.
     *
     * @param array $ratings
     */
    public function create_multiple(array $ratings) {
        $this->require_can_rate_user();
        self::validate_scale_values_for_competencies(
            array_combine(array_column($ratings, 'comp_id'), array_column($ratings, 'scale_value_id'))
        );
        $this->validate_role_for_competencies(array_column($ratings, 'comp_id'));

        $rating_record = [
            'user_id' => $this->subject_user_id,
            'date_assigned' => time(),
            'assigned_by' => user::logged_in()->id,
            'assigned_by_role' => $this->role,
        ];

        builder::get_db()->transaction(function () use ($ratings, $rating_record) {
            $queue_data = [];
            foreach ($ratings as $rating) {
                $rating_entity = new rating_entity(array_merge($rating_record, $rating));
                $rating_entity->save();
                $queue_data[] = [
                    'user_id' => $rating_record['user_id'],
                    'competency_id' => $rating['comp_id']
                ];
            }
            (new aggregation_users_table())->queue_multiple_for_aggregation($queue_data);
        });
    }

    /**
     * Validate that the logged in user can rate for the given user and role.
     */
    private function require_can_rate_user() {
        // Check capability.
        $capability = self::is_logged_in_user($this->subject_user_id)
            ? 'totara/competency:rate_own_competencies'
            : 'totara/competency:rate_other_competencies';
        require_capability($capability, context_user::instance($this->subject_user_id));

        // Check user's role.
        if (!manual::user_has_role($this->subject_user_id, user::logged_in()->id, $this->role)) {
            throw new \coding_exception("You do not have the role {$this->role} for user {$this->subject_user_id}");
        }
    }

    /**
     * Validate that the role is enabled for the given competencies.
     *
     * @param array $competency_ids
     * @return bool
     */
    public function validate_role_for_competencies(array $competency_ids): bool {
        $competency_ids_with_role = role::repository()
            ->select('pathway.comp_id')
            ->join([pathway::TABLE, 'pathway'], 'path_manual_id', 'pathway.path_instance_id')
            ->where_in('pathway.comp_id', $competency_ids)
            ->where('role', $this->role)
            ->get()
            ->pluck('comp_id');

        foreach ($competency_ids as $competency_id) {
            if (!in_array($competency_id, $competency_ids_with_role)) {
                throw new \coding_exception("Role {$this->role} cannot rate for competency {$competency_id}");
            }
        }
        return true;
    }

    /**
     * Validate for a list of competency / scale value pairs that each scale value is valid for the respective competency.
     *
     * @param array $comp_scale_map
     * @return bool
     */
    public static function validate_scale_values_for_competencies(array $comp_scale_map): bool {
        /** @var competency[] $competencies */
        $competencies = competency::repository()
            ->with('scale.values')
            ->where_in('id', array_keys($comp_scale_map))
            ->get()
            ->all(true);
        foreach ($comp_scale_map as $competency_id => $scale_value_id) {
            if (empty($competencies[$competency_id]) || !$competencies[$competency_id]->visible) {
                throw new \coding_exception('Non-existent or invisible competency id given.');
            }
            // Null value is valid.
            if (is_null($scale_value_id)) {
                continue;
            }
            if (!$competencies[$competency_id]->scale->values->find('id', $scale_value_id)) {
                throw new \coding_exception("Invalid scale value {$scale_value_id} for competency {$competency_id}");
            }
        }
        return true;
    }

    protected static function is_logged_in_user(int $user_id): bool {
        return user::logged_in()->id === $user_id;
    }
}
