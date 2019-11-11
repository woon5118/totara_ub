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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package pathway_criteria_group
 */

namespace pathway_criteria_group\observer;

use pathway_criteria_group\aggregation_helper;
use totara_criteria\event\criteria_achievement_changed;

class criteria {

    /**
     * Listen to the criteria_achievement_changed event and mark all users for reaggregation affected
     *
     * @param criteria_achievement_changed $event
     */
    public static function criteria_achievement_changed(criteria_achievement_changed $event) {
        aggregation_helper::mark_for_reaggregate_from_criteria($event->other['criteria_ids'], $event->relateduserid);
    }
}
