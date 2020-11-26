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
 * @package engage_article
 */

use core\theme\settings;
use engage_article\theme\file\article_image;
use engage_article\totara_engage\resource\article;
use totara_core\advanced_feature;

defined('MOODLE_INTERNAL') || die();

class engage_article_image_testcase extends advanced_testcase {

    public function test_article_image(): void {
        $generator = $this->getDataGenerator();
        $user_one = $generator->create_user();
        $this->setUser($user_one);
        $user_context = \context_user::instance($user_one->id);
        $theme_config = theme_config::load('ventura');
        $theme_settings = new settings($theme_config, 0);

        // Get current default image.
        $article_image = new article_image($theme_config);
        $this->assertEquals(true, $article_image->is_available());
        $url = $article_image->get_current_or_default_url();
        $this->assertInstanceOf(moodle_url::class, $url);
        $url = $url->out();
        $this->assertEquals(
            "https://www.example.com/moodle/theme/image.php/_s/ventura/engage_article/1/default",
            $url
        );

        // Update default image.
        $files = [
            [
                'ui_key' => 'engageresource',
                'draft_id' => $this->create_image('new_article_image', $user_context),
            ]
        ];
        $theme_settings->update_files($files);

        // Confirm that new default image is fetched.
        $url = $article_image->get_current_or_default_url();
        $this->assertInstanceOf(moodle_url::class, $url);
        $url = $url->out();
        $this->assertEquals(
            "https://www.example.com/moodle/pluginfile.php/1/totara_core/defaultarticleimage/{$article_image->get_item_id()}/new_article_image.png",
            $url
        );
    }

    public function test_image_enabled() {
        $generator = $this->getDataGenerator();
        $user_one = $generator->create_user();
        $this->setUser($user_one);
        $theme_config = theme_config::load('ventura');

        // Disable advanced feature.
        advanced_feature::disable('engage_resources');

        // Image should be disabled and not found in files.
        $article_image = new article_image($theme_config);
        $this->assertEquals(false, $article_image->is_enabled());

        $theme_settings = new settings($theme_config, 0);
        $files = $theme_settings->get_files();
        foreach ($files as $file) {
            if ($file instanceof article_image) {
                $this->assertFalse($file->is_enabled());
            }
        }
    }

    /**
     * @param string $name
     * @param context $context
     *
     * @return int
     */
    private function create_image(string $name, context $context): int {
        $draft_id = file_get_unused_draft_itemid();
        $fs = get_file_storage();
        $time = time();
        $file_record = new stdClass();
        $file_record->filename = "{$name}.png";
        $file_record->contextid = $context->id;
        $file_record->component = 'user';
        $file_record->filearea = 'draft';
        $file_record->filepath = '/';
        $file_record->itemid = $draft_id;
        $file_record->timecreated = $time;
        $file_record->timemodified = $time;
        $fs->create_file_from_string($file_record, $name);

        return $draft_id;
    }

    /**
     * @return void
     */
    public function test_image_alt_txt(): void {
        global $CFG;

        $generator = $this->getDataGenerator();
        $user_one = $generator->create_user();
        $this->setUser($user_one);

        $doc = [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' =>  'This is an article'
                        ]
                    ],
                ]
            ]
        ];

        $draft_id = file_get_unused_draft_itemid();
        $article = article::create(
            [
                'format' => FORMAT_JSON_EDITOR,
                'content' => json_encode($doc),
                'draft_id' => $draft_id,
                'name' => 'test article',
            ]
        );

        require_once("{$CFG->dirroot}/lib/filelib.php");
        $fs = get_file_storage();
        $record = $this->create_image_record_for_article($article->get_context_id(), $draft_id, $user_one->id, 'test.png');

        $file = $fs->create_file_from_string($record, 'file');
        $url = \moodle_url::make_draftfile_url(
            $file->get_itemid(),
            $file->get_filepath(),
            $file->get_filename()
        );

        $doc['content'][] = [
            'type' => 'image',
            'attrs' => [
                'filename' => $file->get_filename(),
                'url' => $url->out(),
                'alttext' => 'test alt'
            ],
        ];

        // Upload image to article.
        $article->update([
            'content' => json_encode($doc),
            'draft_id' => $draft_id,
            'format' => FORMAT_JSON_EDITOR,
        ]);

        $extra = json_decode($article->get_extra(), true);
        self::assertEquals('test alt', $extra['alt_text']);

        $record = $this->create_image_record_for_article($article->get_context_id(), $draft_id, $user_one->id, 'test1.png');
        $file = $fs->create_file_from_string($record, 'file');
        $url = \moodle_url::make_draftfile_url(
            $file->get_itemid(),
            $file->get_filepath(),
            $file->get_filename()
        );

        $doc = [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'image',
                    'attrs' => [
                        'filename' => $file->get_filename(),
                        'url' => $url->out(),
                        'alttext' => 'New alt'
                    ],
                ]
            ]
        ];

        // Remove old image, upload new one.
        $article->update([
            'content' => json_encode($doc),
            'draft_id' => $draft_id,
            'format' => FORMAT_JSON_EDITOR,
        ]);

        $extra = json_decode($article->get_extra(), true);
        self::assertEquals('New alt', $extra['alt_text']);
    }

    /**
     * @param int $context_id
     * @param int $draft_id
     * @param int $user_id
     * @param string $name
     * @return stdClass
     */
    private function create_image_record_for_article(int $context_id, int $draft_id, int $user_id, string $name): stdClass {
        $record = new \stdClass();
        $record->contextid = $context_id;
        $record->component = 'user';
        $record->filearea = 'draft';
        $record->itemid = $draft_id;
        $record->filename = $name;
        $record->userid = $user_id;
        $record->filepath = '/';

        return $record;
    }
}
