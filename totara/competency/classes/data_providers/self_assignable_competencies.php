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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency\data_providers;

use core\orm\collection;
use core\orm\cursor;
use core\orm\cursor_paginator;
use totara_competency\entities\competency as competency_entity;
use totara_competency\entities\filters\competency_user_assignment_status;
use totara_competency\entities\filters\competency_user_assignment_type;
use totara_competency\models\assignment;
use totara_competency\models\assignment_user;
use core\entities\user;
use totara_competency\models\self_assignable_competency;

class self_assignable_competencies extends user_data_provider {

    private $order_by;

    private $order_dir;

    /**
     * @param string $order_by
     * @param string $order_dir
     * @return $this
     */
    public function set_order(?string $order_by, ?string $order_dir) {
        $this->order_by = strtolower($order_by ?? 'name');
        $this->order_dir  = strtolower($order_dir ?? 'asc');

        return $this;
    }

    /**
     * @param array $filters
     * @return $this|user_data_provider
     */
    public function set_filters(array $filters) {
        // By default filter for visible only
        $filters['visible'] = true;

        // For self assignable competencies we need to override
        // the assignment_type and assignment_status filters as they do need
        // to be user based
        if (isset($filters['assignment_type'])) {
            $filters['assignment_type'] = (new competency_user_assignment_type($this->user->id))
                ->set_value($filters['assignment_type']);
        }

        if (isset($filters['assignment_status'])) {
            $filters['assignment_status'] = (new competency_user_assignment_status($this->user->id))
                ->set_value($filters['assignment_status']);
        }

        if (array_key_exists('framework', $filters) && is_null($filters['framework'])) {
            unset($filters['framework']);
        }

        return parent::set_filters($filters);
    }

    /**
     * @param cursor|null $cursor if null is passed the default limit is applied
     * @return array
     */
    public function fetch_paginated(?cursor $cursor = null): array {
        $repo = competency_entity::repository()
            ->set_filters($this->filters);

        if ($this->is_logged_in_user()) {
            $repo->filter_by_self_assignable($this->user->id);
        } else {
            $repo->filter_by_other_assignable($this->user->id);
        }

        /** @var collection $competencies */
        $query = $repo
            ->set_filters($this->filters)
            ->order_by($this->order_by, $this->order_dir);

        $paginator = new cursor_paginator($query, $cursor);

        $assignments = (new assignment_user($this->user->id))
            ->get_active_assignments_for_competencies($paginator->get_items()->pluck('id'));

        $paginator->get_items()->transform(function ($item) {
            return self_assignable_competency::load_by_entity($item);
        });

        $this->combine_competencies_with_assignments($assignments, $paginator);

        return $paginator->get();
    }

    private function combine_competencies_with_assignments(collection $assignments, cursor_paginator $paginator): void {
        // Now combine competencies and the user assignments
        if ($assignments->count() > 0) {
            foreach ($paginator as $competency) {
                $user_assignments = $assignments->filter('competency_id', $competency->get_id());
                if ($user_assignments->count() > 0) {
                    $user_assignments->transform(function ($assignment_entity) {
                        return assignment::load_by_entity($assignment_entity);
                    });
                    $competency->set_user_assignments($user_assignments);
                }
            }
        }
    }

    public function is_logged_in_user(): bool {
        return $this->user->id === user::logged_in()->id;
    }

}