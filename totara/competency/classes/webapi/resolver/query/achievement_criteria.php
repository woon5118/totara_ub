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

namespace totara_competency\webapi\resolver\query;

use core\webapi\execution_context;
use totara_competency\achievement_configuration;
use totara_competency\entities\competency;
use totara_core\advanced_feature;
use totara_core\feature_not_available_exception;

/**
 * Query to return all achievement criteria related information for a competency
 */
class achievement_criteria implements \core\webapi\query_resolver {
    /**
     * Returns the achivement configuration for a specific competency.
     *
     * @param array $args
     * @param execution_context $ec
     * @return \stdClass
     */
    public static function resolve(array $args, execution_context $ec) {
        advanced_feature::require('perform');

        // TODO: More capability checks
        // TL-21305 will find a better, encapsulated solution for require_login calls.
        require_login(null, false,null, false, true);

        /** @var competency $competency */
        $competency = new competency($args['competency_id']);
        return new achievement_configuration($competency);
    }

}
