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

use core\entities\user;
use totara_competency\entities\competency;
use totara_competency\entities\scale;

abstract class pathway_manual_base_testcase extends advanced_testcase {

    /**
     * @var totara_competency_generator
     */
    protected $generator;

    /**
     * @var user
     */
    protected $user1;

    /**
     * @var user
     */
    protected $user2;

    /**
     * @var competency
     */
    protected $competency1;

    /**
     * @var competency
     */
    protected $competency2;

    /**
     * @var scale
     */
    protected $scale1;

    /**
     * @var scale
     */
    protected $scale2;

    /**
     * Create testing data.
     */
    protected function setUp() {
        $this->generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');

        $this->user1 = new user($this->getDataGenerator()->create_user(), false);
        $this->user2 = new user($this->getDataGenerator()->create_user(), false);

        $this->competency1 = $this->generator->create_competency();
        $this->competency2 = $this->generator->create_competency();

        $this->scale1 = $this->generator->create_scale('1', '1', [
            ['name' => '11', 'proficient' => false, 'default' => true, 'sortorder' => 1],
            ['name' => '12', 'proficient' => true, 'default' => false, 'sortorder' => 2],
        ]);
        $this->scale2 = $this->generator->create_scale('2', '2', [
            ['name' => '21', 'proficient' => false, 'default' => true, 'sortorder' => 1],
            ['name' => '22', 'proficient' => true, 'default' => false, 'sortorder' => 2],
        ]);
    }

    /**
     * Unset the testing data.
     */
    protected function tearDown() {
        $this->generator = null;
        $this->user1 = null;
        $this->user2 = null;
        $this->competency1 = null;
        $this->competency2 = null;
        $this->scale1 = null;
        $this->scale2 = null;
    }

}
