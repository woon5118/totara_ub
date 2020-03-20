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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\formatter\activity;

use coding_exception;
use totara_core\formatter\formatter;
use mod_perform\models\activity\subject_instance as subject_instance_model;

/**
 * Class subject_instance
 *
 * @package mod_perform\formatter\activity
 */
class subject_instance extends formatter {

    /**
     * @var subject_instance_model
     */
    protected $object;

    protected function get_map(): array {
        return [
            'activity' => null,
            'subject' => null,
            'status' => null,
        ];
    }

    protected function get_field(string $field) {
        switch ($field) {
            case 'activity':
                return $this->object->get_activity();
            case 'subject':
                return $this->object->subject_user;
            case 'status':
                return $this->object->get_status();
            default:
                throw new coding_exception('Unexpected field passed to formatter');
        }
    }

    protected function has_field(string $field): bool {
        return array_key_exists($field, $this->get_map());
    }



}