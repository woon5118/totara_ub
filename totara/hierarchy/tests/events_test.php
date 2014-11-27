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
 * @author David Curry <david.curry@totaralms.com>
 * @package totara
 * @subpackage hierarchy
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

global $CFG;
require_once($CFG->dirroot . '/totara/hierarchy/lib.php');

class hierarchy_event_test extends advanced_testcase {

    // TODO - extend the hierarchy datagenerator to support types.
    protected $pos_type_data = array(
        'id' => 1, 'fullname' => 'Position Type', 'shortname' => 'PosType', 'description' => 'Test Description', 'idnumber' => 'PosType_001',
        'timecreated' => '1234567890', 'timemodified' => '1234567890', 'usermodified' => '2',
    );

    protected $org_type_data = array(
        'id' => 1, 'fullname' => 'Organisation Type', 'shortname' => 'OrgType', 'description' => 'Test Description', 'idnumber' => 'OrgType_001',
        'timecreated' => '1234567890', 'timemodified' => '1234567890', 'usermodified' => '2',
    );

    protected $comp_type_data = array(
        'id' => 1, 'fullname' => 'Competency Type', 'shortname' => 'CompType', 'description' => 'Test Description', 'idnumber' => 'CompType_001',
        'timecreated' => '1234567890', 'timemodified' => '1234567890', 'usermodified' => '2',
    );

    protected $goal_type_data = array(
        'id' => 1, 'fullname' => 'Goal Type', 'shortname' => 'GoalType', 'description' => 'Test Description', 'idnumber' => 'GoalType_001',
        'timecreated' => '1234567890', 'timemodified' => '1234567890', 'usermodified' => '2',
    );

    protected $type_changed_data = array(
        'itemid' => 1, 'oldtype' => 0, 'newtype' => 1,
    );

    // TODO - extend the hierarchy datagenerator to support scales.
    protected $comp_scale_data = array(
        'id' => 1, 'name' => 'Competency Scale', 'description' => 'Test Description', 'timemodified' => 1234567890, 'usermodified' => 2, 'defaultid' => 1
    );

    protected $comp_scale_value_data = array(
        'id' => 1, 'name' => 'Competency Assigned', 'idnumber' => 'goalsv_01', 'description' => 'Test Description', 'scaleid' => 1,
        'timemodified' => 1234567890, 'usermodified' => 2, 'proficient' => 0, 'numericscore' => 1, 'sortorder' => 1,
    );

    protected $goal_scale_data = array(
        'id' => 1, 'name' => 'Goal Scale', 'description' => 'Test Description', 'timemodified' => 1234567890, 'usermodified' => 2, 'defaultid' => 1
    );

    protected $goal_scale_value_data = array(
        'id' => 1, 'name' => 'Goal Assigned', 'idnumber' => 'goalsv_01', 'description' => 'Test Description', 'scaleid' => 1,
        'timemodified' => 1234567890, 'usermodified' => 2, 'proficient' => 0, 'numericscore' => 1, 'sortorder' => 1,
    );

    protected $pos_framework, $org_framework, $comp_framework, $goal_framework;
    protected $pos, $org, $comp, $comp2, $goal;
    protected $user, $cohort, $course, $module;

    public function setUp() {
        global $DB;

        parent::setup();
        $this->resetAfterTest(true);
        $datagen = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');

        // Set up some variables for the tests.
        $this->pos_framework = $datagen->create_framework('position');
        $this->org_framework = $datagen->create_framework('organisation');
        $this->comp_framework = $datagen->create_framework('competency');
        $this->goal_framework = $datagen->create_framework('goal');

        $this->pos = $datagen->create_hierarchy($this->pos_framework->id, 'position');
        $this->org = $datagen->create_hierarchy($this->org_framework->id, 'organisation');
        $this->comp = $datagen->create_hierarchy($this->comp_framework->id, 'competency');
        $this->comp2 = $datagen->create_hierarchy($this->comp_framework->id, 'competency');
        $this->goal = $datagen->create_hierarchy($this->goal_framework->id, 'goal');

        $this->user = $this->getDataGenerator()->create_user();
        $this->cohort = $this->getDataGenerator()->create_cohort();
        $this->course = $this->getDataGenerator()->create_course();
        $record = new stdClass();
        $record->course = $this->course->id;
        $this->module = $this->getDataGenerator()->create_module('choice', $record);
    }


    public function test_framework_events() {
        $this->resetAfterTest();
        $sink = $this->redirectEvents();

        // TODO - move framework functionality into lib functions & write tests.
        $this->assertTrue(true);
        $sink->close();
    }

    public function test_framework_legacyevents() {
        // Test Position Framework Created legacy data.
        $event = \hierarchy_position\event\framework_created::create_from_instance($this->pos_framework);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/framework/index.php', array('prefix' => 'position', 'frameworkid' => $this->pos_framework->id));
        $olddata = array(SITEID, 'position', 'framework create', $oldurl, "position framework: {$this->pos_framework->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Position Framework Updated legacy data.
        $event = \hierarchy_position\event\framework_updated::create_from_instance($this->pos_framework);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/framework/view.php', array('prefix' => 'position', 'frameworkid' => $this->pos_framework->id));
        $olddata = array(SITEID, 'position', 'framework update', $oldurl, "position framework: {$this->pos_framework->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Position Framework Deleted legacy data.
        $event = \hierarchy_position\event\framework_deleted::create_from_instance($this->pos_framework);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/framework/index.php', array('prefix' => 'position'));
        $olddata = array(SITEID, 'position', 'framework delete', $oldurl, "position framework: {$this->pos_framework->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Organisation Framework Created legacy data.
        $event = \hierarchy_organisation\event\framework_created::create_from_instance($this->org_framework);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/framework/index.php', array('prefix' => 'organisation', 'frameworkid' => $this->org_framework->id));
        $olddata = array(SITEID, 'organisation', 'framework create', $oldurl, "organisation framework: {$this->org_framework->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Organisation Framework Updated legacy data.
        $event = \hierarchy_organisation\event\framework_updated::create_from_instance($this->org_framework);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/framework/view.php', array('prefix' => 'organisation', 'frameworkid' => $this->org_framework->id));
        $olddata = array(SITEID, 'organisation', 'framework update', $oldurl, "organisation framework: {$this->org_framework->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Organisation Framework Deleted legacy data.
        $event = \hierarchy_organisation\event\framework_deleted::create_from_instance($this->org_framework);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/framework/index.php', array('prefix' => 'organisation'));
        $olddata = array(SITEID, 'organisation', 'framework delete', $oldurl, "organisation framework: {$this->org_framework->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Competency Framework Created legacy data.
        $event = \hierarchy_competency\event\framework_created::create_from_instance($this->comp_framework);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/framework/index.php', array('prefix' => 'competency', 'frameworkid' => $this->comp_framework->id));
        $olddata = array(SITEID, 'competency', 'framework create', $oldurl, "competency framework: {$this->comp_framework->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Competency Framework Updated legacy data.
        $event = \hierarchy_competency\event\framework_updated::create_from_instance($this->comp_framework);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/framework/view.php', array('prefix' => 'competency', 'frameworkid' => $this->comp_framework->id));
        $olddata = array(SITEID, 'competency', 'framework update', $oldurl, "competency framework: {$this->comp_framework->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Competency Framework Deleted legacy data.
        $event = \hierarchy_competency\event\framework_deleted::create_from_instance($this->comp_framework);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/framework/index.php', array('prefix' => 'competency'));
        $olddata = array(SITEID, 'competency', 'framework delete', $oldurl, "competency framework: {$this->comp_framework->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Goal Framework Created legacy data.
        $event = \hierarchy_goal\event\framework_created::create_from_instance($this->goal_framework);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/framework/index.php', array('prefix' => 'goal', 'frameworkid' => $this->goal_framework->id));
        $olddata = array(SITEID, 'goal', 'framework create', $oldurl, "goal framework: {$this->goal_framework->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Goal Framework Updated legacy data.
        $event = \hierarchy_goal\event\framework_updated::create_from_instance($this->goal_framework);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/framework/view.php', array('prefix' => 'goal', 'frameworkid' => $this->goal_framework->id));
        $olddata = array(SITEID, 'goal', 'framework update', $oldurl, "goal framework: {$this->goal_framework->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Goal Framework Deleted legacy data.
        $event = \hierarchy_goal\event\framework_deleted::create_from_instance($this->goal_framework);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/framework/index.php', array('prefix' => 'goal'));
        $olddata = array(SITEID, 'goal', 'framework delete', $oldurl, "goal framework: {$this->goal_framework->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Hierarchy Framework Viewed legacy data.
        $event = \totara_hierarchy\event\framework_viewed::create_from_prefix('position');
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/framework/index.php', array('prefix' => 'position'));
        $olddata = array(SITEID, 'position', 'view framework', $oldurl, "position framework list");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Hierarchy Framework Exported legacy data.
        $event = \totara_hierarchy\event\framework_exported::create_from_instance('position', $this->pos_framework);
        $legacydata = $event->get_legacy_logdata();
        $urlparams = array('id' => $this->pos_framework->id, 'prefix' => 'position');
        $oldurl = new moodle_url('/totara/hierarchy/framework/index.php', $urlparams);
        $olddata = array(SITEID, 'position', 'export framework', $oldurl, "position framework: {$this->pos_framework->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);
    }

    public function test_type_events() {
        $this->resetAfterTest();
        $sink = $this->redirectEvents();

        // TODO - move type functionality into lib functions & write tests.
        $this->assertTrue(true);

        $sink->close();
    }

    public function test_type_legacyevents() {
        // Set up some variables for the tests.
        $pos_type = (object) $this->pos_type_data;
        $org_type = (object) $this->org_type_data;
        $comp_type = (object) $this->comp_type_data;
        $goal_type = (object) $this->goal_type_data;
        $type_changed = $this->type_changed_data;

        // Test Position Type Created legacy data.
        $event = \hierarchy_position\event\type_created::create_from_instance($pos_type);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/type/index.php', array('prefix' => 'position'));
        $olddata = array(SITEID, 'position', 'create type', $oldurl, "position type: {$pos_type->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Position Type Updated legacy data.
        $event = \hierarchy_position\event\type_updated::create_from_instance($pos_type);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/type/edit.php', array('id' => $pos_type->id));
        $olddata = array(SITEID, 'position', 'update type', $oldurl, "position type: {$pos_type->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Position Type Deleted legacy data.
        $event = \hierarchy_position\event\type_deleted::create_from_instance($pos_type);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/type/index.php', array('prefix' => 'position'));
        $olddata = array(SITEID, 'position', 'delete type', $oldurl, "position type: {$pos_type->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Position Type Changed legacy data.
        $event = \hierarchy_position\event\type_changed::create_from_dataobject($type_changed);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/edit.php', array('prefix' => 'position', 'id' => $type_changed['itemid']));
        $olddesc = "position: {$type_changed['itemid']} (type: {$type_changed['oldtype']} -> type: {$type_changed['newtype']})";
        $olddata = array(SITEID, 'position', 'change type', $oldurl, $olddesc);

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Organisation Type Created legacy data.
        $event = \hierarchy_organisation\event\type_created::create_from_instance($org_type);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/type/index.php', array('prefix' => 'organisation'));
        $olddata = array(SITEID, 'organisation', 'create type', $oldurl, "organisation type: {$org_type->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Organisation Type Updated legacy data.
        $event = \hierarchy_organisation\event\type_updated::create_from_instance($org_type);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/type/edit.php', array('id' => $org_type->id));
        $olddata = array(SITEID, 'organisation', 'update type', $oldurl, "organisation type: {$org_type->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Organisation Type Deleted legacy data.
        $event = \hierarchy_organisation\event\type_deleted::create_from_instance($org_type);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/type/index.php', array('prefix' => 'organisation'));
        $olddata = array(SITEID, 'organisation', 'delete type', $oldurl, "organisation type: {$org_type->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Organisation Type Changed legacy data.
        $event = \hierarchy_organisation\event\type_changed::create_from_dataobject($type_changed);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/edit.php', array('prefix' => 'organisation', 'id' => $type_changed['itemid']));
        $olddesc = "organisation: {$type_changed['itemid']} (type: {$type_changed['oldtype']} -> type: {$type_changed['newtype']})";
        $olddata = array(SITEID, 'organisation', 'change type', $oldurl, $olddesc);

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Competency Type Created legacy data.
        $event = \hierarchy_competency\event\type_created::create_from_instance($comp_type);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/type/index.php', array('prefix' => 'competency'));
        $olddata = array(SITEID, 'competency', 'create type', $oldurl, "competency type: {$comp_type->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Competency Type Updated legacy data.
        $event = \hierarchy_competency\event\type_updated::create_from_instance($comp_type);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/type/edit.php', array('id' => $comp_type->id));
        $olddata = array(SITEID, 'competency', 'update type', $oldurl, "competency type: {$comp_type->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Competency Type Deleted legacy data.
        $event = \hierarchy_competency\event\type_deleted::create_from_instance($comp_type);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/type/index.php', array('prefix' => 'competency'));
        $olddata = array(SITEID, 'competency', 'delete type', $oldurl, "competency type: {$comp_type->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Competency Type Changed legacy data.
        $event = \hierarchy_competency\event\type_changed::create_from_dataobject($type_changed);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/edit.php', array('prefix' => 'competency', 'id' => $type_changed['itemid']));
        $olddesc = "competency: {$type_changed['itemid']} (type: {$type_changed['oldtype']} -> type: {$type_changed['newtype']})";
        $olddata = array(SITEID, 'competency', 'change type', $oldurl, $olddesc);

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Goal Type Created legacy data.
        $event = \hierarchy_goal\event\type_created::create_from_instance($goal_type);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/type/index.php', array('prefix' => 'goal'));
        $olddata = array(SITEID, 'goal', 'create type', $oldurl, "goal type: {$goal_type->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Goal Type Updated legacy data.
        $event = \hierarchy_goal\event\type_updated::create_from_instance($goal_type);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/type/edit.php', array('id' => $goal_type->id));
        $olddata = array(SITEID, 'goal', 'update type', $oldurl, "goal type: {$goal_type->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Goal Type Deleted legacy data.
        $event = \hierarchy_goal\event\type_deleted::create_from_instance($goal_type);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/type/index.php', array('prefix' => 'goal'));
        $olddata = array(SITEID, 'goal', 'delete type', $oldurl, "goal type: {$goal_type->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Goal Type Changed legacy data.
        $event = \hierarchy_goal\event\type_changed::create_from_dataobject($type_changed);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/edit.php', array('prefix' => 'goal', 'id' => $type_changed['itemid']));
        $olddesc = "goal: {$type_changed['itemid']} (type: {$type_changed['oldtype']} -> type: {$type_changed['newtype']})";
        $olddata = array(SITEID, 'goal', 'change type', $oldurl, $olddesc);

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Hierarchy Type View legacy data.
        $event = \totara_hierarchy\event\type_viewed::create_from_prefix('position');
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/type/index.php', array('prefix' => 'position'));
        $olddata = array(SITEID, 'position', 'view type list', $oldurl, 'position type list');

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);
    }

    public function test_item_events() {
        $this->resetAfterTest();
        $sink = $this->redirectEvents();

        // TODO - move type functionality into lib functions & write tests.
        $this->assertTrue(true);
    }

    public function test_item_legacyevents() {
        // Test Position Item Created legacy data.
        $event = \hierarchy_position\event\position_created::create_from_instance($this->pos);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/view.php', array('id' => $this->pos->id, 'prefix' => 'position'));
        $olddata = array(SITEID, 'position', 'added item', $oldurl, "position: {$this->pos->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Position Item Deleted legacy data.
        $event = \hierarchy_position\event\position_deleted::create_from_instance($this->pos);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/index.php', array('id' => $this->pos_framework->id, 'prefix' => 'position'));
        $olddata = array(SITEID, 'position', 'delete item', $oldurl, "position: {$this->pos->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Position Item Viewed legacy data.
        $event = \hierarchy_position\event\position_viewed::create_from_instance($this->pos);
        $legacydata = $event->get_legacy_logdata();
        $urlparams = array('prefix' => 'position', 'id' => $this->pos->id);
        $oldurl = new moodle_url('/totara/hierarchy/item/view.php', $urlparams);
        $olddata = array(SITEID, 'position', 'view item', $oldurl, "position: {$this->pos->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Organisation Item Created legacy data.
        $event = \hierarchy_organisation\event\organisation_created::create_from_instance($this->org);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/view.php', array('id' => $this->org->id, 'prefix' => 'organisation'));
        $olddata = array(SITEID, 'organisation', 'added item', $oldurl, "organisation: {$this->org->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Organisation Item Deleted legacy data.
        $event = \hierarchy_organisation\event\organisation_deleted::create_from_instance($this->org);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/index.php', array('id' => $this->org_framework->id, 'prefix' => 'organisation'));
        $olddata = array(SITEID, 'organisation', 'delete item', $oldurl, "organisation: {$this->org->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Organisation Item Viewed legacy data.
        $event = \hierarchy_organisation\event\organisation_viewed::create_from_instance($this->org);
        $legacydata = $event->get_legacy_logdata();
        $urlparams = array('prefix' => 'organisation', 'id' => $this->org->id);
        $oldurl = new moodle_url('/totara/hierarchy/item/view.php', $urlparams);
        $olddata = array(SITEID, 'organisation', 'view item', $oldurl, "organisation: {$this->org->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Competency Item Created legacy data.
        $event = \hierarchy_competency\event\competency_created::create_from_instance($this->comp);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/view.php', array('id' => $this->comp->id, 'prefix' => 'competency'));
        $olddata = array(SITEID, 'competency', 'added item', $oldurl, "competency: {$this->comp->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Competency Item Deleted legacy data.
        $event = \hierarchy_competency\event\competency_deleted::create_from_instance($this->comp);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/index.php', array('id' => $this->comp_framework->id, 'prefix' => 'competency'));
        $olddata = array(SITEID, 'competency', 'delete item', $oldurl, "competency: {$this->comp->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Competency Item Viewed legacy data.
        $event = \hierarchy_competency\event\competency_viewed::create_from_instance($this->comp);
        $legacydata = $event->get_legacy_logdata();
        $urlparams = array('prefix' => 'competency', 'id' => $this->comp->id);
        $oldurl = new moodle_url('/totara/hierarchy/item/view.php', $urlparams);
        $olddata = array(SITEID, 'competency', 'view item', $oldurl, "competency: {$this->comp->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Goal Item Created legacy data.
        $event = \hierarchy_goal\event\goal_created::create_from_instance($this->goal);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/view.php', array('id' => $this->goal->id, 'prefix' => 'goal'));
        $olddata = array(SITEID, 'goal', 'added item', $oldurl, "goal: {$this->goal->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Goal Item Deleted legacy data.
        $event = \hierarchy_goal\event\goal_deleted::create_from_instance($this->goal);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/index.php', array('id' => $this->goal_framework->id, 'prefix' => 'goal'));
        $olddata = array(SITEID, 'goal', 'delete item', $oldurl, "goal: {$this->goal->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Goal Item Viewed legacy data.
        $event = \hierarchy_goal\event\goal_viewed::create_from_instance($this->goal);
        $legacydata = $event->get_legacy_logdata();
        $urlparams = array('prefix' => 'goal', 'id' => $this->goal->id);
        $oldurl = new moodle_url('/totara/hierarchy/item/view.php', $urlparams);
        $olddata = array(SITEID, 'goal', 'view item', $oldurl, "goal: {$this->goal->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);
    }

    public function test_scale_events() {
        $this->resetAfterTest();
        $sink = $this->redirectEvents();

        // TODO - move scale functionality into lib functions & write tests.
        $this->assertTrue(true);
    }

    public function test_scale_legacyevents() {
        // Set up some variables for the tests.
        $comp_scale = (object) $this->comp_scale_data;
        $comp_scale_value = (object) $this->comp_scale_value_data;
        $goal_scale = (object) $this->goal_scale_data;
        $goal_scale_value = (object) $this->goal_scale_value_data;

        // Test Competency Scale Created legacy data.
        $event = \hierarchy_competency\event\scale_created::create_from_instance($comp_scale);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/prefix/competency/scale/view.php', array('id' => $comp_scale->id, 'prefix' => 'competency'));
        $olddata = array(SITEID, 'competency', 'added scale', $oldurl, "competency scale: {$comp_scale->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Competency Scale Updated legacy data.
        $event = \hierarchy_competency\event\scale_updated::create_from_instance($comp_scale);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/prefix/competency/scale/view.php', array('id' => $comp_scale->id, 'prefix' => 'competency'));
        $olddata = array(SITEID, 'competency', 'update scale', $oldurl, "competency scale: {$comp_scale->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Competency Scale Deleted legacy data.
        $event = \hierarchy_competency\event\scale_deleted::create_from_instance($comp_scale);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/framework/index.php', array('id' => $comp_scale->id, 'prefix' => 'competency'));
        $olddata = array(SITEID, 'competency', 'delete competency scale', $oldurl, "competency scale: {$comp_scale->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Competency Scale Value Created legacy data.
        $event = \hierarchy_competency\event\scale_value_created::create_from_instance($comp_scale_value);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/prefix/competency/scale/view.php', array('id' => $comp_scale->id, 'prefix' => 'competency'));
        $olddata = array(SITEID, 'competency', 'added scale value', $oldurl, "competency scale value: {$comp_scale_value->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Competency Scale Value Updated legacy data.
        $event = \hierarchy_competency\event\scale_value_updated::create_from_instance($comp_scale_value);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/prefix/competency/scale/view.php', array('id' => $comp_scale->id, 'prefix' => 'competency'));
        $olddata = array(SITEID, 'competency', 'update scale value', $oldurl, "competency scale value: {$comp_scale_value->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Competency Scale Value Deleted legacy data.
        $event = \hierarchy_competency\event\scale_value_deleted::create_from_instance($comp_scale_value);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/prefix/competency/scale/view.php', array('id' => $comp_scale->id, 'prefix' => 'competency'));
        $olddata = array(SITEID, 'competency', 'delete scale value', $oldurl, "competency scale value: {$comp_scale_value->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Goal Scale Created legacy data.
        $event = \hierarchy_goal\event\scale_created::create_from_instance($goal_scale);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/prefix/goal/scale/view.php', array('id' => $goal_scale->id, 'prefix' => 'goal'));
        $olddata = array(SITEID, 'goal', 'added scale', $oldurl, "goal scale: {$goal_scale->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Goal Scale Updated legacy data.
        $event = \hierarchy_goal\event\scale_updated::create_from_instance($goal_scale);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/prefix/goal/scale/view.php', array('id' => $goal_scale->id, 'prefix' => 'goal'));
        $olddata = array(SITEID, 'goal', 'update scale', $oldurl, "goal scale: {$goal_scale->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Goal Scale Deleted legacy data.
        $event = \hierarchy_goal\event\scale_deleted::create_from_instance($goal_scale);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/framework/index.php', array('id' => $goal_scale->id, 'prefix' => 'goal'));
        $olddata = array(SITEID, 'goal', 'delete goal scale', $oldurl, "goal scale: {$goal_scale->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Goal Scale Value Created legacy data.
        $event = \hierarchy_goal\event\scale_value_created::create_from_instance($goal_scale_value);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/prefix/goal/scale/view.php', array('id' => $goal_scale->id, 'prefix' => 'goal'));
        $olddata = array(SITEID, 'goal', 'added scale value', $oldurl, "goal scale value: {$goal_scale_value->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Goal Scale Value Updated legacy data.
        $event = \hierarchy_goal\event\scale_value_updated::create_from_instance($goal_scale_value);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/prefix/goal/scale/view.php', array('id' => $goal_scale->id, 'prefix' => 'goal'));
        $olddata = array(SITEID, 'goal', 'update scale value', $oldurl, "goal scale value: {$goal_scale_value->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Goal Scale Value Deleted legacy data.
        $event = \hierarchy_goal\event\scale_value_deleted::create_from_instance($goal_scale_value);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/prefix/goal/scale/view.php', array('id' => $goal_scale->id, 'prefix' => 'goal'));
        $olddata = array(SITEID, 'goal', 'delete scale value', $oldurl, "goal scale value: {$goal_scale_value->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);
    }

    public function test_competency_assignment_events() {
        $this->resetAfterTest();
        $sink = $this->redirectEvents();

        // TODO - move scale functionality into lib functions & write tests.
        $this->assertTrue(true);
    }

    public function test_competency_assignment_legacyevents() {
        $eventdata = new \stdClass();
        $eventdata->id = 1;
        $eventdata->instanceid = $this->pos->id;
        $eventdata->competencyid = $this->comp->id;
        $eventdata->fullname = $this->comp->fullname;

        // And some extras necessary for snapshots.
        $eventdata->positionid = $this->pos->id;
        $eventdata->organisationid = $this->org->id;
        $eventdata->templateid = 0;
        $eventdata->timecreated = 1234567890;
        $eventdata->usermodified = 2;
        $eventdata->linktype = 1;

        // Test Position Competency Assigned legacy data.
        $event = \hierarchy_position\event\competency_assigned::create_from_instance($eventdata);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/view.php', array('id' => $this->comp->id, 'prefix' => 'position'));
        $olddata = array(SITEID, 'position', 'create competency assignment', $oldurl, "position: {$this->pos->id} - competency: {$this->comp->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Position Competency Unassigned legacy data.
        $event = \hierarchy_position\event\competency_unassigned::create_from_instance($eventdata);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/view.php', array('id' => $this->pos->id, 'prefix' => 'position'));
        $olddata = array(SITEID, 'position', 'delete competency assignment', $oldurl, "position: {$this->pos->id} - competency: {$this->comp->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Probably the same but switching to the org->id just in case.
        $eventdata->instanceid = $this->org->id;

        // Test Organisation Competency Assigned legacy data.
        $event = \hierarchy_organisation\event\competency_assigned::create_from_instance($eventdata);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/view.php', array('id' => $this->org->id, 'prefix' => 'organisation'));
        $olddata = array(SITEID, 'organisation', 'create competency assignment', $oldurl, "organisation: {$this->org->id} - competency: {$this->comp->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Organisation Competency Unassigned legacy data.
        $event = \hierarchy_organisation\event\competency_unassigned::create_from_instance($eventdata);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/view.php', array('id' => $this->org->id, 'prefix' => 'organisation'));
        $olddata = array(SITEID, 'organisation', 'delete competency assignment', $oldurl, "organisation: {$this->org->id} - competency: {$this->comp->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);
    }

    public function test_evidence_events() {
        $this->resetAfterTest();
        $sink = $this->redirectEvents();

        // Available evidence types.
        $avail_types = array(
            'coursecompletion' => $this->course->id,
            'coursegrade' => $this->course->id,
            'activitycompletion' => $this->module->id
        );

        foreach ($avail_types as $type => $instanceid) {
            $data = new stdClass();
            $data->itemtype = $type;
            $evidence = competency_evidence_type::factory((array)$data);
            $evidence->iteminstance = $instanceid;
            $newevidenceid = $evidence->add($this->comp);

            $events = $sink->get_events();
            $sink->clear();

            $this->assertEquals(count($events), 1);

            $eventdata = $events[0]->get_data();
            $this->assertEquals($eventdata['component'], 'hierarchy_competency');
            $this->assertEquals($eventdata['eventname'], '\hierarchy_competency\event\evidence_created');
            $this->assertEquals($eventdata['action'], 'created');
            $this->assertEquals($eventdata['objecttable'], 'comp_criteria');
            $this->assertEquals($eventdata['objectid'], $newevidenceid);
            $this->assertEquals($eventdata['other']['instanceid'], $instanceid);
            $this->assertEquals($eventdata['other']['competencyid'], $this->comp->id);

            $evidence->delete($this->comp);

            $events = $sink->get_events();
            $sink->clear();

            $this->assertEquals(count($events), 1);

            $eventdata = $events[0]->get_data();
            $this->assertEquals($eventdata['component'], 'hierarchy_competency');
            $this->assertEquals($eventdata['eventname'], '\hierarchy_competency\event\evidence_deleted');
            $this->assertEquals($eventdata['action'], 'deleted');
            $this->assertEquals($eventdata['objecttable'], 'comp_criteria');
            $this->assertEquals($eventdata['objectid'], $newevidenceid);
            $this->assertEquals($eventdata['other']['instanceid'], $instanceid);
            $this->assertEquals($eventdata['other']['competencyid'], $this->comp->id);
        }
    }

    public function test_evidence_legacyevents() {
        $data = new stdClass();
        $data->itemtype = 'coursecompletion';
        $evidence = competency_evidence_type::factory((array)$data);
        $evidence->iteminstance = $this->course->id;
        $newevidenceid = $evidence->add($this->comp);

        // Test Competency Evidence Deleted legacy data.
        $event = \hierarchy_competency\event\evidence_deleted::create_from_instance($evidence->get_record());
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/view.php', array('id' => $this->comp->id, 'prefix' => 'competency'));
        $olddata = array(SITEID, 'competency', 'delete evidence', $oldurl, "competency evidence: {$evidence->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);
    }

    public function test_competency_relation_events() {
        $this->resetAfterTest();
        $sink = $this->redirectEvents();

        // TODO - move relation functionality into lib functions & write tests.
        $this->assertTrue(true);
    }

    public function test_competency_relation_legacyevents() {
        $eventdata = new \stdClass();
        $eventdata->id = 1;
        $eventdata->description = '';
        $eventdata->id1 = $this->comp->id;
        $eventdata->id2 = $this->comp2->id;
        $eventdata->fullname = $this->comp2->fullname;

        // Test Competency Relation Deleted legacy data.
        $event = \hierarchy_competency\event\relation_deleted::create_from_instance($eventdata);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/view.php', array('id' => $this->comp->id, 'prefix' => 'competency'));
        $olddata = array(SITEID, 'competency', 'delete related', $oldurl, "competency: {$this->comp->id} - competency: {$this->comp2->id}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);
    }

    public function test_goal_assignment_events() {
        $this->resetAfterTest();
        $sink = $this->redirectEvents();

        // TODO - move assignment events into lib functions & write tests.
        $this->assertTrue(true);
    }

    public function test_goal_assignment_legacyevents() {
        // Create some dummy data for the events.
        $eventdata = new \stdClass();
        $eventdata->id = 1;
        $eventdata->goalid = $this->goal->id;
        $eventdata->userid = $this->user->id;
        $eventdata->cohortid = $this->cohort->id;
        $eventdata->orgid = $this->org->id;
        $eventdata->posid = $this->pos->id;
        $eventdata->assignmentid = 1;
        $eventdata->assigntype = 1;
        $eventdata->includechildren = 0;
        $eventdata->extrainfo = '';
        $eventdata->timemodified = 1234567890;
        $eventdata->usermodified = 2;

        // Test Goal Assignment User Created legacy data.
        $eventdata->instanceid = $eventdata->userid;
        $event = \hierarchy_goal\event\assignment_user_created::create_from_instance($eventdata);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/prefix/goal/mygoals.php', array('userid' => $eventdata->userid));
        $olddata = array(SITEID, 'goal', 'create goal assignments', $oldurl, "goal: {$this->goal->id} - individual: {$eventdata->userid}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Goal Assignment Cohort Created legacy data.
        $eventdata->instanceid = $eventdata->cohortid;
        $event = \hierarchy_goal\event\assignment_cohort_created::create_from_instance($eventdata);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/cohort/view.php', array('id' => $eventdata->cohortid));
        $olddata = array(SITEID, 'goal', 'create goal assignments', $oldurl, "goal: {$this->goal->id} - cohort: {$eventdata->cohortid}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Goal Assignment Position Created legacy data.
        $eventdata->instanceid = $eventdata->posid;
        $event = \hierarchy_goal\event\assignment_position_created::create_from_instance($eventdata);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/view.php', array('prefix' => 'position', 'id' => $eventdata->posid));
        $olddata = array(SITEID, 'goal', 'create goal assignments', $oldurl, "goal: {$this->goal->id} - position: {$eventdata->posid}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Goal Assignment Organisation Created legacy data.
        $eventdata->instanceid = $eventdata->orgid;
        $event = \hierarchy_goal\event\assignment_organisation_created::create_from_instance($eventdata);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/view.php', array('prefix' => 'organisation', 'id' => $eventdata->orgid));
        $olddata = array(SITEID, 'goal', 'create goal assignments', $oldurl, "goal: {$this->goal->id} - organisation: {$eventdata->orgid}");

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Goal Assignment User Deleted legacy data.
        $eventdata->instanceid = $eventdata->userid;
        $event = \hierarchy_goal\event\assignment_user_deleted::create_from_instance($eventdata);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/view.php', array('id' => $eventdata->goalid, 'prefix' => 'goal'));
        $olddesc = "goal {$this->goal->id} - individual {$this->user->id}";
        $olddata = array(SITEID, 'goal', 'delete goal assignment', $oldurl, $olddesc);

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Goal Assignment Cohort Deleted legacy data.
        $eventdata->instanceid = $eventdata->cohortid;
        $event = \hierarchy_goal\event\assignment_cohort_deleted::create_from_instance($eventdata);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/view.php', array('id' => $eventdata->goalid, 'prefix' => 'goal'));
        $olddesc = "goal {$this->goal->id} - cohort {$this->cohort->id}";
        $olddata = array(SITEID, 'goal', 'delete goal assignment', $oldurl, $olddesc);

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Goal Assignment Position Deleted legacy data.
        $eventdata->instanceid = $eventdata->posid;
        $event = \hierarchy_goal\event\assignment_position_deleted::create_from_instance($eventdata);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/view.php', array('id' => $eventdata->goalid, 'prefix' => 'goal'));
        $olddesc = "goal {$this->goal->id} - position {$this->pos->id}";
        $olddata = array(SITEID, 'goal', 'delete goal assignment', $oldurl, $olddesc);

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);

        // Test Goal Assignment Organisation Deleted legacy data.
        $eventdata->instanceid = $eventdata->orgid;
        $event = \hierarchy_goal\event\assignment_organisation_deleted::create_from_instance($eventdata);
        $legacydata = $event->get_legacy_logdata();
        $oldurl = new moodle_url('/totara/hierarchy/item/view.php', array('id' => $eventdata->goalid, 'prefix' => 'goal'));
        $olddesc = "goal {$this->goal->id} - organisation {$this->org->id}";
        $olddata = array(SITEID, 'goal', 'delete goal assignment', $oldurl, $olddesc);

        $this->assertEquals($legacydata[0], $olddata[0]);
        $this->assertEquals($legacydata[1], $olddata[1]);
        $this->assertEquals($legacydata[2], $olddata[2]);
        $this->assertEquals($legacydata[3]->out(), $olddata[3]->out());
        $this->assertEquals($legacydata[4], $olddata[4]);
    }
}
