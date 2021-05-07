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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_competency
 */

use core\task\manager;
use totara_competency\admin_setting_legacy_aggregation_method;
use totara_competency\migration_helper;
use totara_competency\task\default_criteria_on_install;
use totara_competency\task\migrate_competency_achievements_task;
use totara_core\advanced_feature;

defined('MOODLE_INTERNAL') || die();

function xmldb_totara_competency_install() {
    global $DB;
    $dbman = $DB->get_manager();
    $history_table = new xmldb_table('comp_record_history');
    $comp_table = new xmldb_table('comp');
    $has_competency_records = $dbman->table_exists($history_table) && $DB->count_records('comp_record_history') > 0;
    $has_competencies = $dbman->table_exists($comp_table) && $DB->count_records('comp') > 0;

    if (!advanced_feature::is_enabled('competency_assignment')) {
        // If this is a new install of the plugin with existing previous competency records
        // Make sure we set the legacy aggregation method to highest to match the exact previous behaviour.
        // It can be change later on in the settings.
        if ($has_competencies || $has_competency_records) {
            set_config('legacy_aggregation_method', admin_setting_legacy_aggregation_method::HIGHEST_ACHIEVEMENT, 'totara_competency');
        }

        // Only queue the task to set the defaults if there are actually competencies
        if ($has_competencies) {
            $task = new default_criteria_on_install();
            manager::queue_adhoc_task($task);
        }
    }

    // Only queue the migration tasks if there's anything to migrate
    if ($has_competency_records) {
        $task = new migrate_competency_achievements_task();
        manager::queue_adhoc_task($task);

        // Mark the migration as queued so that it gets picked up
        migration_helper::queue_migration();
    }
}
