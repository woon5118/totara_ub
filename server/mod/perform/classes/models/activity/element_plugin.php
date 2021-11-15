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
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\activity;

use mod_perform\entity\activity\element as element_entity;
use mod_perform\models\response\section_element_response;

/**
 * Class element_plugin
 *
 * Base class for defining a type of element, including its specific behaviour.
 *
 * @package mod_perform\models\activity
 */
abstract class element_plugin {

    const GROUP_QUESTION = 1;
    const GROUP_OTHER = 2;

    /**
     * Element plugin constructor
     */
    private function __construct() {
    }

    /**
     * Load by plugin name
     *
     * @param string $plugin_name
     *
     * @return static
     */
    final public static function load_by_plugin(string $plugin_name): self {
        $plugin_class = "performelement_{$plugin_name}\\{$plugin_name}";
        if (!is_subclass_of($plugin_class, self::class)) {
            throw new \coding_exception('Tried to load an unknown element plugin: ' . $plugin_class);
        }
        return new $plugin_class();
    }

    /**
     * Get all element plugins. Optionally filter to only respondable or non-respondable elements.
     *
     * Returns array of elements, keyed by plugin_name, value is instance of element model.
     *
     * @param bool $get_respondable
     * @param bool $get_non_respondable
     * @return element_plugin[]
     * @throws \coding_exception
     */
    final public static function get_element_plugins(bool $get_respondable = true, bool $get_non_respondable = true): array {
        $elements = \core_component::get_plugin_list('performelement');

        $out = [];
        foreach ($elements as $plugin_name => $plugin_path) {
            /** @var element_plugin $element_plugin */
            $element_plugin = self::load_by_plugin($plugin_name);

            if ($get_respondable && $element_plugin->get_is_respondable()) {
                $out[$plugin_name] = $element_plugin;
            }
            if ($get_non_respondable && !$element_plugin->get_is_respondable()) {
                $out[$plugin_name] = $element_plugin;
            }
        }
        return $out;
    }

    /**
     * Get plugin name, used as a key
     *
     * @return string
     */
    final public function get_plugin_name(): string {
        return explode('\\', static::class)[1];
    }

    /**
     * Get name
     *
     * @return string
     */
    final public function get_name(): string {
        return get_string('name', 'performelement_' . $this->get_plugin_name());
    }

    /**
     * This method return element's admin form vue component name
     *
     * This function is going to be deprecated. Use element_plugin::get_admin_edit_component() instead
     *
     * @return string
     * @deprecated since Totara 13.2
     */
    public function get_admin_form_component(): string {
        debugging(
            '\mod_perform\models\activity\element_plugin::get_admin_form_component() is deprecated and should no longer be used.'
            . ' There is no alternative.',
            DEBUG_DEVELOPER
        );

        return $this->get_component_path('ElementAdminForm');
    }

    /**
     * This method return element's admin form vue component name
     *
     * @return string
     */
    public function get_admin_edit_component(): string {
        return $this->get_component_path('AdminEdit');
    }

    /**
     * This method return element's admin display vue component name
     *
     * This function is going to be deprecated. Use element_plugin::get_admin_view_component() instead
     *
     * @return string
     * @deprecated since Totara 13.2
     */
    public function get_admin_display_component(): string {
        debugging(
            '\mod_perform\models\activity\element_plugin::get_admin_display_component() is deprecated and should no longer be used.'
            . ' There is no alternative.',
            DEBUG_DEVELOPER
        );

        return $this->get_component_path('ElementAdminDisplay');
    }

    /**
     * This method return element's admin view vue component name
     *
     * @return string
     */
    public function get_admin_view_component(): string {
        return $this->get_component_path('AdminView');
    }

    /**
     * This method return element's admin read only display vue component name
     *
     * This function is going to be deprecated. Use element_plugin::get_admin_summary_component() instead
     *
     * @return string
     * @deprecated since Totara 13.2
     */
    public function get_admin_read_only_display_component(): string {
        debugging(
            '\mod_perform\models\activity\element_plugin::get_admin_read_only_display_component() is deprecated and should no longer be used.'
            . ' There is no alternative.',
            DEBUG_DEVELOPER
        );

        return $this->get_component_path('ElementAdminReadOnlyDisplay');
    }

    /**
     * This method return element's admin read only display vue component name
     *
     * @return string
     */
    public function get_admin_summary_component(): string {
        return $this->get_component_path('AdminSummary');
    }

    /**
     * This method return element's print vue component name
     *
     * @return string
     */
    public function get_participant_print_component(): string {
        return $this->get_component_path('ParticipantPrint');
    }

    /**
     * This method return element's user form vue component name
     * @return string
     */
    public function get_participant_form_component(): string {
        return $this->get_component_path('ParticipantForm');
    }

    /**
     * This method return element's user form vue component name
     * @return string
     * @deprecated since Totara 13.2
     */
    public function get_participant_response_component(): string {
        debugging(
            '\mod_perform\models\activity\element_plugin::get_participant_response_component() is deprecated and should no longer be used.'
            . 'Only classes expending \mod_perform\models\activity\respondable_element_plugin should implement this method',
            DEBUG_DEVELOPER
        );

        return $this->get_component_path('ElementParticipantResponse');
    }

    /**
     * Calculate the full path to a tui component related to this element plugin.
     *
     * @param string $suffix
     * @return string
     */
    protected function get_component_path(string $suffix): string {
        return 'performelement_' .
            $this->get_plugin_name() .
            '/components/' .
            $this->get_component_name_prefix() .
            $suffix;
    }

    /**
     * This method return element's default component name prefix
     *
     * @return string
     */
    protected function get_component_name_prefix(): string {
        $prefix = '';
        foreach (explode('_', self::get_plugin_name()) as $name) {
            $prefix .= ucfirst($name);
        }

        return $prefix;
    }

    /**
     * When an element is about to be saved in a section, validate that the configuration of the element
     * meets any requirements of the element plugin
     *
     * If a problem is discovered, throw an exception.
     *
     * @param element_entity $element
     */
    public function validate_element(element_entity $element) {
    }

    /**
     * Do any required actions after the element has been created.
     *
     * @param element $element
     */
    public function post_create(element $element): void {
        // Can be overridden if necessary.
    }

    /**
     * Do any required actions after the element configuration has been updated.
     *
     * @param element $element
     */
    public function post_update(element $element): void {
        // Can be overridden if necessary.
        $this->post_create($element);
    }

    /**
     * Can the user respond to this element.
     *
     * @return bool
     */
    public function get_is_respondable(): bool {
        return $this instanceof respondable_element_plugin;
    }

    /**
     * return true if element has title
     *
     * @return bool
     */
    abstract public function has_title(): bool;

    /**
     * Return Title Text
     *
     * @return string
     */
    abstract public function get_title_text(): string;

    /**
     * return true if element title is required
     *
     * @return bool
     */
    abstract public function is_title_required(): bool;

    /**
     * Return if element plugin is a Question element group or Other element group
     *
     * @return int
     */
    abstract public function get_group(): int;

    /**
     * Return position key to sort element plugin in the list
     *
     * @return int
     */
    abstract public function get_sortorder(): int;
}
