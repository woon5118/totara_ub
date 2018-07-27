<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @package block_admin_related_pages
 */

namespace block_admin_related_pages;

/**
 * Admin related pages block helper class.
 */
final class helper {

    /**
     * The generated map.
     *
     * @var map
     */
    private $map;

    /**
     * Returns a map.
     *
     * @return map
     */
    private function get_map(): map {
        $this->ensure_map_loaded();
        return $this->map;
    }

    /**
     * Ensure that the map has been loaded.
     *
     * This function uses a cache to store the map for the user.
     * This is not cleared and can become stale.
     * However it has a TTL of 10 minutes, so will only be stale for a short while.
     *
     * The functionality is entirely supplementary so this TTL is deemed acceptable.
     */
    private function ensure_map_loaded() {
        if ($this->map !== null) {
            return;
        }

        $cache = \cache::make('block_admin_related_pages', 'map');
        $map = $cache->get('fullmap');
        if ($map !== false) {
            $this->map = $map;
            return;
        }

        $this->map = new map(
            [
                // System.
                new group(
                    [
                        new item('adminnotifications', 'notifications', ''),
                        new item('totararegistration', 'totararegistration', 'totara_core'),
                        new item('flavouroverview', 'flavouroverview', 'totara_flavour'),
                        new item('optionalsubsystems', 'advancedfeatures', 'admin'),
                    ]
                ),

                // Security.
                new group(
                    [
                        new item('optionalsubsystems', 'advancedfeatures', 'admin'),
                        new item('environment', 'environment', 'admin', ['server']),
                        new item('sitepolicies', 'securitysettings', 'tool_sitepolicy', ['security']),
                        new item('reportsecurity', 'pluginname', 'report_security', ['reports']),
                    ]
                ),

                // Course management.
                new group(
                    [
                        new item('coursemgmt', 'courses', 'admin', ['courses']),
                        new item('managemodules', 'activitymodules', '', ['modsettings']),
                        new item('manageenrols', 'enrolments', 'enrol', ['enrolments']),
                        new item('managefilters', 'managefilters', '', ['filtersettings']),
                        new item('gradessettings', 'grades', '', ['grades']),
                    ]
                ),

                // User management.
                new group(
                    [
                        new item('editusers', 'users', 'admin', ['users']),
                        new item('cohorts', 'cohorts', 'cohort', ['audiences']),
                        new item('assignroles', 'permissions', 'role', ['roles']),
                        new item('manageauths', 'authentication', 'admin', ['authsettings']),
                        new item('userdatasettings', 'pluginname', 'totara_userdata', ['userdata']),
                    ]
                ),

                // User creation.
                new group(
                    [
                        new item('editusers', 'users', 'admin'),
                        new item('managesyncelements', 'pluginname', 'tool_totara_sync'),
                    ]
                ),

                // Course creation.
                new group(
                    [
                        new item('coursemgmt', 'courses', 'admin'),
                        new item('manage_content_marketplaces', 'contentmarketplace', 'totara_contentmarketplace'),
                    ]
                ),
            ],
            [
                // Link from courses to tags.
                new item('managetags', 'tags', 'tag', ['courses']),

                // Link from users, positions, and organisations to HR Import
                new item('managesyncelements', 'pluginname', 'tool_totara_sync', ['users', 'positions', 'organisations', 'competencies']),
                new item('editusers', 'users', 'admin', ['tool_totara_sync', 'syncelements']),

                // Link HR Import to positions, organisations, and competencies.
                new item('positionmanage', 'positions', 'totara_hierarchy', ['tool_totara_sync', 'syncelements']),
                new item('organisationmanage', 'organisations', 'totara_hierarchy', ['tool_totara_sync', 'syncelements']),
                new item('competencymanage', 'competencies', 'totara_hierarchy', ['tool_totara_sync', 'syncelements']),

                // Link from the system group to the security group.
                new item('environment', 'environment', 'admin', ['adminnotifications', 'totararegistration', 'flavouroverview', 'optionalsubsystems']),
                new item('sitepolicies', 'securitysettings', 'tool_sitepolicy', ['adminnotifications', 'totararegistration', 'flavouroverview', 'optionalsubsystems']),
                new item('reportsecurity', 'pluginname', 'report_security', ['adminnotifications', 'totararegistration', 'flavouroverview', 'optionalsubsystems']),
            ]
        );

        $hook = new hook\map_generated($this->map);
        $hook->execute();

        $cache->set('fullmap', $this->map);
    }

    /**
     * Returns all of the related pages for the given key.
     *
     * The result will exclude the item belonging to, or with a direct relation to the key.
     *
     * @param string $key
     * @return item[]
     */
    public static function get_related_pages(string $key): array {
        $helper = new self();
        $items = $helper->resolve_related_pages($key);
        return $items;
    }

    /**
     * Resolves and returns related items.
     *
     * @param string $key
     * @return array
     */
    private function resolve_related_pages(string $key): array {
        $map = $this->get_map();
        $items = $map->get_mapped_items($key);
        foreach ($items as $itemid => $item) {
            $attachments = $item->get_related_pages();
            $itemkey = $item->get_key();
            if ($itemkey === $key) {
                unset($items[$itemid]);
                continue;
            }
            if (isset($attachments[$key]) && $attachments[$key] === $itemkey) {
                unset($items[$itemid]);
            }
        }
        return $items;
    }
}
