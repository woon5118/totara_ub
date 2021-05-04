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

use core\collection;
use mod_perform\models\activity\element;
use mod_perform\entity\activity\element as element_entity;
use mod_perform\models\activity\element_plugin;
use mod_perform\models\activity\respondable_element_plugin;

/**
 * @group perform
 */
class mod_perform_element_plugin_model_testcase extends advanced_testcase {

    public function test_load_by_plugin() {
        /** @var element_plugin $short_text_model */
        $short_text_model = element_plugin::load_by_plugin('short_text');

        $this->assertEquals('short_text', $short_text_model->get_plugin_name());
    }

    /**
     * Make sure that the respondable element plugin validation function fails if no title is provided
     *
     * @throws coding_exception
     */
    public function test_respondable_elements_require_title() {
        $respondable_element_plugin = $this->get_mock_respondable_element_plugin();

        $entity = new element_entity();
        $entity->title = 'test title';

        // No exception thrown.
        $respondable_element_plugin->validate_element($entity);

        $entity->title = null;

        $this->expectException('coding_exception');
        $this->expectExceptionMessage('Respondable elements must include a title');
        $respondable_element_plugin->validate_element($entity);
    }

    /**
     * Make sure that the respondable element plugin validation function fails if no title is provided
     *
     * @throws coding_exception
     */
    public function test_respondable_elements_validate_max_title() {
        $respondable_element_plugin = $this->get_mock_respondable_element_plugin();

        $entity = new element_entity();
        $entity->title = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus volutpat accumsan ligula. 
                          Curabitur ut euismod tellus, eget facilisis metus. Fusce eu hendrerit risus, non
                          bibendum arcu. Donec iaculis porta arcu ut sollicitudin. Phasellus tempus elit nisi,
                          at interdum odio convallis dictum. Sed aliquam ligula eu dui sagittis pellentesque.
                          Nullam sodales ac quam condimentum vestibulum. Duis purus ligula, pharetra hendrerit felis
                          vel, consectetur rhoncus erat. Nam arcu felis, lacinia eu rhoncus non, tristique a urna.
                          Praesent ullamcorper dolor lorem, ut suscipit lectus malesuada nec. Interdum et malesuada
                          fames ac ante ipsum primis in faucibus. Sed sed nunc tristique, tincidunt erat nec, auctor dui.
                          Morbi eleifend felis nisi, facilisis vulputate sem lobortis ac. Praesent sit amet porttitor
                          nisl. Quisque mauris magna, consectetur quis neque in, sollicitudin volutpat libero.
                          Aenean metus leo, scelerisque sit amet fringilla eget, luctus vitae dui. Mauris gravida
                          nisl eros, eget auctor erat rutrum quis. Fusce tellus test.';

        $this->expectException('coding_exception');
        $this->expectExceptionMessage('Respondable element title text exceeds the maximum length');
        $respondable_element_plugin->validate_element($entity);
    }

    private function get_mock_respondable_element_plugin(): respondable_element_plugin {
        $respondable_element_plugin = new class extends respondable_element_plugin {
            public function __construct() {
            }
            public function decode_response(?string $encoded_response_data, ?string $encoded_element_data) {
            }
            public function get_group(): int {
                return 0;
            }
            public function get_sortorder(): int {
                return 0;
            }
            public function validate_response(
                ?string $encoded_response_data,
                ?element $element,
                $is_draft_validation = false
            ): collection {
                return collection::new([]);
            }
        };
        return $respondable_element_plugin;
    }
}
