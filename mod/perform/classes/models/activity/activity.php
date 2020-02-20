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
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @author Simon Coggins <simon.coggins@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\activity;

use container_perform\perform as perform_container;
use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\util;

class activity {

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 1;

    /**
     * @var activity_entity
     */
    protected $entity;

    /**
     * activity constructor.
     * @param activity_entity $entity
     */
    private function __construct(activity_entity $entity) {
        $this->entity = $entity;
    }

    // TODO consider a base model class for some of the methods below?

    public static function load_by_id(int $id): self {
        $entity = new activity_entity($id);
        return new static($entity);
    }

    public static function load_by_container_id(int $container_id): self {
        $entity = activity_entity::repository()
            ->where('course', $container_id)
            ->one(true);
        return self::load_by_entity($entity);
    }

    public static function load_by_entity(activity_entity $entity): self {
        if (!$entity->exists()) {
            throw new \coding_exception('Can load only existing entities');
        }
        return new static($entity);
    }

    public function get_id(): int {
        return $this->entity->id;
    }

    /**
     * Get underlying entity
     *
     * @return activity_entity
     */
    public function get_entity(): activity_entity {
        return $this->entity;
    }

    public static function can_create(int $userid = null, \context_coursecat $context = null): bool {
        global $USER;

        if (null == $userid) {
            // Including zero check
            $userid = $USER->id;
        }


        if (null == $context) {
            $categoryid = util::get_default_categoryid();
            if (0 == $categoryid) {
                // Nope, this user is not able to add a performance activity.
                return false;
            }

            $context = \context_coursecat::instance($categoryid);
        }

        return has_capability('mod/perform:create_activity', $context, $userid) &&
            perform_container::can_create_instance($userid, $context);
    }

    public static function create(\stdClass $data, perform_container $container): self {
        global $DB;

        $modinfo = new \stdClass();
        $modinfo->modulename = 'perform';
        $modinfo->course = $container->id;
        $modinfo->name = $data->name;
        $modinfo->timemodified = time();
        $modinfo->visible = true;
        $modinfo->section = 0;
        $modinfo->groupmode = 0;
        $modinfo->groupingid = 0;

        $entity = new activity_entity();

        $entity->course = $container->id;
        $entity->name = $data->name;
        $entity->status = $data->status;

        return $DB->transaction(function () use ($entity, $modinfo, $container) {
            global $CFG, $USER;

            $entity->save();

            $modinfo->instanceid = $entity->id;
            $container->add_module($modinfo);

            $container_context = $container->get_context();
            if (!empty($CFG->performanceactivitycreatornewroleid) and !is_viewing($container_context)) {
                role_assign($CFG->performanceactivitycreatornewroleid, $USER->id, $container_context);
            }

            return self::load_by_entity($entity);
        });
    }

    /**
     * Return the context object for this activity.
     */
    public function get_context(): \context_module {
        $cm = get_coursemodule_from_instance('perform', $this->entity->id, $this->entity->course, false, MUST_EXIST);
        return \context_module::instance($cm->id);
    }
}
