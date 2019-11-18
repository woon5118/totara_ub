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
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author  Petr Skoda <petr.skoda@totaralms.com>
 * @package theme_legacy
 */

/* Developer documentation is in /pix/flex_icons.php file. */

/* Pix only images are not supposed to be converted to flex icons. */
$pixonlyimages = array(
    'screenshot',
    // TODO: add sprite and fp/ stuff if it is not converted to flex icons
);

$icons = array(
    'theme_legacy|notification-info' => array(
        'data' => array(
            'classes' => 'fa-info-circle',
        ),
    ),
    'theme_legacy|notification-success' => array(
        'data' => array(
            'classes' => 'fa-check ft-flip-rtl',
        ),
    ),
    'theme_legacy|notification-warning' => array(
        'data' => array(
            'classes' => 'fa-exclamation-triangle',
        ),
    ),
    'theme_legacy|notification-error' => array(
        'data' => array(
            'classes' => 'fa-bolt ft-flip-rtl',
        ),
    ),
);

$deprecated = [
    'home' => 'totara_core|home',
    'notification-info' => 'theme_legacy|notification-info',
    'notification-success' => 'theme_legacy|notification-success',
    'notification-warning' => 'theme_legacy|notification-warning',
    'notification-error' => 'theme_legacy|notification-error',
];
