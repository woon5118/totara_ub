<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2021 onwards Totara Learning Solutions LTD
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

use totara_competency\admin_setting_legacy_aggregation_method;
use totara_core\advanced_feature;

class totara_competency_install_testcase extends advanced_testcase {

    public function test_learn_installation_without_preexisting_data() {
        global $CFG, $DB;
        require_once $CFG->dirroot . '/totara/competency/db/install.php';

        advanced_feature::disable('competency_assignment');

        // Let's simulate a new install where this config setting does not exist yet
        unset_config('legacy_aggregation_method', 'totara_competency');
        $DB->delete_records('task_adhoc');

        $config_value = get_config('totara_competency', 'legacy_aggregation_method');
        $this->assertFalse($config_value);

        xmldb_totara_competency_install();

        $config_value = get_config('totara_competency', 'legacy_aggregation_method');
        $this->assertFalse($config_value);

        $task_queued = $DB->record_exists_select('task_adhoc', "classname LIKE '%migrate_competency_achievements_task%'");
        $this->assertFalse($task_queued);

        $task_queued = $DB->record_exists_select('task_adhoc', "classname LIKE '%default_criteria_on_install%'");
        $this->assertFalse($task_queued);
    }

    public function test_perform_installation_without_preexisting_data() {
        global $CFG, $DB;
        require_once $CFG->dirroot . '/totara/competency/db/install.php';

        advanced_feature::enable('competency_assignment');

        // Let's simulate a new install where this config setting does not exist yet
        unset_config('legacy_aggregation_method', 'totara_competency');
        $DB->delete_records('task_adhoc');

        $config_value = get_config('totara_competency', 'legacy_aggregation_method');
        $this->assertFalse($config_value);

        xmldb_totara_competency_install();

        $config_value = get_config('totara_competency', 'legacy_aggregation_method');
        $this->assertFalse($config_value);

        $task_queued = $DB->record_exists_select('task_adhoc', "classname LIKE '%migrate_competency_achievements_task%'");
        $this->assertFalse($task_queued);

        $task_queued = $DB->record_exists_select('task_adhoc', "classname LIKE '%default_criteria_on_install%'");
        $this->assertFalse($task_queued);
    }

    public function test_learn_installation_with_preexisting_competencies() {
        global $CFG, $DB;
        require_once $CFG->dirroot . '/totara/competency/db/install.php';

        $this->setAdminUser();

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');

        $user = $this->getDataGenerator()->create_user();

        $scale = $competency_generator->create_scale();
        $fw = $competency_generator->create_framework($scale, 'Talking FW');
        $competency = $competency_generator->create_competency('Talking', $fw);

        advanced_feature::disable('competency_assignment');

        // Let's simulate a new install where this config setting does not exist yet
        unset_config('legacy_aggregation_method', 'totara_competency');
        $DB->delete_records('task_adhoc');

        $config_value = get_config('totara_competency', 'legacy_aggregation_method');
        $this->assertFalse($config_value);

        xmldb_totara_competency_install();

        $config_value = get_config('totara_competency', 'legacy_aggregation_method');
        $this->assertEquals(admin_setting_legacy_aggregation_method::HIGHEST_ACHIEVEMENT, $config_value);

        $task_queued = $DB->record_exists_select('task_adhoc', "classname LIKE '%migrate_competency_achievements_task%'");
        $this->assertFalse($task_queued);

        $task_queued = $DB->record_exists_select('task_adhoc', "classname LIKE '%default_criteria_on_install%'");
        $this->assertTrue($task_queued);
    }

    public function test_perform_installation_with_preexisting_competencies() {
        global $CFG, $DB;
        require_once $CFG->dirroot . '/totara/competency/db/install.php';

        $this->setAdminUser();

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');

        $user = $this->getDataGenerator()->create_user();

        $scale = $competency_generator->create_scale();
        $fw = $competency_generator->create_framework($scale, 'Talking FW');
        $competency = $competency_generator->create_competency('Talking', $fw);

        advanced_feature::enable('competency_assignment');

        // Let's simulate a new install where this config setting does not exist yet
        unset_config('legacy_aggregation_method', 'totara_competency');
        $DB->delete_records('task_adhoc');

        $config_value = get_config('totara_competency', 'legacy_aggregation_method');
        $this->assertFalse($config_value);

        xmldb_totara_competency_install();

        $config_value = get_config('totara_competency', 'legacy_aggregation_method');
        $this->assertFalse($config_value);

        $task_queued = $DB->record_exists_select('task_adhoc', "classname LIKE '%migrate_competency_achievements_task%'");
        $this->assertFalse($task_queued);

        $task_queued = $DB->record_exists_select('task_adhoc', "classname LIKE '%default_criteria_on_install%'");
        $this->assertFalse($task_queued);
    }

    public function test_learn_installation_with_preexisting_competency_records() {
        global $CFG, $DB;
        require_once $CFG->dirroot . '/totara/competency/db/install.php';

        $this->setAdminUser();

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');

        $user = $this->getDataGenerator()->create_user();

        $scale = $competency_generator->create_scale();
        $fw = $competency_generator->create_framework($scale, 'Talking FW');
        $competency = $competency_generator->create_competency('Talking', $fw);

        $record_time = time() - 1;
        $this->add_comp_record($competency->id, $user->id, 1, $record_time, $record_time);
        $this->add_comp_record_history($competency->id, $user->id, 1, $record_time);

        advanced_feature::disable('competency_assignment');

        // Let's simulate a new install where this config setting does not exist yet
        unset_config('legacy_aggregation_method', 'totara_competency');
        $DB->delete_records('task_adhoc');

        $config_value = get_config('totara_competency', 'legacy_aggregation_method');
        $this->assertFalse($config_value);

        xmldb_totara_competency_install();

        $config_value = get_config('totara_competency', 'legacy_aggregation_method');
        $this->assertEquals(admin_setting_legacy_aggregation_method::HIGHEST_ACHIEVEMENT, $config_value);

        $task_queued = $DB->record_exists_select('task_adhoc', "classname LIKE '%migrate_competency_achievements_task%'");
        $this->assertTrue($task_queued);

        $task_queued = $DB->record_exists_select('task_adhoc', "classname LIKE '%default_criteria_on_install%'");
        $this->assertTrue($task_queued);
    }

    public function test_perform_installation_with_preexisting_competency_records() {
        global $CFG, $DB;
        require_once $CFG->dirroot . '/totara/competency/db/install.php';

        $this->setAdminUser();

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');

        $user = $this->getDataGenerator()->create_user();

        $scale = $competency_generator->create_scale();
        $fw = $competency_generator->create_framework($scale, 'Talking FW');
        $competency = $competency_generator->create_competency('Talking', $fw);

        $record_time = time() - 1;
        $this->add_comp_record($competency->id, $user->id, 1, $record_time, $record_time);
        $this->add_comp_record_history($competency->id, $user->id, 1, $record_time);

        advanced_feature::enable('competency_assignment');

        // Let's simulate a new install where this config setting does not exist yet
        unset_config('legacy_aggregation_method', 'totara_competency');
        $DB->delete_records('task_adhoc');

        $config_value = get_config('totara_competency', 'legacy_aggregation_method');
        $this->assertFalse($config_value);

        xmldb_totara_competency_install();

        $config_value = get_config('totara_competency', 'legacy_aggregation_method');
        $this->assertFalse($config_value);

        $task_queued = $DB->record_exists_select('task_adhoc', "classname LIKE '%migrate_competency_achievements_task%'");
        $this->assertTrue($task_queued);

        $task_queued = $DB->record_exists_select('task_adhoc', "classname LIKE '%default_criteria_on_install%'");
        $this->assertFalse($task_queued);
    }

    private function add_comp_record(int $competency_id, int $user_id, int $proficiency, int $timecreated = null, int $timemodified = null): stdClass {
        global $DB;

        $comp_record = new stdClass();
        $comp_record->competencyid = $competency_id;
        $comp_record->userid = $user_id;
        $comp_record->proficiency = $proficiency;
        $comp_record->timecreated = $timecreated ?? time();
        $comp_record->timemodified = $timemodified ?? time();
        $comp_record->id = $DB->insert_record('comp_record', $comp_record);

        return $comp_record;
    }

    private function add_comp_record_history(int $competency_id, int $user_id, int $proficiency, int $timemodified = null): stdClass {
        global $DB;

        $comp_record_history = new stdClass();
        $comp_record_history->competencyid = $competency_id;
        $comp_record_history->userid = $user_id;
        $comp_record_history->proficiency = $proficiency;
        $comp_record_history->timemodified = $timemodified;
        $comp_record_history->usermodified = 500;
        $comp_record_history->id = $DB->insert_record('comp_record_history', $comp_record_history);

        return $comp_record_history;
    }

}