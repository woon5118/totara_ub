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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Petr Skoda <petr.skoda@totaralearning.com>
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_webapi
 */

namespace totara_webapi;

use core\webapi\execution_context;
use core\webapi\mutation_resolver;
use core\webapi\query_resolver;
use core_component;
use GraphQL\Executor\Executor;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * This represents our default resolver. It takes the request information
 * and tries to resolve the operation to the proper query, type or mutation resolver.
 *
 * All resolvers are stored in classes/webapi/resolver/* folders.
 *
 * @package totara_webapi
 */
class default_resolver {

    /**
     * This class can be used as a callable. The GraphQL library will call it when it tries
     * to resolve the operation given to it.
     *
     * @param $source
     * @param $args
     * @param execution_context $ec
     * @param ResolveInfo $info
     *
     * @return mixed|null
     */
    public function __invoke($source, $args, execution_context $ec, ResolveInfo $info) {
        // phpcs:disable Totara.NamingConventions.ValidVariableName.LowerCaseUnderscores
        $ec->set_resolve_info($info);

        $args = (array)$args;
        if ($info->parentType->name === 'Query' or $info->parentType->name === 'Mutation') {
            $otype = ($info->parentType->name === 'Query') ? 'query' : 'mutation';

            [$component, $name] = $this->split_type_name($info->fieldName);
            if (empty($component)) {
                throw new \coding_exception('GraphQL ' . $otype . ' name is invalid', $info->fieldName);
            }
            $classname = "{$component}\\webapi\\resolver\\{$otype}\\{$name}";
            if (!class_exists($classname)) {
                throw new \coding_exception('GraphQL ' . $otype . ' resolver class is missing', $info->fieldName);
            }
            /** @var query_resolver|mutation_resolver $classname */
            return $classname::resolve($args, $ec);
        }

        // Regular data type.
        $parts = explode('_', $info->parentType->name);
        if (!$this->is_introspection_type($info->parentType->name) && count($parts) > 1) {
            [$component, $name] = $this->split_type_name($info->parentType->name);
            if (empty($name)) {
                throw new \coding_exception('Type resolvers must be named as component_name, e.g. totara_job_job');
            }
            $classname = "{$component}\\webapi\\resolver\\type\\{$name}";
            if (class_exists($classname)) {
                /** @var \core\webapi\type_resolver $classname */
                return $classname::resolve($info->fieldName, $source, $args, $ec);
            }
        }

        return Executor::defaultFieldResolver($source, $args, $ec, $info);
        // phpcs:enable
    }

    /**
     * Check if the type name is one used in introspections, which usually starts with two underscores
     * @param string $name
     * @return bool
     */
    private function is_introspection_type(string $name): bool {
        return strpos($name, '__') === 0;
    }

    /**
     * Split type name, i.e. totara_competency_my_query_name into component (totara_competency) and the rest (my_query_name)
     *
     * @param string $name
     * @return array
     */
    private function split_type_name(string $name) {
        if (strpos($name, 'core_') === 0) {
            return ['core', substr($name, 5)];
        }

        // Build flat list out of all plugins and subplugins
        $components = [];
        $types = core_component::get_plugin_types();
        foreach ($types as $type => $typedir) {
            $plugins = core_component::get_plugin_list($type);
            foreach ($plugins as $plugin => $plugindir) {
                $plugin_component = $type.'_'.$plugin;
                $components[$plugin_component] = $plugin_component;
                $subplugins = core_component::get_subplugins($plugin_component);
                foreach ($subplugins ?? [] as $prefix => $subplugin_names) {
                    foreach ($subplugin_names as $subplugin) {
                        $subplugin_component = $prefix . '_' . $subplugin;
                        $components[$subplugin_component] = $subplugin_component;
                    }
                }
            }
        }

        // Now try to find component name by reducing the name one by one and checking existence in component list
        $parts = explode('_', $name);
        while (count($parts) > 0) {
            array_pop($parts);
            $component_search_name = implode('_', $parts);
            if (isset($components[$component_search_name])) {
                return [
                    $component_search_name,
                    substr($name, strlen($component_search_name) + 1)
                ];
            }
        }

        return [null, null];
    }

}