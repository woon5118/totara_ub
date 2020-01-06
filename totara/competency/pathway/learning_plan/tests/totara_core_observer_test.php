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
 * @package pathway_learning_plan
 */


use pathway_learning_plan\observer\totara_core;
use totara_competency\entities\pathway as pathway_entity;
use core\event\admin_settings_changed;
use totara_competency\hook\competency_validity_changed;
use totara_core\advanced_feature;

global $CFG;
require_once($CFG->dirroot.'/lib/adminlib.php');

class pathway_learning_plan_totara_core_observer_testcase extends advanced_testcase {

    public function test_learning_plans_enabled() {
        global $CFG;

        $event_sink = $this->redirectEvents();
        $hook_sink = $this->redirectHooks();
        advanced_feature::enable('learningplans');

        /** @var totara_competency_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $competency1 = $generator->create_competency();
        $competency2 = $generator->create_competency();
        $competency3 = $generator->create_competency();

        $pw1_1 = $generator->create_learning_plan_pathway($competency1);
        $this->assertTrue($pw1_1->is_valid());
        $pw1_2 = $generator->create_learning_plan_pathway($competency1);
        $this->assertTrue($pw1_2->is_valid());
        $pw2_1 = $generator->create_learning_plan_pathway($competency2);
        $this->assertTrue($pw2_1->is_valid());

        $on_disk = pathway_entity::repository()
            ->where('path_type', 'learning_plan')
            ->where('valid', 1)
            ->count();
        $this->assertSame(3, $on_disk);

        // First disable something else
        $event = admin_settings_changed::create(
            [
                'context' => context_system::instance(),
                'other' =>
                 [
                     'olddata' => ['whatever' => advanced_feature::DISABLED]
                 ]
            ]
        );

        $hook_sink->clear();
        totara_core::admin_settings_changed($event);

        $this->assertSame(0, $hook_sink->count());
        $on_disk = pathway_entity::repository()
            ->where('path_type', 'learning_plan')
            ->where('valid', 1)
            ->count();
        $this->assertSame(3, $on_disk);

        // Now disable learningplans
        // We need to disable the setting as well as generate the event to simulate what actually happens
        advanced_feature::disable('learningplans');
        $event = admin_settings_changed::create(
            [
                'context' => context_system::instance(),
                'other' =>
                 [
                     'olddata' => ['s__enablelearningplans' => advanced_feature::ENABLED]
                 ]
            ]
        );

        $hook_sink->clear();
        totara_core::admin_settings_changed($event);

        $hooks = $hook_sink->get_hooks();
        $this->assertSame(1, count($hooks));

        /** @var competency_validity_changed $triggered_hook */
        $triggered_hook = reset($hooks);
        $this->assertTrue($triggered_hook instanceof competency_validity_changed);
        $this->assertEqualsCanonicalizing([$competency1->id, $competency2->id], $triggered_hook->get_competency_ids());

        $on_disk = pathway_entity::repository()
            ->where('path_type', 'learning_plan')
            ->where('valid', 1)
            ->count();
        $this->assertSame(0, $on_disk);

        $on_disk = pathway_entity::repository()
            ->where('path_type', 'learning_plan')
            ->where('valid', 0)
            ->count();
        $this->assertSame(3, $on_disk);


        // And enable it again
        advanced_feature::enable('learningplans');
        $event = admin_settings_changed::create(
            [
                'context' => context_system::instance(),
                'other' =>
                 [
                     'olddata' => ['s__enablelearningplans' => advanced_feature::DISABLED]
                 ]
            ]
        );

        $hook_sink->clear();
        totara_core::admin_settings_changed($event);

        $hooks = $hook_sink->get_hooks();
        $this->assertSame(1, count($hooks));

        /** @var competency_validity_changed $triggered_hook */
        $triggered_hook = reset($hooks);
        $this->assertTrue($triggered_hook instanceof competency_validity_changed);
        $this->assertEqualsCanonicalizing([$competency1->id, $competency2->id], $triggered_hook->get_competency_ids());

        $on_disk = pathway_entity::repository()
            ->where('path_type', 'learning_plan')
            ->where('valid', 1)
            ->count();
        $this->assertSame(3, $on_disk);

        $on_disk = pathway_entity::repository()
            ->where('path_type', 'learning_plan')
            ->where('valid', 0)
            ->count();
        $this->assertSame(0, $on_disk);


        // Check nothing happens if the setting is set to the same value
        $event = admin_settings_changed::create(
            [
                'context' => context_system::instance(),
                'other' =>
                 [
                     'olddata' => ['s__enablelearningplans' => advanced_feature::ENABLED]
                 ]
            ]
        );

        $hook_sink->clear();
        totara_core::admin_settings_changed($event);

        $this->assertSame(0, $hook_sink->count());
        $on_disk = pathway_entity::repository()
            ->where('path_type', 'learning_plan')
            ->where('valid', 1)
            ->count();
        $this->assertSame(3, $on_disk);

        $event_sink->close();
        $hook_sink->close();
    }

}
