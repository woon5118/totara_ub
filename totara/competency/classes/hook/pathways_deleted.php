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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency\hook;

use totara_competency\pathway;
use totara_core\hook\base;

class pathways_deleted extends base {

    /** @var int */
    protected $competency_id;

    /** @var int[] */
    protected $pathway_ids;

    /**
     * @param int $competency_id;
     * @param array $pathway_ids
     */
    public function __construct(int $competency_id, array $pathway_ids) {
        $this->competency_id = $competency_id;
        $this->pathway_ids = $pathway_ids;
    }

    /**
     * @return int
     */
    public function get_competency_id(): int {
        return $this->competency_id;
    }

    /**
     * @return int[]
     */
    public function get_pathway_ids(): array {
        return $this->pathway_ids;
    }

}
