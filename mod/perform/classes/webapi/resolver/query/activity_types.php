<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTDvs
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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
 * @author  Murali Nair <murali.nair@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\webapi\resolver\query;

use \context_coursecat;
use core\webapi\execution_context;
use core\webapi\query_resolver;

use mod_perform\data_providers\activity\activity_type;
use mod_perform\util;

use totara_core\advanced_feature;

defined('MOODLE_INTERNAL') || die();

/**
 * Handles the "mod_perform_activity_types" GraphQL query.
 */
class activity_types implements query_resolver {
    /**
     * {@inheritdoc}
     */
    public static function resolve(array $args, execution_context $ec) {
        self::authorize($ec);

        return (new activity_type())
            ->get();
    }

    /**
     * Checks whether the user is authenticated and sets the correct context
     * for the graphql execution.
     *
     * @param execution_context $ec graphql execution context to update.
     */
    private static function authorize(execution_context $ec): void {
        advanced_feature::require('performance_activities');
        require_login(null, false, null, false, true);

        $category_id = util::get_default_category_id();
        $context = context_coursecat::instance($category_id);
        require_capability('mod/perform:view_manage_activities', $context);

        $ec->set_relevant_context($context);
    }
}
