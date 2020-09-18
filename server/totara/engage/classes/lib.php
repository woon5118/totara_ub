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
 * @author  Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @package totara_engage
 */

namespace totara_engage;

use totara_core\advanced_feature;

defined('MOODLE_INTERNAL') || die;

/**
 * Library class for Totara Engage.
 *
 * Contains static methods only, that are to be treated as public API.
 */
final class lib {

    public static function allow_view_user_profile(): bool {
        $features = [
            'engage_resources',
            // 'ml_recommender', Not required for recommenders. Left here for posterity only as this is an Engage feature.
            // 'totara_msteams', Not required for MS teams. Left here for posterity only as this is an Engage feature.
        ];
        foreach ($features as $feature) {
            if (advanced_feature::is_enabled($feature)) {
                return true;
            }
        }
        return false;
    }

}