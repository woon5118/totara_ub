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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\webapi\resolver\mutation;

use coding_exception;
use core\orm\query\exceptions\record_not_found_exception;
use core\webapi\execution_context;
use core\webapi\middleware\require_advanced_feature;
use core\webapi\mutation_resolver;
use core\webapi\resolver\has_middleware;
use mod_perform\models\activity\section;
use mod_perform\webapi\middleware\require_activity;
use moodle_exception;

class update_section_settings implements mutation_resolver, has_middleware {
    /**
     * This updates the list of relationships for a specified section.
     *
     * {@inheritdoc}
     */
    public static function resolve(array $args, execution_context $ec) {
        $args = $args['input'];

        try {
            $section = section::load_by_id($args['section_id']);
        } catch (record_not_found_exception $e) {
            throw new coding_exception('Specified section id does not exist');
        }

        $activity = $section->get_activity();
        $context = $activity->get_context();
        if (!$activity->can_manage()) {
            throw new moodle_exception('invalid_activity', 'mod_perform');
        }

        $ec->set_relevant_context($context);

        if (isset($args['title'])) {
            $section->update_title($args['title']);
        }

        return ['section' => $section->update_relationships($args['relationships'])];
    }

    /**
     * {@inheritdoc}
     */
    public static function get_middleware(): array {
        return [
            new require_advanced_feature('performance_activities'),
            require_activity::by_section_id('input.section_id', true)
        ];
    }
}