<?php
/**
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package engage_survey
 */
namespace engage_survey\webapi\resolver\query;

use core\webapi\execution_context;
use core\webapi\query_resolver;
use engage_survey\totara_engage\resource\survey;
use totara_core\advanced_feature;

/**
 * Class owner
 * @package engage_survey\webapi\resolver\query
 */
final class owner implements query_resolver {
    /**
     * @param array $args
     * @param execution_context $ec
     * @return \stdClass
     */
    public static function resolve(array $args, execution_context $ec): \stdClass {
        require_login();
        advanced_feature::require('engage_resources');

        $survey = survey::from_resource_id($args['resourceid']);
        $user_id = $survey->get_userid();

        return \core_user::get_user($user_id, '*', MUST_EXIST);
    }
}