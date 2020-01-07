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
 * @author Chris Snyder <chris.snyder@totaralearning.com>
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_facetoface
 */

defined('MOODLE_INTERNAL') || die();

// NOTE: Declare one by one instead of bulky `use \mod_facetoface\{seminar, signup}` to possibly avoid merge conflict

// Model classes
use mod_facetoface\room;
use mod_facetoface\seminar;
use mod_facetoface\seminar_event;
use mod_facetoface\seminar_session;
use mod_facetoface\signup;
use mod_facetoface\signup_status;
use mod_facetoface\session_status;

// State classes
use mod_facetoface\signup\state\state;
use mod_facetoface\signup\condition\condition;
use mod_facetoface\signup\restriction\restriction;
use mod_facetoface\signup\transition;

// Other classes
use mod_facetoface\event_dates;
use mod_facetoface\attendance\event_attendee;
use mod_facetoface\attendance\attendance_helper;

use mod_facetoface\dashboard\filter_list;
use mod_facetoface\dashboard\render_session_option;
use mod_facetoface\dashboard\filters\filter as dashboard_filter;

use mod_facetoface\query\query_interface;
use mod_facetoface\query\statement;
use mod_facetoface\query\event\filter\filter as query_filter;
use mod_facetoface\query\event\sortorder\sortorder as query_sortorder;
use mod_facetoface\traits\crud_mapper;

// Renderer class - mod_facetoface_renderer
require_once(__DIR__ . '/../renderer.php');

/**
 * Class mod_facetoface_code_quality_testcase
 */
class mod_facetoface_code_quality_testcase extends advanced_testcase {

    /**
     * Set true to disable code inspection.
     * @var boolean
     */
    private $disabled_code_inspector = false;

    /**
     * @var string[]
     */
    private $tested_classes = [
        // self test
        mod_facetoface_code_quality_testcase::class,

        room::class,
        seminar::class,
        seminar_event::class,
        seminar_session::class,
        signup::class,
        signup_status::class,
        session_status::class,

        transition::class,
        event_attendee::class,
        attendance_helper::class,

        event_dates::class,
        filter_list::class,
        render_session_option::class,
        statement::class,
        crud_mapper::class,

        mod_facetoface_renderer::class,
    ];

    /** @var string[] */
    private $whitelist_crlf = [
        'pix',
        'tests/fixtures',
    ];

    /**
     * Get the fully qualified class names for testing docblocks and type hints.
     *
     * @return string[]
     */
    private function get_classes_to_test(): array {
        $tested_classes = $this->tested_classes;
        // Load all state classes
        self::add_inherited_classes($tested_classes, 'signup\state', state::class, 'classes/signup/state');
        // Load all condition classes
        self::add_inherited_classes($tested_classes, 'signup\condition', condition::class, 'classes/signup/condition');
        // Load all restriction classes
        self::add_inherited_classes($tested_classes, 'signup\restriction', restriction::class, 'classes/signup/restriction');
        // Load all dashboard filter classes
        self::add_inherited_classes($tested_classes, 'dashboard\filters', dashboard_filter::class, 'classes/dashboard/filters');
        // Load all query classes
        self::add_inherited_classes($tested_classes, 'query', query_interface::class, 'classes/query');
        // Load all query filter classes
        self::add_inherited_classes($tested_classes, 'query\event\filter', query_filter::class, 'classes/query/event/filter');
        // Load all query sortorder classes
        self::add_inherited_classes($tested_classes, 'query\event\sortorder', query_sortorder::class, 'classes/query/event/sortorder');
        // Load all template classes
        self::add_inherited_classes($tested_classes, 'output', null, 'classes/output');
        // Load all template builder classes
        self::add_inherited_classes($tested_classes, 'output\builder', null, 'classes/output/builder');
        // Load all xxx_helper and xxx_list classes
        self::add_matching_classes($tested_classes, '/^mod_facetoface\\\\[^\\\\]+(_helper|_list)$/', 'classes');
        return $tested_classes;
    }

    /**
     * Add inherited classes to the class list array.
     *
     * @param string[]      $tested_classes The array of classes to append the list of found classes
     * @param string        $namespace      The plugin namespace without 'mod_facetoface\'
     * @param string|null   $baseclass      The base class name without a namespace
     * @param string        $relpath        The relative path to /mod/facetoface/
     * @return void
     */
    private static function add_inherited_classes(array &$tested_classes, string $namespace, ?string $baseclass, string $relpath): void {
        $classes = \core_component::get_namespace_classes($namespace, $baseclass, 'mod_facetoface');
        $tested_classes = array_unique(array_merge($tested_classes, $classes));
        if ($baseclass !== null && !in_array($baseclass, $tested_classes)) {
            $tested_classes[] = $baseclass;
        }
    }

    /**
     * Add classes whose name matches the specified pattern to the class list array.
     *
     * @param string[]  $tested_classes The array of classes to append the list of found classes
     * @param string    $pattern        Regular expression pattern
     * @param string    $relpath        The relative path to /mod/facetoface/
     * @return void
     */
    private static function add_matching_classes(array &$tested_classes, string $pattern, string $relpath): void {
        $classes = array_keys(\core_component::get_component_classes_in_namespace('mod_facetoface'));
        $classes = array_filter($classes, function ($name) use ($pattern) {
            return (bool)preg_match($pattern, $name);
        });
        $tested_classes = array_unique(array_merge($tested_classes, $classes));
    }

    /**
     * Ensure a docblock is present on the class.
     *
     * @param string $classname The name of the class to inspect
     * @return array list of errors found
     */
    public function inspect_class_docblock(string $classname): array {
        $errors = array();
        try {
            $class = new ReflectionClass($classname);
            // check class docblock - must be present
            $docblock = $class->getDocComment();
            if (($error = self::check_common_docblock_problems($docblock, 'class', ['* Undocumented class'])) != null) {
                $errors[] = $error;
            }
            // TODO: should have a package hint
            /*
            if (strpos($docblock, '* @package') === false) {
               $errors[] = 'Class docblock missing @package declaration';
            }
            if (strpos($docblock, '* @subpackage') === false) {
               $errors[] = 'Class docblock missing @subpackage declaration';
            }
            */
        } catch (\ReflectionException $e) {
            $errors[] = $e->getMessage();
        }

        self::insert_summary_if_any_error($errors, $classname, 'class docblock');
        return $errors;
    }

    /**
     * Ensure docblocks are present on all class properties.
     *
     * @param string $classname The name of the class to inspect
     * @return array list of errors found
     */
    public function inspect_property_docblocks(string $classname): array {
        $errors = array();
        try {
            $class = new ReflectionClass($classname);

            foreach ($class->getProperties() as $property) {
                $docblock = $property->getDocComment();

                if (($error = self::check_common_docblock_problems($docblock, '$'.$property->getName(), ['* Undocumented variable'])) != null) {
                    $errors[] = $error;
                } else if (preg_match('/\*\s+@var/', $docblock) == false) {
                    $errors[] = "Missing @var declaration for \${$property->getName()}";
                } else {
                    // Invalid pattern to match @param declarations as below:
                    /**
                     * @var [type]
                     */
                    $pattern = '/@var\s+\[type\]/';
                    if (preg_match($pattern, $docblock)) {
                        $errors[] = "Invalid @var type for \${$property->getName()}";
                    }
                }
            }
        } catch (\ReflectionException $e) {
            $errors[] = $e->getMessage();
        }

        self::insert_summary_if_any_error($errors, $classname, 'property docblocks');
        return $errors;
    }

    /**
     * Ensure docblocks are present on all class methods.
     *
     * You MUST include a description even if it appears to be obvious from the @param and/or @return lines.
     * An exception is made for overridden methods which make no change to the meaning of the parent method and maintain the same
     * arguments/return values. In this case you should omit the comment completely.
     * Another exception is made for methods whose return type is void, in which case @return void may be omitted.
     *
     * @param string $classname The name of the class to inspect
     * @return array list of errors found
     */
    public function inspect_method_docblocks(string $classname): array {
        $to_names = function ($e) {
            return $e->getName();
        };
        $errors = array();

        try {
            $class = new ReflectionClass($classname);
            $class_file = $class->getFileName();

            foreach ($class->getMethods() as $method) {
                if ($method->getFileName() != $class_file) {
                    // Method is defined elsewhere and used here as a trait or implementation.
                    continue;
                }
                $docblock = $method->getDocComment();
                $parameters = $method->getParameters();

                if (empty($docblock)) {
                    // does this method override a method in the parent class with same arguments/return values?
                    try {
                        $prototype = $method->getPrototype();
                        $parameter_names = array_map($to_names, $parameters);
                        $prototype_parameter_names = array_map($to_names, $prototype->getParameters());
                        if ($prototype_parameter_names != $parameter_names) {
                            $errors[] = "No method docblock for {$method->class}::{$method->getName()}(), which extends {$prototype->getDeclaringClass()->getName()}::{$prototype->getName()}() but has different parameters";
                        }
                    } catch (\ReflectionException $e) {
                        // There is a ReflectionException thrown if the method does not have a prototype.
                        $errors[] = "No method docblock for {$method->class}::{$method->getName()}()";
                    }
                } else {
                    if (($error = self::check_common_docblock_problems($docblock, "{$method->class}::{$method->getName()}()", ['* Undocumented function'])) != null) {
                        $errors[] = $error;
                        // If the docblock seems empty, then stop further checking.
                        if (strpos($error, 'Empty') !== false) {
                            continue;
                        }
                    }
                    $inherited_doc = self::is_inherited_doc($docblock);
                    foreach ($parameters as $parameter) {
                        // Pattern to match invalid @param declarations as below:
                        /**
                         * @param [type] $foo docblock template inserted by docblock extension
                         */
                        $pattern = '/@param\s+\[type\]\s+(\.\.\.|)\$' . preg_quote($parameter->getName()) . '\W/';
                        if (preg_match($pattern, $docblock)) {
                            $errors[] = "Method {$method->class}::{$method->getName()}() docblock invalid @param type for \${$parameter->getName()}";
                            continue;
                        }
                        // Pattern to match invalid @param declarations as below:
                        /**
                         * @param $foo docblock template inserted by docblock extension
                         */
                        $pattern = '/\*\s+@param\s+(\.\.\.|)\$' . preg_quote($parameter->getName()) . '\W/';
                        if (preg_match($pattern, $docblock)) {
                            $errors[] = "Method {$method->class}::{$method->getName()}() docblock missing @param type for \${$parameter->getName()}";
                            continue;
                        }
                        // Pattern to match @param declarations as below:
                        /**
                         * @param \mod_facetoface\seminar $foo is a seminar
                         * @param string ...$foo
                         * @param int|null $bar_dog is $foo's magic number
                         * @param \stdClass[] $users array of user records
                         * @param bool $foo $bar should be null if this is true
                         * @param int  $bar we like to line things using spaces too
                         */
                        $param_type = self::extract_param_type($docblock, $parameter);
                        if ($param_type === false) {
                            // If the docblock has @inheritDoc, then @param is optional.
                            if ($inherited_doc == false) {
                                $errors[] = "Method {$method->class}::{$method->getName()}() docblock missing @param declaration for \${$parameter->getName()}";
                            }
                        } else if ($parameter->hasType()) {
                            $error = self::check_mismatched_typehint($param_type, self::get_type_string($parameter->getType()));
                            if (is_string($error)) {
                                $errors[] = "Method {$method->class}::{$method->getName()}() docblock has incorrect @param type for \${$parameter->getName()}: '{$error}' expected";
                            } else if ($error === false) {
                                $errors[] = "Method {$method->class}::{$method->getName()}() docblock has incorrect @param type for \${$parameter->getName()}";
                            }
                        }
                    }
                    // Ensure @return matches the return type
                    $return_type = self::extract_return_type($docblock);
                    if ($return_type === false) {
                        if ($inherited_doc == false && self::is_magic_method($method) == false) {
                            if ($method->hasReturnType() && self::get_type_string($method->getReturnType()) === 'void') {
                                // CITE: the @return tag MAY be omitted here, in which case @return void is implied.
                                // https://docs.phpdoc.org/references/phpdoc/tags/return.html
                                // Because looking for all `return` statements in a method is too much work, here is our rule:
                                // If a method has a 'void' return type hint, '@return void' may be omitted.
                            } else {
                                $errors[] = "Method {$method->class}::{$method->getName()}() docblock missing @return declaration";
                            }
                            // Already handled; No need of guess game.
                            continue;
                        }
                    } else if ($return_type === 'this') {
                        // Special error message for '@return this'
                        $errors[] = "Method {$method->class}::{$method->getName()}() docblock has incorrect '@return this': 'self' expected";
                        // Already handled; No need of guess game.
                        continue;
                    }
                    if ($inherited_doc) {
                        continue;
                    }
                    // Try to catch frequent mistakes of @return types.
                    // For example, the return type hint is signup while the @return type is bool.
                    if ($method->hasReturnType()) {
                        $error = self::check_mismatched_typehint($return_type, self::get_type_string($method->getReturnType()));
                        if (is_string($error)) {
                            $errors[] = "Method {$method->class}::{$method->getName()}() docblock has incorrect @return type: '{$error}' expected";
                        } else if ($error === false) {
                            $errors[] = "Method {$method->class}::{$method->getName()}() docblock has incorrect @return type";
                        }
                    }
                }
            }
        } catch (\ReflectionException $e) {
            $errors[] = $e->getMessage();
        }

        self::insert_summary_if_any_error($errors, $classname, 'method docblocks');
        return $errors;
    }

    /**
     * Ensure class methods parameters are type-hinted.
     *
     * @param string $classname The name of the class to inspect
     * @return array list of errors found
     */
    public function inspect_method_parameter_hints(string $classname): array {
        $errors = array();
        try {
            $class = new ReflectionClass($classname);
            $class_file = $class->getFileName();

            foreach ($class->getMethods() as $method) {
                if ($method->getFileName() != $class_file) {
                    // Method is defined elsewhere and used here as a trait or implementation.
                    continue;
                }

                $docblock = $method->getDocComment();
                $parameters = $method->getParameters();
                foreach ($parameters as $parameter) {
                    if ($parameter->hasType() === false) {
                        // Exception: mixed or null parameters cannot supply a type hint.
                        $type = self::extract_param_type($docblock, $parameter);
                        if ($type !== false && !self::is_typehint_possible($type)) {
                            continue;
                        }
                        // Exception: Cannot supply a type hint if a parent class doesn't have one.
                        if (self::can_supply_parameter_typehint($method, $parameter) === false) {
                            continue;
                        }
                        $errors[] = "Method {$method->class}::{$method->getName()}() parameter \${$parameter->getName()} is missing a type hint";
                    }
                }
            }
        } catch (\ReflectionException $e) {
            $errors[] = $e->getMessage();
        }

        self::insert_summary_if_any_error($errors, $classname, 'method parameter hints');
        return $errors;
    }

    /**
     * Ensure class methods have explicit return types.
     *
     * @param string $classname The name of the class to inspect
     * @return array list of errors found
     */
    public function inspect_method_return_hints(string $classname): array {
        $errors = array();
        try {
            $class = new ReflectionClass($classname);
            $class_file = $class->getFileName();

            foreach ($class->getMethods() as $method) {
                $docblock = $method->getDocComment();
                if ($method->getFileName() != $class_file) {
                    // Method is defined elsewhere and used here as a trait or implementation.
                    continue;
                }

                // __construct, __get, __set, et al are magic methods and rarely have a specific return type.
                if (self::is_magic_method($method)) {
                    continue;
                }

                if ($method->hasReturnType() == false) {
                    $type = self::extract_return_type($docblock);
                    // Exception: mixed or null returns cannot supply a return type hint.
                    if ($type !== false && !self::is_typehint_possible($type)) {
                        continue;
                    }
                    // Exception: Cannot supply a type hint if a parent class doesn't have one.
                    if (self::can_supply_return_typehint($method) === false) {
                        continue;
                    }
                    $errors[] = "Method {$method->class}::{$method->getName()}() is missing a return type hint";
                }
            }
        } catch (\ReflectionException $e) {
            $errors[] = $e->getMessage();
        }

        self::insert_summary_if_any_error($errors, $classname, 'method return hints');
        return $errors;
    }

    /**
     * Summarise the number of errors and insert it to the top of the array of errors.
     *
     * @param array     $errors
     * @param string    $classname
     * @param string    $problem
     */
    private static function insert_summary_if_any_error(array &$errors, string $classname, string $problem): void {
        if (!empty($errors)) {
            array_unshift($errors, sprintf("[[ %d problem(s) with %s %s ]]", count($errors), $problem, $classname));
        }
    }

    /**
     * Check the common problems of docblock.
     *
     * @param string    $docblock The docblock to test.
     * @param string    $what The docblock for what?
     * @param array     $undocs The array of the fragments of undocument docblock
     * @return string|null
     */
    private static function check_common_docblock_problems(string $docblock, string $what, array $undocs) {
        if (empty($docblock)) {
            return "Empty docblock for $what";
        }
        foreach ($undocs as $undoc) {
            if (strpos($docblock, $undoc) !== false) {
                return "Undocumented docblock for $what";
            }
        }
        // Report empty docblocks as below:
        /**
         *
         */
        /** *** **/
        if (preg_match('/^\/\*[\*\s]*\*\/$/', $docblock)) {
            return "Empty docblock for $what";
        }
        return null;
    }

    /**
     * Check the param type and the type hint are compatible.
     *
     * @param string $paramtype     The type string in the PHPDoc block.
     * @param string $typehint      The type hint.
     * @return string|bool          - true if no errors
     *                              - false if types are not compatible
     *                              - string if types are not compatible and guess the correct one
     */
    private static function check_mismatched_typehint(string $paramtype, string $typehint) {
        $typedef = self::translate_known_return_type($paramtype, true);
        $typehint = self::translate_known_return_type($typehint, false);
        if ($typedef === false || $typehint === false) {
            // true for known invalid return types.
            $error = true;
        } else if ($typedef === null || $typehint === null) {
            // Give up because mixed types are too complicated.
            $error = false;
        } else {
            // Both are known return types and comparable as string.
            $error = $typedef !== $typehint;
        }
        if ($error) {
            if (is_string($typehint) && substr($typehint, 0, 1) !== '$') {
                // Give us some clue if possible.
                return $typehint;
            } else {
                // Nah, got to find it out by yourself.
                return false;
            }
        }
        // No problem found.
        return true;
    }

    /**
     * Extract the param type declaration from a docblock.
     *
     * @param string $docblock                  The PHPDoc block
     * @param ReflectionParameter $parameter    The name of the param.
     * @return string|false                     The type of the param, or false if not found.
     */
    private static function extract_param_type(string $docblock, \ReflectionParameter $parameter) {
        $pattern = '/\*\s+@param\s+([^\s]+)\s+(\.\.\.|)\$' . preg_quote($parameter->getName()) . '\W/';
        if (preg_match($pattern, $docblock, $matches) === 1) {
            return $matches[1];
        }
        return false;
    }

    /**
     * Extract the return type declaration from a docblock.
     *
     * @param string $docblock      The PHPDoc block
     * @return string|false         The return type, or false if not found.
     */
    private static function extract_return_type(string $docblock) {
        $pattern = '/\*\s+@return\s+([^\s]+)/';
        if (preg_match($pattern, $docblock, $matches) === 1) {
            return $matches[1];
        }
        return false;
    }

    /**
     * Check whether the docblock has `inheritedDoc` or not.
     *
     * @param string $docblock      The PHPDoc block
     * @return boolean
     */
    private static function is_inherited_doc(string $docblock): bool {
        $pattern = '/(\*\s+@inheritDoc|\{@inheritedDoc\})/';
        return preg_match($pattern, $docblock) != false;
    }

    /**
     * Check whether the method is a magic method or not.
     *
     * @param ReflectionMethod $method          The current method
     * @return boolean
     */
    private static function is_magic_method(\ReflectionMethod $method): bool {
        // TODO: check more explicitly
        return substr($method->getName(), 0, 2) === '__';
    }

    /**
     * Check whether the overridden method has a parameter type hint.
     *
     * @param ReflectionMethod $method          The current method
     * @param ReflectionParameter $parameter    The parameter of $method
     * @return boolean
     */
    private static function can_supply_parameter_typehint(\ReflectionMethod $method, \ReflectionParameter $parameter): bool {
        try {
            $prototype = $method->getPrototype();
            foreach ($prototype->getParameters() as $protoparam) {
                if ($protoparam->getName() === $parameter->getName()) {
                    return $protoparam->hasType();
                }
            }
        } catch (\ReflectionException $e) {
            // Nothing to do.
        }
        // No prototype means yes.
        return true;
    }

    /**
     * Check whether the overridden method has a return type hint.
     *
     * @param ReflectionMethod $method          The current method
     * @return boolean
     */
    private static function can_supply_return_typehint(\ReflectionMethod $method): bool {
        try {
            $prototype = $method->getPrototype();
            return $prototype->hasReturnType();
        } catch (\ReflectionException $e) {
            // Nothing to do.
        }
        // No prototype means yes.
        return true;
    }

    /**
     * Check whether the type can have a type hint.
     *
     * @param string $type          The return type string
     * @return boolean
     */
    private static function is_typehint_possible(string $type): bool {
        return $type !== 'mixed' && $type !== 'null' && $type !== 'object' && strpos($type, '|') === false;
        // Replace the above line with below only if we need to support >= PHP 7.2
        //return $type !== 'mixed' && $type !== 'null' && strpos($type, '|') === false;
    }

    /**
     * Convert a return type into the corresponding known return type.
     *
     * @param string $type          A return type string
     * @param boolean $in_docblock  True to accept the type definition of docblock
     * @return string|false|null    If a string is returned, it is known to be valid
     *                              If false is returned, the return type is known to be invalid
     *                              If null is returned, the return type is out of hand
     */
    private static function translate_known_return_type(string $type, bool $in_docblock) {
        // Known return type hints
        static $known_return_types = [
            'string' => 'string',
            'object' => null,
            'array' => 'array',
            'iterable' => 'array',
            'self' => null,
            'void' => 'void',
            'float' => 'float',
            'double' => 'float',
            'bool' => 'bool',
            'int' => 'int',
        ];
        // Known @return types
        static $known_type_definitions = [
            'object' => null,
            'mixed' => null,
            'resource' => null,
            'callable' => null,
            'null' => '$null$',
            'false' => 'bool',
            'true' => 'bool',
            'boolean' => 'bool',
            'integer' => 'int',
        ];
        if (array_key_exists($type, $known_return_types)) {
            return $known_return_types[$type];
        }
        $found = $known_type_definitions[$type] ?? -1;
        if ($found === -1) {
            // Heuristically look for some types.
            if (strpos($type, '[]') !== false) {
                // Treat 'something[]' as array.
                $found = 'array';
            } else if (strpos($type, '|') !== false) {
                // 'mixed' types are too complicated.
                $found = null;
            }
        }
        if ($found !== -1) {
            if ($in_docblock) {
                return $found;
            } else {
                return false;
            }
        }
        return '$user$';
    }

    /**
     * Get the type name as string if possible.
     *
     * @param \ReflectionType|null $type
     * @return string|null
     */
    private static function get_type_string(?\ReflectionType $type): ?string {
        if ($type instanceof \ReflectionNamedType) {
            return $type->getName();
        }
        return null;
    }

    /**
     * Recursively scan source files for for carriage return characters
     *
     * @param string $directory Path to directory to scan
     * @param array  $errors Referenced array of errors
     * @return void
     */
    public function scan_for_crlf(string $directory, array &$errors): void {
        global $CFG;
        $root = str_replace(DIRECTORY_SEPARATOR, '/', $CFG->dirroot) . '/mod/facetoface/';
        $relpath = str_replace(DIRECTORY_SEPARATOR, '/', $directory);
        if (strpos($relpath, $root) === 0) {
            $relpath = substr($relpath, strlen($root));
        }
        if (in_array($relpath, $this->whitelist_crlf)) {
            return;
        }
        $handle = opendir($directory);
        while (false !== ($file = readdir($handle))) {
            // Ignore all dotfiles, including ..
            if (substr($file,0,1) == '.') {
                continue;
            }
            $file_name = $directory.DIRECTORY_SEPARATOR.$file;
            $file_type = filetype($file_name);
            if ($file_type == 'dir') {
                // Directory recursion.
                $this->scan_for_crlf($file_name, $errors);
            } else {
                $contents = file_get_contents($file_name);
                // Look for \r to cover all types of line-ending that aren't \n.
                // If you need \r\n somewhere, please use the CRLF constant.
                if (strpos($contents, "\r") !== false) {
                    $errors[] = "{$file_type} {$file_name} has a carriage return";
                }
            }
        }
        closedir($handle);
    }

    /**
     * Test suite for core seminar classes
     *
     * @return void
     */
    public function test_core_seminar_classes(): void {
        if ($this->disabled_code_inspector) {
            $this->markTestIncomplete('Code inspection is temporarily disabled.');
        }

        $errors = array();

        foreach ($this->get_classes_to_test() as $class) {
            // Accumulate errors.
            $errors = array_merge($errors, $this->inspect_class_docblock($class));
            $errors = array_merge($errors, $this->inspect_property_docblocks($class));
            $errors = array_merge($errors, $this->inspect_method_docblocks($class));
            $errors = array_merge($errors, $this->inspect_method_parameter_hints($class));
            $errors = array_merge($errors, $this->inspect_method_return_hints($class));
        }

        if (!empty($errors)) {
            // Dump all errors at once.
            $this->fail(implode(PHP_EOL, $errors));
        }
    }

    /**
     * Check seminar source code for CRLF line endings
     *
     * @return void
     */
    public function test_line_endings_in_source_files(): void {
        global $CFG;

        $source_directory = $CFG->dirroot . '/mod/facetoface';
        $errors = array();
        $this->scan_for_crlf($source_directory, $errors);

        if (count($errors) >= 50) {
            $errors = ["Excessive number of files containing carriage returns. Please check git autocrlf settings and/or editor line endings."];
        }
        if (count($errors)) {
            $this->fail("Found CRLF line endings in seminar source code" . PHP_EOL . implode(PHP_EOL, $errors));
        }
    }

    /**
     * Self-test inspect_class_docblock().
     */
    public function test_inspect_class_docblock(): void {
        require_once(__DIR__ . '/fixtures/code_quality_test_classes.php');

        $errors = $this->inspect_class_docblock(mod_facetoface\tests\docblock\class_has_no_docblock::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Empty docblock for class', $errors[1]);
        $errors = $this->inspect_class_docblock(mod_facetoface\tests\docblock\class_docblock_has_only_stars::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Empty docblock for class', $errors[1]);
        $errors = $this->inspect_class_docblock(mod_facetoface\tests\docblock\class_docblock_starts_with_single_star::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Empty docblock for class', $errors[1]);
        $errors = $this->inspect_class_docblock(mod_facetoface\tests\docblock\class_docblock_undocumented::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Undocumented docblock for class', $errors[1]);
        $errors = $this->inspect_class_docblock(mod_facetoface\tests\docblock\class_docblock_is_ok::class);
        $this->assertCount(0, $errors);
    }

    /**
     * Self-test inspect_property_docblocks().
     */
    public function test_inspect_property_docblocks(): void {
        require_once(__DIR__ . '/fixtures/code_quality_test_classes.php');

        $errors = $this->inspect_property_docblocks(mod_facetoface\tests\docblock\prop_has_no_docblock::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Empty docblock for $foo', $errors[1]);
        $errors = $this->inspect_property_docblocks(mod_facetoface\tests\docblock\prop_has_invalid_var_type::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Invalid @var type for $foo', $errors[1]);
        $errors = $this->inspect_property_docblocks(mod_facetoface\tests\docblock\prop_docblock_has_only_stars::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Empty docblock for $foo', $errors[1]);
        $errors = $this->inspect_property_docblocks(mod_facetoface\tests\docblock\prop_docblock_starts_with_single_star::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Empty docblock for $foo', $errors[1]);
        $errors = $this->inspect_property_docblocks(mod_facetoface\tests\docblock\prop_docblock_undocumented::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Undocumented docblock for $foo', $errors[1]);
        $errors = $this->inspect_property_docblocks(mod_facetoface\tests\docblock\prop_docblock_has_no_type::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Missing @var declaration for $foo', $errors[1]);
        $errors = $this->inspect_property_docblocks(mod_facetoface\tests\docblock\prop_docblock_is_ok1::class);
        $this->assertCount(0, $errors);
        $errors = $this->inspect_property_docblocks(mod_facetoface\tests\docblock\prop_docblock_is_ok2::class);
        $this->assertCount(0, $errors);
    }

    /**
     * Self-test inspect_method_docblocks().
     */
    public function test_inspect_method_docblocks(): void {
        require_once(__DIR__ . '/fixtures/code_quality_test_classes.php');

        $errors = $this->inspect_method_docblocks(mod_facetoface\tests\docblock\method_has_no_docblock::class);
        $this->assertCount(2, $errors);
        $this->assertSame('No method docblock for mod_facetoface\tests\docblock\method_has_no_docblock::foo()', $errors[1]);
        $errors = $this->inspect_method_docblocks(mod_facetoface\tests\docblock\method_has_no_docblock_but_different_param_name::class);
        $this->assertCount(2, $errors);
        $this->assertSame('No method docblock for mod_facetoface\tests\docblock\method_has_no_docblock_but_different_param_name::foo(), which extends mod_facetoface\tests\docblock\method_docblock_base::foo() but has different parameters', $errors[1]);
        $errors = $this->inspect_method_docblocks(mod_facetoface\tests\docblock\method_docblock_has_only_stars1::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Empty docblock for mod_facetoface\tests\docblock\method_docblock_has_only_stars1::foo()', $errors[1]);
        $errors = $this->inspect_method_docblocks(mod_facetoface\tests\docblock\method_docblock_has_only_stars2::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Empty docblock for mod_facetoface\tests\docblock\method_docblock_has_only_stars2::foo()', $errors[1]);
        $errors = $this->inspect_method_docblocks(mod_facetoface\tests\docblock\method_docblock_starts_with_single_star::class);
        $this->assertCount(2, $errors);
        $this->assertSame('No method docblock for mod_facetoface\tests\docblock\method_docblock_starts_with_single_star::foo()', $errors[1]);
        $errors = $this->inspect_method_docblocks(mod_facetoface\tests\docblock\method_docblock_has_extra_empty_docblock::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Empty docblock for mod_facetoface\tests\docblock\method_docblock_has_extra_empty_docblock::foo()', $errors[1]);
        $errors = $this->inspect_method_docblocks(mod_facetoface\tests\docblock\method_docblock_undocumented::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Undocumented docblock for mod_facetoface\tests\docblock\method_docblock_undocumented::foo()', $errors[1]);
        $errors = $this->inspect_method_docblocks(mod_facetoface\tests\docblock\method_docblock_missing_param1::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Method mod_facetoface\tests\docblock\method_docblock_missing_param1::foo() docblock missing @param declaration for $bar', $errors[1]);
        $errors = $this->inspect_method_docblocks(mod_facetoface\tests\docblock\method_docblock_missing_param2::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Method mod_facetoface\tests\docblock\method_docblock_missing_param2::foo() docblock missing @param declaration for $flag', $errors[1]);
        $errors = $this->inspect_method_docblocks(mod_facetoface\tests\docblock\method_docblock_missing_param_type::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Method mod_facetoface\tests\docblock\method_docblock_missing_param_type::foo() docblock missing @param type for $bar', $errors[1]);
        $errors = $this->inspect_method_docblocks(mod_facetoface\tests\docblock\method_docblock_invalid_param_type::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Method mod_facetoface\tests\docblock\method_docblock_invalid_param_type::foo() docblock invalid @param type for $bar', $errors[1]);
        $errors = $this->inspect_method_docblocks(mod_facetoface\tests\docblock\method_docblock_invalid_param_type_hint1::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Method mod_facetoface\tests\docblock\method_docblock_invalid_param_type_hint1::foo() docblock has incorrect @param type for $bar: \'int\' expected', $errors[1]);
        $errors = $this->inspect_method_docblocks(mod_facetoface\tests\docblock\method_docblock_invalid_param_type_hint2::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Method mod_facetoface\tests\docblock\method_docblock_invalid_param_type_hint2::foo() docblock has incorrect @param type for $bar: \'array\' expected', $errors[1]);
        $errors = $this->inspect_method_docblocks(mod_facetoface\tests\docblock\method_docblock_missing_return_type::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Method mod_facetoface\tests\docblock\method_docblock_missing_return_type::foo() docblock missing @return declaration', $errors[1]);
        $errors = $this->inspect_method_docblocks(mod_facetoface\tests\docblock\method_docblock_incorrect_return_type_hint1::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Method mod_facetoface\tests\docblock\method_docblock_incorrect_return_type_hint1::foo() docblock has incorrect @return type: \'float\' expected', $errors[1]);
        $errors = $this->inspect_method_docblocks(mod_facetoface\tests\docblock\method_docblock_incorrect_return_type_hint2::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Method mod_facetoface\tests\docblock\method_docblock_incorrect_return_type_hint2::foo() docblock has incorrect @return type: \'bool\' expected', $errors[1]);
        $errors = $this->inspect_method_docblocks(mod_facetoface\tests\docblock\method_docblock_incorrect_return_type_hint3::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Method mod_facetoface\tests\docblock\method_docblock_incorrect_return_type_hint3::foo() docblock has incorrect @return type: \'void\' expected', $errors[1]);
        $errors = $this->inspect_method_docblocks(mod_facetoface\tests\docblock\method_docblock_incorrect_return_type_hint4::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Method mod_facetoface\tests\docblock\method_docblock_incorrect_return_type_hint4::foo() docblock has incorrect @return type', $errors[1]);
        $errors = $this->inspect_method_docblocks(mod_facetoface\tests\docblock\method_docblock_incorrect_return_type_hint5::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Method mod_facetoface\tests\docblock\method_docblock_incorrect_return_type_hint5::foo() docblock has incorrect \'@return this\': \'self\' expected', $errors[1]);
        // TODO: Detect 'integer[]|string' as 'mixed' in translate_known_return_type()
        // $errors = $this->inspect_method_docblocks(mod_facetoface\tests\docblock\method_docblock_incorrect_return_type_hint6::class);
        // $this->assertCount(2, $errors);
        // $this->assertSame('Method mod_facetoface\tests\docblock\method_docblock_incorrect_return_type_hint6::foo() docblock has type-mismatched @return declaration', $errors[1]);
        $errors = $this->inspect_method_docblocks(mod_facetoface\tests\docblock\method_docblock_is_ok::class);
        $this->assertCount(0, $errors);
        $errors = $this->inspect_method_docblocks(mod_facetoface\tests\docblock\method_docblock_inheritdoc_is_ok::class);
        $this->assertCount(0, $errors);
        $errors = $this->inspect_method_docblocks(mod_facetoface\tests\docblock\accepted_with_no_parameter_hint::class);
        $this->assertCount(0, $errors);
    }

    /**
     * Self-test inspect_method_parameter_hints().
     */
    public function test_inspect_method_parameter_hints(): void {
        require_once(__DIR__ . '/fixtures/code_quality_test_classes.php');

        // NOTE: inspect_method_parameter_hints() reports nothing about param_has_no_docblock1 even though its method does not have a docblock.
        $errors = $this->inspect_method_parameter_hints(mod_facetoface\tests\docblock\param_has_no_docblock1::class);
        $this->assertCount(0, $errors);
        $errors = $this->inspect_method_parameter_hints(mod_facetoface\tests\docblock\param_has_no_docblock2::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Method mod_facetoface\tests\docblock\param_has_no_docblock2::foo() parameter $bar is missing a type hint', $errors[1]);
        $errors = $this->inspect_method_parameter_hints(mod_facetoface\tests\docblock\param_missing_typehint::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Method mod_facetoface\tests\docblock\param_missing_typehint::foo() parameter $bar is missing a type hint', $errors[1]);
        $errors = $this->inspect_method_parameter_hints(mod_facetoface\tests\docblock\accepted_with_no_parameter_hint::class);
        $this->assertCount(0, $errors);
    }

    /**
     * Self-test inspect_method_return_hints().
     */
    public function test_inspect_method_return_hints(): void {
        require_once(__DIR__ . '/fixtures/code_quality_test_classes.php');

        $errors = $this->inspect_method_return_hints(mod_facetoface\tests\docblock\return_missing_type_hint::class);
        $this->assertCount(2, $errors);
        $this->assertSame('Method mod_facetoface\tests\docblock\return_missing_type_hint::foo() is missing a return type hint', $errors[1]);
        $errors = $this->inspect_method_return_hints(mod_facetoface\tests\docblock\return_magic_methods_are_ignored::class);
        $this->assertCount(0, $errors);
        // TODO: distinguish magic methods and non-magic methods
        // $errors = $this->inspect_method_parameter_hints(mod_facetoface\tests\docblock\return_non_magic_methods_are_not_ignored::class);
        // $this->assertCount(2, $errors);
        // $this->assertSame('Method mod_facetoface\tests\docblock\return_non_magic_methods_are_not_ignored::__foo() is missing a return type hint', $errors[1]);
        $errors = $this->inspect_method_return_hints(mod_facetoface\tests\docblock\return_type_is_ok::class);
        $this->assertCount(0, $errors);
        $errors = $this->inspect_method_return_hints(mod_facetoface\tests\docblock\accepted_with_no_parameter_hint::class);
        $this->assertCount(0, $errors);
    }
}
