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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package container_workspace
 */

namespace container_workspace\webapi\middleware;

use Closure;
use container_workspace\exception\workspace_exception;
use container_workspace\workspace;
use core\webapi\middleware;
use core\webapi\resolver\payload;
use core\webapi\resolver\result;
use core_container\factory;
use container_workspace\interactor\workspace\interactor as workspace_interactor;

/**
 * Use this middleware if your request contains a workspace id argument and requires the user to have access to the workspace.
 * If the workspace id is not correctly set or empty, or the current user doesn't have access to the workspace an exception will be thrown.
 */
class require_workspace_members_access implements middleware {

    /**
     * @var string
     */
    protected $workspace_id_argument_name;

    /**
     * @param string $workspace_id_argument_name the argument name for the workspace id
     */
    public function __construct(string $workspace_id_argument_name) {
        $this->workspace_id_argument_name = $workspace_id_argument_name;
    }

    /**
     * @inheritDoc
     */
    public function handle(payload $payload, Closure $next): result {
        global $USER;

        $workspace_id = $payload->get_variable($this->workspace_id_argument_name);
        if (empty($workspace_id)) {
            throw workspace_exception::on_view();
        }

        /** @var workspace $workspace */
        $workspace = factory::from_id($workspace_id);
        if (!$workspace->is_typeof(workspace::get_type())) {
            throw workspace_exception::on_view();
        }

        $workspace_interactor = new workspace_interactor($workspace, $USER->id);
        if (!$workspace_interactor->can_view_members()) {
            throw workspace_exception::on_view();
        }

        return $next($payload);
    }

}
