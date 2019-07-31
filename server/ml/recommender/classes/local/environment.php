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
 * @package ml_recommender
 */
namespace ml_recommender\local;

/**
 * Class environment
 * @package ml_recommender
 */
final class environment {
    /**
     * Preventing this class from being constructed
     * environment constructor.
     */
    private function __construct() {
    }

    /**
     * @return string
     */
    public static function get_data_path(): string {
        $data_path = get_config('ml_recommender', 'data_path');
        if ($data_path == '' || $data_path === false) {
            throw new \coding_exception("The data path for recommender is not set");
        }

        return $data_path;
    }

    /**
     * Returning the result count config.
     *
     * @return int
     */
    public static function get_user_result_count(): int {
        $result_count = get_config('ml_recommender', 'user_result_count');
        if (false === $result_count) {
            throw new \coding_exception("Cannot fetch the user result count config");
        }

        return (int) $result_count;
    }

    /**
     * Returning the related count config.
     *
     * @return int
     */
    public static function get_item_result_count(): int {
        $result_count = get_config('ml_recommender', 'item_result_count');

        if (false === $result_count) {
            throw new \coding_exception("Cannot fetch the user result count config");
        }

        return (int) $result_count;
    }

    /**
     * Path to Python executable.
     *
     * @return string
     * @throws \coding_exception
     */
    public static function get_py3path(): string {
        $py3path = get_config('ml_recommender', 'py3path');

        if (false === $py3path) {
            throw new \coding_exception("Cannot fetch py3 path config");
        }

        return $py3path;
    }

    /**
     * Recommender query type.
     *
     * @return string
     * @throws \coding_exception
     */
    public static function get_query(): string {
        $query = get_config('ml_recommender', 'query');

        if (false === $query) {
            throw new \coding_exception("Cannot fetch query config");
        }

        return $query;
    }

    /**
     * Number of threads/cores that recommender engine may use.
     *
     * @return int
     * @throws \coding_exception
     */
    public static function get_threads(): int {
        $threads = get_config('ml_recommender', 'threads');

        if (false === $threads) {
            throw new \coding_exception("Cannot fetch threads config");
        }

        return (int) $threads;
    }
}
