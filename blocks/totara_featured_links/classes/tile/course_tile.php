<?php
/**
 * This file is part of Totara LMS
 *
 * Copyright (C) 2017 onwards Totara Learning Solutions LTD
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
 * @author Andrew McGhie <andrew.mcghie@totaralearning.com>
 * @package block_totara_featured_links
 */

namespace block_totara_featured_links\tile;

/**
 * Class course_tile
 * @package block_totara_featured_links
 */
class course_tile extends base{
    protected $used_fields = ['courseid', // int The id of the course that the tile links to.
        'background_color', // string The hex value of the background color.
        'heading_location', // string Where the heading is located 'top' or 'bottom'.
        'progressbar'];
    protected $content_form = '\block_totara_featured_links\tile\course_form_content';
    protected $content_template = 'block_totara_featured_links/content_course';
    protected $content_class = 'block-totara-featured-links-content block-totara-featured-links-course';

    /** @var string This is the name of the class which defines the visibility form */
    protected $visibility_form = '\block_totara_featured_links\tile\course_form_visibility';

    /**
     * @var \stdClass $course the database row of the course
     *
     * Call $this->get_course() to load this property.
     */
    protected $course = null;

    /**
     * Does the custom adding of the tile
     * this tile however doesn't need anything run
     */
    public function add_tile() {

    }

    /**
     * returns the name and id in the right indexes
     * @inheritdoc
     */
    public function get_content_form_data() {
        $dataobj = parent::get_content_form_data();
        if (!empty($this->get_course())) {
            $dataobj->course_name = $this->get_course()->fullname;
        }
        if (isset($this->data_filtered->courseid)) {
            $dataobj->course_name_id = $this->data_filtered->courseid;
        }
        return $dataobj;
    }

    /**
     * returns the name of the tile that will be displayed
     * @return string NAME
     */
    public static function get_name() {
        return get_string('course_name', 'block_totara_featured_links');
    }

    /**
     * Puts the data from the class in a way which the template can render
     * @return array
     */
    protected function get_content_template_data() {
        global $USER, $DB;
        if (empty($this->get_course())) {
            return null;
        }
        if (!$status = $DB->get_field('course_completions', 'status', array('userid' => $USER->id, 'course' => $this->data->courseid))) {
            $status = null;
        }
        if (isset($this->data->progressbar) && $this->data->progressbar == '1') {
            $progressbar = totara_display_course_progress_bar($USER->id, $this->data->courseid, $status);
        } else {
            $progressbar = false;
        }

        return [
            'heading' => $this->get_course()->fullname,
            'progress_bar' =>  $progressbar,
            'content_class' => (empty($this->content_class) ? '' : $this->content_class),
            'heading_location' => (empty($this->data_filtered->heading_location) ? '' : $this->data_filtered->heading_location),
            'notempty' => true
        ];
    }

    /**
     * Gets the data for the wrapper eg url and background color
     * @param \renderer_base $renderer
     * @return array
     */
    public function get_content_wrapper_template_data(\renderer_base $renderer) {
        global $CFG;
        $data = parent::get_content_wrapper_template_data($renderer);
        $data['background_color'] = (!empty($this->data_filtered->background_color) ?
            $this->data_filtered->background_color :
            false);
        $data['alt_text'] = $this->get_accessibility_text();
        $data['url'] = (!empty($this->get_course()) ? $CFG->wwwroot.'/course/view.php?id='.$this->get_course()->id : false);
        return $data;
    }

    /**
     * moves a file from the draft area to a defined area
     * @param \stdClass $data
     * @return void
     */
    public function save_content_tile($data) {
        if (isset($data->course_name_id)) {
            $this->data->courseid = $data->course_name_id;
        }
        if (isset($data->heading_location)) {
            $this->data->heading_location = $data->heading_location;
        }
        if (isset($data->background_color)) {
            $this->data->background_color = $data->background_color;
        }
        if (isset($data->progressbar)) {
            $this->data->progressbar = $data->progressbar;
        }
        return;
    }

    /**
     * Returns true if the user is allowed to view the content of this tile.
     *
     * This gives custom tile types a way of removing the tile if the user does not have permission to view the content of the tile.
     * If this returns true then the standard visibility checks are made by {@link self::is_visible()}.
     * If this returns false then the user is deemed to not be allowed to see the content of the tile, and consequently
     * other visibility checks are not made, the user is simply not checked.
     *
     * @return bool
     */
    protected function user_can_view_content() {
        if (empty($this->get_course())) {
            // This function is used when viewing the content, not when modifying the tile.
            // So return false as there is no content to view.
            return false;
        } else {
            return totara_course_is_viewable($this->get_course());
        }
    }

    /**
     * Gets whether the tile is visible to the user by the custom rules defined by the tile.
     * This should only be used by the is_visible() function.
     * @return int (-1 = hidden, 0 = no rule, 1 = showing)
     */
    public function is_visible_tile() {
        return 0;
    }

    /**
     * Saves the data for the custom visibility.
     * Should only modify the custom_rules variable so the reset of the visibility and tile options are left the same
     * when its saved to the database
     * @param \stdClass $data all the data from the form
     * @return string
     */
    public function save_visibility_tile($data) {
        return '';
    }

    /**
     * Returns an array that the template will uses to put in text to help with accessibility
     * @return array
     */
    public function get_accessibility_text() {
        return ['sr-only' => get_string('course_sr-only', 'block_totara_featured_links', !empty($this->course->fullname) ? $this->course->fullname : '')];
    }

    /**
     * Returns the course this tile is associated with.
     *
     * @return \stdClass|bool The course record or false if there is no associated course.
     */
    public function get_course($reload = false) {
        global $DB;

        if (!isset($this->course) or $reload) {
            if (!empty($this->data->courseid) && totara_course_is_viewable($this->data->courseid)) {
                $this->course = $DB->get_record('course', ['id' => $this->data->courseid]);
            } else {
                $this->course = false;
            }
        }

        return $this->course;
    }

    /**
     * We'll return that the course was deleted if that is the case.
     *
     * @return string of text shown if a tile is hidden but being viewed in edit mode.
     */
    protected function get_hidden_text() {
        if (empty($this->get_course())) {
            return get_string('course_has_been_deleted', 'block_totara_featured_links');
        } else {
            return parent::get_hidden_text();
        }
    }
}