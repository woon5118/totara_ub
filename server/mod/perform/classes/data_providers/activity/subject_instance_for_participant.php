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

namespace mod_perform\data_providers\activity;

use core\collection;
use core\orm\entity\repository;
use core\orm\query\builder;
use core\pagination\cursor;
use core\pagination\cursor_paginator;
use mod_perform\data_providers\cursor_paginator_trait;
use mod_perform\data_providers\provider;
use mod_perform\entity\activity\activity as activity_entity;
use mod_perform\entity\activity\filters\subject_instance_id;
use mod_perform\entity\activity\filters\subject_instances_about;
use mod_perform\entity\activity\filters\subject_instances_activity_type;
use mod_perform\entity\activity\filters\subject_instances_overdue;
use mod_perform\entity\activity\filters\subject_instances_participant_progress;
use mod_perform\entity\activity\participant_instance;
use mod_perform\entity\activity\subject_instance;
use mod_perform\entity\activity\subject_instance as subject_instance_entity;
use mod_perform\entity\activity\subject_instance_repository;
use mod_perform\entity\activity\track as track_entity;
use mod_perform\entity\activity\track_user_assignment as track_user_assignment_entity;
use mod_perform\models\activity\subject_instance as subject_instance_model;
use mod_perform\models\response\subject_sections;
use mod_perform\state\participant_instance\participant_instance_progress;
use mod_perform\state\state_helper;
use mod_perform\state\subject_instance\active;

/**
 * Class subject_instance
 *
 * @package mod_perform\data_providers\activity
 *
 * @method collection|subject_instance_model[] get
 */
class subject_instance_for_participant extends provider {
    use cursor_paginator_trait;

    /**
     * @var int
     */
    protected $participant_id;

    /** @var int */
    protected $participant_source;

    /**
     * @param int $participant_id The id of the user we would like to get activities that they are participating in.
     * @param int $participant_source see participant_source model for constants
     */
    public function __construct(int $participant_id, int $participant_source) {
        $this->participant_id = $participant_id;
        $this->participant_source = $participant_source;
    }

    /**
     * @param subject_instance_repository|repository $repository
     * @param string|string[] $about Subject instance about constant(s)
     */
    protected function filter_query_by_about(repository $repository, $about): void {
        if (!is_array($about)) {
            $about = [$about];
        }

        $repository->set_filter(
            (new subject_instances_about($this->participant_id, 'si'))->set_value($about)
        );
    }

    /**
     * @param subject_instance_repository|repository $repository
     * @param int|array $subject_instance_ids Subject instance ID(s)
     */
    protected function filter_query_by_subject_instance_id(repository $repository, $subject_instance_ids): void {
        if (!is_array($subject_instance_ids)) {
            $subject_instance_ids = [$subject_instance_ids];
        }

        $repository->set_filter(
            (new subject_instance_id('si'))->set_value($subject_instance_ids)
        );
    }

    /**
     * @param subject_instance_repository|repository $repository
     * @param int|int[] $activity_types Activity type ID(s)
     */
    protected function filter_query_by_activity_type(repository $repository, $activity_types): void {
        if (!is_array($activity_types)) {
            $activity_types = [$activity_types];
        }

        $repository->set_filter(
            (new subject_instances_activity_type('a'))->set_value($activity_types)
        );
    }

    /**
     * @param subject_instance_repository|repository $repository
     * @param string|string[] $progress_values Progress state names(s)
     */
    protected function filter_query_by_participant_progress(repository $repository, $progress_values): void {
        if (!is_array($progress_values)) {
            $progress_values = [$progress_values];
        }

        $all_progress_names = state_helper::get_all_names('participant_instance', participant_instance_progress::get_type());
        $all_progress_names = array_flip($all_progress_names);
        
        $progress_values = array_map(function ($progress) use ($all_progress_names) {
            if (!isset($all_progress_names[$progress])) {
                throw new \coding_exception("{progress} is not a valid participant progress type");
            }
            return $all_progress_names[$progress];
        }, $progress_values);
        
        $repository->set_filter(
            (new subject_instances_participant_progress($this->participant_id, 'target_participant'))
                ->set_value($progress_values)
        );
    }

    /**
     * @param subject_instance_repository|repository $repository
     * @param int $is_overdue Show only overdue | not overdue
     */
    protected function filter_query_by_overdue(repository $repository, int $is_overdue): void {
        $repository->set_filter(
            (new subject_instances_overdue('si'))->set_value($is_overdue)
        );
    }

    /**
     * Build query for user activities that can be managed by the logged in user.
     *
     * @return subject_instance_repository
     */
    protected function build_query(): repository {
        global $CFG;
        require_once($CFG->dirroot . "/totara/coursecatalog/lib.php");

        [$totara_visibility_sql, $totara_visibility_params] = totara_visibility_where();

        return subject_instance_entity::repository()
            ->as('si')
            ->with('subject_user')
            ->with([
                'track.activity' => function (repository $repository) {
                    $repository
                        ->with('settings')
                        ->with('type');
                }
            ])
            ->with('job_assignment')
            ->with([
                'participant_instances' => function (repository $repository) {
                    $repository
                        ->with([
                            'participant_sections' => function (repository $repository) {
                                $repository
                                    ->with('section.section_relationships.core_relationship')
                                    ->with([
                                        'participant_instance' => function (repository $repository) {
                                            $repository
                                                ->with('core_relationship')
                                                ->with('participant_user')
                                                ->with('external_participant')
                                                ->with('subject_instance.track.activity');
                                        }
                                    ]);
                            }
                        ])
                        ->with('core_relationship')
                        ->with('subject_instance.track.activity')
                        ->with('participant_user')
                        ->with('external_participant');
                }
            ])
            ->join([track_user_assignment_entity::TABLE, 'tua'], 'track_user_assignment_id', 'id')
            ->join([track_entity::TABLE, 't'], 'tua.track_id', 'id')
            ->join([activity_entity::TABLE, 'a'], 't.activity_id', 'id')
            ->join('course', 'a.course', 'id')
            ->join(['user', 'su'], 'subject_user_id', 'id')
            ->where('su.deleted', 0)
            ->where_raw($totara_visibility_sql, $totara_visibility_params)
            ->where_exists($this->get_target_participant_exists())
            ->where('status', active::get_code())
            // Cursors don't work when aliases are included in the order by
            // Newest subject instances at the top of the list
            ->order_by('created_at', 'desc')
            // Order by id as well is so that tests wont fail if two rows are inserted within the same second
            ->order_by('id', 'desc');
    }

    /**
     * Map the subject instance entities to their respective model class.
     *
     * @return collection|subject_instance_model[]
     */
    protected function process_fetched_items(): collection {
        return $this->items->map_to(subject_instance_model::class);
    }

    private function get_target_participant_exists(): builder {
        return participant_instance::repository()
            ->as('target_participant')
            ->where_raw('target_participant.subject_instance_id = si.id')
            ->where('participant_id', $this->participant_id)
            ->where('participant_source', $this->participant_source)
            ->get_builder();
    }

    /**
     * Returns sections and their participants related to the current set of
     * subject instances.
     *
     * @return collection|subject_sections[] a list of subject sections.
     */
    public function get_subject_sections(): collection {
        $subject_instances = $this->get();
        return subject_sections::create_from_subject_instances($subject_instances);
    }

    /**
     * Returns next page of sections and their participants related to the current set of
     * subject instances.
     *
     * @param string $cursor
     * @param int $page_size
     * @return \stdClass
     */
    public function get_subject_sections_page(string $cursor = '', int $page_size = cursor_paginator::DEFAULT_ITEMS_PER_PAGE): \stdClass {
        $cursor = !empty($cursor) ? cursor::decode($cursor) : cursor::create()->set_limit($page_size);
        $paginator = $this->get_next($cursor, true);
        $items = $paginator->get_items()->map_to(subject_instance_model::class);

        $next_cursor = $paginator->get_next_cursor();
        return (object)[
            'items' => subject_sections::create_from_subject_instances($items),
            'total' => $paginator->get_total(),
            'next_cursor' => $next_cursor === null ? '' : $next_cursor->encode(),
        ];
    }

    /**
     * Get a single subject instance, and only return it if the specified user is allowed to view it.
     *
     * @param int $subject_instance_id
     * @return subject_instance_model
     */
    public function get_subject_instance(int $subject_instance_id): ?subject_instance_model {
        return $this
            ->add_filters(['subject_instance_id' => $subject_instance_id])
            ->get()
            ->first();
    }

}