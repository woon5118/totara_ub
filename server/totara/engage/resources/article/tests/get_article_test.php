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

use totara_webapi\graphql;
use core\webapi\execution_context;

class totara_engage_get_article_testcase extends advanced_testcase {

    /**
     * @return void
     */
    public function test_get_article_from_graphql(): void {
        $this->setAdminUser();

        /** @var engage_article_generator $articlegen */
        $articlegen = $this->getDataGenerator()->get_plugin_generator('engage_article');
        $article = $articlegen->create_article();

        $ec = execution_context::create('ajax', 'engage_article_get_article');
        $result = graphql::execute_operation($ec, ['id' => $article->get_id()]);

        $this->assertEmpty($result->errors);
        $this->assertNotEmpty($result->data);
    }
}