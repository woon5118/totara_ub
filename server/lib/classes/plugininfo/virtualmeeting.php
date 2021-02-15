<?php

namespace core\plugininfo;

use admin_root;
use coding_exception;
use core\plugininfo\base;
use core_plugin_manager;
use part_of_admin_tree;
use totara_core\virtualmeeting\exception\not_implemented_exception;
use totara_core\virtualmeeting\exception\unsupported_exception;
use totara_core\virtualmeeting\plugin\factory\factory;
use totara_core\virtualmeeting\plugin\factory\feature_factory;

/**
 * Manages virtual meeting plugins.
 */
class virtualmeeting extends base {
    /** @var string|null */
    private $description = null;

    /**
     * @inheritDoc
     */
    public function is_uninstall_allowed() {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function get_settings_section_name() {
        return $this->type . '_' . $this->name;
    }

    /**
     * @return string
     */
    private function resolve_factory_class(): string {
        return $this->component . '_factory';
    }

    /**
     * Return whether the PoC plugins are available or not.
     * $CFG->virtual_meeting_poc_plugin must be set before site installation, and cannot be set or changed afterwards.
     *
     * @return boolean
     */
    public static function is_poc_available(): bool {
        global $CFG;
        if (defined('BEHAT_SITE_RUNNING') || (defined('PHPUNIT_TEST') && PHPUNIT_TEST)) {
            return true;
        }
        // @codeCoverageIgnoreStart
        if ((!empty($CFG->debugdeveloper) || (!empty($CFG->sitetype) && $CFG->sitetype === 'development')) && !empty($CFG->virtual_meeting_poc_plugin)) {
            return true;
        }
        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Return an array of the PoC plugins.
     * Note that the PoC plugins are not actually installed on a system.
     *
     * @return array of [name => displayname]
     * @codeCoverageIgnore
     */
    private static function get_poc_plugins(): array {
        return [
            'poc_app' => 'PoC App',
            'poc_user' => 'PoC User',
        ];
    }

    /**
     * @inheritDoc
     */
    public function is_installed_and_upgraded() {
        if (self::is_poc_available()) {
            return true;
        }
        return parent::is_installed_and_upgraded(); // @codeCoverageIgnore
    }

    /**
     * @inheritDoc
     */
    public static function get_plugins($type, $typerootdir, $typeclass, $pluginman) {
        global $CFG;
        // If PoC plugin is enabled, all you see is PoC plugins.
        if ($type === 'virtualmeeting' && self::is_poc_available()) {
            $plugins = [];
            $names = self::get_poc_plugins();
            foreach (self::get_enabled_plugins() as $name => $x) {
                $plugin              = new self();
                $plugin->type        = $type;
                $plugin->typerootdir = $CFG->dirroot.'/totara/core/classes/virtualmeeting/poc';
                $plugin->name        = $name;
                $plugin->rootdir     = $CFG->dirroot.'/totara/core/classes/virtualmeeting/poc';
                $plugin->displayname = $names[$name] ?? $name;
                $plugin->versiondb   = '2020120100';
                $plugin->pluginman   = $pluginman;
                $plugin->source      = core_plugin_manager::PLUGIN_SOURCE_STANDARD;
                $plugin->description = $plugin->displayname;
                $plugins[$name] = $plugin;
            }
            return $plugins;
        }
        return parent::get_plugins($type, $typerootdir, $typeclass, $pluginman); // @codeCoverageIgnore
    }

    /**
     * Get the plugininfo instance of all plugins.
     *
     * @return self[]
     * @codeCoverageIgnore
     */
    public static function get_all_plugins(): array {
        return core_plugin_manager::instance()->get_plugins_of_type('virtualmeeting');
    }

    /**
     * Get the plugininfo instance of all available plugins.
     *
     * @return self[]
     */
    public static function get_available_plugins(): array {
        $plugins = self::get_all_plugins();
        foreach ($plugins as $pluginname => $plugin) {
            if (!$plugin->is_available()) {
                unset($plugins[$pluginname]);
            }
        }
        return $plugins;
    }

    /**
     * Load a plugininfo instance.
     *
     * @param string $pluginname
     * @return self
     */
    public static function load(string $pluginname): self {
        $plugins = self::get_all_plugins();
        if (!isset($plugins[$pluginname])) {
            throw new coding_exception('unknown plugin name: '.$pluginname);
        }
        return $plugins[$pluginname];
    }

    /**
     * Load only an available plugininfo instance.
     *
     * @param string $pluginname
     * @return self
     */
    public static function load_available(string $pluginname): self {
        $plugin = self::load($pluginname);
        if (!$plugin->is_available()) {
            throw new coding_exception('plugin not available: '.$pluginname);
        }
        return $plugin;
    }

    /**
     * Create the factory instance of the plugin.
     *
     * @return factory
     * @internal Do NOT call this method.
     */
    public function create_factory(): factory {
        $pluginname = $this->name;
        $plugins = self::get_all_plugins();
        if (!isset($plugins[$pluginname])) {
            throw new coding_exception("plugin not found: {$pluginname}");
        }
        $libpath = $this->full_path('lib.php');
        require_once($libpath);
        $class = $this->resolve_factory_class();
        return new $class();
    }

    /**
     * Get the name of the plugin
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function get_name(): string {
        return $this->displayname;
    }

    /**
     * Get the description of the plugin
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function get_description(): string {
        if ($this->description === null) {
            $this->description = get_string('plugindesc', $this->component);
        }
        return $this->description;
    }

    /**
     * Return whether the plugin is available or not.
     * Note that unavailable plugins are the ones that are not properly configured or disabled by a developer.
     *
     * @return boolean
     */
    public function is_available(): bool {
        $factory = $this->create_factory();
        return $factory->is_available();
    }

    /**
     * Return whether the plugin has the particulate characteristic or not.
     *
     * @param string $feature one of constants defined in the totara_core\virtualmeeting\plugin\feature class
     * @return boolean
     */
    public function get_feature(string $feature): bool {
        // In the future, the default value might depend on a feature.
        // Note that the rhyme is not intentional.
        $default = false;
        $factory = $this->create_factory();
        try {
            if ($factory instanceof feature_factory) {
                return $factory->get_feature($feature);
            }
        } catch (unsupported_exception $ex) {
            // Swallow exception.
        }
        return $default;
    }

    /**
     * @inheritDoc
     */
    public static function get_enabled_plugins() {
        // If PoC plugin is enabled, all you see is PoC plugins.
        if (self::is_poc_available()) {
            $plugins = array_keys(self::get_poc_plugins());
            return array_combine($plugins, $plugins);
        }
        // @codeCoverageIgnoreStart
        $plugins = core_plugin_manager::instance()->get_installed_plugins('virtualmeeting');
        $enabled = array();
        foreach ($plugins as $plugin => $version) {
            $enabled[$plugin] = $plugin;
        }
        return $enabled;
        // @codeCoverageIgnoreEnd
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function load_settings(part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {
        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        $fullpath = $this->full_path('lib.php');
        if (!$hassiteconfig || empty($fullpath) || !file_exists($fullpath)) {
            return;
        }

        try {
            $factory = $this->create_factory($this->name);
        } catch (coding_exception $ex) {
            // swallow exception
            debugging('Cannot load a virtual meeting plugin: '.$this->name);
            return;
        }

        $section = $this->get_settings_section_name();
        $page = $factory->create_setting_page($section, $this->displayname, $adminroot->fulltree, $this->is_enabled() === false);

        /** @var admin_root $adminroot */
        if ($page !== null) {
            $adminroot->add($parentnodename, $page);
        }
    }
}
