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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\entity\activity\filters;

use core\orm\entity\filter\filter;
use mod_perform\entity\activity\participant_instance as participant_instance_entity;

class subject_instances_participant_progress extends filter {

    /**
     * @var string
     */
    protected $participant_instance_alias;

    public function __construct(int $participant_id, string $participant_instance_alias = 'pi') {
        parent::__construct([$participant_id]);
        $this->participant_instance_alias = $participant_instance_alias;
    }

    public function apply(): void {
        $builder = participant_instance_entity::repository()
            ->as('target_participant_progress')
            ->where_raw('target_participant_progress.subject_instance_id = si.id')
            ->where('participant_id', $this->get_participant_id())
            ->where('progress', $this->value)
            ->get_builder();

        $this->builder->where_exists($builder);
    }

    protected function get_participant_id() {
        return $this->params[0] ?? null;
    }
}