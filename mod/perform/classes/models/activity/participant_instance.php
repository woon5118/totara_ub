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

namespace mod_perform\models\activity;

use context_module;
use core\orm\entity\model;
use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\entities\activity\subject_instance as subject_instance_entity;
use mod_perform\state\state;
use mod_perform\state\state_aware;

/**
 * Class participant_instance
 *
 * @package mod_perform\models\activity
 *
 * @property-read int $progress
 * @property-read int $participant_id
 */
class participant_instance extends model {

    use state_aware;

    protected $entity_attribute_whitelist = [
        'progress',
        'participant_id',
        'participant_sections'
    ];

    protected $model_accessor_whitelist = [
        'progress_status'
    ];

    /**
     * @var participant_instance_entity
     */
    protected $entity;

    public static function get_entity_class(): string {
        return participant_instance_entity::class;
    }

    public function get_current_state_code(): int {
        return $this->progress;
    }

    protected function update_state_code(state $state): void {
        $this->entity->progress = $state::get_code();
        $this->entity->update();
    }

    public function get_context(): context_module {
        /** @var subject_instance_entity $subject_instance_entity */
        $subject_instance_entity = $this->entity->subject_instance;
        $subject_instance = new subject_instance($subject_instance_entity);

        return $subject_instance->get_context();
    }

    /**
     * Update progress status according to section progress.
     */
    public function update_progress_status() {
        $this->get_state()->update_progress();
    }

    /**
     * Get internal name of current progress state.
     *
     * @return string
     */
    public function get_progress_status(): string {
        return $this->get_state()->get_name();
    }
}
