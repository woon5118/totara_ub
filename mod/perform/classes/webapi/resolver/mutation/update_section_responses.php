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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\webapi\resolver\mutation;

use coding_exception;
use core\entities\user;
use core\orm\collection;
use core\webapi\execution_context;
use core\webapi\mutation_resolver;
use mod_perform\data_providers\activity\responses_for_participant_section;
use mod_perform\models\activity\element_response;
use mod_perform\models\activity\participant_section;
use totara_core\advanced_feature;

class update_section_responses implements mutation_resolver {

    /**
     * @param array $args
     * @param execution_context $ec
     * @return participant_section
     */
    public static function resolve(array $args, execution_context $ec): participant_section {
        advanced_feature::require('performance_activities');
        require_login(null, false, null, false, true);

        $input = $args['input'];

        $participant_id = user::logged_in()->id;

        $data_provider = new responses_for_participant_section($participant_id, $input['participant_section_id']);
        $data_provider->fetch();

        $participant_section = $data_provider->get_participant_section();

        if ($participant_section === null) {
            throw new coding_exception(sprintf('Participant section not found for id %d', $input['participant_section_id']));
        }

        $context = $participant_section->get_context();
        $ec->set_relevant_context($context);

        // This includes not yet entered responses.
        $existing_element_responses = $data_provider->get_responses();

        // Existing elements with the updated response data.
        $updated_element_responses = self::add_updates_to_existing($existing_element_responses, $input['update']);

        $participant_section->set_element_responses($updated_element_responses);
        $participant_section->complete();

        return $participant_section;
    }

    private static function add_updates_to_existing(collection $existing_element_responses, array $updates): collection {
        foreach ($updates as $update) {
            $section_element_id = $update['section_element_id'];
            $response_data = $update['response_data'];

            /** @var element_response $existing_element_response */
            $existing_element_response = $existing_element_responses->find(
                function (element_response $element_response) use ($section_element_id) {
                    return (int) $element_response->section_element_id === (int) $section_element_id;
                }
            );

            if ($existing_element_response === null) {
                throw new coding_exception(sprintf('Section element not found for id %d', $section_element_id));
            }

            $existing_element_response->set_response_data($response_data);
        }

        return $existing_element_responses;
    }
}