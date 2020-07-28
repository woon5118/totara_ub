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
 * @author Kunle Odusan <kunle.odusan@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\activity;

use core\orm\entity\model;
use mod_perform\entities\activity\external_participant as external_participant_entity;
use moodle_page;

/**
 * Class section_element
 *
 * The presence of an element within a section.
 *
 * @property-read int $id ID
 * @property-read string $fullname
 * @property-read string $email
 * @property-read string $profileimageurlsmall
 *
 * @package mod_perform\models\activity
 */
class external_participant extends model {

    protected $entity_attribute_whitelist = [
        'id',
        'email',
    ];

    protected $model_accessor_whitelist = [
        'fullname',
        'profileimageurlsmall',
    ];

    /**
     * Default image filename.
     *
     * @var string
     */
    private $image_filename = 'u/f2';

    /**
     * @var external_participant_entity
     */
    protected $entity;

    /**
     * @inheritDoc
     */
    protected static function get_entity_class(): string {
        return external_participant_entity::class;
    }

    /**
     * Create a new section element, by joining the section and element
     *
     * @param string $fullname
     * @param string $email
     *
     * @return static
     */
    public static function create(string $fullname, string $email): self {
        $entity = new external_participant_entity();
        $entity->name = $fullname;
        $entity->email = $email;
        $entity->save();

        return static::load_by_entity($entity);
    }

    public function get_fullname(): string {
        return $this->entity->name;
    }

    /**
     * Get the profile image of an external participant.
     *
     * @return string
    */
    public function get_profileimageurlsmall(): string {
        return $this->get_default_image();
    }

    /**
     * Get default image string.
     *
     * @return string
     */
    private function get_default_image(): string {
        return (new moodle_page())->get_renderer('core')->image_url($this->image_filename)->out(false);
    }
}
