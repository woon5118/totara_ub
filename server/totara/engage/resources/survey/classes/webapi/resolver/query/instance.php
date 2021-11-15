<?php
/**
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package engage_survey
 */
namespace engage_survey\webapi\resolver\query;

use core\webapi\execution_context;
use core\webapi\middleware\require_advanced_feature;
use core\webapi\middleware\require_login;
use core\webapi\query_resolver;
use core\webapi\resolver\has_middleware;
use engage_survey\totara_engage\resource\survey;
use totara_engage\access\access_manager;

final class instance implements query_resolver, has_middleware {
    /**
     * @param array             $args
     * @param execution_context $ec
     *
     * @return survey
     */
    public static function resolve(array $args, execution_context $ec): survey {
        global $USER;
        if (!$ec->has_relevant_context()) {
            $ec->set_relevant_context(\context_user::instance($USER->id));
        }

        /** @var survey $suvey */
        $survey = survey::from_resource_id($args['resourceid']);

        if (!access_manager::can_access($survey, $USER->id)) {
            throw new \coding_exception("User with id '{$USER->id}' does not have access to this survey");
        }

        return $survey;
    }

    /**
     * @inheritDoc
     */
    public static function get_middleware(): array {
        return [
            new require_login(),
            new require_advanced_feature('engage_resources'),
        ];
    }

}