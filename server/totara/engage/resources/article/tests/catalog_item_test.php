<?php
/**
 * This file is part of Totara LMS
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
 * @author David Curry <david.curry@totaralearning.com>
 * @package engage_article
 */

defined('MOODLE_INTERNAL') || die();

use totara_catalog\provider_handler;

class engage_article_catalog_item_testcase extends advanced_testcase {

    /**
     * @return void
     */
    public function test_article_provider_exists(): void {
        $providerhandler = provider_handler::instance();

        $providers = $providerhandler->get_all_provider_classes();

        $found = false;
        foreach ($providers as $provider) {
            if ($provider == 'engage_article\totara_catalog\article') {
                $found = true;
            }
        }

        $this->assertTrue($found, 'article catalog provider not found.');
    }
}
