<?php
/*
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
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package mod_perform
 */

use mod_perform\dates\resolvers\dynamic\another_activity_date;

class mod_perform_date_resolver_dynamic_source_another_activity_testcase extends advanced_testcase {

    public function test_get_option() {
        $activity_date_resolver = new another_activity_date();
        $result = $activity_date_resolver->get_options();
        $this->assertCount(2, $result);
    }

    public function test_option_is_available() {
        $activity_date_resolver = new another_activity_date();
        $this->assertTrue(
            $activity_date_resolver->option_is_available(another_activity_date::ACTIVITY_COMPLETED_DAY)
        );

        $this->assertTrue(
            $activity_date_resolver->option_is_available(another_activity_date::ACTIVITY_INSTANCE_CREATION_DAY)
        );
    }
}