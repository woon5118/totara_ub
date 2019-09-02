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
 * @package totara_competency
 */

use pathway_manual\achievement_detail;
use pathway_manual\manual;
use totara_competency\entities\pathway_achievement;

class pathway_manual_achievement_detail_testcase extends advanced_testcase {

    public function test_get_achieved_via_strings_empty() {
        $detail = new achievement_detail();
        $this->assertSame([], $detail->get_achieved_via_strings());
    }

    public function test_get_achieved_via_strings_integration() {
        /** @var totara_hierarchy_generator $totara_hierarchy_generator */
        $totara_hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $compfw = $totara_hierarchy_generator->create_comp_frame([]);
        $comp = $totara_hierarchy_generator->create_comp(['frameworkid' => $compfw->id]);
        $competency = new \totara_competency\entities\competency($comp);
        $scale = $competency->scale;
        $value1 = $scale->scale_values->first();
        $scale->scale_values->next();
        $value2 = $scale->scale_values->current();

        $manual = new manual();
        $manual->set_competency($competency);
        $manual->set_roles(['self']);
        $manual->save();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $manual->set_manual_value($user1->id, $user1->id, 'self', $value1->id, '');
        $manual->set_manual_value($user2->id, $user2->id, 'self', $value2->id, '');

        $achievement = pathway_achievement::get_current($manual, $user1->id);

        $detail = new achievement_detail();
        $detail->set_related_info(json_decode($achievement->related_info, true));
        $expected_string = 'rating by ' . fullname($user1) . ' (self)';
        $this->assertSame([$expected_string], $detail->get_achieved_via_strings());
    }
}
