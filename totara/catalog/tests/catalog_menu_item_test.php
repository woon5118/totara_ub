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

use totara_catalog\totara\menu\catalog;
use totara_core\totara\menu\menu;

defined('MOODLE_INTERNAL') || die();

/**
 * Class catalog_menu_item_test
 *
 * Tests for catalog menu item.
 *
 * @package totara_catalog
 * @group totara_catalog
 */
class totara_catalog_catalog_menu_item_testcase extends advanced_testcase {

    public function visibility_data_provider() {
        return [
            ['totara', true, menu::SHOW_ALWAYS],
            ['totara', false, menu::HIDE_ALWAYS],
            ['enhanced', true, menu::HIDE_ALWAYS],
            ['enhanced', false, menu::HIDE_ALWAYS],
            ['moodle', true, menu::HIDE_ALWAYS],
            ['moodle', false, menu::HIDE_ALWAYS],
        ];
    }

    /**
     * @dataProvider visibility_data_provider
     *
     * @param $config
     * @param $has_visible_sibling
     * @param $expected
     */
    public function test_check_visibility($config, $has_visible_sibling, $expected) {
        global $CFG;
        $orig_conf_value = $CFG->catalogtype;
        $CFG->catalogtype = $config;

        $mock_item = $this->getMockBuilder(catalog::class)
            ->setMethods(['has_visible_sibling'])
            ->disableOriginalConstructor()
            ->getMock();
        $mock_item->method('has_visible_sibling')
            ->willReturn($has_visible_sibling);

        $rm = new ReflectionMethod(catalog::class, 'check_visibility');
        $rm->setAccessible(true);

        $this->assertSame($expected, $rm->invoke($mock_item));
        $CFG->catalogtype = $orig_conf_value;
    }
}
