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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency\entities;


use core\orm\entity\entity;

/**
 * entity competency_scale
 *
 * @property-read int $id ID
 * @property string $name Scale name
 * @property string $description Scale description
 * @property int $timemodified Time modified
 * @property int $usermodified User modified
 * @property int $defaultid Default id
 *
 * @package totara_competency\entities
 */
class competency_scale extends entity {

    protected $table = 'comp_scale';

}
