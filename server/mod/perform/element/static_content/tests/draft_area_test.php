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
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package performelement_static_element
 */

defined('MOODLE_INTERNAL') || die();

use container_perform\perform;
use mod_perform\models\activity\element;
use mod_perform\models\activity\element_plugin;
use mod_perform\models\activity\section_element;
use performelement_static_content\static_content;
use totara_webapi\phpunit\webapi_phpunit_helper;

class performelement_static_content_draft_area_testcase extends advanced_testcase {

    use webapi_phpunit_helper;

    public function test_post_create_update(): void {
        global $USER;

        $this->setAdminUser();

        // Generate some data.
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $perform_generator->create_activity_in_container();
        $section = $perform_generator->create_section($activity);

        // First get unused draft id.
        $draft_id = file_get_unused_draft_itemid();
        $context = \context_user::instance($USER->id);

        // Create a file in draft area.
        $data['wekaDoc'] = $this->create_document($draft_id, $context);
        $data['docFormat'] = 'FORMAT_JSON_EDITOR';
        $data['format'] = 'HTML';
        $data['draftId'] = $draft_id;
        $data = json_encode($data);

        // Create element.
        $default_context = context_coursecat::instance(perform::get_default_category_id());
        $element = element::create(
            $default_context,
            'short_text',
            'test element 1 title',
            'test identifier',
            $data,
            true
        );

        $section_element = section_element::create($section, $element, 123);

        // Test post_create.
        /** @var static_content $plugin */
        $plugin = element_plugin::load_by_plugin('static_content');
        $plugin->post_create($element);

        $data = json_decode($element->data, true);

        // Confirm that the element_id have been added to the data.
        $this->assertArrayHasKey('element_id', $data);
        $this->assertEquals($element->id, $data['element_id']);

        // Confirm that the file URL has been rewritten.
        $this->assertStringContainsString('@@PLUGINFILE@@/test_file.png', $data['wekaDoc']);

        // Now that the element exists we can test the draft ID mutation.
        $draft_id = $this->resolve_graphql_mutation(
            'performelement_static_content_prepare_draft_area',
            [
                'section_id' => $section_element->section_id,
                'element_id' => $section_element->element_id,
            ]
        );

        $this->assertIsInt($draft_id);
        $this->assertGreaterThan(0, $draft_id);

        // Confirm that the draft area contains the image added to element content.
        $fs = get_file_storage();
        $file_exist = $fs->file_exists($context->id,
            'user',
            'draft',
            $draft_id,
            '/',
            'test_file.png'
        );
        $this->assertEquals(true, $file_exist);
    }

    private function create_document(int $draft_id, context $context): string {
        // Create a draft image.
        $draft = new \stdClass();
        $draft->filename = "test_file.png";
        $draft->filepath = '/';
        $draft->component = 'user';
        $draft->filearea = 'draft';
        $draft->itemid = $draft_id;
        $draft->contextid = $context->id;

        $fs = get_file_storage();
        $file = $fs->create_file_from_string($draft, 'blah blah');
        $url = \moodle_url::make_draftfile_url(
            $draft_id,
            $draft->filepath,
            $draft->filename
        )->out(false);

        return json_encode(
            [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'attachments',
                        'content' => [
                            [
                                'type' => 'attachment',
                                'attrs' => [
                                    'url' => $url,
                                    'filename' => $draft->filename,
                                    'size' => $file->get_filesize(),
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            JSON_UNESCAPED_SLASHES
        );
    }

}