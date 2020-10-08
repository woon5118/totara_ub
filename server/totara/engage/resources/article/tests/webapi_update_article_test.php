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
 * @package engage_article
 */
defined('MOODLE_INTERNAL') || die();

use core\json_editor\node\paragraph;
use engage_article\totara_engage\resource\article;
use totara_webapi\phpunit\webapi_phpunit_helper;

class engage_article_webapi_update_article_testcase extends advanced_testcase {
    use webapi_phpunit_helper;

    /**
     * @return void
     */
    public function test_update_article_with_content_format_different_from_json_editor(): void {
        $generator = $this->getDataGenerator();
        $user_one = $generator->create_user();

        $this->setUser($user_one);

        /** @var engage_article_generator $article_generator */
        $article_generator = $generator->get_plugin_generator('engage_article');
        $article = $article_generator->create_article();

        // Format that will result in errors for updating the article.
        $formats = [
            FORMAT_PLAIN,
            FORMAT_HTML,
            FORMAT_MARKDOWN,
            FORMAT_MOODLE
        ];

        $error_counter = 0;
        foreach ($formats as $format) {
            try {
                $this->resolve_graphql_mutation(
                    'engage_article_update',
                    [
                        'resourceid' => $article->get_id(),
                        'name' => $article->get_name(),
                        'content' => 'Format value',
                        'format' => $format
                    ]
                );
            } catch (coding_exception $e) {
                self::assertStringContainsString('The format value is invalid', $e->getMessage());
                $error_counter++;
            }
        }

        self::assertCount($error_counter, $formats);
    }

    /**
     * @return void
     */
    public function test_update_article_with_content_format_json_editor(): void {
        $generator = $this->getDataGenerator();
        $user_one = $generator->create_user();

        $this->setUser($user_one);

        /** @var engage_article_generator $article_generator */
        $article_generator = $generator->get_plugin_generator('engage_article');
        $article = $article_generator->create_article();

        // Update article with new content and with format json editor.
        /** @var article $updated_article */
        $updated_article = $this->resolve_graphql_mutation(
            'engage_article_update',
            [
                'resourceid' => $article->get_id(),
                'name' => $article->get_name(),
                'content' => json_encode([
                    'type' => 'doc',
                    'content' => [
                        paragraph::create_json_node_from_text('woho')
                    ]
                ]),
                'format' => FORMAT_JSON_EDITOR
            ]
        );

        self::assertInstanceOf(article::class, $updated_article);
        self::assertEquals($article->get_id(), $updated_article->get_id());
    }
}