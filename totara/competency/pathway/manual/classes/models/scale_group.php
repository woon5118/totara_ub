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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package totara_competency
 */

namespace pathway_manual\models;

use core\orm\collection;
use totara_competency\entities\scale;
use totara_competency\entities\scale_value;

/**
 * Class scale_group
 *
 * This is necessary for grouping competencies that can be manually rated
 * into sections based upon their respective scale.
 *
 * @package pathway_manual\models
 */
class scale_group {

    /**
     * @var scale
     */
    protected $scale;

    /**
     * @var rateable_competency[]
     */
    protected $rateable_competencies;

    protected function __construct(scale $scale) {
        $this->scale = $scale;
        $this->rateable_competencies = [];
    }

    /**
     * Get the rateable competencies.
     *
     * @return rateable_competency[]
     */
    public function get_rateable_competencies(): array {
        return $this->rateable_competencies;
    }

    /**
     * Get the values for this scale.
     *
     * @return scale_value[]|collection
     */
    public function get_scale_values() {
        return $this->scale->values;
    }

    /**
     * Build scale groups by grouping a list of competencies by the scale they have.
     *
     * @param rateable_competency[] $competencies
     * @return scale_group[]
     */
    public static function build_from_competencies(array $competencies): array {
        $scales = [];

        foreach ($competencies as $competency) {
            $scale = $competency->get_entity()->scale;

            if (!isset($scales[$scale->id])) {
                $scales[$scale->id] = new static($scale);
            }

            $scales[$scale->id]->rateable_competencies[] = $competency;
        }

        return array_values($scales);
    }

}
