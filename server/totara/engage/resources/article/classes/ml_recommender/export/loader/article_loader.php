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
namespace engage_article\ml_recommender\export\loader;

use ml_recommender\export\loader\content_loader;
use engage_article\totara_engage\resource\article;
use totara_engage\access\access;

/**
 * Class article_loader
 * @package engage_article\ml_recommender\export\loader
 */
final class article_loader extends content_loader {
    /**
     * @return int[]
     */
    public function get_all_ids(): array {
        global $DB;

        $sql = '
            SELECT er.id FROM "ttr_engage_resource" er
            INNER JOIN "ttr_engage_article" ea ON ea.id = er.instanceid AND er.resourcetype = :resource_type
            WHERE er.access = :access
        ';

        $resource_type = article::get_resource_type();
        return $DB->get_fieldset_sql(
            $sql,
            [
                'resource_type' => $resource_type,
                'access' => access::PUBLIC
            ]
        );
    }

    /**
     * Note that we only count the public article for now.
     * @return int
     */
    public function get_total(): int {
        global $DB;

        $sql = '
            SELECT COUNT(er.id) FROM "ttr_engage_resource" er
            INNER JOIN "ttr_engage_article" ea ON ea.id = er.instanceid
                AND er.resourcetype = :resource_type
            WHERE er.access = :access
        ';

        return $DB->count_records_sql(
            $sql,
            [
                'resource_type' => article::get_resource_type(),
                'access' => access::PUBLIC
            ]
        );
    }

    /**
     * @return string
     */
    public function get_content_type(): string {
        return article::get_resource_type();
    }
}