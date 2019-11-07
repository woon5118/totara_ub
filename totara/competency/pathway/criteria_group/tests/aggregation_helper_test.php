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
 * @package pathway_criteria_group
 */

use totara_criteria\item_evaluator;
use pathway_criteria_group\criteria_group;
use totara_criteria\criterion;
use PHPUnit\Framework\MockObject\MockObject;
use totara_competency\entities\scale_value;
use totara_competency\pathway;
use pathway_criteria_group\aggregation_helper;

class pathway_criteria_group_aggregation_helper_testcase extends advanced_testcase {

    public function test_with_no_groups() {

        $pathways = aggregation_helper::get_pathways_containing_criterion_item(202, pathway::PATHWAY_STATUS_ACTIVE);
        $this->assertCount(0, $pathways);

        // Neither user nor criterion item exists.
        // Simply no pathways to update will be found and execution will continue without
        // throwing any exception.
        aggregation_helper::aggregate_based_on_item(101, 202);
    }

    public function test_with_group_and_mock_item() {
        global $DB;

        $item_id = 101;

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $competency = $competency_generator->create_competency();

        /** @var scale_value $scale_value */
        $scale_value = $competency->scale->sorted_values_high_to_low->first();

        $group = new criteria_group();

        /** @var criterion|MockObject $mock_criterion */
        $mock_criterion = $this->getMockForAbstractClass(criterion::class, [], 'mock_criteria');
        $mock_criterion->method('get_items_type')->willReturn('mock');
        $mock_criterion->add_items([$item_id]);
        $mock_criterion->save();
        class_alias(get_class($mock_criterion), 'criteria_mock_criteria\mock_criteria');
        totara_competency\plugintypes::enable_plugin('mock_criteria', 'criteria', 'totara_criteria');

        $criterion_item_id = $DB->get_field('totara_criteria_item', 'id', ['item_id' => $item_id]);

        $group->add_criterion($mock_criterion);
        $group->set_competency($competency);
        $group->set_scale_value($scale_value);
        $group->save();

        $pathways = aggregation_helper::get_pathways_containing_criterion_item($criterion_item_id, pathway::PATHWAY_STATUS_ACTIVE);
        $this->assertCount(1, $pathways);
    }
}