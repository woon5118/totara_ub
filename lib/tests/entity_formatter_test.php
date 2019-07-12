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
 * @package core_orm
 * @category test
 */

use core\date_format;
use core\format;
use core\orm\entity\formatter;
use totara_core\formatter\field\date_field_formatter;
use totara_core\formatter\field\string_field_formatter;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir. '/orm/tests/entity_testcase.php');

class core_orm_entity_formatter_testcase extends entity_testcase {

    public function test_formatter() {
        $time = 1561672231;
        $time2 = 1561682231;

        $data = [
            'name' => '<span class="test">Test Entity</span>',
            'type' => 1,
            'is_deleted' => 0,
            'params' => 'foobar',
            'created_at' => $time,
            'updated_at' => $time2
        ];

        $entity = new sample_entity($data, false);

        $formatter = new class($entity, context_system::instance()) extends formatter {
            protected function get_map(): array {
                return [
                    'name' => string_field_formatter::class,
                    'type' => null,
                    'parent_id' => null,
                    'is_deleted' => null,
                    'params' => 'format_params',
                    'created_at' => date_field_formatter::class,
                    'updated_at' => date_field_formatter::class
                ];
            }

            protected function format_params($value, $format) {
                return $value.'/'.$format;
            }
        };

        $this->assertEquals('Test Entity', $formatter->format('name', format::FORMAT_HTML));
        $this->assertEquals($entity->type, $formatter->format('type'));
        $this->assertEquals($entity->is_deleted, $formatter->format('is_deleted'));
        $this->assertEquals('foobar/format', $formatter->format('params', 'format'));
        $this->assertEquals('2019-06-28T05:50:31+0800', $formatter->format('created_at', date_format::FORMAT_ISO8601));
        $this->assertEquals('2019-06-28T08:37:11+0800', $formatter->format('updated_at', date_format::FORMAT_ISO8601));

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Unknown field idontexist');

        $formatter->format('idontexist');
    }


}