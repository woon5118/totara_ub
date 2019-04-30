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
 * @package mod_facetoface
 */

defined('MOODLE_INTERNAL') || die();

use \mod_facetoface\{
    attendees_helper, seminar, seminar_event, seminar_event_list,
    seminar_session, signup, seminar_session_list
};
use \mod_facetoface\attendance\event_attendee;

class mod_facetoface_code_quality_testcase extends advanced_testcase {

    private $tested_classes = [
        attendees_helper::class,
        event_attendee::class,
        seminar::class,
        seminar_event::class,
        seminar_event_list::class,
        seminar_session::class,
        signup::class,
        seminar_session_list::class
    ];

    /**
     * Inspect the docblock of a class and return either 'OK' or errors
     *
     * @param string $classname The name of the class to inspect
     * @return string 'OK' or list of errors found
     */
    public function inspect_class_docblock(string $classname): string {
        $errors = array();
        try {
            $class = new ReflectionClass($classname);
            // check class docblock - must be present
            $docblock = $class->getDocComment();
            if (empty($docblock)) {
                $errors[] = 'Empty class docblock';
            }
            /**
             * TODO should have a package hint
             * else if (strpos($docblock, '* @package')===false) {
             * $errors[] = 'Class docblock missing @package declaration';
             * }
             */
        } catch (\ReflectionException $e) {
            $errors[] = $e->getMessage();
        }
        if (empty($errors)) {
            return 'OK';
        } else {
            return implode("\n", $errors);
        }
    }

    /**
     * Ensure docblocks are present on all class properties.
     *
     * @param string $classname The name of the class to inspect
     * @return string 'OK' or list of errors found
     */
    public function inspect_property_docblocks(string $classname): string {
        $errors = array();
        try {
            $class = new ReflectionClass($classname);

            foreach ($class->getProperties() as $property) {
                $docblock = $property->getDocComment();

                if (empty($docblock)) {
                    $errors[] = "No property docblock for {$property->getName()}";
                } else if (strpos($docblock, '* @var') === false) {
                    $errors[] = "Property {$property->getName()} docblock missing @var declaration";
                }
            }
        } catch (\ReflectionException $e) {
            $errors[] = $e->getMessage();
        }

        if (empty($errors)) {
            return 'OK';
        } else {
            return implode("\n", $errors);
        }
    }

    /**
     * Ensure docblocks are present on all class methods.
     *
     * You MUST include a description even if it appears to be obvious from the @param and/or @return lines.
     * An exception is made for overridden methods which make no change to the meaning of the parent method and maintain the same
     * arguments/return values. In this case you should omit the comment completely.
     *
     * @param string $classname The name of the class to inspect
     * @return string 'OK' or list of errors found
     */
    public function inspect_method_docblocks(string $classname): string {
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
                        $prototype_parameters = $prototype->getParameters();
                        if ($prototype_parameters != $parameters) {
                            $errors[] = "No method docblock for {$method->class}::{$method->getName()}(), which extends {$prototype->getDeclaringClass()}::{$prototype->getName()}() but has different parameters";
                        }
                    } catch (\ReflectionException $e) {
                        // There is a ReflectionException thrown if the method does not have a prototype.
                        $errors[] = "No method docblock for {$method->class}::{$method->getName()}()";
                    }
                } else {
                    foreach ($parameters as $parameter) {
                        // pattern to match @param declarations as below:
                        /**
                         * @param \mod_facetoface\seminar $foo is a seminar
                         * @param string ...$foo
                         * @param int|null $bar_dog is $foo's magic number
                         * @param \stdClass[] $users array of user records
                         * @param bool $foo $bar should be null if this is true
                         * @param int  $bar we like to line things using spaces too
                         */
                        $pattern = '/@param [^\s]+\s+(\.\.\.|)\$'.$parameter->getName().'\W/';
                        if (preg_match($pattern, $docblock) != 1) {
                            $errors[] = "Method {$method->class}::{$method->getName()}() docblock missing @param declaration for {$parameter->getName()}";
                        }
                    }
                }
            }
        } catch (\ReflectionException $e) {
            $errors[] = $e->getMessage();
        }

        if (empty($errors)) {
            return 'OK';
        } else {
            return implode("\n", $errors);
        }
    }

    /**
     * Ensure class methods parameters are type-hinted.
     *
     * @param string $classname The name of the class to inspect
     * @return string 'OK' or list of errors found
     */
    public function inspect_method_parameter_hints(string $classname): string {
        $errors = array();
        try {
            $class = new ReflectionClass($classname);
            $class_file = $class->getFileName();

            foreach ($class->getMethods() as $method) {
                if ($method->getFileName() != $class_file) {
                    // Method is defined elsewhere and used here as a trait or implementation.
                    continue;
                }

                $parameters = $method->getParameters();
                foreach ($parameters as $parameter) {
                    if ($parameter->hasType() === false) {
                        $errors[] = "Method {$method->class}::{$method->getName()} parameter {$parameter->getName()} is missing a type hint.";
                    }
                }
            }
        } catch (\ReflectionException $e) {
            $errors[] = $e->getMessage();
        }

        if (empty($errors)) {
            return 'OK';
        } else {
            return implode("\n", $errors);
        }
    }

    /**
     * Ensure class methods have explicit return types.
     *
     * @param string $classname The name of the class to inspect
     * @return string 'OK' or list of errors found
     */
    public function inspect_method_return_hints(string $classname): string {
        $errors = array();
        try {
            $class = new ReflectionClass($classname);
            $class_file = $class->getFileName();

            foreach ($class->getMethods() as $method) {
                if ($method->getFileName() != $class_file) {
                    // Method is defined elsewhere and used here as a trait or implementation.
                    continue;
                }

                // __construct, __get, __set, et al are magic methods and rarely have a specific return type.
                if (substr($method->getName(),0,2)=='__') {
                    continue;
                }

                if ($method->hasReturnType() === false) {
                    $errors[] = "Method {$method->class}::{$method->getName()} is missing a return type hint.";
                }
            }
        } catch (\ReflectionException $e) {
            $errors[] = $e->getMessage();
        }

        if (empty($errors)) {
            return 'OK';
        } else {
            return implode("\n", $errors);
        }
    }

    /**
     * Recursively scan source files for for carriage return characters
     *
     * @param string $directory Path to directory to scan
     * @param array  $errors Referenced array of errors
     * @return void
     */
    public function scan_for_crlf(string $directory, array &$errors): void {
        $handle = opendir($directory);
        while (false !== ($file = readdir($handle))) {
            // Ignore all dotfiles, including ..
            if (substr($file,0,1) == '.') {
                continue;
            }
            // Ignore media files, add other extensions here as necessary.
            if (in_array(substr($file,-4), ['.gif', '.jpg', '.png', '.svg'])) {
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
    }

    /**
     * Test suite for core seminar classes
     *
     * @return void
     */
    public function test_core_seminar_classes(): void {
        $this->resetAfterTest();

        foreach ($this->tested_classes as $class) {
            $result = $this->inspect_class_docblock($class);
            $this->assertEquals('OK', $result, "Problem with {$class} class docblock");

            $result = $this->inspect_property_docblocks($class);
            $this->assertEquals('OK', $result, "Problem with {$class} property docblocks");

            $result = $this->inspect_method_docblocks($class);
            $this->assertEquals('OK', $result, "Problem with {$class} method docblocks");

            $result = $this->inspect_method_parameter_hints($class);
            $this->assertEquals('OK', $result, "Problem with {$class} method docblocks");

            $result = $this->inspect_method_return_hints($class);
            $this->assertEquals('OK', $result, "Problem with {$class} method docblocks");
        }
    }

    /**
     * Check seminar source code for CRLF line endings
     *
     * @return void
     */
    public function test_line_endings_in_source_files(): void {
        $this->resetAfterTest();
        global $CFG;

        $source_directory = implode(DIRECTORY_SEPARATOR, [$CFG->dirroot, 'mod', 'facetoface']);
        $errors = array();
        $this->scan_for_crlf($source_directory, $errors);

        if (empty($errors)) {
            $result = 'OK';
        } else {
            if (count($errors) >= 50) {
                $result = "Excessive number of files containing carriage returns. Please check git autocrlf settings and/or editor line endings.";
            } else {
                $result = implode("\n", $errors);
            }
        }

        $this->assertEquals('OK', $result, "Found CRLF line endings in seminar source code");
    }
}