<?php
/*
 * This file is part of Totara LMS
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
 * @author Chris Snyder <chris.snyder@totaralearning.com>
 * @package totara_mobile
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Tests the totara current learning query resolver
 */
class totara_mobile_webapi_graphql_typename_testcase extends advanced_testcase {

    /**
     * Scan *.graphql files and ensure __typename properties are included for every type
     *
     * @param string $directory Path to directory to scan
     * @param array  $errors Referenced array of errors
     * @return void
     */
    private function scan_for_typename(string $directory, array &$errors): void {
        global $CFG;
        $directory = str_replace(DIRECTORY_SEPARATOR, '/', $directory);
        $handle = opendir($directory);
        while (false !== ($file = readdir($handle))) {
            // Ignore all dotfiles, including ..
            if (substr($file,0,1) == '.') {
                continue;
            }
            // Ignore everything except .graphql files
            if (substr($file,-8) != '.graphql') {
                continue;
            }
            $file_name = $directory.DIRECTORY_SEPARATOR.$file;
            $contents = file_get_contents($file_name);
            $contents = trim($contents);
            // Skip mutations
            if (substr($contents, 0, 5) != 'query') {
                continue;
            }
            // Trim final }
            $contents = substr($contents, 0, -1);
            // Look for __typename before each closing curly }
            $lines = explode("\n", $contents);
            foreach ($lines as $lx=>$line) {
                $line = trim($line);
                if ($line == '}') {
                    $prevline = trim($lines[$lx - 1]);
                    if ($prevline != '__typename') {
                        $errors[] = "{$file_name} line {$lx}";
                    }
                }
            }
        }
        closedir($handle);
    }

    /**
     * Check mobile graphql files for typename properties
     *
     * @return void
     */
    public function test_typename_in_mobile_graphql(): void {
        global $CFG;

        $source_directory = $CFG->dirroot . '/totara/mobile/webapi/mobile';
        $errors = array();

        $this->scan_for_typename($source_directory, $errors);

        if (count($errors)) {
            $this->fail("Failed to find expected __typename properties in these *.graphql files:" . PHP_EOL . implode(PHP_EOL, $errors));
        }
    }
}
