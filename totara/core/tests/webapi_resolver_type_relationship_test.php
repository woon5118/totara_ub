<?php
/*
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package totara_core
 */

use core\date_format;
use core\format;
use totara_core\entities\relationship as relationship_entity;
use totara_core\relationship\relationship;
use totara_webapi\phpunit\webapi_phpunit_helper;

require_once(__DIR__ . '/relationship_resolver_test.php');

/**
 * @covers \totara_core\webapi\resolver\type\relationship
 * @group totara_core_relationship
 */
class totara_core_webapi_resolver_query_relationships_testcase extends advanced_testcase {

    use webapi_phpunit_helper;

    private const TYPE = 'totara_core_relationship';

    public function test_resolve_relationship_type_fields() {
        self::setAdminUser();

        $relationship = relationship::create([test_resolver_three::class]);

        $relationship_entity = new relationship_entity($relationship->id);
        $relationship_entity->created_at = 12345;
        $relationship_entity->save();
        $relationship = relationship::load_by_entity($relationship_entity);

        $this->assertEquals($relationship->id, $this->resolve_graphql_type(
            self::TYPE, 'id', $relationship, []
        ));

        $this->assertEquals('12345', $this->resolve_graphql_type(
            self::TYPE, 'created_at', $relationship, ['format' => date_format::FORMAT_TIMESTAMP]
        ));

        // Test XSS formatting
        $this->assertEquals('resolver_threealert(\'Bad!\')', $this->resolve_graphql_type(
            self::TYPE, 'name', $relationship, ['format' => format::FORMAT_PLAIN]
        ));
        $this->assertEquals('resolver_three<script>alert(\'Bad!\')</script>', $this->resolve_graphql_type(
            self::TYPE, 'name', $relationship, ['format' => format::FORMAT_RAW]
        ));
    }

}
