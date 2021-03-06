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
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package totara_catalog
 */

defined('MOODLE_INTERNAL') || die();

use totara_catalog\local\param_processor;

/**
 * @group totara_catalog
 */
class totara_catalog_param_processor_testcase extends advanced_testcase {

    /**
     * @var param_processor
     */
    private $param_processor = null;

    protected function setUp(): void {
        global $PAGE;
        parent::setup();
        $PAGE->set_context(context_system::instance());
        $this->setAdminUser();
        $this->param_processor = new param_processor();
    }

    protected function tearDown(): void {
        $this->param_processor = null;
        parent::tearDown();
    }

    public function test_get_template() {
        $template = $this->param_processor->get_template();
        $this->assertInstanceOf('totara_catalog\\output\\catalog', $template);
    }

    public function test_get_template_with_optional_param() {
        global $_POST;
        $_POST['debug'] = 'true';
        $_POST['itemstyle'] = 'wide';
        $template = $this->param_processor->get_template();
        $this->assertInstanceOf('totara_catalog\\output\\catalog', $template);
        $this->assertArrayHasKey('debug', $template->get_template_data());
    }
}
