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

use ml_recommender\local\exporter;
use ml_recommender\local\export\item_data;
use ml_recommender\local\export\user_data;
use ml_recommender\local\export\user_interactions;
use ml_recommender\local\environment;

class ml_recommender_exporter_testcase extends advanced_testcase {
    /**
     * This test is to annoy someone who add new export types. So that it can remind that
     * person to know it can cause regression.
     *
     * @return void
     */
    public function test_get_export_classes(): void {
        $classes = exporter::get_export_classes();

        self::assertNotEmpty($classes);

        // Default to 3, bump this number if you add more.
        self::assertCount(3, $classes);
        foreach ($classes as $export_class) {
            self::assertContainsEquals(
                $export_class,
                [item_data::class, user_data::class, user_interactions::class]
            );
        }
    }

    /**
     * @return void
     */
    public function test_get_csv_path(): void {
        $tmp_path = environment::get_temp_path();
        $tmp_path = rtrim($tmp_path, "/\\");

        // Default falling back to temp path of environment.
        self::assertEquals(
            "{$tmp_path}/this_is_name_42.csv",
            exporter::get_export_csv_file_path("this_is_name", 42)
        );

        self::assertEquals(
            "/boom/x/o/me/begining_42.csv",
            exporter::get_export_csv_file_path("begining", "42", "/boom/x/o/me/")
        );

        self::assertEquals(
            "/boom/x/o/me/begining_42.csv",
            exporter::get_export_csv_file_path("begining", "42", "/boom/x/o/me")
        );

        self::assertEquals(
            "/boom/x/o/me/begining_42.csv",
            exporter::get_export_csv_file_path("begining", "42", "/boom/x/o/me\/")
        );
    }

    /**
     * @return void
     */
    public function test_get_tenant_csv_path(): void {
        $tmp_path = environment::get_temp_path();
        $tmp_path = rtrim($tmp_path, "/\\");

        self::assertEquals(
            "{$tmp_path}/tenants.csv",
            exporter::get_tenant_csv_file_path()
        );

        self::assertEquals(
            "/boom/x/o/me/tenants.csv",
            exporter::get_tenant_csv_file_path("/boom/x/o/me/")
        );

        self::assertEquals(
            "/boom/x/o/me/tenants.csv",
            exporter::get_tenant_csv_file_path("/boom/x/o/me")
        );

        self::assertEquals(
            "/boom/x/o/me/tenants.csv",
            exporter::get_tenant_csv_file_path("/boom/x/o/me\/")
        );
    }
}