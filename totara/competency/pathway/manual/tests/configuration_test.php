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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package pathway_manual
 */

use pathway_manual\manual;
use totara_competency\pathway;

class pathway_manual_configuration_testcase extends advanced_testcase {

    private function setup_data() {
        global $DB;

        $data = new class() {
            public $competency;
        };

        /** @var totara_hierarchy_generator $hierarchy_generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $data->competency = $generator->create_competency();

        return $data;
    }

    public function test_check_valid_roles() {
        // Without validation
        $this->assertTrue(manual::check_is_valid_role(manual::ROLE_SELF));
        $this->assertTrue(manual::check_is_valid_role(manual::ROLE_MANAGER));
        $this->assertTrue(manual::check_is_valid_role(manual::ROLE_APPRAISER));
        $this->assertTrue(manual::check_is_valid_role([manual::ROLE_SELF, manual::ROLE_MANAGER, manual::ROLE_APPRAISER]));

        // With validation
        $this->assertTrue(manual::check_is_valid_role(manual::ROLE_SELF, true));
        $this->assertTrue(manual::check_is_valid_role(manual::ROLE_MANAGER, true));
        $this->assertTrue(manual::check_is_valid_role(manual::ROLE_APPRAISER, true));
        $this->assertTrue(manual::check_is_valid_role([manual::ROLE_SELF, manual::ROLE_MANAGER, manual::ROLE_APPRAISER], true));

        // Test invalid roles without validation
        $this->assertFalse(manual::check_is_valid_role('blah blah blah'));
        $this->assertFalse(manual::check_is_valid_role(['blah', 'blah blah blah']));

        // Test invalid roles with validation
        $this->expectExceptionMessage("Invalid role(s) specified: 'blah blah blah'");
        manual::check_is_valid_role('blah blah blah', true);
    }

    public function test_setting_valid_roles() {
        $data = $this->setup_data();

        $manual = new manual();
        $manual->set_competency($data->competency);

        // Roles are likely to be added without keys. Allowing this permits easier flow when information comes from
        // the client.
        $manual->set_roles([manual::ROLE_MANAGER, manual::ROLE_SELF]);

        $returned_roles = $manual->get_roles();

        $this->assertCount(2, $returned_roles);
        $this->assertEquals(manual::ROLE_MANAGER, $returned_roles[manual::ROLE_MANAGER]);
        $this->assertEquals(manual::ROLE_SELF, $returned_roles[manual::ROLE_SELF]);
    }

    public function test_setting_roles_overwrites() {
        $data = $this->setup_data();

        $manual = new manual();
        $manual->set_competency($data->competency);

        // Roles are likely to be added without keys. Allowing this permits easier flow when information comes from
        // the client.
        $manual->set_roles([manual::ROLE_MANAGER, manual::ROLE_SELF]);

        $returned_roles = $manual->get_roles();
        $this->assertCount(2, $returned_roles);

        $manual->set_roles([manual::ROLE_APPRAISER]);
        $returned_roles = $manual->get_roles();
        $this->assertCount(1, $returned_roles);
        $this->assertEquals(manual::ROLE_APPRAISER, $returned_roles[manual::ROLE_APPRAISER]);
    }

    public function test_setting_invalid_roles() {
        $data = $this->setup_data();

        $manual = new manual();
        $manual->set_competency($data->competency);

        $this->expectException(\coding_exception::class);

        $manual->set_roles([manual::ROLE_MANAGER, 'notarole']);
    }

    public function test_save_load_configuration() {
        $data = $this->setup_data();

        $manual = new manual();
        $manual->set_competency($data->competency);
        $manual->set_sortorder(2);
        $manual->set_roles([manual::ROLE_MANAGER, manual::ROLE_SELF]);

        $manual->save();
        $pw_id = $manual->get_id();
        $instance_id = $manual->get_path_instance_id();

        $this->validate_num_rows([
            ['totara_competency_pathway', [], 1],
            ['pathway_manual', [], 1],
            ['pathway_manual_role', [], 2],
        ]);
        $this->validate_roles($instance_id, [manual::ROLE_MANAGER, manual::ROLE_SELF]);

        unset($manual);

        $loaded = manual::fetch($pw_id);
        $this->assertEquals(2, $loaded->get_sortorder());
        $roles = $loaded->get_roles();

        $this->assertCount(2, $roles);
        $this->assertEquals(manual::ROLE_MANAGER, $roles[manual::ROLE_MANAGER]);
        $this->assertEquals(manual::ROLE_SELF, $roles[manual::ROLE_SELF]);
    }

    public function test_update() {
        global $DB;

        $data = $this->setup_data();

        $manual = new manual();
        $manual->set_competency($data->competency);
        $manual->set_sortorder(2);
        $manual->set_roles([manual::ROLE_MANAGER, manual::ROLE_SELF]);

        $manual->save();
        $pw_id = $manual->get_id();
        $instance_id = $manual->get_path_instance_id();

        $this->validate_num_rows([
            ['totara_competency_pathway', [], 1],
            ['pathway_manual', [], 1],
            ['pathway_manual_role', [], 2],
        ]);
        $this->validate_roles($instance_id, [manual::ROLE_MANAGER, manual::ROLE_SELF]);

        $pw_row = $DB->get_record('totara_competency_pathway', ['id' => $pw_id]);
        $instance_row = $DB->get_record('pathway_manual', ['id' => $instance_id]);

        // Sleeping to ensure timestamps are different
        $this->waitForSecond();

        // Now save without making changes
        // Ensure nothing changed on the db
        $manual->save();
        $this->validate_num_rows([
            ['totara_competency_pathway', [], 1],
            ['pathway_manual', [], 1],
            ['pathway_manual_role', [], 2],
        ]);
        $this->validate_roles($instance_id, [manual::ROLE_MANAGER, manual::ROLE_SELF]);

        $updated_pw_row = $DB->get_record('totara_competency_pathway', []);
        $updated_instance_row = $DB->get_record('pathway_manual', []);
        $this->assertEquals($pw_row, $updated_pw_row);
        $this->assertEquals($instance_row, $updated_instance_row);

        // Sleeping to ensure timestamps are different
        $this->waitForSecond();

        // Now make a change - remove role
        $manual->set_roles([manual::ROLE_MANAGER]);
        $manual->save();

        $this->validate_num_rows([
            ['totara_competency_pathway', [], 1],
            ['pathway_manual', [], 1],
            ['pathway_manual_role', [], 1],
        ]);
        $this->validate_roles($instance_id, [manual::ROLE_MANAGER]);

        $updated_pw_row = $DB->get_record('totara_competency_pathway', ['id' => $pw_id]);
        // Timemodified should have changed
        $this->assertNotEquals($pw_row->pathway_modified, $updated_pw_row->pathway_modified);
        // Check other attributes
        unset($pw_row->pathway_modified);
        unset($updated_pw_row->pathway_modified);
        $this->assertEquals($pw_row, $updated_pw_row);

        $updated_instance_row = $DB->get_record('pathway_manual', ['id' => $instance_id]);
        $this->assertEquals($instance_row, $updated_instance_row);


        // Add role
        $manual->set_roles([manual::ROLE_MANAGER, manual::ROLE_APPRAISER]);
        $manual->save();

        $this->validate_num_rows([
            ['pathway_manual', [], 1],
            ['pathway_manual_role', [], 2],
        ]);
        $this->validate_roles($instance_id, [manual::ROLE_MANAGER, manual::ROLE_APPRAISER]);

        $updated_instance_row = $DB->get_record('pathway_manual', []);
        $this->assertEquals($instance_row, $updated_instance_row);
    }

    public function test_delete() {
        global $DB;

        $data = $this->setup_data();

        $manual = new manual();
        $manual->set_competency($data->competency);
        $manual->set_sortorder(2);
        $manual->set_roles([manual::ROLE_MANAGER, manual::ROLE_SELF]);

        $manual->save();
        $pw_id = $manual->get_id();
        $instance_id = $manual->get_path_instance_id();

        $this->validate_num_rows([
            ['totara_competency_pathway', [], 1],
            ['pathway_manual', [], 1],
            ['pathway_manual_role', [], 2],
        ]);
        $this->validate_roles($instance_id, [manual::ROLE_MANAGER, manual::ROLE_SELF]);

        $pw_row = $DB->get_record('totara_competency_pathway', ['id' => $pw_id]);

        // Sleeping to ensure timestamps are different
        $this->waitForSecond();

        // Now delete it
        $manual->delete();
        $this->validate_num_rows([
            ['totara_competency_pathway', [], 1],
            ['pathway_manual', [], 0],
            ['pathway_manual_role', [], 0],
        ]);

        $this->assertTrue($manual->is_archived());
        $this->assertNull($manual->get_path_instance_id());

        $updated_pw_row = $DB->get_record('totara_competency_pathway', ['id' => $pw_id]);
        $this->assertEquals(pathway::PATHWAY_STATUS_ARCHIVED, $updated_pw_row->status);
        $this->assertNotEquals($pw_row->pathway_modified, $updated_pw_row->pathway_modified);
    }

    /**
     * Test dump_pathway_configuration
     */
    public function test_dump_pathway_configuration() {
        global $DB;

        $data = $this->setup_data();

        $manual = new manual();
        $manual->set_competency($data->competency);
        $manual->set_sortorder(2);
        $manual->set_roles([manual::ROLE_MANAGER, manual::ROLE_SELF]);
        $manual->save();

        $expected = $DB->get_record('pathway_manual', ['id' => $manual->get_path_instance_id()]);
        $expected->roles = $DB->get_records('pathway_manual_role', ['path_manual_id' => $manual->get_path_instance_id()]);

        $actual = manual::dump_pathway_configuration($manual->get_path_instance_id());
        $this->assertEqualsCanonicalizing($expected, $actual);
    }

    /**
     * Test validate
     */
    public function test_validate() {
        $data = $this->setup_data();

        // Without roles
        $manual = new manual();
        $manual->validate();
        $this->assertFalse($manual->is_valid());

        // With a role
        $manual->set_roles([manual::ROLE_MANAGER]);
        $manual->validate();
        $this->assertTrue($manual->is_valid());

        $manual->set_roles([manual::ROLE_SELF, manual::ROLE_APPRAISER]);
        $manual->validate();
        $this->assertTrue($manual->is_valid());

        $manual->set_roles([]);
        $manual->validate();
        $this->assertFalse($manual->is_valid());
    }


    /**
     * Validate the number of rows in the specified tables
     *
     * @param array $totest Test definition. Each array element is an array containing
     *                      the table name, query conditions and expected number of rows
     */
    private function validate_num_rows(array $totest) {
        global $DB;

        foreach ($totest as $el) {
            if (count($el) < 3) {
                throw new coding_exception('validate_num_rows require 3 array elements for each table to test');
            }

            $rows = $DB->get_records($el[0], $el[1]);
            $this->assertSame((int)$el[2], count($rows));
        }
    }

    /**
     * Validate that the expected roles are stored for the the pathway
     *
     * @param int $instance_id Pathway instance id
     * @param array $expected_roles Array of expected roles
     */
    private function validate_roles(int $instance_id, array $expected_roles) {
        global $DB;

        $rows = $DB->get_records('pathway_manual_role', ['path_manual_id' => $instance_id]);

        $this->assertSame(count($expected_roles), count($rows));
        while ($row = array_pop($rows)) {
            $this->assertTrue(in_array($row->role, $expected_roles));
        }
    }

}
