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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\hook;

use coding_exception;
use core\collection;
use mod_perform\task\service\subject_instance_dto;
use totara_core\hook\base;

/**
 * This hook contains a collection of subject instance dtos created
 * by the subject instance creation service
 *
 * @package mod_perform\hook
 */
class subject_instances_created extends base {

    /**
     * @var collection|subject_instance_dto[]
     */
    protected $subject_instance_dtos;

    /**
     * @param collection|subject_instance_dto[] $subject_instance_dtos
     */
    public function __construct(collection $subject_instance_dtos) {
        // Validate that this only contains dtos
        $non_dtos = $subject_instance_dtos->filter(function ($item) {
            return !$item instanceof subject_instance_dto;
        });
        if ($non_dtos->count() > 0) {
            throw new coding_exception('Expecting a collection of subject instance dtos');
        }
        $this->subject_instance_dtos = $subject_instance_dtos;
    }

    public function get_dtos(): collection {
        return $this->subject_instance_dtos;
    }

}