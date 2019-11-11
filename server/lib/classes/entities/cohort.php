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
 * @package core
 */

namespace core\entities;

use core\orm\entity\entity;

/**
 * @property int $contextid
 * @property string $name
 * @property string $idnumber
 * @property string $description
 * @property int $descriptionformat
 * @property bool $visible
 * @property string $component
 * @property int $timecreated
 * @property int $cohorttype
 * @property int $modifierid
 * @property int $timemodified
 * @property bool $visibility
 * @property int $alertmembers
 * @property int $startdate
 * @property int $enddate
 * @property bool $active
 * @property int $calculationstatus
 * @property int $activecollectionid
 * @property int $draftcollectionid
 * @property bool $broken
 * @property-read string $display_name
 *
 * @method static cohort_repository repository()
 *
 * @package totara_competency\entities
 */
class cohort extends entity implements expandable {

    use expand;

    protected $expand_table = 'cohort_members';
    protected $expand_select_column = 'userid';
    protected $expand_query_column = 'cohortid';

    public const TABLE = 'cohort';

    protected $extra_attributes = [
        'display_name'
    ];

    /**
     * Get unified display name that can be referred to safely, just an alias in this case
     *
     * @return string
     */
    protected function get_display_name_attribute() {
        return $this->name;
    }
}
