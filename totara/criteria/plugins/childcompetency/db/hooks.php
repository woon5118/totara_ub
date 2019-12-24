<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package criteria_childcompetency
 */

use criteria_childcompetency\watcher\achievement;
use criteria_childcompetency\watcher\competency;
use totara_competency\hook\competency_achievement_updated;
use totara_competency\hook\pathways_created;
use totara_competency\hook\pathways_deleted;
use totara_competency\hook\pathways_updated;

defined('MOODLE_INTERNAL') || die();

$watchers = [
    [
        'hookname' => competency_achievement_updated::class,
        'callback' => achievement::class.'::updated',
    ],
    [
        'hookname' => pathways_created::class,
        'callback' => competency::class.'::pathway_configuration_changed',
    ],
    [
        'hookname' => pathways_updated::class,
        'callback' => competency::class.'::pathway_configuration_changed',
    ],
    [
        'hookname' => pathways_deleted::class,
        'callback' => competency::class.'::pathway_configuration_changed',
    ],
    [
        'hookname' => \totara_competency\hook\competency_achievement_updated_bulk::class,
        'callback' => \criteria_childcompetency\watcher\achievement::class.'::updated_bulk',
    ],
];
