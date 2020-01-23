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
 * @package mod_perform
 */

namespace mod_perform\models\activity;

use mod_perform\entities\activity\activity as activity_entity;
use container_perform\perform;
use context_module;
use mod_perform\activity_create_exception;

class activity {

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

    public static function create(\stdClass $data): ?self {
        global $CFG, $USER;

        if (empty($data->name)) {
            // TODO this should be a string key and module
            throw new activity_create_exception('Activity name must be provided');
        }

        $courseinfo = new \stdClass();
        $courseinfo->fullname = $data->name;
        $courseinfo->category = $data->category ?? perform::get_default_categoryid();

        $container = perform::create($courseinfo);

        $modinfo = new \stdClass();
        $modinfo->modulename = 'perform';
        $modinfo->course = $container->id;
        $modinfo->name = $data->name;
        $modinfo->timemodified = time();
        $modinfo->visible = true;
        $modinfo->section = 0;
        $modinfo->groupmode = 0;
        $modinfo->groupingid = 0;
        $cm = $container->add_module($modinfo);

        $containercontext = $container->get_context();
        if (!empty($CFG->performanceactivitycreatornewroleid) and !is_viewing($containercontext)) {
            // TODO should I be setting component and itemid args here? What to?
            role_assign($CFG->performanceactivitycreatornewroleid, $USER->id, $containercontext);
            // TODO alternative approach - create an enrolment, but no enrolment plugins enabled for containers by default?
            //enrol_try_internal_enrol($container->id, $USER->id, $CFG->performanceactivitycreatornewroleid);
        }

        // Reload full activity entity after creation.
        $perform = self::load_by_id($cm->instance);

        // TODO provide way to access CM and containers object from activity model?
        return $perform;
    }

    /**
     * Return the context object for this activity.
     */
    public function get_context(): context_module {
        $cm = get_coursemodule_from_instance('perform', $this->entity->id, $this->entity->course, false, MUST_EXIST);
        return context_module::instance($cm->id);
    }
}
