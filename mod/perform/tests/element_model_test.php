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
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package mod_perform
 */

use container_perform\perform;
use mod_perform\models\activity\element;

/**
 * @group perform
 */
class mod_perform_element_model_testcase extends advanced_testcase {

    public function test_create() {
        $default_context = context_coursecat::instance(perform::get_default_category_id());

        $element = element::create($default_context, 'short_text', 'test element 1 title', 123);

        // Reload, just to make sure that we're getting it out of the DB.
        /** @var element $actual_element */
        $element_model = element::load_by_id($element->id);

        $this->assertSame('short_text', $element_model->plugin_name);
        $this->assertSame('test element 1 title', $element_model->title);
    }

}