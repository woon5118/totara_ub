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


use totara_competency\entities\scale_value;
use totara_competency\entities\competency_pathway_achievement;

abstract class pathway_aggregation {

    /** @var string $agg_type Type of aggregatioon. Obtained from class */
    private $agg_type;

    /** @var int[] Scale value ids */
    private $achieved_value_id;

    /** @var pathway_achievement[][] */
    private $achieved_via;

    /**
     * @var int[] IDs of users to be aggregated.
     */
    private $user_ids;

    /**
     * @var pathway[]
     */
    private $pathways;


    /**
     * Constructor.
     */
    final public function __construct() {
        $reflect = new \ReflectionClass($this);
        $this->agg_type = $reflect->getShortName();
    }

    public function get_agg_type(): string {
        return $this->agg_type;
    }

    public function set_user_ids(array $user_ids): pathway_aggregation {
        if (isset($this->user_ids)) {
            throw new \coding_exception('Users already set. This can only be done once.');
        }
        $this->user_ids = $user_ids;

        return $this;
    }

    public function set_pathways(array $pathways): pathway_aggregation {
        if (isset($this->pathways)) {
            throw new \coding_exception('Pathways already set. This can only be done once.');
        }
        $this->pathways = $pathways;

        return $this;
    }

    private function initialise_achieved() {
        if (!isset($this->user_ids)) {
            throw new \coding_exception('Users have not been set.');
        }
        $this->achieved_value = [];
        $this->achieved_via = [];
        foreach ($this->user_ids as $user_id) {
            $this->achieved_value_id[$user_id] = null;
            $this->achieved_via[$user_id] = [];
        }
    }

    /**
     * @param \stdClass $user
     * @param pathway[] $pathways
     */
    public function aggregate(): pathway_aggregation {
        $this->initialise_achieved();
        $this->do_aggregation();

        return $this;
    }

    /**
     * @return int[]
     */
    protected function get_user_ids(): array {
        return $this->user_ids;
    }

    /**
     * @return pathway[]
     */
    protected function get_pathways(): array {
        return $this->pathways;
    }

    protected function set_achieved_value_id($user_id, ?int $scale_value = null) {
        $this->achieved_value_id[$user_id] = $scale_value;
    }

    protected function set_achieved_via($user_id, array $achieved_via) {
        $this->achieved_via[$user_id] = $achieved_via;
    }

    protected abstract function do_aggregation();

    public function get_achieved_value_id($user_id): ?int {
        if (!isset($this->achieved_value_id)) {
            throw new \coding_exception('Aggregation has not been run');
        }
        if (!array_key_exists($user_id, $this->achieved_value_id)) {
            throw new \coding_exception('User ' . $user_id . ' was not added for aggregation');
        }
        return $this->achieved_value_id[$user_id];
    }

    /**
     * @return pathway_achievement[]
     */
    public function get_achieved_via($user_id): array {
        if (!isset($this->achieved_via)) {
            throw new \coding_exception('Aggregation has not been run');
        }
        if (!array_key_exists($user_id, $this->achieved_via)) {
            throw new \coding_exception('User ' . $user_id . ' was not added for aggregation');
        }
        return $this->achieved_via[$user_id];
    }

    public function get_title(): string {
        $namespace = (new \ReflectionClass($this))->getNamespaceName();
        return get_string('title', $namespace);
    }

    /**
     * Get the description of the aggregation type for display purposes
     *
     * @return string
     */
    public function get_description(): string {
        $namespace = (new \ReflectionClass($this))->getNamespaceName();
        return get_string('description', $namespace);
    }

    /**
     * Return the name of the javascript function handling pathway aggration editing
     *
     * @return ?string Javascript function name. In v1, this must be the name of an existing
     *                function in achievement_paths.js. Null or an empty string indicates
     *                that no user interaction is required / allowed when changing to this
     *                aggregation type
     */
    public function get_aggregation_js_function(): ?string {
        return null;
    }

}