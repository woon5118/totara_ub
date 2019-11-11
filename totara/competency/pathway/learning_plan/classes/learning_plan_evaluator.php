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

namespace pathway_learning_plan;


use totara_competency\pathway;
use totara_competency\pathway_evaluator;
use totara_competency\pathway_evaluator_user_source;

class learning_plan_evaluator extends pathway_evaluator {

    /**
     * @param pathway $pathway
     * @param pathway_evaluator_user_source $user_id_source
     */
    public function __construct(pathway $pathway, pathway_evaluator_user_source $user_id_source) {
        $learning_plan_user_source = $this->get_learning_plan_user_source($user_id_source);
        parent::__construct($pathway, $learning_plan_user_source);
    }

    /**
     * Instantiate and return a manual_user_source using the same user_id_source as the pathway
     *
     * @param pathway_evaluator_user_source $user_id_source
     * @return learning_plan_evaluator_user_source
     */
    private function get_learning_plan_user_source(pathway_evaluator_user_source $user_id_source): learning_plan_evaluator_user_source {
        return new learning_plan_evaluator_user_source($user_id_source->get_source(), $user_id_source->is_full_user_set());
    }

}
