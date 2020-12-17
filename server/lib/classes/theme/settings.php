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

use core\hook\tenant_customizable_theme_settings as tenant_customizable_theme_settings_hook;
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

    /** @var theme_file[] */
    private $file_instances = [];

    /** @var tenant_customizable_theme_settings_hook */
    private $tenant_settings_hook;

    /**
     * settings constructor.
     *
     * @param theme_config $theme_config
     * @param int $tenant_id
     */
    public function __construct(theme_config $theme_config, int $tenant_id) {
        $this->theme_config = $theme_config;
        $this->tenant_id = $tenant_id;

        $default_tenant_can_customize = [
            'brand' => '*',
            'colours' => '*',
            'images' => ['sitelogin'],
            'custom' => ['formcustom_field_customfooter'],
            'tenant' => '*',
        ];

        $this->tenant_settings_hook = new tenant_customizable_theme_settings_hook($default_tenant_can_customize);
        $this->tenant_settings_hook->execute();
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

        $theme = $this->theme_config->name;

        $categories = $this->get_default_categories();
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
            $values = $DB->get_field('config_plugins', 'value', $this->get_config_parameters($this->tenant_id, $theme));
            if (!empty($values)) {
                $theme_categories = json_decode($values, true);
                $this->merge_categories($categories, $theme_categories);
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
        $instances = $this->get_file_instances();
        foreach ($instances as $instance) {
            if ($instance->is_enabled() && $this->can_manage($instance)) {
                $categories = array_merge($categories, $instance->get_default_categories());
            }
        }
        return $categories;
    }

    /**
     * @return theme_file[]
     */
    public function get_files(): array {
        return $this->get_file_instances();
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
                return !$this->is_tenant_customizable_category($category['name']);
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
     * @param array $files
     */
    public function validate_files(array $files): void {
        $instances = $this->get_file_instances();
        foreach ($files as $file) {
            foreach ($instances as $instance) {
                if ($instance->get_ui_key() === $file['ui_key']) {
                    // Confirm that the user has capability to manage the file.
                    if (!$instance->is_enabled() || !$this->can_manage($instance)) {
                        throw new \moodle_exception('nopermissionthemefile', 'error');
                    }
                    continue 2;
                }
            }
        }
    }

    /**
     * @param array $categories
     */
    public function update_categories(array $categories): void {
        global $DB;

        $condition = $this->get_config_parameters();

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
     * @param bool $copy_site_files
     */
    public function update_files(array $files, bool $copy_site_files = false): void {
        $instances = $this->get_file_instances();

        // Update files.
        foreach ($files as $file) {
            foreach ($instances as $instance_key => $instance) {
                if ($instance->get_ui_key() === $file['ui_key']) {
                    if (empty($file['action']) || $file['action'] === 'SAVE') {
                        // If draft ID is set we need to save it.
                        $instance->save_files($file['draft_id']);
                        unset($instances[$instance_key]);
                    } else if (!empty($file['action']) && $file['action'] === 'RESET') {
                        // If draft ID is not set and current URL is empty we need to delete the
                        // current stored file and restore the theme file to its default.
                        $instance->delete();
                    }

                    continue 2;
                }
            }
        }

        if ($this->tenant_id > 0 && $copy_site_files) {
            // Make copy of customizable files not yet customized for the tenant
            foreach ($instances as $instance_key => $instance) {
                if ($this->is_tenant_customizable_image($instance->get_ui_category(), $instance->get_ui_key())) {
                    $instance->copy_site_file_to_tenant();
                }
            }

            // Reset file instances
            $this->file_instances = [];
            $this->get_file_instances();
        }
    }

    /**
     * Delete the current stored file for a specific theme file so that it defaults
     * back to the default setting or site setting if removed for a tenant.
     *
     * @param array $file
     *
     * @return theme_file|null
     */
    public function delete_file(array $file): ?theme_file {
        $instances = $this->get_file_instances();

        foreach ($instances as $instance) {
            if ($instance->get_ui_key() === $file['ui_key']) {
                $instance->delete();
                return $instance;
            }
        }

        return null;
    }

    /**
     * @return theme_file[]
     */
    private function get_file_instances(): array {
        // Get classes and instantiate them all.
        if (empty($this->file_instances)) {
            $classes = helper::get_classes();
            foreach ($classes as $class) {
                $instance = new $class($this->theme_config);
                $instance->set_tenant_id($this->tenant_id);
                $this->file_instances[] = $instance;
            }
        }
        return $this->file_instances;
    }

    /**
     * Get a specific property.
     *
     * @param string $category
     * @param string $property
     *
     * @return array|null
     */
    public function get_property(string $category, string $property, ?array $categories = null): ?array {
        $categories = $categories ?? $this->get_categories();
        foreach ($categories as $cat) {
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
        $custom_css = '';
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
                    $custom_css .= "\n{$property['value']}\n";
                }
            }
        }

        // Return any category css with custom css added to the end.
        return $css . $custom_css;
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
     * Check if tenant previously enabled
     *
     * @return bool
     */
    public function is_initial_tenant_branding(): bool {
        return $this->tenant_id > 0 && $this->get_property('tenant', 'formtenant_field_tenant') === null;
    }

    /**
     * Check if tenant branding is enabled.
     *
     * @return bool
     */
    public function is_tenant_branding_enabled(): bool {
        return $this->is_enabled('tenant', 'formtenant_field_tenant', false);
    }

    /**
     * Check whether tenant branding is being re-enabled
     *
     * @param array $categories
     * @return bool
     */
    public function is_re_enabling_tenant_branding(array $categories): bool {
        if ($this->tenant_id === 0) {
            return false;
        }

        // Not previously set
        $prop = $this->get_property('tenant', 'formtenant_field_tenant');
        if ($prop === null) {
            return false;
        }

        // Set but currently enabled
        if (filter_var($prop['value'], FILTER_VALIDATE_BOOLEAN)) {
            return false;
        }

        // Set but currently disabled
        $new_prop = $this->get_property('tenant', 'formtenant_field_tenant', $categories);
        return !empty($new_prop)
            ? filter_var($new_prop['value'], FILTER_VALIDATE_BOOLEAN) ?? false
            : false;
    }


    /**
     * Confirm if a user has the capability required to manage a theme file.
     *
     * @param theme_file $theme_file
     *
     * @return bool
     */
    public function can_manage(theme_file $theme_file): bool {
        $context = $theme_file->get_context();
        if ($context instanceof \context_tenant) {
            $tenant = \core\record\tenant::fetch($context->tenantid);
            $context = \context_coursecat::instance($tenant->categoryid);
        }
        return has_capability('totara/tui:themesettings', $context);
    }

    /**
     * Return the list of theme settings that can be customized for tenants
     *
     * @return array
     */
    public function get_customizable_tenant_theme_settings(): array {
        return $this->tenant_settings_hook->get_customizable_settings();
    }

    /**
     * @param int|null $tenant_id
     * @param string|null $theme_name
     * @return array
     */
    private function get_config_parameters(?int $tenant_id = null, ?string $theme_name = null): array {
        $tenant_id = $tenant_id ?? $this->tenant_id;
        $theme_name = $theme_name ?? $this->theme_config->name;

        return [
            'name' => "tenant_{$tenant_id}_settings",
            'plugin' => "theme_{$theme_name}"
        ];
    }

    /**
     * @param string $name
     * @return bool
     */
    private function is_tenant_customizable_category(string $name): bool {
        return $this->tenant_settings_hook->is_tenant_customizable_category($name);
    }

    /**
     * @param string $category
     * @param string $ui_key
     * @return bool
     */
    private function is_tenant_customizable_image(string $category, string $ui_key): bool {
        return $this->tenant_settings_hook->is_tenant_customizable_category_setting($category, $ui_key);
    }
}