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

use core\theme\file\helper;
use core\theme\file\theme_file;
use core\theme\validation\property\property_validator;
use theme_config;

/**
 * Theme appearance settings manager.
 */
final class settings {

    /** @var theme_config */
    private $theme_config;

    /** @var int */
    private $tenant_id;

    /**
     * settings constructor.
     *
     * @param theme_config $theme_config
     * @param int $tenant_id
     */
    public function __construct(theme_config $theme_config, int $tenant_id) {
        $this->theme_config = $theme_config;
        $this->tenant_id = $tenant_id;
    }

    /**
     * Get setting categories for theme.
     *
     * @param bool $tenant_enabled
     *
     * @return array|mixed
     */
    public function get_categories($tenant_enabled = true): array {
        global $DB;

        // Get theme inheritance.
        $themes = $this->theme_config->parents;
        $themes = array_reverse($themes);

        // Add current theme to array.
        array_push($themes, $this->theme_config->name);

        // For each theme in hierarchy get the settings for tenant.
        $categories = $this->get_default_categories();
        foreach ($themes as $theme) {
            // Get all variables for site.
            $values = $DB->get_field('config_plugins', 'value', [
                'name' => "tenant_0_settings",
                'plugin' => "theme_{$theme}"
            ]);
            if (!empty($values)) {
                $theme_categories = json_decode($values, true);
                $this->merge_categories($categories, $theme_categories);
            }

            // Get all variables for current tenant and override site.
            if ($tenant_enabled && $this->tenant_id > 0) {
                $values = $DB->get_field('config_plugins', 'value', [
                    'name' => "tenant_{$this->tenant_id}_settings",
                    'plugin' => "theme_{$theme}"
                ]);
                if (!empty($values)) {
                    $theme_categories = json_decode($values, true);
                    $this->merge_categories($categories, $theme_categories);
                }
            }
        }

        return $categories;
    }

    /**
     * Overwrite values in array1 with values in array2.
     *
     * @param array $categories1
     * @param array $categories2
     */
    private function merge_categories(array &$categories1, array $categories2) {
        foreach ($categories2 as $category2) {
            foreach ($categories1 as &$category1) {
                if ($category1['name'] === $category2['name']) {
                    $this->merge_properties(
                        $category1['properties'],
                        $category2['properties']
                    );
                    continue 2;
                }
            }
            $categories1[] = $category2;
        }
    }

    /**
     * Overwrite values in array1 with values in array2.
     *
     * @param array $props1
     * @param array $props2
     */
    private function merge_properties(array &$props1, array $props2) {
        foreach ($props2 as $prop2) {
            foreach ($props1 as &$prop1) {
                if ($prop1['name'] === $prop2['name']) {
                    $prop1['value'] = $prop2['value'];
                    continue 2;
                }
            }
            $props1[] = $prop2;
        }
    }

    /**
     * Get default category properties.
     *
     * @return array
     */
    private function get_default_categories(): array {
        $categories = [];
        $classes = helper::get_classes();
        foreach ($classes as $class) {
            /** @var theme_file $theme_file */
            $theme_file = new $class($this->theme_config);
            if ($theme_file->is_enabled()) {
                $categories = array_merge($categories, $theme_file->get_default_categories());
            }
        }
        return $categories;
    }

    /**
     * @param int $user_id
     *
     * @return array
     */
    public function get_files(int $user_id): array {
        $files = [];
        $classes = helper::get_classes();
        foreach ($classes as $class) {
            /** @var theme_file $theme_file */
            $theme_file = new $class($this->theme_config);
            $theme_file->set_tenant_id($this->tenant_id);
            $theme_file->set_user_id($user_id);
            if ($theme_file->is_enabled()) {
                $files[] = $theme_file;
            }
        }
        return $files;
    }

    /**
     * @param array $categories
     */
    public function validate_categories(array $categories): void {
        foreach ($categories as $category) {
            foreach ($category['properties'] as $property) {
                $this->validate_property($property);
            }
        }

        // Only brand and colours for tenants.
        if ($this->tenant_id !== 0) {
            $categories = array_filter($categories, function ($category) {
                return !in_array($category['name'], ['brand', 'colours', 'tenant']);
            });
            if (sizeof($categories) > 0) {
                throw new \invalid_parameter_exception('Tenants are only allowed to update brand and colours.');
            }
        }
    }

    /**
     * @param array $property
     */
    public function validate_property(array $property): void {
        $validator = "core\\theme\\validation\\property\\{$property['type']}_validator";
        if (class_exists($validator)) {
            $validator = new $validator();
            if (!$validator instanceof property_validator) {
                throw new \coding_exception('Invalid validator class');
            }
            $validator->validate($property);
        }
    }

    /**
     * @param array $categories
     */
    public function update_categories(array $categories): void {
        global $DB;

        $condition = [
            'name' => "tenant_{$this->tenant_id}_settings",
            'plugin' => "theme_{$this->theme_config->name}"
        ];

        // Update per category if found, otherwise insert new record.
        $cats = $categories;
        if ($record = $DB->get_record('config_plugins', $condition)) {
            $cats = json_decode($record->value, true);
            foreach ($categories as $category) {
                // Update category if found.
                foreach ($cats as &$cat) {
                    if ($cat['name'] === $category['name']) {
                        $cat['properties'] = $category['properties'];
                        continue 2;
                    }
                }
                // Add new category if not found.
                $cats[] = $category;
            }
        }
        set_config($condition['name'], json_encode($cats), $condition['plugin']);
    }

    /**
     * @param array $files
     * @param int $user_id
     */
    public function update_files(array $files, int $user_id): void {
        // Get classes and instantiate them all.
        $classes = helper::get_classes();
        $instances = [];
        foreach ($classes as $class) {
            $instances[] = new $class($this->theme_config);
        }

        // Update files.
        foreach ($files as $file) {
            /** @var theme_file $instance */
            foreach ($instances as $instance) {
                if ($instance->get_ui_key() === $file['ui_key']) {
                    $instance->set_user_id($user_id);
                    $instance->set_tenant_id($this->tenant_id);
                    if ($instance->is_enabled()) {
                        $instance->save_files($file['draft_id']);
                    }
                    continue 2;
                }
            }
        }
    }

    /**
     * Get a specific property.
     *
     * @param string $category
     * @param string $property
     *
     * @return array|null
     */
    public function get_property(string $category, string $property): ?array {
        $categories = $this->get_categories();
        foreach($categories as $cat) {
            if ($cat['name'] === $category) {
                foreach ($cat['properties'] as $prop) {
                    if ($prop['name'] === $property) {
                        return $prop;
                    }
                }
                break;
            }
        }

        return null;
    }

    /**
     * Get a list of css variables from the categories.
     *
     * @return string
     */
    public function get_css_variables(): string {
        // If tenant then check if tenant enabled.
        $tenant_enabled = false;
        if ($this->tenant_id > 0) {
            $tenant_enabled = $this->is_tenant_branding_enabled();
        }

        $css = '';
        $categories = $this->get_categories($tenant_enabled);
        foreach ($categories as $category) {
            // Each colour property needs to be in the root element
            // of the document tree.
            if ($category['name'] === 'colours') {
                $switches = array_filter($category['properties'], function (array $property) {
                    return $property['type'] === 'boolean';
                });
                $css = ':root{';
                foreach ($category['properties'] as $property) {
                    if ($property['type'] !== 'value') {
                        continue;
                    }
                    if ($this->is_on($switches, $property['name'])) {
                        $css .= "--{$property['name']}: {$property['value']};";
                    }
                }
                $css .= '}';
                continue;
            }

            // Custom CSS is just output as it is - the user will need
            // to know how to format it correctly.
            if ($category['name'] === 'custom') {
                foreach ($category['properties'] as $property) {
                    $css .= "\n{$property['value']}\n";
                }
            }
        }
        return $css;
    }

    /**
     * Check if a value is controlled by a switch and that the switch is on.
     *
     * @param array $switches
     * @param string $name
     *
     * @return bool
     */
    private function is_on(array $switches, string $name): bool {
        foreach ($switches as $switch) {
            if (isset($switch['selectors']) && in_array($name, $switch['selectors'])) {
                return filter_var($switch['value'], FILTER_VALIDATE_BOOLEAN);
            }
        }
        return true;
    }

    /**
     * Check if a setting is enabled.
     *
     * @param string $category
     * @param string $property
     * @param bool $default
     *
     * @return bool
     */
    public function is_enabled(string $category, string $property, bool $default = false): bool {
        $prop = $this->get_property($category, $property);
        return !empty($prop)
            ? filter_var($prop['value'], FILTER_VALIDATE_BOOLEAN) ?? $default
            : $default;
    }

    /**
     * Check if tenant branding is enabled.
     *
     * @return bool
     */
    public function is_tenant_branding_enabled(): bool {
        return $this->is_enabled('tenant', 'formtenant_field_tenant', false);
    }

}