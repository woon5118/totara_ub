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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package criteria_childcompetency
 */

namespace criteria_childcompetency\watcher;

use criteria_childcompetency\childcompetency;
use totara_competency\hook\pathways_created;
use totara_competency\hook\pathways_updated;
use totara_competency\hook\pathways_deleted;
use totara_core\hook\base;
use totara_criteria\entities\criteria_item as item_entity;
use totara_criteria\entities\criterion as criterion_entity;
use totara_criteria\hook\criteria_validity_changed;

class competency {

    /**
     * @param pathways_created|pathways_updated|pathways_deleted $hook
     * @throws \coding_exception
     */
    public static function pathway_configuration_changed(base $hook) {
        if (!$hook instanceof pathways_created && !$hook instanceof pathways_updated && !$hook instanceof pathways_deleted) {
            throw new coding_exception('Expected pathways_created, pathways_updated or pathways_deleted hook');
        }

        $competency_id = $hook->get_competency_id();

        // Find parents with childcompetency criteria
        // Re-validate them and trigger criteria_validity_changed for applicable criteria

        $criteria = criterion_entity::repository()
            ->as('tc')
            ->join([item_entity::TABLE, 'tci'], 'tc.id', 'tci.criterion_id')
            ->where('tc.plugin_type', 'childcompetency')
            ->where('tci.item_type', 'competency')
            ->where('tci.item_id', $competency_id)
            ->get();

        $affected_criteria = [];
        foreach ($criteria as $criterion) {
            $parent = childcompetency::fetch_from_entity($criterion);
            $parent->validate();
            if ($parent->is_valid() != $criterion->valid) {
                $parent->save_valid();
                $affected_criteria[] = $criterion->id;
            }
        }

        if (!empty($affected_criteria)) {
            $hook = new criteria_validity_changed($affected_criteria);
            $hook->execute();
        }
    }

}
