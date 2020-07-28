<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTDvs
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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
 * @author  Murali Nair <murali.nair@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\activity\helpers;

use mod_perform\models\activity\activity;
use mod_perform\models\activity\activity_setting;
use mod_perform\models\activity\section;

/**
 * Encapsulates the business logic to execute when an activity toggles between
 * single section and multisection content.
 */
class activity_multisection_toggler {
    /**
     * @var activity target activity.
     */
    private $activity = null;

    /**
     * Default constructor.
     *
     * @param activity $activity target activity whose multisection setting is
     *        to be changed.
     */
    public function __construct(activity $activity) {
        $this->activity = $activity;
    }

    /**
     * Returns the multisection setting. Note the returned value is adjusted
     * according to the actual section count in the activity:
     * - if the original setting is single, but there are multiple sections,
     *   then the setting is updated to be multiple.
     * - In all other cases, the original setting is returned as is.
     *
     * @return bool the adjusted multisection setting.
     */
    public function get_current_setting(): bool {
        $setting_key = activity_setting::MULTISECTION;

        $activity_settings = $this->activity->settings;
        $is_multisection = (bool)$activity_settings->lookup($setting_key);

        if (!$is_multisection && $this->activity->sections->count() > 1) {
            // The multisection value is false (ie single section) but there are
            // multiple sections. This inconsistency arises if the caller did not
            // use $this->set() to execute business rules but updated the activity
            // setting directly.
            $is_multisection = true;
            $activity_settings->update([$setting_key => $is_multisection]);
        }

        return $is_multisection;
    }

    /**
     * Changes the multisection setting for the previously specified activity.
     *
     * @param bool $new_setting true if the activity should allow multisection
     *        content.
     *
     * @return activity the updated activity.
     */
    public function set(bool $new_setting): activity {
        global $DB;

        $activity_settings = $this->activity->settings;

        $setting_key = activity_setting::MULTISECTION;
        $existing_setting = (bool)$activity_settings->lookup($setting_key);
        if ($existing_setting === $new_setting) {
            // Takes care of single -> single or multisection -> multisection.
            return $this->activity;
        }

        if ($this->activity->is_active()) {
            throw new \coding_exception('Can\'t toggle multisection on an active activity.');
        }

        $DB->transaction(
            function () use ($activity_settings, $setting_key, $new_setting) {
                if (!$new_setting) {
                    // This is only executed if multisection -> single section.
                    $this->merge_sections();
                } else {
                    // This is only executed if single section -> multisection.
                    $this->rebuild_first_section();
                }

                $activity_settings->update([$setting_key => $new_setting]);
            }
        );

        return $this->activity->refresh(true);
    }

    /**
     * Merges activity content in multiple sections into a single one.
     */
    private function merge_sections(): void {
        $sections = $this->activity->sections; // Already sorted in the correct order.
        if ($sections->count() === 0) {
            return;
        }

        $elements_to_transfer = [];
        foreach ($sections as $section) {
            $section_elements = $section->section_elements->sort('sort_order');

            foreach ($section_elements as $section_element) {
                $elements_to_transfer[] = $section_element->element;

                $section_element->delete();
            }

            $section->delete();
        }

        $merged_section = section::create($this->activity);
        foreach ($elements_to_transfer as $order => $element) {
            $merged_section->add_element($element);
        }
    }

    /**
     * Adjusts the sections in an activity when moving from single section
     * to multisection mode.
     *
     * NB: this method deletes existing section(s) and recreates them so that
     * sections appear as "new" to callers. As of now, this is acceptable since
     * only draft activities (ie those activities that do not have participants
     * or their responses) are allowed to change the multisection setting. If
     * there is a need in the future to account for participant responses as
     * well, then this method will need to change.
     */
    private function rebuild_first_section(): void {
        $sections = $this->activity->sections;
        if ($sections->count() === 0) {
            return;
        }

        // Ideally, the section count is always consistent with the multisection
        // setting. However, that cannot be 100% enforced because callers can set
        // up these values independently of each other. Which is why this method
        // does not assume there is only one section.
        foreach ($sections as $section) {
            $title = $section->title;

            // Remove original section.
            $elements_to_transfer = [];
            $section_elements = $section->section_elements->sort('sort_order');
            foreach ($section_elements as $section_element) {
                $elements_to_transfer[] = $section_element->element;

                $section_element->delete();
            }

            $relationships_to_transfer = [];
            $relationships = $section->get_section_relationships();
            foreach ($relationships as $section_relationship) {
                $relationships_to_transfer[] = [
                    'core_relationship_id' => $section_relationship->core_relationship->id,
                    'can_view' => $section_relationship->can_view
                ];
            }

            $section->delete(); // This also removes the relationships.

            // And then recreate it.
            $rebuilt_section = section::create($this->activity)
                ->update_title($title)
                ->update_relationships($relationships_to_transfer);

            foreach ($elements_to_transfer as $order => $element) {
                $rebuilt_section->add_element($element);
            }
        }
    }
}
