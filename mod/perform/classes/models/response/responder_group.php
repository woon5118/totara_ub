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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\response;

use core\collection;

/**
 * Class responder_group
 *
 * Represents a group of responses to a particular element for a particular relationship type.
 * For example a responder group could hold all the responses for question one for all managers of a subject.
 *
 * @package mod_perform\models\response
 */
class responder_group {

    /**
     * @var string
     */
    protected $relationship_name;

    /**
     * @var collection
     */
    protected $responses;

    /**
     * other_responder_group constructor.
     *
     * @param string $relationship_name
     * @param collection|section_element_response[] $responses
     */
    public function __construct(string $relationship_name, collection $responses) {
        $this->relationship_name = $relationship_name;
        $this->responses = $responses;
    }

    /**
     * @return string
     */
    public function get_relationship_name(): string {
        return $this->relationship_name;
    }

    /**
     * @return collection|section_element_response
     */
    public function get_responses(): collection {
        return $this->responses;
    }

}