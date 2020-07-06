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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 * @category test
 */

use mod_perform\models\activity\relationship;
use totara_core\entities\relationship as core_relationship_entity;
use totara_core\entities\relationship_resolver as relationship_resolver_entity;

/**
 * @group perform
 */
class mod_perform_relationship_model_testcase extends advanced_testcase{

    /**
     * @param string $core_relationship_resolver_class
     * @param bool $expected_result
     * @dataProvider is_subject_provider
     */
    public function test_is_subject(string $core_relationship_resolver_class, bool $expected_result): void {
        $core_relationship_entity = $this->get_core_relationship_entity_from_resolver($core_relationship_resolver_class);

        $relationship = new relationship($core_relationship_entity);

        self::assertEquals($expected_result, $relationship->get_is_subject());
    }

    public function is_subject_provider(): array {
        return [
            'Subject' => [totara_core\relationship\resolvers\subject::class, true],
            'Manager' => [totara_job\relationship\resolvers\manager::class, false],
            'Appraiser' => [totara_job\relationship\resolvers\appraiser::class, false],
        ];
    }

    protected function get_core_relationship_entity_from_resolver(string $resolver_class_name): core_relationship_entity {
        /** @var relationship_resolver_entity $resolver */
        $resolver = relationship_resolver_entity::repository()->where('class_name', $resolver_class_name)
            ->order_by('id')
            ->first();

        return $resolver->relationship;
    }

}