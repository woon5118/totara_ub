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

/**
 * Theme appearance settings helper.
 */
final class helper {

    /**
     * @param theme_settings $theme_settings
     * @return array
     */
    public static function output_theme_settings(theme_settings $theme_settings): array {
        global $USER;

        // Get files and categories.
        $files = $theme_settings->get_files($USER->id);
        $categories = $theme_settings->get_categories();

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

        return [
            'categories' => $categories,
            'files' => $files,
            'flavours' => $theme_settings->get_flavours(),
        ];
    }

}