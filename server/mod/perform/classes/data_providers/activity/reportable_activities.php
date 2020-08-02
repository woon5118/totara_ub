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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\data_providers\activity;

use core\collection;
use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\models\activity\activity;
use mod_perform\state\activity\draft;
use mod_perform\util;

/**
 * Class element_identifier
 * @package mod_perform
 */
class reportable_activities {

    /**
     * @var collection
     */
    private $items = null;

    /**
     *
     * @return collection of activities
     */
    public function get(): collection {
        if (is_null($this->items)) {
            $this->fetch();
        }
        return $this->items;
    }

    /**
     *
     * @return reportable_activities this object.
     */
    public function fetch(): reportable_activities {
        //get reportable activities
        $this->items = $this->get_reportable_activities();

        return $this;
    }

    /**
     * Get Reportable activities
     *
     * @return collection
     */
    private function get_reportable_activities() :collection {
        global $USER;

        if (util::has_report_on_all_subjects_capability($USER->id)) {
            return activity_entity::repository()
                ->filter_by_visible()
                ->where_not_in('status', [draft::get_code()])
                ->order_by('id')
                ->get()
                ->map_to(activity::class);
        }

        // Early exit if they can not even potentially report on any subjects
        if (!has_capability_in_any_context('mod/perform:report_on_subject_responses')) {
            return new collection();
        }

        $reportable_users = util::get_permitted_users($USER->id, 'mod/perform:report_on_subject_responses');

        return activity_entity::repository()->find_by_subject_user_id(...$reportable_users)->map_to(activity::class);
    }
}