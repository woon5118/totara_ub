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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency;

class settings {

    public static function is_continuous_tracking_enabled(): bool {
        $config = (int) get_config('totara_competency', 'continuous_tracking');
        return $config === admin_setting_continuous_tracking::ENABLED;
    }

    public static function should_unassign_keep_records(): bool {
        $config = (int) get_config('totara_competency', 'unassign_behaviour');
        return $config === admin_setting_unassign_behaviour::KEEP;
    }

}