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
 * @package mod_perform
 */

use core\webapi\execution_context;
use core\webapi\mutation_resolver;
use core\webapi\query_resolver;
use totara_core\advanced_feature;
use totara_core\feature_not_available_exception;

use totara_webapi\phpunit\webapi_phpunit_helper;

/**
 * @group perform
 */
class mod_perform_advanced_feature_disable_testcase extends advanced_testcase {
    use webapi_phpunit_helper;

    protected function setUp() {
        parent::setUp();
        advanced_feature::disable('performance_activities');
    }

    private function get_webapi_mutation_provider(): array {
        $result = [];
        $mutations = core_component::get_namespace_classes('webapi\\resolver\\mutation', mutation_resolver::class, 'mod_perform');
        foreach ($mutations as $mutation) {
            $result[] = [$mutation];
        }
        return $result;
    }

    public function test_webapi_mutators_throw_error_if_feature_is_disabled() {
        /** @var mutation_resolver[] $mutators */
        $mutators = core_component::get_namespace_classes('webapi\\resolver\\mutation', mutation_resolver::class, 'mod_perform');
        $this->assertGreaterThan(0, count($mutators));

        foreach ($mutators as $mutator) {
            try {
                $operation = str_replace('\\webapi\\resolver\\mutation\\', '_', $mutator);
                $this->resolve_graphql_mutation($operation);
                $this->fail('Mutator ' . $mutator . ' must call advanced_feature::require(\'performance_activities\')');
            } catch (feature_not_available_exception $exception) {
                continue;
            } catch (Exception $exception) {
                $this->fail('Mutator ' . $mutator . ' must call advanced_feature::require(\'performance_activities\')');
            }
        }
    }

    public function test_webapi_queries_throw_error_if_feature_is_disabled() {
        /** @var mutation_resolver[] $queries */
        $queries = core_component::get_namespace_classes('webapi\\resolver\\query', query_resolver::class, 'mod_perform');
        $this->assertGreaterThan(0, $queries);

        foreach ($queries as $query) {
            try {
                $operation = str_replace('\\webapi\\resolver\\query\\', '_', $query);
                $this->resolve_graphql_query($operation);
                $this->fail('Query' . $query . ' must call advanced_feature::require(\'performance_activities\')');
            } catch (\totara_core\feature_not_available_exception $exception) {
                continue;
            } catch (Exception $exception) {
                $this->fail('Query' . $query . ' must call advanced_feature::require(\'performance_activities\')');
            }
        }
    }

    public function test_controllers_throw_error_if_feature_is_disabled() {
        $controllers = $this->get_controller_classes();
        $this->assertGreaterThan(0, count($controllers));

        foreach ($controllers as $controller) {
            try {
                (new $controller())->process();
                $this->fail('Controller ' . $controller . ' must call advanced_feature::require(\'performance_activities\')');
            } catch (feature_not_available_exception $exception) {
                continue;
            } catch (Exception $exception) {
                $this->fail('Controller ' . $controller . ' must call advanced_feature::require(\'performance_activities\')');
            }
        }
    }

    /**
     * @return \totara_mvc\controller[]
     */
    private function get_controller_classes(): array {
        return array_filter(
            array_keys(core_component::get_component_classes_in_namespace('mod_perform')),
            static function (string $class_name) {
                return strpos($class_name, 'mod_perform\\controllers') !== false &&
                    !(new ReflectionClass($class_name))->isAbstract();
            }
        );
    }

    /**
     * Helper to get execution context
     *
     * @param string $type
     * @param string|null $operation
     * @return execution_context
     */
    private function get_execution_context(string $type = 'dev', ?string $operation = null): execution_context {
        return execution_context::create($type, $operation);
    }

}
