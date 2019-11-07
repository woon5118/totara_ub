<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_assignment
 * @subpackage competency
 */

use totara_competency\admin_setting_continuous_tracking;
use totara_competency\admin_setting_unassign_behaviour;
use totara_core\advanced_feature;

defined('MOODLE_INTERNAL') || die();

/** @var $ADMIN \admin_root */

global $CFG;

$ADMIN->add(
    'competencies',
    new admin_externalpage(
        'competency_assignment',
        get_string('title:index', 'tassign_competency'),
        "{$CFG->wwwroot}/totara/assignment/plugins/competency/index.php",
        "totara/competency:manage",
        !advanced_feature::is_enabled('competency_assignment')
    )
);
$ADMIN->add(
    'competencies',
    new admin_externalpage(
        'competency_assignment_users',
        get_string('title:users', 'totara_competency'),
        "{$CFG->wwwroot}/totara/assignment/plugins/competency/users.php",
        "totara/competency:manage",
        !advanced_feature::is_enabled('competency_assignment')
    )
);
$ADMIN->add(
    'competencies',
    new admin_externalpage(
        'competency_assignment_create',
        get_string('title:create', 'tassign_competency'),
        "{$CFG->wwwroot}/totara/assignment/plugins/competency/create.php",
        "totara/competency:manage",
        !advanced_feature::is_enabled('competency_assignment')
    )
);

if (advanced_feature::is_enabled('competency_assignment')) {
    $settings_page = \hierarchy_competency\admin_settings::load_or_create_settings_page($ADMIN);
    if (!is_array($settings_page->req_capability)) {
        $settings_page->req_capability = [$settings_page->req_capability];
    }
    $settings_page->req_capability[] = 'totara/competency:manage';
    $settings_page->req_capability = array_unique($settings_page->req_capability);

    if ($ADMIN->fulltree) {
        $settings_page->add(new admin_setting_heading(
            'totara_competency/heading',
            new lang_string('settings:unassignment:header', 'totara_competency'),
            new lang_string('settings:unassignment:text', 'totara_competency')
        ));

        $settings_page->add(new admin_setting_unassign_behaviour(
            admin_setting_unassign_behaviour::NAME,
            new lang_string('settings:unassign_behaviour', 'totara_competency'),
            new lang_string('settings:unassign_behaviour:description', 'totara_competency')
        ));

        $settings_page->add(new admin_setting_continuous_tracking(
            'totara_competency/continuous_tracking',
            new lang_string('settings:continuous_tracking', 'totara_competency'),
            new lang_string('settings:continuous_tracking:description', 'totara_competency')
        ));
    }
}