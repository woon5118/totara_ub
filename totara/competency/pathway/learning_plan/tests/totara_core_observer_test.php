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
use totara_competency\hook\competency_validity_changed;
use totara_core\advanced_feature;
use totara_core\hook\advanced_feature_disabled;
use totara_core\hook\advanced_feature_enabled;

class pathway_learning_plan_totara_core_observer_testcase extends advanced_testcase {

    public function test_learning_plans_enabled() {
        $sink = $this->redirectHooks();
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
        $sink->clear();

        // First disable something else
        /** @var advanced_feature_disabled $hook */
        $hook = new advanced_feature_disabled('whatever');
        totara_core::feature_disabled($hook);

        $this->assertSame(0, $sink->count());
        $on_disk = pathway_entity::repository()
            ->where('path_type', 'learning_plan')
            ->where('valid', 1)
            ->count();
        $this->assertSame(3, $on_disk);

        // Now disable learningplans
        $hook = new advanced_feature_disabled('learningplans');
        totara_core::feature_disabled($hook);

        $hooks = $sink->get_hooks();
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
        $sink->clear();


        // And enable it again
        $hook = new advanced_feature_enabled('learningplans');
        totara_core::feature_enabled($hook);

        $hooks = $sink->get_hooks();
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

        $sink->close();
    }

}
