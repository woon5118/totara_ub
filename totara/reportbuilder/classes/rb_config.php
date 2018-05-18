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
 * @author Yuliya Bozhko <yuliya.bozhko@totaralearning.com>
 * @package totara_reportbuilder
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class rb_config
 */
class rb_config {

    /**
     * @var array data to be passed to the embedded object constructor
     */
    private $embeddata = [];

    /**
     * @var int Saved search ID if displaying a saved search
     */
    private $sid = 0;

    /**
     * @var int ID of a user the report is generated for.
     */
    private $reportfor = 0;

    /**
     * @var bool Force no cache usage. Only works if cache for current report is enabled
     *           and generated
     */
    private $nocache = false;

    /**
     * @var rb_global_restriction_set global report restrictions info
     */
    private $globalrestrictionset;

    /**
     * @return array
     */
    public function get_embeddata(): ?array {
        return $this->embeddata;
    }

    /**
     * @param array $embeddata
     */
    public function set_embeddata(array $embeddata = null): void {
        $this->embeddata = $embeddata;
    }

    /**
     * @return int
     */
    public function get_sid(): ?int {
        return $this->sid;
    }

    /**
     * @param int $sid
     */
    public function set_sid(int $sid = null): void {
        $this->sid = $sid;
    }

    /**
     * @return int
     */
    public function get_reportfor(): ?int {
        return $this->reportfor;
    }

    /**
     * @param int $reportfor
     */
    public function set_reportfor(int $reportfor = null): void {
        $this->reportfor = $reportfor;
    }

    /**
     * @return bool
     */
    public function get_nocache(): ?bool {
        return $this->nocache;
    }

    /**
     * @param bool $nocache
     */
    public function set_nocache(bool $nocache = null): void {
        $this->nocache = $nocache;
    }

    /**
     * @return rb_global_restriction_set
     */
    public function get_global_restriction_set(): ?rb_global_restriction_set {
        return $this->globalrestrictionset;
    }

    /**
     * @param rb_global_restriction_set $globalrestrictionset
     */
    public function set_global_restriction_set(rb_global_restriction_set $globalrestrictionset = null): void {
        $this->globalrestrictionset = $globalrestrictionset;
    }
}
