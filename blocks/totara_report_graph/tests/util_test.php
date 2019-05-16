<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2016 onwards Totara Learning Solutions LTD
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
 * @author Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @package block_totara_report_graph
 */

/**
 * Test the util class for report graph block.
 *
 * @author Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @package block_totara_report_graph
 */
class block_totara_report_graph_util_testcase extends advanced_testcase {

    use \block_totara_report_graph\phpunit\block_testing;

    /**
     * Test the util normalise_size_and_user_input method.
     */
    public function test_normalise_size_and_user_input() {

        // First up test valid values:
        $this->assertSame('64px', \block_totara_report_graph\util::normalise_size_and_user_input('64'));
        $this->assertSame('64px', \block_totara_report_graph\util::normalise_size_and_user_input('64px'));
        $this->assertSame('64em', \block_totara_report_graph\util::normalise_size_and_user_input('64em'));
        $this->assertSame('64%', \block_totara_report_graph\util::normalise_size_and_user_input('64%'));
        $this->assertSame('64.32px', \block_totara_report_graph\util::normalise_size_and_user_input('64.32'));
        $this->assertSame('64.32px', \block_totara_report_graph\util::normalise_size_and_user_input('64.32px'));
        $this->assertSame('64.32em', \block_totara_report_graph\util::normalise_size_and_user_input('64.32em'));
        $this->assertSame('64.32%', \block_totara_report_graph\util::normalise_size_and_user_input('64.32%'));
        $this->assertSame('0.32px', \block_totara_report_graph\util::normalise_size_and_user_input('0.32'));
        $this->assertSame('0.32px', \block_totara_report_graph\util::normalise_size_and_user_input('0.32 px'));
        $this->assertSame('0.32px', \block_totara_report_graph\util::normalise_size_and_user_input('.32 PX'));
        $this->assertSame('64px', \block_totara_report_graph\util::normalise_size_and_user_input('64PX'));
        $this->assertSame('64px', \block_totara_report_graph\util::normalise_size_and_user_input('64pX'));
        $this->assertSame('64px', \block_totara_report_graph\util::normalise_size_and_user_input('64 PX'));
        $this->assertSame('64px', \block_totara_report_graph\util::normalise_size_and_user_input('64  px'));
        $this->assertSame('64px', \block_totara_report_graph\util::normalise_size_and_user_input(' 64PX'));
        $this->assertSame('64px', \block_totara_report_graph\util::normalise_size_and_user_input('64px '));
        $this->assertSame('64px', \block_totara_report_graph\util::normalise_size_and_user_input('  64  PX  '));
        $this->assertSame('64px', \block_totara_report_graph\util::normalise_size_and_user_input('  64  '));
        $this->assertSame('', \block_totara_report_graph\util::normalise_size_and_user_input('0'));
        $this->assertSame('', \block_totara_report_graph\util::normalise_size_and_user_input('0px'));
        $this->assertSame('', \block_totara_report_graph\util::normalise_size_and_user_input('0em'));
        $this->assertSame('', \block_totara_report_graph\util::normalise_size_and_user_input('0 px'));
        $this->assertSame('', \block_totara_report_graph\util::normalise_size_and_user_input('-0%'));
        $this->assertSame('', \block_totara_report_graph\util::normalise_size_and_user_input('0000px'));
        $this->assertSame('-75px', \block_totara_report_graph\util::normalise_size_and_user_input('-75px'));
        $this->assertSame('-75px', \block_totara_report_graph\util::normalise_size_and_user_input('-0075px'));
        $this->assertSame('75px', \block_totara_report_graph\util::normalise_size_and_user_input('0075px'));
        $this->assertSame('0.0075px', \block_totara_report_graph\util::normalise_size_and_user_input('.0075px'));
        $this->assertSame('0.75px', \block_totara_report_graph\util::normalise_size_and_user_input('00.75px'));
        $this->assertSame('', \block_totara_report_graph\util::normalise_size_and_user_input('00.00'));
        $this->assertSame('', \block_totara_report_graph\util::normalise_size_and_user_input(''));
        $this->assertSame('', \block_totara_report_graph\util::normalise_size_and_user_input('   '));

        // Now test invalid values:
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('px'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('em'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('%'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('64px;'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('64em;'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('64%;'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('0;'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('64pxpx'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('64pxem'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('64emem'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('64%%'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('64%64%'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('%%64'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('64fu'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('64.'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('64.px'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('.64.'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('..64'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('64..32'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('64.32.'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('64.3.2'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('.64.32'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('--64'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('64-'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('6-4'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('-'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('.'));
        $this->assertSame(null, \block_totara_report_graph\util::normalise_size_and_user_input('sixty four pixels'));
    }

}