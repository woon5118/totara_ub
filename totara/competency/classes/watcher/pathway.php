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
 * @package totara_competency
 */

namespace totara_competency\watcher;


use coding_exception;
use totara_competency\aggregation_users_table;
use totara_competency\hook\pathways_created;
use totara_competency\hook\pathways_deleted;
use totara_competency\hook\pathways_updated;
use totara_core\hook\base;

class pathway {

    /**
     * @param pathways_created|pathways_updated|pathways_deleted $hook
     * @throws \coding_exception
     */
    public static function pathway_configuration_changed(base $hook) {
        if (!$hook instanceof pathways_created && !$hook instanceof pathways_updated && !$hook instanceof pathways_deleted) {
            throw new coding_exception('Expected pathways_created, pathways_updated or pathways_deleted hook');
        }

        $competency_id = $hook->get_competency_id();
        (new aggregation_users_table())->queue_all_assigned_users_for_aggregation($competency_id);
    }

}
