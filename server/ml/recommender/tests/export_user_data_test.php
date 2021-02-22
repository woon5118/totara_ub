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
 * @package ml_recommender
 */
defined('MOODLE_INTERNAL') || die();

use ml_recommender\local\csv\writer;
use ml_recommender\local\environment;
use ml_recommender\local\export\user_data;

class export_user_data_testcase extends advanced_testcase {
    /**
     * @var string
     */
    private $data_path;

    /**
     * @return void
     */
    protected function setUp(): void {
        $this->data_path = environment::get_data_path();
        $this->data_path = rtrim($this->data_path,  "/\\");

        if (!is_dir($this->data_path)) {
            make_writable_directory($this->data_path);
        }
    }

    /**
     * @return void
     */
    protected function tearDown(): void {
        global $CFG;

        if (is_dir($this->data_path)) {
            require_once("{$CFG->dirroot}/lib/filelib.php");

            // Delete the data path.
            fulldelete($this->data_path);
        }

        $this->data_path = null;
    }

    /**
     * @return void
     */
    public function test_export_user_data(): void {
        $generator = self::getDataGenerator();

        $user_one = $generator->create_user();
        $user_two = $generator->create_user();

        $csv_file = "{$this->data_path}/boom.csv";
        $writer = new writer($csv_file);

        $exporter = new user_data();
        $result = $exporter->export($writer);
        self::assertTrue($result);

        $writer->close();
        self::assertTrue(file_exists($csv_file));

        // Due to the records fetch from database can be randomly ordered, hence its not
        // reliable to check expected contents against actual content. However, we can
        // do some sort of different checks such as partial checks.
        $actual_content = file_get_contents($csv_file);
        self::assertStringContainsString("user_id,lang", $actual_content);

        self::assertStringContainsString("{$user_one->id},{$user_one->lang}", $actual_content);
        self::assertStringContainsString("{$user_two->id},{$user_two->lang}", $actual_content);

        $admin_user = get_admin();
        self::assertStringContainsString("{$admin_user->id},{$admin_user->lang}", $actual_content);

        $guest_user = guest_user();
        self::assertStringNotContainsString("{$guest_user->id},{$guest_user->lang}", $actual_content);

        // There should be 4 rows in total from the actual CSV content.
        self::assertEquals(4, substr_count($actual_content, "\n"));
    }

    /**
     * @return void
     */
    public function test_cannot_export_data(): void {
        $generator = self::getDataGenerator();

        /** @var totara_tenant_generator $tenant_generator */
        $tenant_generator = $generator->get_plugin_generator('totara_tenant');
        $tenant_generator->enable_tenants();

        $tenant = $tenant_generator->create_tenant();

        $csv_file = "{$this->data_path}/file.csv";
        $writer = new writer($csv_file);

        $exporter = new user_data();
        $exporter->set_tenant($tenant);

        $result = $exporter->export($writer);
        self::assertFalse($result);

        // Despite of not writing to the file, the file was still created
        // because it was opened for any writes.
        self::assertTrue(file_exists($csv_file));
        self::assertEmpty(file_get_contents($csv_file));

        $writer->close();
    }

    /**
     * @return void
     */
    public function test_export_tenant_user(): void {
        $generator = self::getDataGenerator();

        /** @var totara_tenant_generator $tenant_generator */
        $tenant_generator = $generator->get_plugin_generator('totara_tenant');
        $tenant_generator->enable_tenants();

        $tenant = $tenant_generator->create_tenant();

        $user_one = $generator->create_user(['tenantid' => $tenant->id]);
        $user_two = $generator->create_user();

        $tenant_csv_file = "{$this->data_path}/tenant_x.csv";
        $writer = new writer($tenant_csv_file);

        $exporter = new user_data();
        $exporter->set_tenant($tenant);

        $result = $exporter->export($writer);
        $writer->close();

        self::assertTrue($result);
        self::assertTrue(file_exists($tenant_csv_file));

        $actual_content = file_get_contents($tenant_csv_file);
        self::assertStringContainsString("user_id,lang", $actual_content);

        // There should only be two rows in the csv content.
        self::assertEquals(2, substr_count($actual_content, "\n"));

        // Check for the existing of user two - who is not a tenant member nor tenant participant.
        self::assertStringNotContainsString("{$user_two->id},{$user_two->lang}", $actual_content);

        // Check for the existing of user one - who is a tenant member.
        self::assertStringContainsString("{$user_one->id},{$user_one->lang}", $actual_content);

        // Check for admin, which should not be included.
        $admin_user = get_admin();
        self::assertStringNotContainsString("{$admin_user->id},{$admin_user->lang}", $actual_content);

        // Check for guest user, which should not be included.
        $guest_user = guest_user();
        self::assertStringNotContainsString("{$guest_user->id},{$guest_user->lang}", $actual_content);
    }
}