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
 * @author  Valerii Kuznetsov <valerii.kuznetsov@totaralearning.com>
 * @package ml_recommender
 */

namespace ml_recommender\local;

/**
 * Class flag manage possible multi-processing conflicts
 */
class flag {
    /**
     * Set of "lock" file names indicating various stages of workflow
     */
    const ML_STARTED = 'ml_started';
    const ML_COMPLETED = 'ml_completed';
    const EXPORT_STARTED = 'export_started';
    const EXPORT_COMPLETED = 'export_completed';
    const IMPORT_STARTED = 'import_started';
    const IMPORT_COMPLETED = 'import_completed';

    /**
     * Remove all "locking" flags. E.g. flags that are used to prevent parallel runs of serial processes.
     * Use with caution and only when no parallel processes are running
     * @param string $data_path
     * @param bool $silent
     */
    public static function clean_all(string $data_path, bool $silent = false) {
        $flags = [
            self::ML_STARTED,
            self::EXPORT_STARTED,
            self::IMPORT_STARTED,
        ];
        foreach ($flags as $flag) {
            $filepath = $data_path . '/' . $flag;
            if (file_exists($filepath)) {
                if (!$silent) {
                    mtrace("Found lock: $flag. Removing.");
                }
                unlink($filepath);
            }
        }
    }

    /**
     * Displays problem with lock, lock timestamp, and exit if it is not force
     * @param string $message
     * @param string $filepath
     */
    public static function problem(string $message, string $filepath) {
        $timestamp = 0;
        if (file_exists($filepath) && is_readable($filepath)) {
            $timestamp = file_get_contents($filepath);
        }
        $date = $timestamp ? date("Y-m-d H:i:s", $timestamp) : '(unknown)';
        $fullmessage = "Lock problem: $message. Created: $date. Location: $filepath.";
        debugging($fullmessage . ' Possibly parallel process is still running, or it was crashed. Use CLI scripts with --force to continue.');
    }
}