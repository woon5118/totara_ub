<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * @package mod_perform
 * @category test
 */

use mod_perform\constants;

defined('MOODLE_INTERNAL') || die();

/**
 * @group perform
 */
class mod_perform_external_participant_token_validator_testcase extends advanced_testcase {

    public function test_valid_token() {
        $this->markTestIncomplete();
        $data = $this->setup_data();
    }

    private function setup_data() {
        $generator = $this->generator();

        $configuration = mod_perform_activity_generator_configuration::new()
            ->enable_creation_of_manual_participants()
            ->set_relationships_per_section(
                [
                    constants::RELATIONSHIP_EXTERNAL,
                    constants::RELATIONSHIP_SUBJECT,
                    constants::RELATIONSHIP_MANAGER
                ]
            );

        $generator->create_full_activities($configuration);
    }

    /**
     * @return mod_perform_generator
     */
    protected function generator(): mod_perform_generator {
        return $this->getDataGenerator()->get_plugin_generator('mod_perform');
    }
}