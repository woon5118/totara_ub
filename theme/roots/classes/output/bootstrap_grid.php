<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2016 onwards Totara Learning Solutions LTD
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
 * @copyright 2016 onwards Totara Learning Solutions LTD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Joby Harding <joby.harding@totaralearning.com>
 * @package   theme_roots
 */

namespace theme_roots\output;

/**
 * Class bootstrap_grid
 *
 * Class based implementation of theme_bootstrap
 * lib function with the same name. We re-implement
 * as that function contains some rtl switching which
 * actually breaks the layout in Totara.
 *
 * @package theme_bootstrap
 */
class bootstrap_grid {

    /**
     * @var bool
     */
    protected $side_pre = false;

    /**
     * @var bool
     */
    protected $side_post = false;

    /**
     * Set gridf
     */
    public function has_side_pre() {
        $this->side_pre = true;

        return $this;
    }

    public function has_side_post() {
        $this->side_post = true;

        return $this;
    }

    /**
     * Return classes which should be applied to configured regions.
     */
    public function get_regions_classes() {

        if ($this->side_pre && $this->side_post) {
            return array(
                'content' => 'col-sm-6 col-sm-push-3 col-lg-8 col-lg-push-2',
                'pre' => 'col-sm-3 col-sm-pull-6 col-lg-2 col-lg-pull-8',
                'post' => 'col-sm-3 col-lg-2',
            );
        }

        if ($this->side_pre && !$this->side_post) {
            return array(
                'content' => 'col-sm-9 col-sm-push-3 col-lg-10 col-lg-push-2',
                'pre' => 'col-sm-3 col-sm-pull-9 col-lg-2 col-lg-pull-10',
                'post' => 'empty',
            );
        }

        if (!$this->side_pre && $this->side_post) {
            return array(
                'content' => 'col-sm-9 col-lg-10',
                'pre' => 'empty',
                'post' => 'col-sm-3 col-lg-2',
            );
        }

        // No side-pre or side-post.
        return array(
            'content' => 'col-md-12',
            'pre' => 'empty',
            'post' => 'empty',
        );

    }

}
