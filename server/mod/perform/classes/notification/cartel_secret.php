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

namespace mod_perform\notification;

use coding_exception;
use core\orm\collection;
use mod_perform\entities\activity\subject_instance as subject_instance_entity;
use mod_perform\models\activity\activity as activity_model;
use mod_perform\models\activity\notification as notification_model;
use mod_perform\models\activity\notification_recipient as notification_recipient_model;
use mod_perform\models\activity\subject_instance as subject_instance_model;
use totara_core\relationship\relationship;

/**
 * The cartel_secret class.
 */
class cartel_secret extends cartel {
    /** @var subject_instance_entity[] */
    private $subject_instances;

    /**
     * Constructor. *Do not instantiate this class directly. Use the factory class.*
     *
     * @param subject_instance_entity[] $subject_instances
     */
    public function __construct(array $subject_instances) {
        $collection = new collection($subject_instances);
        subject_instance_entity::repository()
            ->with('manual_relationship_selection_progress.manual_relationship_selectors.user')
            ->load_relations($collection);
        $this->subject_instances = $collection;
    }

    /**
     * @param string $class_key
     * @throws coding_exception
     */
    public function dispatch(string $class_key): void {
        foreach ($this->subject_instances as $instance) {
            $activity = activity_model::load_by_entity($instance->activity());
            $notification = notification_model::load_by_activity_and_class_key($activity, $class_key);
            $dealer = factory::create_dealer_on_notification($notification);
            if (!$dealer) {
                // The notification is not active, recipients are not set, etc.
                continue;
            }
            $placeholders = placeholder::from_subject_instance(subject_instance_model::load_by_entity($instance));
            $recipients = notification_recipient_model::load_by_notification($notification, true);
            /** @var relationship[] $relationships */
            $relationships = [];
            foreach ($recipients as $recipient) {
                $relationships[$recipient->relationship_id] = $recipient->relationship;
            }
            foreach ($instance->manual_relationship_selection_progress as $progress) {
                $selector_relationship = $relationships[$progress->manual_relationship_selection->selector_relationship_id] ?? false;
                if ($selector_relationship) {
                    foreach ($progress->manual_relationship_selectors as $selector) {
                        $placeholders->set_participant($selector->user, $selector_relationship);
                        $dealer->post($selector->user, $selector_relationship, $placeholders);
                    }
                }
            }
        }
    }
}
