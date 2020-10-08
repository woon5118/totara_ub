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

use totara_webapi\phpunit\webapi_phpunit_helper;
use core\json_editor\node\paragraph;
use engage_article\totara_engage\resource\article;

class engage_article_webapi_create_article_testcase extends advanced_testcase {
    use webapi_phpunit_helper;

    /**
     * @return void
     */
    public function test_create_article_with_different_format_from_json_editor(): void {
        $generator = $this->getDataGenerator();
        $user_one = $generator->create_user();

        $this->setUser($user_one);
        $formats = [
            FORMAT_PLAIN,
            FORMAT_MARKDOWN,
            FORMAT_HTML,
            FORMAT_HTML
        ];

        $error_counter = 0;
        // These formats will give us error.
        foreach ($formats as $format) {
            try {
                $this->resolve_graphql_mutation(
                    'engage_article_create',
                    [
                        'name' => 'Hello world',
                        'content' => 'Hello world',
                        'format' => $format
                    ]
                );
            } catch (coding_exception $e) {
                self::assertStringContainsString('The format value is invalid', $e->getMessage());
                $error_counter += 1;
            }
        }

        self::assertCount($error_counter, $formats);
    }

    /**
     * @return void
     */
    public function test_create_article_with_format_json_editor(): void {
        $generator = $this->getDataGenerator();
        $user_one = $generator->create_user();

        $this->setUser($user_one);

        /** @var article $article */
        $article = $this->resolve_graphql_mutation(
            'engage_article_create',
            [
                'name' => 'Hello world',
                'content' => json_encode([
                    'type' => 'doc',
                    'content' => [paragraph::create_json_node_from_text('Hello world')]
                ]),
                'format' => FORMAT_JSON_EDITOR
            ]
        );

        self::assertInstanceOf(article::class, $article);
        self::assertEquals('Hello world', $article->get_name());
    }
}