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
 * @package totara_criteria
 */

namespace totara_criteria\task;

use core\task\scheduled_task;
use totara_competency\plugintypes;
use totara_criteria\criterion;
use totara_criteria\criterion_factory;
use totara_criteria\item_evaluator;

class evaluate_items extends scheduled_task {

    public function get_name() {
        return 'todo lang string coursecompletion evaluate';
    }

    public function execute() {
        $criteria_types = plugintypes::get_enabled_plugins('criteria', 'totara_criteria');

        /** @var item_evaluator[] $evaluators */
        $evaluators = [];

        foreach ($criteria_types as $criteria_type) {
            /** @var criterion $classname */
            $classname = criterion_factory::get_classname($criteria_type);
            $evaluators[] = $classname::item_evaluator();
        }

        $this->update_item_records($evaluators);
    }

    /**
     * @param item_evaluator[] $evaluators
     */
    public function update_item_records($evaluators) {
        // Some can use the same evaluator. But they only need to be run once.
        $evaluators = array_unique($evaluators);
        // Empty values represent a class that does not use an evaluator. Filter these out.
        $evaluators = array_filter($evaluators);

        foreach ($evaluators as $evaluator) {
            $evaluator::update_item_records();
        }
    }
}