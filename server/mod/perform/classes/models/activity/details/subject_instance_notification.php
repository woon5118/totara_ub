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
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\activity\details;

use coding_exception;
use core\orm\query\builder;
use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\entities\activity\track as track_entity;
use mod_perform\entities\activity\track_user_assignment as track_user_assignment_entity;
use mod_perform\entities\activity\subject_instance as subject_instance_entity;
use mod_perform\models\activity\activity as activity_model;
use stdClass;

/**
 * @property-read integer $id
 * @property-read integer $subject_user_id
 * @property-read integer|null $job_assignment_id
 * @property-read integer|null $due_date
 * @property-read integer $instance_created_at
 */
class subject_instance_notification {
    /** @var stdClass */
    private $record;

    /**
     * Private constructor.
     *
     * @param stdClass $record
     */
    private function __construct(stdClass $record) {
        $this->record = $record;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        if (!property_exists($this->record, $name)) {
            throw new coding_exception("Unknown property {$name}");
        }
        return $this->record->{$name};
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function __isset($name) {
        return property_exists($this->record, $name);
    }

    /**
     * @param string $name
     * @param boolean $value
     */
    public function __set($name, $value) {
        throw new coding_exception('Cannot modify a read-only object');
    }

    /**
     * @param string $name
     */
    public function __unset($name) {
        throw new coding_exception('Cannot modify a read-only object');
    }

    /**
     * Load subject_instance_notification records related to the activity.
     *
     * @param activity_entity $activity
     * @return subject_instance_notification[]
     */
    public static function load_by_activity(activity_entity $activity): array {
        return builder::table(subject_instance_entity::TABLE, 'si')
            ->join([track_user_assignment_entity::TABLE, 'tua'], 'si.track_user_assignment_id', 'tua.id')
            ->join([track_entity::TABLE, 't'], 'tua.track_id', 't.id')
            ->where('t.activity_id', $activity->id)
            ->where_null('si.completed_at')
            ->where('tua.deleted', false)
            ->select(['si.id', 'tua.subject_user_id', 'tua.job_assignment_id', 'si.due_date AS due_date'])
            // how can I get an instance creation time?
            ->add_select('si.created_at AS instance_created_at')
            ->map_to(function ($item) {
                return new self($item);
            })
            ->get()
            ->all(false);
    }
}
