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
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package container_workspace
 */

namespace container_workspace\totara_engage\share\recipient;

use container_workspace\interactor\workspace\interactor;
use container_workspace\workspace;
use container_workspace\loader\workspace\loader as workspace_loader;
use core\orm\query\builder;
use core_container\factory;
use totara_core\advanced_feature;
use totara_engage\share\recipient\helper as recipient_helper;
use totara_engage\access\access;
use totara_engage\share\recipient\recipient;
use totara_engage\share\shareable;
use totara_engage\exception\share_exception;

class library extends recipient {

    /**
     * Area identifying this recipient.
     */
    public const AREA = 'LIBRARY';

    /**
     * @inheritDoc
     */
    public function validate(): void {
        global $USER;
        advanced_feature::require('container_workspace');

        // Make sure that this is a valid workspace.
        if (!workspace_loader::exists($this->instanceid)) {
            throw new \coding_exception("Invalid workspace with ID {$this->instanceid}");
        }

        $workspace = factory::from_id($this->instanceid);
        $type = workspace::get_type();
        if (!$workspace->is_typeof($type)) {
            throw new \coding_exception('invalid workspace instanceid');
        }

        $workspace_interactor = new interactor($workspace, $USER->id);
        if (!$workspace_interactor->can_share_resources()) {
            throw new share_exception('error:share_to_workspace', 'container_workspace');
        }
    }

    /**
     * @inheritDoc
     */
    public function get_label(): string {
        return get_string('spaces', 'container_workspace');
    }

    /**
     * @inheritDoc
     */
    public function get_summary(): string {
        return get_string('space', 'container_workspace');
    }

    /**
     * @inheritDoc
     */
    public function get_data() {
        global $USER;

        $workspace = $this->get_workspace();
        $interactor = new interactor($workspace, $USER->id);

        return [
            'category' => 'WORKSPACE',
            'fullname' => $workspace->get_name(),
            'imageurl' => $workspace->get_image(),
            'imagealt' => get_string('workspace_image_alt', 'container_workspace'),
            'unshare' => $interactor->can_unshare_resources()
        ];
    }

    /**
     * @return workspace
     */
    protected function get_workspace(): workspace {
        /** @var workspace $workspace */
        $workspace = factory::from_id($this->instanceid);

        if (!$workspace->is_typeof(workspace::get_type())) {
            throw new \coding_exception("Cannot find workspace with id '{$this->instanceid}'");
        }

        return $workspace;
    }

    /**
     * @inheritDoc
     */
    public function get_minimum_access(): int {
        $workspace = $this->get_workspace();
        if ($workspace->is_public()) {
            return access::PUBLIC;
        }

        if ($workspace->is_private() || $workspace->is_hidden()) {
            return access::RESTRICTED;
        }

        // This should never be happened - but who knows :shrug:
        throw new \coding_exception("Invalid workspace's access");
    }

    /**
     * @inheritDoc
     */
    public static function search(string $search, ?shareable $instance): array {
        global $DB, $USER;

        // If workspaces are disabled, search will always return nothing
        if (advanced_feature::is_disabled('container_workspace')) {
            return [];
        }

        $uniqueparam = $DB->get_unique_param();

        // Find all workspaces that this user has joined or any public workspaces.
        $builder = builder::table('course', 'c')
            ->left_join(['enrol', 'e'], 'e.courseid', 'c.id')
            ->left_join(['user_enrolments', 'ue'], 'ue.enrolid', 'e.id')
            ->left_join(['user', 'u'], 'u.id', 'ue.userid')
            ->where('c.category', workspace::get_default_category_id())
            ->where('c.containertype', workspace::get_type())
            ->where(function(builder $builder) use($USER) {
                // TODO: using visible flag for now until proper workspace access is implemented
                $builder->where('ue.userid', $USER->id)
                    ->or_where('c.visible', 1);
            })
            ->where_raw(
                $DB->sql_like('c.fullname', ":{$uniqueparam}", false),
                [$uniqueparam => "%{$search}%"]
            );

        $records = $builder->fetch();
        $recipients = [];

        /** @var \stdClass $record */
        foreach ($records as $record) {
            $recipients[] = new self($record->id);
        }

        return $recipients;
    }

    /**
     * @inheritDoc
     */
    public static function is_user_permitted(shareable $instance, int $user_id): bool {
        // If workspaces are disabled, the user is not permitted to share
        if (advanced_feature::is_disabled('container_workspace')) {
            return false;
        }

        // If this sharable instance is shared with a workspace that the user is a member of then
        // the user should be permitted.
        $builder = builder::table('engage_share', 'es')
            ->join(['engage_share_recipient', 'esr'], 'es.id', 'esr.shareid')
            ->join(['course', 'c'], 'esr.instanceid', 'c.id')
            ->join(['enrol', 'e'], 'e.courseid', 'c.id')
            ->join(['user_enrolments', 'ue'], 'ue.enrolid', 'e.id')
            ->join(['user', 'u'], 'u.id', 'ue.userid')
            ->where('es.itemid', $instance->get_id())
            ->where('es.component', $instance::get_resource_type())
            ->where('esr.area', library::AREA)
            ->where('esr.component', recipient_helper::get_component(static::class))
            ->where('c.containertype', 'container_workspace')
            ->where(function(builder $builder) use($user_id) {
                $builder->where_null('ue.userid')
                    ->or_where('ue.userid', $user_id)
                    ->where('ue.status', ENROL_USER_ACTIVE);
            });

        return $builder->exists();
    }

}