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
 * @author Cody Finegan <cody.finegan@totaralearning.com>
 * @package block_totara_recently_viewed
 */

namespace block_totara_recently_viewed\container_course;

use block_totara_recently_viewed\card as base_card;
use completion_completion;
use container_course\course;
use moodle_url;

/**
 * Course card for the recently viewed block
 *
 */
class card implements base_card {
    /**
     * @var course
     */
    private $course;

    /**
     * @param int $id
     * @return base_card
     */
    public static function from_id(int $id): base_card {
        $card = new static();
        $card->course = course::from_id($id);

        return $card;
    }

    /**
     * @return int
     */
    public function get_id(): int {
        return $this->course->get_id();
    }

    /**
     * @param bool $is_dashboard
     * @return moodle_url
     */
    public function get_url(bool $is_dashboard): moodle_url {
        return $this->course->get_view_url();
    }

    /**
     * @return string|null
     */
    public function get_title(): ?string {
        return $this->course->fullname;
    }

    /**
     * @return string|null
     */
    public function get_subtitle(): ?string {
        return null;
    }

    /**
     * @return int|null
     */
    public function get_user_id(): ?int {
        return null;
    }

    /**
     * @param bool $tile_view
     * @return moodle_url|null
     */
    public function get_image(bool $tile_view): ?\moodle_url {
        global $CFG, $OUTPUT;

        require_once("{$CFG->dirroot}/lib/filelib.php");
        $fs = get_file_storage();

        $component = 'course'; // Files are stored in course not container_course
        $context = $this->course->get_context();

        $files = $fs->get_area_files(
            $context->id,
            $component,
            'images',
            0
        );

        // Remove any directory.
        $files = array_filter(
            $files,
            function(\stored_file $file): bool {
                return !$file->is_directory();
            }
        );

        if (empty($files)) {
            return $OUTPUT->image_url('course_defaultimage', 'core');
        }

        if (1 !== count($files)) {
            debugging("There are more than one default image file", DEBUG_DEVELOPER);
        }

        /** @var \stored_file $file */
        $file = reset($files);

        return \moodle_url::make_pluginfile_url(
            $context->id,
            $component,
            'images',
            0,
            '/',
            $file->get_filename()
        );
    }

    /**
     * @return array
     */
    public function get_extra_data(): array {
        global $USER;

        // Check the user's enrollment
        $enrol_status = enrol_get_users_course_status($this->course->get_id(), $USER->id);
        $show_progress = false;
        $percent = 0;

        if ($enrol_status === ENROL_USER_ACTIVE) {
            $completion = new completion_completion(['userid' => $USER->id, 'course' => $this->course->get_id()]);
            $progress_info = $completion->get_progressinfo();
            $percent = $progress_info->get_percentagecomplete();

            if ($percent !== false) {
                $show_progress = true;
            }
        }

        return [
            'show_progress' => $show_progress,
            'progress' => $percent . '%',
            'progress_small' => $percent < 30,
        ];
    }

    /**
     * @return bool
     */
    public function is_library_card(): bool {
        return false;
    }
}