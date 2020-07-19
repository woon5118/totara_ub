<?php
/**
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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package totara_evidence
 * @category test
 */

use core\orm\query\builder;

global $CFG;
require_once($CFG->dirroot . '/totara/evidence/db/install.php');
require_once($CFG->dirroot . '/totara/evidence/db/upgradelib.php');
require_once($CFG->dirroot . '/totara/evidence/tests/generator/lib.php');

abstract class totara_evidence_migration_testcase extends advanced_testcase {

    protected function setUp(): void {
        global $DB;
        parent::setUp();

        // Delete the system types that are installed by default
        $DB->delete_records('totara_evidence_type');
        $DB->delete_records('totara_evidence_type_info_field');

        $this->create_tables();
    }

    protected function tearDown(): void {
        parent::tearDown();
        $this->drop_tables();
    }

    protected function onNotSuccessfulTest(Throwable $t): void {
        $this->drop_tables();
        $this->purge_files();
        parent::onNotSuccessfulTest($t);
    }

    /**
     * @return object[]
     */
    protected function create_fields(): array {
        return [
            $this->generator()->create_evidence_field([
                'fullname' => 'Field 1', 'shortname' => 'oldtypename', 'sortorder' => 4, 'datatype' => 'checkbox',
            ]),
            $this->generator()->create_evidence_field([
                'fullname' => 'Field 2', 'shortname' => 'oldtypename1', 'sortorder' => 3, 'datatype' => 'checkbox',
            ]),
            $this->generator()->create_evidence_field([
                'fullname' => 'Field 3', 'shortname' => 'oldtypename2', 'sortorder' => 2, 'datatype' => 'checkbox',
            ]),
            $this->generator()->create_evidence_field([
                'fullname' => 'Field 4', 'shortname' => 'oldtypename4', 'sortorder' => 1, 'datatype' => 'checkbox',
            ]),
        ];
    }

    /**
     * @return object[]
     */
    protected function create_types(): array {
        return [
            $this->generator()->create_evidence_type([
                'name' => 'Type 1',
                'description' => 'Type 1 Description',
                'timemodified' => '1',
                'usermodified' => '1',
                'sortorder' => '2',
            ]),
            $this->generator()->create_evidence_type([
                'name' => 'Type 2',
                'description' => 'Type 2 Description',
                'timemodified' => '2',
                'usermodified' => '2',
                'sortorder' => '3',
            ]),
            $this->generator()->create_evidence_type([
                'name' => 'Type 3',
                'description' => 'Type 3 Description',
                'timemodified' => '3',
                'usermodified' => '3',
                'sortorder' => '1',
            ]),
        ];
    }

    protected function generator(): totara_evidence_migration_generator {
        global $CFG;
        return new totara_evidence_migration_generator(new testing_data_generator());
    }

    protected function plan_generator(): totara_plan_generator {
        global $CFG;
        require_once("$CFG->dirroot/totara/plan/tests/generator/totara_plan_generator.class.php");
        return new totara_plan_generator(new testing_data_generator());
    }

    private function create_tables(): void {
        global $DB;
        $dbman = $DB->get_manager();

        $table = new xmldb_table('dp_plan_evidence');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10');
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10');
        $table->add_field('evidencetypeid', XMLDB_TYPE_INTEGER, '10');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10');
        $table->add_field('readonly', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('evidencetypeid', XMLDB_KEY_FOREIGN, ['evidencetypeid'], 'dp_evidence_type', ['id']);
        $table->add_key('dpplanevid_user_fk', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
        $table->add_key('dpplanevid_use_fk', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $dbman->create_table($table);

        $table = new xmldb_table('dp_plan_evidence_info_field');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('fullname', XMLDB_TYPE_CHAR, '1333');
        $table->add_field('shortname', XMLDB_TYPE_CHAR, '1333');
        $table->add_field('datatype', XMLDB_TYPE_CHAR, '255');
        $table->add_field('description', XMLDB_TYPE_TEXT);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('hidden', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL);
        $table->add_field('locked', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL);
        $table->add_field('required', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL);
        $table->add_field('forceunique', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL);
        $table->add_field('defaultdata', XMLDB_TYPE_TEXT);
        $table->add_field('param1', XMLDB_TYPE_TEXT);
        $table->add_field('param2', XMLDB_TYPE_TEXT);
        $table->add_field('param3', XMLDB_TYPE_TEXT);
        $table->add_field('param4', XMLDB_TYPE_TEXT);
        $table->add_field('param5', XMLDB_TYPE_TEXT);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $dbman->create_table($table);

        $table = new xmldb_table('dp_plan_evidence_info_data');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('fieldid', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL);
        $table->add_field('evidenceid', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL);
        $table->add_field('data', XMLDB_TYPE_TEXT);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('dpplanevidenceinfodata_fie_fk', XMLDB_KEY_FOREIGN, ['fieldid'], 'dp_plan_evidence_info_field', ['id']);
        $table->add_key('dpplanevidenceinfodata_evi_fk', XMLDB_KEY_FOREIGN, ['evidenceid'], 'dp_plan_evidence', ['id']);
        $table->add_index('dpplanevidinfodata_fieevi_uix', XMLDB_INDEX_UNIQUE, ['fieldid', 'evidenceid']);
        $dbman->create_table($table);

        $table = new xmldb_table('dp_plan_evidence_info_data_param');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('dataid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('value', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('dpplanevidenceinfodata_para_dat_fk', XMLDB_KEY_FOREIGN, ['dataid'], 'dp_plan_evidence_info_data', ['id']);
        $table->add_index('dpplanevidenceinfodata_val_ix', XMLDB_INDEX_NOTUNIQUE, ['value']);
        $dbman->create_table($table);

        $table = new xmldb_table('dp_evidence_type');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL);
        $table->add_field('description', XMLDB_TYPE_TEXT);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('dpevidtype_use_fk', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
        $dbman->create_table($table);
    }

    private function drop_tables(): void {
        global $DB;
        $dbman = $DB->get_manager();

        $old_tables = [
            new xmldb_table('dp_plan_evidence'),
            new xmldb_table('dp_plan_evidence_info_field'),
            new xmldb_table('dp_plan_evidence_info_data'),
            new xmldb_table('dp_plan_evidence_info_data_param'),
            new xmldb_table('dp_evidence_type'),
        ];

        foreach ($old_tables as $table) {
            if ($dbman->table_exists($table)) {
                $dbman->drop_table($table);
            }
        }
    }

    private function purge_files(): void {
        $fs = get_file_storage();
        $context = context_system::instance()->id;
        $fs->delete_area_files($context, 'totara_customfield', 'evidence');
        $fs->delete_area_files($context, 'totara_customfield', 'evidence_filemgr');
        $fs->delete_area_files($context, 'totara_customfield', 'textarea');
        $fs->delete_area_files($context, 'totara_customfield', 'old_evidence');
        $fs->delete_area_files($context, 'totara_customfield', 'old_evidence_filemgr');
        $fs->delete_area_files($context, 'totara_customfield', 'old_evidence_textarea');
        $fs->delete_area_files($context, 'totara_evidence', 'type');
        $fs->delete_area_files($context, 'totara_plan', 'dp_evidence_type');
    }

}

class totara_evidence_migration_generator extends totara_evidence_generator {

    protected $evidence_fields = [];
    protected $evidence_fields_count = 0;

    /**
     * @param array $record
     * @return object
     */
    public function create_evidence_type(array $record = []) {
        $type_id = builder::table('dp_evidence_type')->insert(array_merge([
            'description' => "Type Description $this->evidence_types_count",
            'timemodified' => $this->evidence_types_count,
            'usermodified' => $this->evidence_types_count,
            'sortorder' => $this->evidence_types_count,
        ], $record));
        $record = builder::table('dp_evidence_type')->find($type_id);

        $this->evidence_types[] = $record;
        $this->evidence_types_count++;

        return $record;
    }

    /**
     * @param array $record
     * @return object
     */
    public function create_evidence_field(array $record = []) {
        $field_num = $this->number_padding($this->evidence_fields_count + 1, $this->max_evidence_fields);
        $field = $this->evidence_field_data[rand(0, count($this->evidence_field_data) - 1)];

        $field_id = builder::table('dp_plan_evidence_info_field')->insert(array_merge([
            'hidden'      => '0',
            'locked'      => '0',
            'required'    => '0',
            'forceunique' => '0',
            'fullname'    => 'Custom Field #' . $field_num,
            'shortname'   => 'FIELD' . $field_num,
            'description' => 'Extra Custom Field Number ' . $field_num,
            'sortorder' => $this->max_evidence_fields - $this->evidence_fields_count,
        ], $field, $record));
        $field = builder::table('dp_plan_evidence_info_field')->find($field_id);

        $this->evidence_fields[] = $field;
        $this->evidence_fields_count++;

        return $field;
    }

    /**
     * @param array $record
     * @return object
     */
    public function create_evidence_item(array $record = []) {
        $item_id = builder::table('dp_plan_evidence')->insert(array_merge([
            'name' => "Evidence #$this->evidence_items_count",
            'evidencetypeid' => 0,
            'timecreated' => $this->evidence_items_count,
            'timemodified' => $this->evidence_items_count,
            'usermodified' => $this->evidence_items_count,
            'userid' => $this->evidence_items_count,
        ], $record));
        $item_record = builder::table('dp_plan_evidence')->find($item_id);

        $this->create_evidence_field_data(
            $item_record,
            builder::table('dp_plan_evidence_info_field')->get(),
            'evidence',
            'dp_plan_evidence'
        );

        $this->evidence_items[] = $item_record;
        $this->evidence_items_count++;
        return $item_record;
    }

    /**
     * @param array $record
     * @param string|null $content
     * @return stored_file
     */
    public function create_test_file(array $record, string $content = null): stored_file {
        return get_file_storage()->create_file_from_string(
            array_merge([
                'contextid' => context_system::instance()->id,
                'component' => 'totara_customfield',
                'filearea' => 'evidence',
                'filepath' => '/',
                'filename' => random_string() . '.txt',
            ], $record),
            $content
        );
    }

    /**
     * @param int $evidence_id
     * @param int $plan_id
     * @param int $item_id
     * @param string $component
     * @return object
     */
    public function create_evidence_relation(int $evidence_id, int $plan_id,
                                             int $item_id, string $component = 'objective'): object {
        $relation_id = builder::table('dp_plan_evidence_relation')->insert([
            'evidenceid' => $evidence_id,
            'planid' => $plan_id,
            'itemid' => $item_id,
            'component' => $component
        ]);
        return builder::table('dp_plan_evidence_relation')->find($relation_id);
    }

}
