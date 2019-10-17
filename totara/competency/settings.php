<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_competency
 */

use totara_core\advanced_feature;

defined('MOODLE_INTERNAL') || die;

global $CFG;

if ($hassiteconfig) {
    $ADMIN->add(
        'modules',
        new admin_category(
            'totara_competency',
            get_string('pluginname', 'totara_competency'),
            !advanced_feature::is_enabled('competency_assignment')
        )
    );

    $ADMIN->add(
        'totara_competency',
        new admin_externalpage(
            'totara_competency_aggregation-managetypes',
            get_string('managetypes_aggregation', 'totara_competency'),
            new moodle_url("/totara/competency/managetypes.php", ['plugin' => 'aggregation']),
            ['moodle/site:config'],
            !advanced_feature::is_enabled('competency_assignment')
        )
    );

    $ADMIN->add(
        'totara_competency',
        new admin_externalpage(
            'totara_competency_pathway-managetypes',
            get_string('managetypes_pathway', 'totara_competency'),
            new moodle_url("/totara/competency/managetypes.php", ['plugin' => 'pathway']),
            ['moodle/site:config'],
            !advanced_feature::is_enabled('competency_assignment')
        )
    );
}

