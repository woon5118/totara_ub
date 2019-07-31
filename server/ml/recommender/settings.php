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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Vernon Denny <vernon.denny@totaralearning.com>
 * @package ml_recommender
 */

defined('MOODLE_INTERNAL') || die;

// Recommender system configuration.
$settings->add(
    new admin_setting_heading(
        'ml_recommender/recsysconfigs',
        get_string('recsysconfigs', 'ml_recommender'),
        get_string('recsysconfigs', 'ml_recommender')
    )
);

// Number of items-for-users records to return from recommender.
$options = [];
for ($i = 1; $i < 50; $i++) {
    $options[$i] = $i;
}

$settings->add(
    new admin_setting_configselect(
        'ml_recommender/user_result_count',
        new lang_string('user_result_count_label','ml_recommender'),
        new lang_string('user_result_count_help', 'ml_recommender'),
        25,
        $options
    )
);

// Number of items-for-items records to return from recommender.
$settings->add(
    new admin_setting_configselect(
        'ml_recommender/item_result_count',
        new lang_string('item_result_count_label','ml_recommender'),
        new lang_string('item_result_count_help', 'ml_recommender'),
        15,
        $options
    )
);
