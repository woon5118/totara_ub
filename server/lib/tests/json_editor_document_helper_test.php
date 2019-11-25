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
 * @package core
 */
defined('MOODLE_INTERNAL') || die();

use core\json_editor\helper\document_helper;
use core\json_editor\node\paragraph;
use core\json_editor\node\mention;
use core\json_editor\node\attachments;
use core\json_editor\node\attachment;
use core\json_editor\node\text;
use core\json_editor\node\image;
use core\json_editor\node\video;
use core\json_editor\node\audio;
use core\json_editor\node\bullet_list;
use core\json_editor\node\ruler;
use core\json_editor\node\ordered_list;

class core_json_editor_document_helper_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_validate_document(): void {
        $this->assertFalse(
            document_helper::is_valid_document(['type' => 'something else', 'xxx' => 'd'])
        );

        $this->assertFalse(
            document_helper::is_valid_document(['type' => 'doc', 'content' => null])
        );

        $this->assertTrue(
            document_helper::is_valid_document(['type' => 'doc', 'content' => []])
        );

        $proper_document = [
            'type' => 'doc',
            'content' => [
                [
                    'type' => paragraph::get_type(),
                    'content' => [
                        [
                            'type' => text::get_type(),
                            'text' => 'this is value ',
                            'marks' => [
                                ['type' => 'strong']
                            ]
                        ],
                        [
                            'type' => mention::get_type(),
                            'attrs' => [
                                'display' => "<script>alert('hello world');</script>",
                                'id' => '15'
                            ]
                        ]
                    ]
                ],
                [
                    'type' => attachments::get_type(),
                    'content' => [
                        [
                            'type' => attachment::get_type(),
                            'attrs' => [
                                'filename' => 'some_file.png',
                                'url' => 'http://example.com',
                                'size' => 1920
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->assertTrue(document_helper::is_valid_document($proper_document));
        $this->assertTrue(document_helper::is_valid_json_document(json_encode($proper_document)));
    }

    /**
     * @return void
     */
    public function test_clean_raw_node(): void {
        $proper_document = [
            'type' => 'doc',
            'content' => [
                [
                    'type' => paragraph::get_type(),
                    'content' => [
                        [
                            'type' => text::get_type(),
                            'text' => 'this is value ',
                            'marks' => [
                                ['type' => 'strong']
                            ]
                        ],
                        [
                            'type' => mention::get_type(),
                            'attrs' => [
                                'display' => "<script>alert('hello world');</script>",
                                'id' => '15'
                            ]
                        ]
                    ]
                ],
                [
                    'type' => attachments::get_type(),
                    'content' => [
                        [
                            'type' => attachment::get_type(),
                            'attrs' => [
                                'filename' => 'some_file.png',
                                'url' => 'http://example.com',
                                'size' => 1920
                            ]
                        ]
                    ]
                ]
            ]
        ];

        // There is some xss node inside.
        $json_document = json_encode($proper_document);
        $cleaned = document_helper::clean_json_document($json_document);
        $this->assertNotSame($json_document, $cleaned);

        $cleaned_document = document_helper::parse_document($cleaned);

        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertIsArray($cleaned_document);
        $this->assertArrayHasKey('content', $cleaned_document);

        // Paragraph
        $paragraph_node = reset($cleaned_document['content']);
        $this->assertArrayHasKey('type', $paragraph_node);
        $this->assertEquals(paragraph::get_type(), $paragraph_node['type']);

        $this->assertArrayHasKey('content', $paragraph_node);
        $this->assertNotEmpty($paragraph_node['content']);

        // Text node
        $text_node = reset($paragraph_node['content']);
        $this->assertArrayHasKey('type', $text_node);
        $this->assertArrayHasKey('text', $text_node);

        $this->assertEquals(text::get_type(), $text_node['type']);
        $this->assertEquals("this is value ", $text_node['text']);

        // Mention node
        $mention_node = end($paragraph_node['content']);
        $this->assertArrayHasKey('type', $mention_node);
        $this->assertArrayHasKey('attrs', $mention_node);
        $this->assertIsArray($mention_node['attrs']);
        $this->assertNotEmpty($mention_node['attrs']);

        $this->assertEquals(mention::get_type(), $mention_node['type']);
        $this->assertArrayHasKey('display', $mention_node['attrs']);
        $this->assertArrayHasKey('id', $mention_node['attrs']);

        $this->assertSame(15, $mention_node['attrs']['id']);
        $this->assertSame("alert('hello world');", $mention_node['attrs']['display']);
    }

    /**
     * @return void
     */
    public function test_parse_document(): void {
        $this->assertEmpty(document_helper::parse_document(null));
        $this->assertEmpty(document_helper::parse_document(false));
        $this->assertSame(
            ['type' => 'doc'],
            document_helper::parse_document(['type' => 'doc'])
        );

        $this->assertSame(
            [
                'type' => 'doc',
                'content' => null
            ],
            document_helper::parse_document(json_encode(['type' => 'doc', 'content' => null]))
        );
    }

    /**
     * @return void
     */
    public function test_validate_document_content_nodes(): void {
        global $CFG, $USER;
        $this->setAdminUser();

        $this->assertFalse(
            document_helper::is_valid_document([
                'type' => 'doc',
                'content' => [
                    mention::create_raw_node($USER->id),
                    text::create_json_node_from_text('This is some text')
                ]
            ])
        );

        $this->assertTrue(
            document_helper::is_valid_document([
                'type' => 'doc',
                'content' => [
                    paragraph::create_json_node_from_text('This is some text')
                ]
            ])
        );

        // Create stored files so that we can have more nodes to test
        require_once("{$CFG->dirroot}/lib/filelib.php");
        $fs = get_file_storage();
        $context_id = \context_user::instance($USER->id)->id;

        $image_record = new stdClass();
        $image_record->itemid = file_get_unused_draft_itemid();
        $image_record->contextid = \context_user::instance($USER->id)->id;
        $image_record->filename = uniqid() . ".png";
        $image_record->filepath = '/';
        $image_record->component = 'user';
        $image_record->filearea = 'draft';

        $video_record = new stdClass();
        $video_record->itemid = file_get_unused_draft_itemid();
        $video_record->contextid = $context_id;
        $video_record->filename = uniqid() . ".mp4";
        $video_record->filepath = '/';
        $video_record->component = 'user';
        $video_record->filearea = 'draft';

        $audio_record = new stdClass();
        $audio_record->itemid = file_get_unused_draft_itemid();
        $audio_record->contextid = $context_id;
        $audio_record->filename = uniqid() . ".mp3";
        $audio_record->filepath = '/';
        $audio_record->component = 'user';
        $audio_record->filearea = 'draft';


        $image_file = $fs->create_file_from_string($image_record, "Wop owp ;d-;[wd pfel");
        $video_file = $fs->create_file_from_string($video_record, "dokkok oblplpef ldpelp");
        $audio_file = $fs->create_file_from_string($audio_record, "kokoko ;[de;[e ;w");

        $this->assertTrue(
            document_helper::is_valid_document([
                'type' => 'doc',
                'content' => [
                    paragraph::create_json_node_from_text('woop wopow oiokvoce oe'),
                    image::create_raw_node_from_image($image_file),
                    attachments::create_raw_node_from_list([$image_file, $audio_file, $video_file]),
                    video::create_raw_node($video_file),
                    audio::create_raw_node($audio_file),
                    bullet_list::create_raw_node_from_texts(['wow', 'do', 'ddu', 'hit', 'dajiwe', 'kokoe']),
                    ruler::create_raw_node(),
                    ordered_list::create_raw_node_from_texts(['dota', '2', 'me', 'pudge', 'that'])
                ]
            ])
        );
    }

    /**
     * @return void
     */
    public function test_sanitize_document(): void {
        $html = /** @lang text */'<img src="x" onerror="alert(\'This file failed\')"/>';
        $result = document_helper::sanitize_json([
            'type' => 'doc',
            'content' => [
                paragraph::create_json_node_from_text($html)
            ],
        ]);

        $this->assertIsArray($result);

        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('content', $result);

        $this->assertSame('doc', $result['type']);
        $this->assertCount(1, $result['content']);

        $paragraph = reset($result['content']);
        $this->assertArrayHasKey('type', $paragraph);
        $this->assertArrayHasKey('content', $paragraph);

        $this->assertEquals(paragraph::get_type(), $paragraph['type']);
        $this->assertIsArray($paragraph['content']);
        $this->assertCount(1, $paragraph['content']);

        $text = reset($paragraph['content']);
        $this->assertArrayHasKey('type', $text);
        $this->assertArrayHasKey('text', $text);

        $this->assertEquals(text::get_type(), $text['type']);
        $this->assertSame(
            s($html),
            $text['text']
        );
    }
}