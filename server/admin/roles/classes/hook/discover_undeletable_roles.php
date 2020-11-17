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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author  Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package core_role
 */

namespace core_role\hook;

/**
 * Hook for monitoring of system roles setting changes which are assigned under User Policies
 */
class discover_undeletable_roles extends \totara_core\hook\base {

    /**
     * @var array of strings
     */
    private $undeletable_roles = [];

    public function __construct(array $undeletable_roles) {
        $this->undeletable_roles = $undeletable_roles;
    }

    public function get_undeletable_roles(): array {
        return $this->undeletable_roles;
    }

    /**
     * Add another role
     * @param int $roleid
     * @param string $config_name
     * @param string $label
     */
    public function add_undeletable_role(int $roleid, string $config_name, string $label): void {
        $this->undeletable_roles[$roleid] = [
            'config_name' => $config_name,
            'label' => $label,
        ];
    }

    /**
     * Is this role can't be delete
     * @param int $roleid
     * @return bool
     */
    public function is_role_undeletable(int $roleid): bool {
        return isset($this->undeletable_roles[$roleid]);
    }

    /**
     * Return the label where the role is assigned
     * @param int $roleid
     * @return string
     */
    public function get_undeletable_role_label(int $roleid): string {
        return isset($this->undeletable_roles[$roleid]['label']) ?
            $this->undeletable_roles[$roleid]['label'] :
            '';
    }

    /**
     * Return the role config name, i.e. managerroleid
     * @param int $roleid
     * @return string
     */
    public function get_undeletable_role_config_name(int $roleid): string {
        return isset($this->undeletable_roles[$roleid]['config_name']) ?
            $this->undeletable_roles[$roleid]['config_name'] :
            '';
    }
}