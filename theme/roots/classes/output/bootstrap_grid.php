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
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2016 onwards Totara Learning Solutions LTD
 * @author    Joby Harding <joby.harding@totaralearning.com>
 * @author    Murali Nair <murali.nair@totaralearning.com>
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
     * @var bool
     */
    protected $top = false;

    /**
     * @var bool
     */
    protected $bottom = false;

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

    public function has_top() {
        $this->top = true;

        return $this;
    }

    public function has_bottom() {
        $this->bottom = true;

        return $this;
    }

    /**
     * Return classes which should be applied to configured regions.
     *
     * @param array $add_block_regions  List of regions where blocks can be added.
     * @return array
     */
    public function get_regions_classes(array $add_block_regions = []): array {
        // NOTE: when making changes here make sure you apply them also to theme/roots/less/totara/core.less

        $classes = [];
        $classes['top'] = 'col-sm-12';
        $classes['bottom'] = 'col-sm-12';
        $classes['content'] = 'col-md-12';
        $classes['pre'] = 'empty';
        $classes['post'] = 'empty';

        if ($this->side_pre && $this->side_post) {
            $classes['content'] = 'col-sm-12 col-md-6 col-md-push-3';
            $classes['pre'] = 'col-sm-6 col-md-3 col-md-pull-6';
            $classes['post'] = 'col-sm-6 col-md-3';
            return $this->add_editing_class($classes, $add_block_regions);
        }

        if ($this->side_pre && !$this->side_post) {
            $classes['content'] = 'col-sm-12 col-md-9 col-md-push-3';
            $classes['pre'] = 'col-sm-6 col-md-3 col-md-pull-9';
            $classes['post'] = 'empty';
            return $this->add_editing_class($classes, $add_block_regions);
        }

        if (!$this->side_pre && $this->side_post) {
            $classes['content'] = 'col-sm-12 col-md-9';
            $classes['pre'] = 'empty';
            $classes['post'] = 'col-sm-6 col-sm-offset-6 col-md-3 col-md-offset-0';
            return $this->add_editing_class($classes, $add_block_regions);
        }

        // No side-pre or side-post.
        return $this->add_editing_class($classes, $add_block_regions);
    }

    /**
     * @param array $classes
     * @param array $add_block_regions
     * @return array
     */
    private function add_editing_class(array $classes, array $add_block_regions): array {
        $editing_class = ' editing-region-border';
        if ($this->side_pre && in_array('side-pre', $add_block_regions)) {
            $classes['pre'] .= $editing_class;
        }
        if ($this->side_post && in_array('side-post', $add_block_regions)) {
            $classes['post'] .= $editing_class;
        }
        if ($this->top && in_array('top', $add_block_regions)) {
            $classes['top'] .= $editing_class;
        }
        if ($this->bottom && in_array('bottom', $add_block_regions)) {
            $classes['bottom'] .= $editing_class;
        }
        if (in_array('main', $add_block_regions)) {
            $classes['content'] .= $editing_class;
        }

        return $classes;
    }

}
