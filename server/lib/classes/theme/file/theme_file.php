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

namespace core\theme\file;

use core\files\file_area;
use core\files\file_helper;
use core\files\type\file_type;
use context;
use moodle_url;
use stored_file;
use theme_config;

/**
 * Class theme_file
 *
 * Abstract base class for theme settings related files.
 *
 * @package core\theme\file
 */
abstract class theme_file {

    /** @var string */
    protected $theme;

    /** @var theme_config */
    protected $theme_config;

    /** @var int */
    protected $tenant_id = 0;

    /** @var int */
    protected $user_id;

    /** @var context */
    protected $context;

    /** @var file_type */
    protected $type;

    /** @var int */
    protected $item_id = 0;

    /**
     * theme_file constructor.
     *
     * @param theme_config|null $theme_config
     * @param string|null $theme
     */
    public function __construct(?theme_config $theme_config = null, ?string $theme = null) {
        global $CFG;
        $this->theme_config = $theme_config
            ?? theme_config::load($theme ?? $CFG->theme);
    }

    /**
     * @param int $tenant_id
     */
    public function set_tenant_id(int $tenant_id): void {
        $this->tenant_id = $tenant_id;
    }

    /**
     * @param int $user_id
     */
    public function set_user_id(int $user_id): void {
        $this->user_id = $user_id;
    }

    /**
     * @param context $context
     */
    public function set_context(context $context): void {
        $this->context = $context;
    }

    /**
     * @return context|null
     */
    public function get_context(): ?context {
        if (empty($this->context)) {
            return $this->get_default_context();
        }
        return $this->context;
    }

    /**
     * @param theme_config $theme_config
     */
    public function set_theme_config(theme_config $theme_config): void {
        $this->theme_config = $theme_config;
    }

    /**
     * @param int $item_id
     */
    public function set_item_id(int $item_id): void {
        $this->item_id = $item_id;
    }

    /**
     * Get item ID of the theme plugin.
     *
     * @param string|null $theme
     *
     * @return int
     *
     */
    public function get_item_id(?string $theme = null): int {
        global $DB;

        $plugin = "theme_" . ($theme ?? $this->theme_config->name);
        $name = "tenant_{$this->tenant_id}_settings";
        if (!get_config($plugin, $name)) {
            set_config($name, '{}', $plugin);
        }

        // To keep this settings unique per theme we need to get a
        // unique ID and the plugin ID is as good as any.
        $this->item_id = $DB->get_field(
            'config_plugins',
            'id',
            [
                'plugin' => $plugin,
                'name' => $name,
            ]
        );

        return $this->item_id;
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function get_name(): string {
        return "{$this->get_component()}/{$this->get_area()}";
    }

    /**
     * Indicates whether a default file exist if a
     * custom one has not been provided.
     *
     * @return bool
     */
    public function has_default(): bool {
        return true;
    }

    /**
     * Get file currently overriding the default file.
     *
     * @param int|null $item_id
     *
     * @return stored_file|null
     */
    public function get_current_imagefile(?int $item_id = null): ?stored_file {
        $item_id = $item_id ?? $this->get_item_id();

        // Get context.
        $context = $this->get_context();
        if (empty($context)) {
            return null;
        }

        // Get files for current component and context.
        $file_helper = new file_helper(
            $this->get_component(),
            $this->get_area(),
            $context
        );
        $file_helper->set_item_id($item_id);
        $files = $file_helper->get_stored_files();

        // If no files found then return default URL.
        if (empty($files)) {
            return null;
        }

        // Return first file found.
        /** @var stored_file $file */
        $file = reset($files);

        return $file;
    }

    /**
     * Get the URL to the current file.
     *
     * @return moodle_url|null
     */
    public function get_current_url(): ?moodle_url {
        // If site has not yet been installed we can return null here as no
        // image would have been overridden at this point.
        if (during_initial_install()) {
            return null;
        }

        $file = $this->get_current_imagefile();
        if (empty($file)) {
            // Check if any parent has this file.
            $parents = $this->theme_config->parents;
            foreach ($parents as $parent) {
                $item_id = $this->get_item_id($parent);
                $file = $this->get_current_imagefile($item_id);
                if (!empty($file)) {
                    break;
                }
            }
        }
        return !empty($file) ? $this->get_url($file) : null;
    }

    /**
     * Get the current URL or default as fallback.
     *
     * @return moodle_url
     */
    public function get_current_or_default_url(): moodle_url {
        $url = $this->get_current_url();
        if (empty($url)) {
            $url = $this->get_default_url();
        }
        return $url;
    }

    /**
     * @param stored_file $file
     *
     * @return moodle_url
     */
    protected function get_url(stored_file $file): moodle_url {
        return moodle_url::make_pluginfile_url(
            $this->get_context()->id,
            $this->get_component(),
            $this->get_area(),
            $this->get_item_id(),
            '/',
            $file->get_filename()
        );
    }

    /**
     * Get the URL to the default file.
     *
     * @return moodle_url|null
     */
    public function get_default_url(): ?moodle_url {
        if ($this->has_default()) {
            $parts = explode('/', static::get_id());
            return $this->type->create_url(
                $this->theme_config->name,
                $parts[0],
                $parts[1],
                theme_get_revision()
            );
        }
        return null;
    }

    /**
     * Get file area to which files need to be uploaded.
     *
     * @return file_area
     */
    public function get_file_area(): file_area {
        $file_helper = new file_helper(
            $this->get_component(),
            $this->get_area(),
            $this->get_context()
        );
        return $file_helper->create_file_area($this->user_id);
    }

    /**
     * Get the files in the draft area.
     *
     * @param int $draft_id
     *
     * @return stored_file[]
     */
    protected function get_draft_files(int $draft_id): array {
        global $USER;

        // Get files in user draft area.
        $file_helper = new file_helper(
            'user',
            'draft',
            \context_user::instance($USER->id)
        );
        $file_helper->set_item_id($draft_id);
        $file_helper->set_sort('timecreated desc');
        return $file_helper->get_stored_files();
    }

    /**
     * The file area might be polluted with multiple uploaded files or the user
     * might have uploaded a file with an incorrect extension. We need to clean
     * the draft area to get rid of such invalid files.
     *
     * @param stored_file[] $files
     *
     * @return stored_file[]
     */
    protected function clean_draft_files(array $files): array {
        // Get the list of valid file extensions for this theme file.
        $extensions = $this->get_type()->get_valid_extensions();
        $mimetypes = [];
        foreach ($extensions as $extension) {
            $mimetypes[] = mimeinfo('type', $extension);
        }

        // The draft area might be polluted with irrelevant files. We are only interested
        // in the most recent file uploaded with the right extension. The files should be
        // fetched according to time created so we basically can just use the first file
        // that is correct and remove the rest.
        $found = false;
        foreach ($files as $key => $file) {
            if (!$found && in_array($file->get_mimetype(), $mimetypes)) {
                // We found a file and it should stay in the files array.
                $found = true;
                continue;
            }
            // Delete the files that we don't want.
            $file->delete();
            unset($files[$key]);
        }

        return $files;
    }

    /**
     * Save files that are currently in draft area.
     *
     * @param int $draft_id
     */
    public function save_files(int $draft_id): void {
        // Get the settings currently used for this theme file.
        $setting = new \admin_setting_configstoredfile(
            $this->get_name(),
            '',
            '',
            $this->get_area(),
            $this->get_item_id(),
            [
                'accepted_types' => $this->get_type()->get_group(),
                'context' => $this->get_context()
            ]
        );

        // Get current file name.
        $current = $setting->get_setting();

        // Get and clean files in draft area.
        $files = $this->get_draft_files($draft_id);
        $files = $this->clean_draft_files($files);

        // If we have any files left after cleaning then we need to save them.
        if (sizeof($files) > 0) {
            if ($setting->write_setting($draft_id) !== '') {
                throw new \moodle_exception('themesavefiles', 'error');
            }
            $setting->post_write_settings($current);
        }
    }

    /**
     * Is this feature enabled. This is different from is_available as this
     * function should be used to determine if the feature is enabled.
     *
     * @return bool
     */
    public function is_enabled(): bool {
        return true;
    }

    /**
     * Get default properties.
     *
     * @return array
     */
    public function get_default_categories(): array {
        return [];
    }

    /**
     * Is this file available. This is different from is_enabled as this
     * function should be used to determine if the file is available based
     * on settings or if the file exists.
     *
     * @return bool
     */
    public function is_available(): bool {
        return $this->is_enabled();
    }

    /**
     * Get the default context for theme file.
     *  - Tenant context to fetch files limited to a specific tenant.
     *  - System context if we don't have tenants.
     *
     * @param int|null $tenant_id
     *
     * @return context|null
     */
    protected function get_default_context(?int $tenant_id = null): ?context {
        global $USER;

        if (!empty($tenant_id)) {
            return \context_tenant::instance($tenant_id);
        } elseif (!empty($USER->tenantid)) {
            return \context_tenant::instance($USER->tenantid);
        }

        return \context_system::instance();
    }

    /**
     * Get the ID of the file currently being used by the system
     * that is being overwritten.
     * For example:
     *    For $OUTPUT->image_url('filename', 'component') the ID
     *    would be 'component/filename'.
     *
     * @return string
     */
    abstract static public function get_id(): string;

    /**
     * @return string
     */
    abstract public function get_component(): string;

    /**
     * @return string
     */
    abstract public function get_area(): string;

    /**
     * Get a unique key to map to theme categories.
     *
     * @return string
     */
    abstract public function get_ui_key(): string;

    /**
     * Get the category to which this must be merged.
     *
     * @return string
     */
    abstract public function get_ui_category(): string;

    /**
     * Get the type of file.
     *
     * @return file_type
     */
    abstract public function get_type(): file_type;

}