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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package core
 */

namespace core\theme;

use core\theme\settings as theme_settings;
use core\theme\file\theme_file;
use totara_core\advanced_feature;

/**
 * Theme appearance settings helper.
 */
final class helper {

    /**
     * @param theme_settings $theme_settings
     * @return array
     */
    public static function output_theme_settings(theme_settings $theme_settings): array {
        // Get files and categories.
        $files = $theme_settings->get_files();
        $categories = $theme_settings->get_categories();

        // Remove the files that are disabled or the user does not have access to.
        $files = array_filter($files, function ($file) use ($theme_settings) {
            return $file->is_enabled() && $theme_settings->can_manage($file);
        });

        // The FE requirement is that we map the image files to categories
        // for easy mapping between settings and files.
        /** @var theme_file $file */
        $file_categories = [];
        foreach ($files as $file) {
            $file_categories[$file->get_ui_category()][] = [
                'name' => $file->get_ui_key(),
                'type' => 'file',
                'value' => '',
            ];
        }

        // Reorg categories.
        foreach ($file_categories as $key => $properties) {
            foreach ($categories as &$category) {
                if ($category['name'] === $key) {
                    $category['properties'] = array_merge($category['properties'], $properties);
                    continue 2;
                }
            }

            // Insert new category.
            $categories[] = [
                'name' => $key,
                'properties' => $properties
            ];
        }

        // Flavours.
        $engage_resources = advanced_feature::is_enabled('engage_resources');
        $container_workspace = advanced_feature::is_enabled('container_workspace');
        $flavours  = [
            // Learn will always be true as creating a course is part of core.
            'learn' => true,
            // No settings yet for perform.
            'perform' => false,
            // Determine if the engage flavour is enabled.
            'engage' => $engage_resources || $container_workspace,
        ];

        return [
            'categories' => $categories,
            'files' => $files,
            'flavours' => $flavours,
            'context_id' => self::get_context()->id,
        ];
    }

    /**
     * @return \context
     */
    private static function get_context(): \context {
        global $CFG, $USER;
        if ($CFG->tenantsenabled && $USER->tenantid) {
            return \context_tenant::instance($USER->tenantid);
        }
        return \context_system::instance();
    }

}