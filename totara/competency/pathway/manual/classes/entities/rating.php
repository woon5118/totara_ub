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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @package pathway_manual;
 */

namespace pathway_manual\entities;

use core\orm\entity\entity;

/**
 * Class rating
 *
 * Acts as a record of a user having rated another user, or themselves, along a scale for a given competency.
 *
 * @property-read int $id ID
 * @property int $comp_id
 * @property int $user_id
 * @property int $scale_value_id
 * @property int $date_assigned
 * @property int $assigned_by
 * @property string $assigned_by_role
 * @property string $comment
 */
class rating extends entity {

    public const TABLE = 'pathway_manual_rating';
}
