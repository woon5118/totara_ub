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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package core_orm
 * @category test
 */

use core\orm\collection;
use core\orm\entity\entity;
use core\orm\entity\relations\belongs_to;
use core\orm\entity\relations\has_many;
use core\orm\entity\relations\has_many_through;
use core\orm\entity\relations\has_one;

defined('MOODLE_INTERNAL') || die();

/**
 * Class orm_entity_relation_testcase
 *
 * @package core
 * @group orm
 */
abstract class orm_entity_relation_testcase extends advanced_testcase {

    /**
     * Date generator shortcut
     *
     * @return testing_data_generator
     */
    protected function generator() {
        return self::getDataGenerator();
    }

    /**
     * Remove the created table after test
     */
    protected function tearDown() {
        parent::tearDown();
        $this->drop_tables();
    }

    /**
     * @return moodle_database
     */
    protected function db() {
        return $GLOBALS['DB'];
    }

    /**
     * @return database_manager
     */
    protected function db_man() {
        return $this->db()->get_manager();
    }

    /**
     * Create sample table for the entity
     */
    protected function create_tables() {
        $this->create_parent_table()
            ->create_child_table()
            ->create_passport_table();
    }

    /**
     * Create table for sample_parent_entity::class
     *
     * @return $this
     */
    protected function create_parent_table() {
        $name = sample_parent_entity::TABLE;

        if ($this->db_man()->table_exists($name)) {
            return $this;
        }

        $table = new xmldb_table($name);

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'John Doe');

        $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null);
        $table->add_field('updated_at', XMLDB_TYPE_INTEGER, '10', null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        $this->db_man()->create_table($table);

        return $this;
    }

    /**
     * Drop table for sample_parent_entity::class
     *
     * @return $this
     */
    protected function drop_parent_table() {
        return $this->drop_table(sample_parent_entity::class);
    }

    /**
     * Create table for sample_passport_entity::class
     *
     * @return $this
     */
    protected function create_passport_table() {
        $name = sample_passport_entity::TABLE;

        if ($this->db_man()->table_exists($name)) {
            return $this;
        }

        $table = new xmldb_table($name);

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'John Doe');

        $table->add_field('parent_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);

        $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null);
        $table->add_field('updated_at', XMLDB_TYPE_INTEGER, '10', null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        $this->db_man()->create_table($table);

        return $this;
    }

    /**
     * Drop table for sample_passport_entity::class
     *
     * @return $this
     */
    protected function drop_passport_table() {
        return $this->drop_table(sample_passport_entity::class);
    }

    /**
     * Create table for sample_parent_entity::class
     *
     * @return $this
     */
    protected function create_child_table() {
        $name = sample_child_entity::TABLE;

        if ($this->db_man()->table_exists($name)) {
            return $this;
        }

        $table = new xmldb_table($name);

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'John Doe');
        $table->add_field('type', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
        $table->add_field('parent_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('description', XMLDB_TYPE_TEXT);

        $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null);
        $table->add_field('updated_at', XMLDB_TYPE_INTEGER, '10', null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        $this->db_man()->create_table($table);

        return $this;
    }

    /**
     * Drop table for sample_child_entity::class
     *
     * @return $this
     */
    protected function drop_child_table() {
        return $this->drop_table(sample_child_entity::class);
    }

    /**
     * Drop table with a given name
     *
     * @param string $name Table name
     * @return $this
     */
    protected function drop_table(string $name) {
        if ($this->db_man()->table_exists($name)) {
            $this->db_man()->drop_table(new xmldb_table($name));
        }

        return $this;
    }

    /**
     * Remove the created table after test
     *
     * @return $this
     */
    protected function drop_tables() {
        return $this->drop_child_table()
            ->drop_parent_table()
            ->drop_passport_table();
    }

    /**
     * Returns a set of sample records to test against
     *
     * @return array
     */
    protected function sample_records() {
        return [
            sample_parent_entity::class => [
                [
                    'name' => 'Mobile phones',
                    'created_at' => time(),
                    'updated_at' => time(),
                ],
                [
                    'name' => 'Tablets',
                    'created_at' => time(),
                    'updated_at' => time(),
                ],
                [
                    'name' => 'Personal Computers',
                    'created_at' => time(),
                    'updated_at' => time(),
                ],
                [
                    'name' => 'Calculators',
                    'created_at' => '946684799', // A second prior to another millennium
                    'updated_at' => '946684799', // A second prior to another millennium
                ],
                [
                    'name' => 'Bluetooth speakers',
                    'created_at' => '946684798', // Two seconds prior to another millennium
                    'updated_at' => '946684798', // Two seconds prior to another millennium
                ],
            ],

            sample_passport_entity::class => [
                [
                    'name' => 'Precision passport',
                    'parent_id' => 4,
                    'created_at' => '946684799', // A second prior to another millennium
                    'updated_at' => '946684799', // A second prior to another millennium
                ],
                [
                    'name' => 'Performance passport',
                    'parent_id' => 3,
                    'created_at' => time(),
                    'updated_at' => time(),
                ],
                [
                    'name' => 'Durability passport',
                    'parent_id' => 2,
                    'created_at' => time(),
                    'updated_at' => time(),
                ],
                [
                    'name' => 'Quality passport',
                    'parent_id' => 1,
                    'created_at' => time(),
                    'updated_at' => time(),
                ],
            ],

            sample_child_entity::class => [
                // Mobile phones:
                [
                    'name' => 'Samsung Galaxy 6',
                    'type' => 1,
                    'parent_id' => 1,
                    'description' => '',
                    'created_at' => '633679200', // 30th Jan 1990
                    'updated_at' => '633679200', // 30th Jan 1990
                ],
                [
                    'name' => 'Samsung Galaxy Note',
                    'type' => 1,
                    'parent_id' => 1,
                    'description' => '',
                    'created_at' => '946684799', // A second prior to another millennium
                    'updated_at' => '946684799', // A second prior to another millennium
                ],
                [
                    'name' => 'Apple iPhone X',
                    'type' => 1,
                    'parent_id' => 1,
                    'description' => '',
                    'created_at' => time(), // Now
                    'updated_at' => time(), // Now
                ],
                // Tablets:
                [
                    'name' => 'Apple iPad',
                    'type' => 2,
                    'parent_id' => 2,
                    'description' => '',
                    'created_at' => '633679200', // 30th Jan 1990
                    'updated_at' => '633679200', // 30th Jan 1990
                ],
                [
                    'name' => 'Samsung Galaxy Tab',
                    'type' => 3,
                    'parent_id' => 2,
                    'description' => '',
                    'created_at' => '633679200', // 30th Jan 1990
                    'updated_at' => '633679200', // 30th Jan 1990
                ],
                [
                    'name' => 'Lenovo Tab',
                    'type' => 3,
                    'parent_id' => 2,
                    'description' => '',
                    'created_at' => '633679200', // 30th Jan 1990
                    'updated_at' => '633679200', // 30th Jan 1990
                ],
                // Personal computers
                [
                    'name' => 'HP Personal computer',
                    'type' => 4,
                    'parent_id' => 3,
                    'description' => '',
                    'created_at' => '1356048000', // 21th Dec 2012
                    'updated_at' => '1356048000', // 21th Dec 2012
                ],
                [
                    'name' => 'DELL Personal computer',
                    'type' => 4,
                    'parent_id' => 3,
                    'description' => '',
                    'created_at' => '1356048000', // 21th Dec 2012
                    'updated_at' => '1356048000', // 21th Dec 2012
                ],
                [
                    'name' => 'Apple iMac PRO',
                    'type' => 5,
                    'parent_id' => 3,
                    'description' => '',
                    'created_at' => '633679200', // 21th Dec 2012
                    'updated_at' => '633679200', // 21th Dec 2012
                ],
                // Calculators:
                [
                    'name' => 'Casio vintage calculator',
                    'type' => 6,
                    'parent_id' => 4,
                    'description' => '',
                    'created_at' => time(), // Now
                    'updated_at' => time(), // Now
                ],
                [
                    'name' => 'Citizen vintage calculator',
                    'type' => 6,
                    'parent_id' => 4,
                    'description' => '',
                    'created_at' => time(), // Now
                    'updated_at' => time(), // Now
                ],
                [
                    'name' => 'Modern scientific calculator',
                    'type' => 7,
                    'parent_id' => 4,
                    'description' => '',
                    'created_at' => time(), // Now
                    'updated_at' => time(), // Now
                ],
                // Bluetooth speakers:
                // There is no bluetooth speakers currently available... :(

                // Orphan record
                [
                    'name' => 'The Big Unknown',
                    'type' => 69,
                    'parent_id' => 0,
                    'description' => 'The Big Unknown is so well known so it doesn\'t need a description',
                    'created_at' => 0, // Now
                    'updated_at' => 0, // Now
                ],
            ]
        ];
    }

    /**
     * Populate database with a few sample record to test more complex things like sorting, filtering, etc.
     *
     * @return array
     */
    protected function create_sample_records() {

        $this->create_tables();
        $records = $this->sample_records();

        foreach ($records as $entity => &$items) {
            foreach ($items as &$record) {
                $record['id'] = $this->db()->insert_record($entity::TABLE, (object) $record);
            }
        }

        return $records;
    }

}

/**
 * Class sample_entity used for testing a entity
 *
 * @property string $name
 * @property int $created_at
 * @property int $updated_at
 * @property-read collection $children Children items
 * @property-read sample_passport_entity $passport Passport entity
 */
class sample_parent_entity extends entity {

    public const TABLE = 'test__sample_parent';

    public const CREATED_TIMESTAMP = 'created_at';
    public const UPDATED_TIMESTAMP = 'updated_at';

    /**
     * Passport
     *
     * @return has_one
     */
    public function passport(): has_one {
        return $this->has_one(sample_passport_entity::class, 'parent_id');
    }

    /**
     * Children relationship
     *
     * @return has_many
     */
    public function children(): has_many {
        return $this->has_many(sample_child_entity::class, 'parent_id');
    }

    public function reversed_children(): has_many {
        return $this->has_many(sample_child_entity::class, 'parent_id')
            ->order_by('id', 'desc');
    }
}

/**
 * Class sample_entity used for testing a entity
 *
 * @property string $name
 * @property int $created_at
 * @property int $updated_at
 * @property-read collection $children Children items
 */
class sample_passport_entity extends entity {

    public const TABLE = 'test__sample_passport';

    public const CREATED_TIMESTAMP = 'created_at';
    public const UPDATED_TIMESTAMP = 'updated_at';

    protected $with = [
        'parent'
    ];

    /**
     * Children relationship
     *
     * @return belongs_to
     */
    public function parent(): belongs_to {
        return $this->belongs_to(sample_parent_entity::class, 'parent_id');
    }

    public function children(): has_many_through {
        return $this->has_many_through(sample_child_entity::class, sample_parent_entity::class, 'id', 'parent_id', 'parent_id', 'id');
    }
}

/**
 * Class sample_entity used for testing a entity
 *
 * @property-read string $capital_name Name returned in capital case
 * @property-read sample_parent_entity $parent Name
 */
class sample_child_entity extends entity {

    public const TABLE = 'test__sample_child';

    public function parent(): belongs_to {
        return $this->belongs_to(sample_parent_entity::class, 'parent_id');
    }

    public function type(): has_one {
        return $this->has_one(sample_child_entity::class, 'id');
    }

    public function a_type(): belongs_to {
        return $this->belongs_to(sample_child_entity::class, 'type');
    }

    public const CREATED_TIMESTAMP = 'created_at';
    public const UPDATED_TIMESTAMP = 'updated_at';
}
