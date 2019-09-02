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
 * @package criteria_coursecompletion
 */

namespace criteria_childcompetency;

use criteria_childcompetency\items_processor;
use hierarchy_competency\event\competency_created;
use hierarchy_competency\event\competency_deleted;
use hierarchy_competency\event\competency_moved;
use hierarchy_competency\event\competency_updated;
use totara_competency\entities\competency;
use totara_competency\pathway;

class observer {

    public static function competency_created(competency_created $event) {
        // Update the items fpr the new competency's parent
        $competency = new competency($event->objectid);
        if (!empty($competency->parentid)) {
            items_processor::update_items($competency->parentid);
        }
    }

    public static function competency_moved(competency_moved $event) {
        global $DB;

        // The event doesn't provide information on the previous parent.
        // We therefore need to find it through existing
        // childcompetency criteria with an item for this competency.
        static::update_items_of_item_competency($event->objectid);

        // If new parent is not top, update it's items as well
        $competency = new competency($event->objectid);
        if (!empty($competency->parentid)) {
            items_processor::update_items($competency->parentid);
        }
    }

    public static function competency_deleted(competency_deleted $event) {
        // The event doesn't provide information on the previous parent.
        // We therefore need to find it through existing
        // childcompetency criteria with an item for this competency.
        static::update_items_of_item_competency($event->objectid);
    }

    /**
     * Update the items of the competency that currently have a criteria_item
     * linked to this competency id
     * @param  int $comp_id The competency id
     */
    private static function update_items_of_item_competency(int $comp_id) {
        global $DB;

        // Although there should only be 1 competency that has this competency as child,
        // there may be more than 1 childcompetency criteria linked to this competency
        $sql =
            "SELECT DISTINCT cp.comp_id
               FROM {totara_criteria_item} tci
               JOIN {pathway_criteria_group_criterion} pcgc
                 ON pcgc.criterion_type = :criteriontype
                AND pcgc.criterion_id = tci.criterion_id
               JOIN {totara_competency_pathway} cp
                 ON cp.path_type = :pathtype
                AND cp.path_instance_id = pcgc.criteria_group_id
                AND cp.status = :activestatus
              WHERE tci.item_type = :itemtype
                AND tci.item_id = :compid";
        $params = [
            'criteriontype' => 'childcompetency',
            'pathtype' => 'criteria_group',
            'itemtype' => 'competency',
            'compid' => $comp_id,
            'activestatus' => pathway::PATHWAY_STATUS_ACTIVE,
        ];

        if ($comp_id = $DB->get_field_sql($sql, $params)) {
            items_processor::update_items($comp_id);
        }
    }
}