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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency;

use totara_competency\entities\pathway_achievement;

abstract class overall_aggregation {

    /** @var string $agg_type Type of aggregatioon. Obtained from class */
    private $agg_type;

    /** @var pathway[] Pathways to aggregate over*/
    protected $pathways = [];

    /** @var array Achievement information per user */
    private $user_achievement = [];

    final public function __construct() {
        $reflect = new \ReflectionClass($this);
        $this->agg_type = $reflect->getShortName();
    }

    /**
     * Returns the type of the aggregation, i.e. highest or first
     *
     * @return string
     */
    public function get_agg_type(): string {
        return $this->agg_type;
    }

    /**
     * Set the pathways to aggregate over
     *
     * @param array $pathways
     * @return overall_aggregation
     */
    public function set_pathways(array $pathways): overall_aggregation {
        $this->pathways = $pathways;
        return $this;
    }

    /**
     * @return pathway[]
     */
    protected function get_pathways(): array {
        return $this->pathways;
    }

    /**
     * @param int $user_id Id of user to aggregate
     * @return array [int, array] Keys: scale_value_id, achieved_via
     */
    public function aggregate_for_user(int $user_id): array {
        if (!isset($this->user_achievement[$user_id])) {
            $this->do_aggregation($user_id);
        }

        // TODO: can possibly make use of a separate class, but seems like an overkill at this point
        return empty($this->user_achievement[$user_id])
            ? ['scale_value_id' => null, 'achieved_via' => []]
            : $this->user_achievement[$user_id];
    }

    /**
     * Aggregate a users value for a competency
     *
     * @param int $user_id
     * @return void
     */
    abstract protected function do_aggregation(int $user_id): void;

    /**
     * @param $user_id
     * @param array|pathway_achievement[] $achieved_via
     * @param int|null $scale_value_id
     */
    protected function set_user_achievement($user_id, array $achieved_via, ?int $scale_value_id = null) {
        // For now taking the last value set
        $this->user_achievement[$user_id] = ['scale_value_id' => $scale_value_id, 'achieved_via' => $achieved_via];
    }

    /**
     * Return id of the achieved scale value
     *
     * @return int|null
     */
    public function get_achieved_value_id($user_id): ?int {
        if (!isset($this->user_achievement[$user_id])) {
            return null;
        }

        return $this->user_achievement[$user_id]['scale_value_id'];
    }

    /**
     * Return the list of pathway achievement through which the user achieved this
     * @return pathway_achievement[]
     */
    public function get_achieved_via($user_id): array {
        if (!isset($this->user_achievement[$user_id])) {
            return [];
        }

        return $this->user_achievement[$user_id]['achieved_via'];
    }

    /**
     * Human readable title for this aggregation
     *
     * @return string
     */
    public function get_title(): string {
        $namespace = (new \ReflectionClass($this))->getNamespaceName();
        return get_string('title', $namespace);
    }

    /**
     * Get the human readable description of the aggregation type for display purposes
     *
     * @return string
     */
    public function get_description(): string {
        $namespace = (new \ReflectionClass($this))->getNamespaceName();
        return get_string('description', $namespace);
    }

    /**
     * Return the name of the javascript function handling pathway aggregation editing
     *
     * @return string|null Javascript function name. In v1, this must be the name of an existing
     *                     function in achievement_paths.js. Null or an empty string indicates
     *                     that no user interaction is required / allowed when changing to this
     *                     aggregation type
     */
    public function get_aggregation_js_function(): ?string {
        return null;
    }

}
