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
 * @author  Joby Harding <joby.harding@totaralms.com>
 * @author  Petr Skoda <petr.skoda@totaralms.com>
 * @package mod_assign
 */

/* Developer documentation is in /pix/flex_icons.php file. */

$translations = array(
    'mod_assign|gradefeedback' => 'edit',
);

$map = array(
    'mod_assign|icon' =>
        array(
            'template' => 'core/flex_icon_stack',
            'data' =>
                array(
                    'classes' =>
                        array(
                            'stack_first' => 'fa fa-file-text-o ft-stack-main',
                            'stack_second' => 'fa fa-thumb-tack ft-stack-suffix ft-state-info',
                        ),
                ),
        ),
);
