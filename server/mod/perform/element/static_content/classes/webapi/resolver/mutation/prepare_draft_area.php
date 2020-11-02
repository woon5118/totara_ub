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
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package performelement_static_content
 */

namespace performelement_static_content\webapi\resolver\mutation;

use core\webapi\execution_context;
use core\webapi\middleware\require_advanced_feature;
use core\webapi\middleware\require_login;
use core\webapi\mutation_resolver;
use core\webapi\resolver\has_middleware;
use mod_perform\entities\activity\section_element;
use mod_perform\models\activity\element;
use mod_perform\webapi\middleware\require_activity;
use mod_perform\webapi\middleware\require_manage_capability;

class prepare_draft_area implements mutation_resolver, has_middleware {

    /**
     * @inheritDoc
     */
    public static function resolve(array $args, execution_context $ec) {
        $element_id = $args['element_id'];
        $section_id = $args['section_id'];

        /** @var section_element $section_element_entity */
        $section_element_entity = section_element::repository()
            ->where('element_id', $element_id)
            ->where('section_id', $section_id)
            ->with('element')
            ->one(true);

        // We've confirmed that the user has manage_capability on the
        // activity but we need to make sure that the element belongs
        // to the right activity.
        if (empty($section_element_entity)) {
            throw new \coding_exception('Invalid element for section');
        }

        $data = $section_element_entity->element->data;
        $data = json_decode($data, true);
        $draft_id = null;
        $data['wekaDoc'] = file_prepare_draft_area(
            $draft_id,
            $section_element_entity->element->context_id,
            'performelement_static_content',
            'content',
            $element_id,
            null,
            $data['wekaDoc']
        );

        return  $draft_id;
    }

    /**
     * @inheritDoc
     */
    public static function get_middleware(): array {
        return [
            new require_advanced_feature('performance_activities'),
            new require_login(),
            require_activity::by_section_id('section_id', true),
            new require_manage_capability(),
        ];
    }

}