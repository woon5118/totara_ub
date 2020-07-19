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
 * @author Murali Nair <murali.nair@totaralearning.com>
 * @package mod_perform
 * @category test
 */

use mod_perform\models\activity\activity;
use mod_perform\models\activity\activity_setting;
use mod_perform\entities\activity\activity_setting as activity_setting_entity;

/**
 * @coversDefaultClass activity_setting.
 *
 * @group perform
 */
class mod_perform_activity_setting_model_testcase extends advanced_testcase {
    /**
     * @covers ::create
     * @covers ::update
     * @covers ::delete
     */
    public function test_crud(): void {
        $activity = $this->create_test_data();

        $name = activity_setting::MULTISECTION;
        $value = "this is a test";

        $setting = activity_setting::create($activity, $name, $value);
        $this->assertGreaterThan(0, $setting->id, 'wrong id');
        $this->assertEquals($name, $setting->name, 'wrong name');
        $this->assertEquals($value, $setting->value, 'wrong value');
        $this->assertEquals($activity->id, $setting->activity->id, 'wrong parent');

        $value = "43423";
        $setting->update($value);
        $this->assertEquals($value, $setting->value, 'wrong value');

        $db_count = activity_setting_entity::repository()
            ->where('activity_id', $activity->id)
            ->count();
        $this->assertEquals(1, $db_count, 'wrong db settings count');

        $setting->delete();

        $db_count = activity_setting_entity::repository()
            ->where('activity_id', $activity->id)
            ->count();
        $this->assertEquals(0, $db_count, 'wrong db settings count');
    }

    /**
     * @covers ::create
     */
    public function test_invalid_setting_name(): void {
        $activity = $this->create_test_data();

        $name = "aaa";

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage("invalid activity setting name: $name");
        activity_setting::create($activity, $name, 'abc');
    }

    /**
     * Generates test data.
     *
     * @return activity the test activity.
     */
    private function create_test_data(): activity {
        $this->setAdminUser();

        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        return $perform_generator->create_activity_in_container();
    }
}
