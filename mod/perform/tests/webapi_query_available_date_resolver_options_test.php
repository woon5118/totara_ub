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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 * @category test
 */

use core\webapi\execution_context;
use mod_perform\dates\resolvers\dynamic\resolver_option;
use mod_perform\models\activity\track;
use mod_perform\models\activity\track_assignment as track_assignment_model;
use mod_perform\webapi\resolver\query\available_date_resolver_options;
use mod_perform\webapi\resolver\type\track_assignment;
use mod_perform\webapi\resolver\type\user_grouping;
use totara_core\advanced_feature;
use totara_webapi\phpunit\webapi_phpunit_helper;

/**
 * @coversDefaultClass track.
 *
 * @group perform
 */
class mod_perform_webapi_query_available_date_resolver_options_testcase extends advanced_testcase {
    private const QUERY = 'mod_perform_default_track_settings';

    use webapi_phpunit_helper;

    /**
     * Please note that this resolver is called from a multiple operation query
     * @see mod_perform_webapi_query_default_track_settings_testcase
     */
    public function test_find(): void {
        $context = $this->create_webapi_context(self::QUERY);

        $resolver_options = available_date_resolver_options::resolve([], $context);

        self::assertGreaterThan(0, count($resolver_options));
        self::assertContainsOnlyInstancesOf(resolver_option::class, $resolver_options);
    }

}
