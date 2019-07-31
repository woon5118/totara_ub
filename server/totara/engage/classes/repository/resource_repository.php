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
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package totara_engage
 */

namespace totara_engage\repository;

use core\orm\entity\repository;
use core\orm\query\builder;
use totara_engage\entity\engage_resource;

final class resource_repository extends repository {

    /**
     * @param int $instanceid
     * @param string $resourcetype
     * @return engage_resource
     */
    public function get_from_instance(int $instanceid, string $resourcetype): engage_resource {
        $builder = builder::table(engage_resource::TABLE, 'r')
            ->map_to(engage_resource::class)
            ->where('r.instanceid', $instanceid)
            ->where('r.resourcetype', $resourcetype);

        /** @var engage_resource $entity */
        $entity = $builder->one();
        return $entity;
    }

}