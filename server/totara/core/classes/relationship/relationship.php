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

namespace totara_core\relationship;

use coding_exception;
use core\orm\entity\model;
use core\orm\query\builder;
use totara_core\entities\relationship as relationship_entity;
use totara_core\entities\relationship_resolver as relationship_resolver_entity;
use totara_core\relationship\resolvers\subject;

/**
 * A dynamically defined way of identifying the many users that are associated with a single user based on a given input.
 * @link https://help.totaralearning.com/display/PROD/Relationships Documentation
 *
 * @property-read int $id
 * @property-read int $created_at
 * @property-read string $name
 *
 * @package totara_core\relationship
 */
final class relationship extends model {

    /**
     * @var relationship_entity
     */
    protected $entity;

    protected $entity_attribute_whitelist = [
        'id',
        'created_at',
    ];

    protected $model_accessor_whitelist = [
        'name',
        'name_plural',
    ];

    /**
     * @return string|relationship_entity
     */
    protected static function get_entity_class(): string {
        return relationship_entity::class;
    }

    /**
     * Get this relationship's name.
     *
     * @return string
     */
    public function get_name(): string {
        // In the future, relationships can be user (admin) specified, including their names. (Stored in the DB)
        // But until then, the name of a relationship will just be the name of their first resolver.
        return $this->get_resolvers()[0]::get_name();
    }

    /**
     * Get this relationship's plural version of it's name.
     *
     * @return string
     */
    public function get_name_plural(): string {
        // In the future, relationships can be user (admin) specified, including their names. (Stored in the DB)
        // But until then, the name of a relationship will just be the name of their first resolver.
        return $this->get_resolvers()[0]::get_name_plural();
    }

    /**
     * Get the timestamp for when this relationship was created.
     *
     * @return int
     */
    public function get_date_created(): int {
        return $this->entity->created_at;
    }

    /**
     * Get the users from the given raw data.
     *
     * @param array $data e.g. ['job_assignment_id' => 2]
     *
     * @return int[]
     */
    public function get_users(array $data): array {
        $user_ids = [];
        foreach ($this->get_resolvers() as $relationship_resolver) {
            $user_ids[] = $relationship_resolver::get_users($data);
        }
        $all_user_ids = array_merge(...$user_ids);
        return array_unique($all_user_ids);
    }

    /**
     * Resolver classes associated with this relationship.
     *
     * @return string[]|relationship_resolver[] Relationship resolver class names.
     */
    public function get_resolvers(): array {
        return $this->entity->resolvers->pluck('class_name');
    }

    /**
     * Create a new relationship.
     *
     * @param string[] $resolver_class_names Array of relationship resolver class names, e.g. [subject::class, manager::class]
     * @return relationship
     */
    public static function create(array $resolver_class_names): self {
        self::validate_resolvers($resolver_class_names);

        $relationship = builder::get_db()->transaction(static function () use ($resolver_class_names) {
            $relationship = new relationship_entity();
            $relationship->save();

            foreach ($resolver_class_names as $resolver_class_name) {
                $relationship_resolver = new relationship_resolver_entity();
                $relationship_resolver->relationship_id = $relationship->id;
                $relationship_resolver->class_name = $resolver_class_name;
                $relationship_resolver->save();
            }

            return $relationship;
        });

        return self::load_by_entity($relationship);
    }

    /**
     * Delete this relationship.
     */
    public function delete(): void {
        $this->entity->delete();
    }

    /**
     * Make sure the specified array contains valid subclasses of relationship_resolver.
     *
     * @param array $resolver_class_names
     * @throws coding_exception
     */
    private static function validate_resolvers(array $resolver_class_names): void {
        if (empty($resolver_class_names)) {
            throw new coding_exception('Must specify at least one relationship resolver!');
        }

        foreach ($resolver_class_names as $resolver_class_name) {
            if (!is_a($resolver_class_name, relationship_resolver::class, true)) {
                throw new coding_exception($resolver_class_name . ' must be an instance of ' . relationship_resolver::class);
            }
        }

        if (!relationship_resolver::are_resolvers_compatible(...$resolver_class_names)) {
            throw new coding_exception(
                'The specified resolvers do not share at least one common input and are therefore incompatible.'
            );
        }
    }

    /**
     * Can the specified fields be accepted as input to this relationship?
     *
     * @param string[] $fields fields to check e.g. ['job_assignment_id'] or ['user_id', 'course_id']
     * @return bool
     */
    public function is_acceptable_input(array $fields): bool {
        if (empty($fields)) {
            throw new coding_exception('Must specify at least one field to relationship::is_acceptable_input()');
        }

        foreach ($this->get_resolvers() as $resolver) {
            if (!$resolver::is_acceptable_input($fields)) {
                return false;
            }
        }

        return true;
    }

}
