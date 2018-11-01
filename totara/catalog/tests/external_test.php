<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package totara_catalog
 */

use totara_catalog\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . "/totara/catalog/tests/output_test_base.php");

/**
 * Class external_test
 *
 * Tests for external class.
 *
 * @package totara_catalog
 * @group totara_catalog
 */
class external_test extends output_test_base {

    /**
     * Test get_catalog_template_data_parameters() method.
     */
    public function test_get_catalog_template_data_parameters() {
        $params = external::get_catalog_template_data_parameters();
        $this->assertInstanceOf(external_function_parameters::class, $params);
        $this->assertCount(8, $params->keys);

        $expected_external_values = [
            'itemstyle' => 'alpha',
            'limitfrom' => 'int',
            'maxcount' => 'int',
            'orderbykey' => 'alpha',
            'resultsonly' => 'bool',
            'debug' => 'bool',
            'request' => 'raw',
        ];
        foreach ($expected_external_values as $key => $type) {
            $this->assertArrayHasKey($key, $params->keys);
            $param_key = $params->keys[$key];
            $this->assertInstanceOf(external_value::class, $param_key);
            $this->assertEquals($type, $param_key->type);
            $this->assertNotEmpty($param_key->desc);
            $this->assertTrue($param_key->allownull);
            $this->assertSame(1, $param_key->required);
            $this->assertNull($param_key->default);
        }

        $filterparams = $params->keys['filterparams'];
        $this->assertInstanceOf(external_single_structure::class, $filterparams);
        $expected_filter_params = [
            'course_acttyp_browse',
            'course_format_panel-demo',
            'course_format_panel-singleactivity',
            'course_format_panel-social',
            'course_format_panel-topics',
            'course_format_panel-weeks',
            'course_format_browse',
            'tag_browse',
            'course_type_panel-1',
            'course_type_panel-0',
            'course_type_panel-2',
            'course_type_browse',
            'catalog_cat_panel',
            'catalog_cat_browse',
            'catalog_fts',
            'catalog_learning_type_panel-certification',
            'catalog_learning_type_panel-course',
            'catalog_learning_type_panel-program',
            'catalog_learning_type_browse',
        ];
        $this->assertCount(count($expected_filter_params), $filterparams->keys);
        foreach ($expected_filter_params as $key) {
            $this->assertArrayHasKey($key, $filterparams->keys);
            $filterparam_key = $filterparams->keys[$key];
            $this->assertInstanceOf(external_value::class, $filterparam_key);
        }
    }

    /**
     * Test get_catalog_template_data() method.
     *
     * That method is basically just a wrapper for catalog::create(), which is tested elsewhere. So we
     * just do a basic call and assertion here.
     */
    public function test_get_catalog_template_data() {
        $params = $this->get_catalog_default_params();
        $actual = external::get_catalog_template_data(...$params);
        $expected = $this->get_expected_catalog_template_data();
        $this->assert_catalog_template_data($expected, $actual);
    }

    /**
     * Test get_catalog_template_data_returns() method.
     */
    public function test_get_catalog_template_data_returns() {
        $this->assertNull(external::get_catalog_template_data_returns());
    }

    /**
     * Test get_details_template_data_parameters() method.
     */
    public function test_get_details_template_data_parameters() {
        $params = external::get_details_template_data_parameters();
        $this->assertInstanceOf(external_function_parameters::class, $params);
        $this->assertCount(2, $params->keys);

        $expected_external_values = [
            'catalogid' => 'int',
            'request' => 'raw',
        ];
        foreach ($expected_external_values as $key => $type) {
            $this->assertArrayHasKey($key, $params->keys);
            $param_key = $params->keys[$key];
            $this->assertInstanceOf(external_value::class, $param_key);
            $this->assertEquals($type, $param_key->type);
            $this->assertNotEmpty($param_key->desc);
            $this->assertTrue($param_key->allownull);
            $this->assertSame(1, $param_key->required);
            $this->assertNull($param_key->default);
        }
    }

    /**
     * Test get_details_template_data() method.
     */
    public function test_get_details_template_data() {
        global $DB;
        $this->resetAfterTest();

        $test_default_titles = [
            'course' => 'Test course 1',
            'program' => 'Program Fullname',
            'certification' => 'Program Fullname'
        ];
        foreach (['course', 'program', 'certification'] as $provider) {
            $object_id = $this->create_object_for_provider($provider);
            $catalog_record = $DB->get_record('catalog', ['objecttype' => $provider, 'objectid' => $object_id], 'id');
            $expected = array_replace(
                $this->get_default_expected_item_template_data($provider, $object_id),
                [
                    'id'    => $catalog_record->id,
                    'title' => $test_default_titles[$provider],
                ]
            );

            $actual = external::get_details_template_data($catalog_record->id, 'arbitrary request string');
            $this->assertEquals($expected, $actual);
        }
    }

    /**
     * Test get_details_template_data_returns() method.
     */
    public function test_get_details_template_data_returns() {
        $this->assertNull(external::get_details_template_data_returns());
    }
}
