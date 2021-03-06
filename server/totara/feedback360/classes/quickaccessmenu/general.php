<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @package totara_feedback360
 */

namespace totara_feedback360\quickaccessmenu;

use \totara_core\quickaccessmenu\group;
use \totara_core\quickaccessmenu\item;
use \totara_core\advanced_feature;

class general implements \totara_core\quickaccessmenu\provider {

    public static function get_items(): array {
        if (advanced_feature::is_disabled('appraisals') && advanced_feature::is_enabled('feedback360')) {
            return [
                item::from_provider(
                    'managefeedback360',
                    group::get(group::PERFORM),
                    new \lang_string('legacyfeatures', 'totara_appraisal'),
                    6000
                ),
            ];
        } else {
            return [];
        }
    }

}