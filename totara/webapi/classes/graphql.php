<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @package totara_webapi
 */

namespace totara_webapi;

use core\webapi\execution_context;
use GraphQL\Language\Parser;
use GraphQL\Type\Schema;
use GraphQL\Utils\AST;
use GraphQL\Utils\BuildSchema;
use GraphQL\Utils\SchemaExtender;
use GraphQL\Utils\SchemaPrinter;

/**
 * Main GraphQL API intended for plugins such as External API or mobile support.
 */
final class graphql {

    /**
     * Returns content of core schema file
     *
     * @return string
     */
    public static function get_core_schema_file_content(): string {
        global $CFG;

        // NOTE: Subsystems schemas are not supported intentionally,
        //       the reason is that Frankenstyle in subsystems would
        //       make the schema structure illogical.

        // The core schema file comes first
        $root_schema_file = $CFG->dirroot . '/lib/webapi/schema.graphqls';
        if (!file_exists($root_schema_file) || !is_readable($root_schema_file)) {
            throw new \coding_exception('Core has to have at least a schema.graphqls file');
        }
        return self::get_schema_file_content($root_schema_file);
    }

    /**
     * Returns all schema file contents
     *
     * @return string[]
     */
    public static function get_schema_file_contents(): array {
        global $CFG;

        $schemas = [];

        // Add any additional files from core
        $filenames = self::get_graphqls_files($CFG->dirroot . '/lib/webapi');
        foreach ($filenames as $filename) {
            // Core file is skipped as it is read separately
            if (preg_match("/\\/schema\\.graphqls$/", $filename)) {
                continue;
            }
            $schemas[] = self::get_schema_file_content($filename);
        }

        // Then read all plugin schema files, here the order or names do not matter
        // as they will all be merged together and then extend the main schema
        $types = \core_component::get_plugin_types();
        foreach ($types as $type => $typedir) {
            $plugins = \core_component::get_plugin_list($type);
            foreach ($plugins as $plugin => $plugindir) {
                $filenames = self::get_graphqls_files("$plugindir/webapi/");
                foreach ($filenames as $filename) {
                    $schemas[] = self::get_schema_file_content($filename);
                }
            }
        }

        if ($CFG->debugdeveloper) {
            foreach (\core_component::get_core_subsystems() as $subsystem => $dir) {
                if (!$dir) {
                    continue;
                }
                $filenames = self::get_graphqls_files("$dir/webapi");
                if (!empty($filenames)) {
                    debugging('.graphqls files are not allowed in core subsystems, use lib/webapi/schema.graphqls instead');
                }
            }
        }

        return $schemas;
    }

    /**
     * Read contents of given schema file
     *
     * @param string $filename
     * @return string
     * @throws \Exception
     */
    private static function get_schema_file_content(string $filename): string {
        $content = file_get_contents($filename);
        if ($content === false) {
            throw new \Exception('Could not read schema file '.$filename);
        }
        return $content;
    }

    /**
     * Get all .graphqls files in given folder
     *
     * @param string $dir
     * @return array
     */
    public static function get_graphqls_files(string $dir): array {
        return self::get_files_from_dir($dir, 'graphqls');
    }

    /**
     * Get all files with given extension from directory
     *
     * @param string $dir
     * @param string $extension
     * @return array
     */
    protected static function get_files_from_dir(string $dir, string $extension): array {
        if (!file_exists($dir) || !is_readable($dir) || !is_dir($dir)) {
            return [];
        }

        // We could use glob() but using open/readdir is more performant
        $files = [];
        if ($handle = opendir($dir)) {
            while (false !== ($file_name = readdir($handle))) {
                if (preg_match("/\\.{$extension}$/", $file_name)) {
                    $name = basename($file_name, ".{$extension}");
                    if ($name !== clean_param($name, PARAM_SAFEDIR)) {
                        continue;
                    }
                    $files[$name] = "{$dir}/{$file_name}";
                }
            }

            closedir($handle);
        }

        return $files;
    }

    /**
     * Returns the schema instance
     *
     * @return \GraphQL\Type\Schema
     */
    public static function get_schema() {
        $schema = self::get_parsed_schema();

        // Hack custom scalars to use our parsing and serialisation.
        $scalars = \core_component::get_namespace_classes('webapi\scalar', 'core\webapi\scalar');
        foreach ($scalars as $classname) {
            $parts = explode('\\', $classname);
            $component = reset($parts);
            $name = end($parts);
            $scalarname = $component . '_' . $name;
            $type = $schema->getType($scalarname);
            if (!$type) {
                debugging("GraphQL scalar '$scalarname' described by class {$classname} is not defined in GraphQL schema", DEBUG_DEVELOPER);
                continue;
            }
            if (!($type instanceof \GraphQL\Type\Definition\CustomScalarType)) {
                debugging("GraphQL scalar '$scalarname' described by class {$classname} collides with another type in GraphQL schema", DEBUG_DEVELOPER);
                continue;
            }
            $type->config['serialize'] = [$classname, 'serialize'];
            $type->config['parseValue'] = [$classname, 'parse_value'];
            $type->config['parseLiteral'] = [$classname, 'parse_literal'];
        }

        // Add support for PARAM_ equivalents - note those are intended to be used for input cleaning only!
        $params = \core_component::get_namespace_classes('webapi\param', 'core\webapi\param', 'core');
        foreach ($params as $classname) {
            $parts = explode('\\', $classname);
            $name = end($parts);
            $paramname = 'param_' . $name;
            $type = $schema->getType($paramname);
            if (!$type) {
                debugging("GraphQL param '$paramname' described by class {$classname} is not defined in GraphQL schema", DEBUG_DEVELOPER);
                continue;
            }
            if (!($type instanceof \GraphQL\Type\Definition\CustomScalarType)) {
                debugging("GraphQL param '$paramname' described by class {$classname} collides with another type in GraphQL schema", DEBUG_DEVELOPER);
                continue;
            }
            $type->config['serialize'] = [$classname, 'serialize'];
            $type->config['parseValue'] = [$classname, 'parse_value'];
            $type->config['parseLiteral'] = [$classname, 'parse_literal'];
        }

        return $schema;
    }

    protected static function get_parsed_schema(): Schema {
        global $CFG;
        // In debug mode we skip the caching
        if ($CFG->debugdeveloper) {
            return self::build_schema();
        }

        $cache = \cache::make('totara_webapi', 'schema');
        $parsed_schema = $cache->get('parsed_schema');
        if ($parsed_schema) {
            $parsed_schema = AST::fromArray($parsed_schema);
            return BuildSchema::build($parsed_schema);
        }

        $schema = self::build_schema();

        $parsed_schema = Parser::parse(SchemaPrinter::doPrint($schema));
        $cache->set('parsed_schema', AST::toArray($parsed_schema));

        return $schema;
    }

    /**
     * @return \GraphQL\Type\Schema
     */
    protected static function build_schema(): Schema {
        $schema = self::get_core_schema_file_content();
        if (empty($schema)) {
            throw new \Exception('Core schema file content cannot be empty.');
        }

        // We need to build with the core schema file first as
        // all other schema files will be treated as extension
        $schema = \GraphQL\Utils\BuildSchema::build($schema);

        $schemas = self::get_schema_file_contents();
        if (empty($schemas)) {
            throw new \Exception('Schema file contents cannot be empty.');
        }

        return SchemaExtender::extend($schema, Parser::parse(implode("\n", $schemas)));
    }

    /**
     * Returns list of valid persisted operations and their file locations.
     *
     * @param string $type API type such as 'ajax', 'external' or 'mobile'
     * @return array operation name is key, value is the full file path to persisted operation
     */
    public static function get_persisted_operations(string $type) {
        global $CFG;

        if ($type !== clean_param($type, PARAM_ALPHA)) {
            throw new \coding_exception('Invalid operation type');
        }

        if ($CFG->debugdeveloper) {
            return self::build_persisted_operations_array($type);
        }

        $cache = \cache::make('totara_webapi', 'persistedoperations');
        $operations = $cache->get($type);
        if (!$operations) {
            $operations = self::build_persisted_operations_array($type);
            $cache->set($type, $operations);
        }

        return $operations;
    }

    /**
     * Build array containing all persisted operations for the given type
     * @param string $type
     * @return array
     */
    protected static function build_persisted_operations_array(string $type): array {
        global $CFG;

        $operations = [];

        $files = self::get_files_from_dir($CFG->libdir . '/webapi/' . $type, 'graphql');
        foreach ($files as $name => $file) {
            $operation_name = 'core_' . $name;
            $operations[$operation_name] = $file;
        }

        foreach (\core_component::get_core_subsystems() as $subsystem => $full_dir) {
            $files = self::get_files_from_dir($full_dir . '/webapi/' . $type, 'graphql');
            foreach ($files as $name => $file) {
                $operation_name = 'core_' . $subsystem . '_' . $name;
                $operations[$operation_name] = $file;
            }
        }

        $plugin_types = \core_component::get_plugin_types();
        foreach ($plugin_types as $plugin_type => $unused) {
            $plugins = \core_component::get_plugin_list($plugin_type);
            foreach ($plugins as $plugin => $full_dir) {
                $files = self::get_files_from_dir($full_dir . '/webapi/' . $type, 'graphql');
                foreach ($files as $name => $file) {
                    $operation_name = $plugin_type . '_' . $plugin . '_' . $name;
                    $operations[$operation_name] = $file;
                }
            }
        }

        return $operations;
    }

    /**
     * Returns list of required capabilities in system context for each operation.
     *
     * NOTE: this is not enforced automatically,
     *       it is used for documentation and to create default roles for External API.
     *
     * @param string $type API type such as 'ajax', 'external' or 'mobile'
     * @return array where keys are operation names and values lists of capability names
     */
    public static function get_role_capabilities(string $type) {
        $result = [];

        $alloperations = graphql::get_persisted_operations($type);
        foreach ($alloperations as $operationname => $file) {
            $result[$operationname] = [];
            $content = file_get_contents($alloperations[$operationname]);
            if (!$content) {
                continue;
            }
            if (!preg_match('/# role capabilities:(.*)/', $content, $matches)) {
                if ($type === 'external') {
                    debugging("External persisted operation {$operationname} does not include '# role capabilities:' comment", DEBUG_DEVELOPER);
                }
                continue;
            }

            $capabilities = $matches[1];
            $capabilities = explode(',', $capabilities);
            $capabilities = array_map('trim', $capabilities);
            foreach ($capabilities as $capability) {
                if (!get_capability_info($capability)) {
                    debugging("Persisted operation {$operationname} includes invalid '# role capabilities:' comment", DEBUG_DEVELOPER);
                    continue;
                }
                $result[$operationname][] = $capability;
            }
        }

        return $result;
    }


    /**
     * Default field resolver - do not call directly.
     *
     * @param mixed $source the data coming from parent
     * @param array $args optional arguments specified in query or mutation
     * @param execution_context $ec graphql execution context array
     * @param \GraphQL\Type\Definition\ResolveInfo $info
     * @return mixed|null
     */
    public static function default_resolver($source, $args, execution_context $ec, \GraphQL\Type\Definition\ResolveInfo $info) {
        $ec->set_resolve_info($info);
        $args = (array)$args;
        if ($info->parentType->name === 'Query' or $info->parentType->name === 'Mutation') {
            $otype = ($info->parentType->name === 'Query') ? 'query' : 'mutation';

            [$component, $name] = self::split_type_name($info->fieldName);
            if (empty($component)) {
                throw new \coding_exception('GraphQL ' . $otype . ' name is invalid', $info->fieldName);
            }
            $classname = "{$component}\\webapi\\resolver\\{$otype}\\{$name}";
            if (!class_exists($classname)) {
                throw new \coding_exception('GraphQL ' . $otype . ' resolver class is missing', $info->fieldName);
            }
            /** @var \core\webapi\query_resolver|\core\webapi\mutation_resolver $classname */
            return $classname::resolve($args, $ec);
        }

        // Regular data type.
        $parts = explode('_', $info->parentType->name);
        if (!self::is_introspection_type($info->parentType->name) && count($parts) > 1) {
            [$component, $name] = self::split_type_name($info->parentType->name);
            if (empty($name)) {
                throw new \coding_exception('Type resolvers must be named as component_name, e.g. totara_job_job');
            }
            $classname = "{$component}\\webapi\\resolver\\type\\{$name}";
            if (class_exists($classname)) {
                /** @var \core\webapi\type_resolver $classname */
                return $classname::resolve($info->fieldName, $source, $args, $ec);
            }
        }

        return \GraphQL\Executor\Executor::defaultFieldResolver($source, $args, $ec, $info);
    }

    /**
     * Check if the type name is one used in introspections, which usually starts with two underscores
     * @param string $name
     * @return bool
     */
    private static function is_introspection_type(string $name): bool {
        return strpos($name, '__') === 0;
    }

    /**
     * Split type name, i.e. totara_competency_my_query_name into component (totara_competency) and the rest (my_query_name)
     *
     * @param string $name
     * @return array
     */
    private static function split_type_name(string $name) {
        if (strpos($name, 'core_') === 0) {
            return ['core', substr($name, 5)];
        }

        // Build flat list out of all plugins and subplugins
        $components = [];
        $types = \core_component::get_plugin_types();
        foreach ($types as $type => $typedir) {
            $plugins = \core_component::get_plugin_list($type);
            foreach ($plugins as $plugin => $plugindir) {
                $plugin_component = $type.'_'.$plugin;
                $components[$plugin_component] = $plugin_component;
                $subplugins = \core_component::get_subplugins($plugin_component);
                foreach ($subplugins ?? [] as $prefix => $subpluginnames) {
                    foreach ($subpluginnames as $subplugin) {
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

    /**
     * Get root for operation execution.
     *
     * @return array
     */
    public static function get_server_root(\GraphQL\Type\Schema $schema) {
        return [];
    }

    /**
     * Execute persisted GraphQL query or mutation.
     *
     * @param execution_context $ec
     * @param array $variables
     * @return \GraphQL\Executor\ExecutionResult
     */
    public static function execute_operation(execution_context $ec, array $variables) {
        $schema = self::get_schema();
        $schema->assertValid();

        $type = $ec->get_type();
        $operationname = $ec->get_operationname();

        $alloperations = self::get_persisted_operations($type);
        if (!isset($alloperations[$operationname])) {
            throw new \coding_exception('Invalid Web API operation name');
        }
        $operationstring = file_get_contents($alloperations[$operationname]);
        if (!$operationstring) {
            throw new \coding_exception('Invalid Web API operation file');
        }

        $result = \GraphQL\GraphQL::executeQuery(
            $schema,
            $operationstring,
            self::get_server_root($schema),
            $ec,
            $variables,
            $operationname,
            [self::class, 'default_resolver'],
            null
        );

        return $result;
    }
}