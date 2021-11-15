<?php
/**
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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\webapi\resolver\mutation;

use core\webapi\execution_context;
use core\webapi\middleware\require_advanced_feature;
use core\webapi\mutation_resolver;
use core\webapi\resolver\has_middleware;
use mod_perform\data_providers\response\participant_section;
use mod_perform\data_providers\response\participant_section_with_responses;
use mod_perform\models\activity\helpers\external_participant_token_validator;
use mod_perform\models\activity\participant_source;

class update_section_responses_external_participant implements mutation_resolver, has_middleware {
    /**
     * {@inheritdoc}
     */
    public static function resolve(array $args, execution_context $ec) {
        // This mutation is supposed to work only for non-logged in external users
        global $USER;
        if ($USER && $USER->id > 0) {
            return null;
        }

        $input = $args['input'];

        $participant_section_id = $input['participant_section_id'];
        $token = $input['token'] ?? null;
        $is_draft = $input['is_draft'] ?? false;
        if (empty($token)) {
            return null;
        }

        $validator = new external_participant_token_validator($token);
        if (!$validator->is_valid() || $validator->is_subject_instance_closed()) {
            return null;
        }

        $participant_instance = $validator->get_participant_instance();
        $participant_id = $participant_instance->participant_id;

        $participant_section = (new participant_section($participant_id, participant_source::EXTERNAL))
            ->find_by_section_id($participant_section_id);

        // Something is not valid, we do only return null to not reveal anything through error messages
        if ($participant_section === null) {
            return null;
        }
        $participant_section_with_responses = (new participant_section_with_responses($participant_section))
            ->process_for_response_submission()->build();
        $ec->set_relevant_context($participant_section_with_responses->get_context());

        $participant_section_with_responses->set_responses_data_from_request($input['update']);
        if ($is_draft) {
            $participant_section->draft();
        } else {
            $participant_section->complete();
        }

        return [
            'participant_section' => $participant_section
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function get_middleware(): array {
        return [
            new require_advanced_feature('performance_activities'),
        ];
    }
}