<?php
/*
 * This file is part of Totara LMS
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
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_facetoface
 */

namespace mod_facetoface\detail;

defined('MOODLE_INTERNAL') || die();

use stdClass;
use context;
use context_system;
use moodle_url;
use mod_facetoface_renderer;
use rb_facetoface_summary_asset_embedded;
use mod_facetoface\asset;
use mod_facetoface\output\seminarresource_card;
use mod_facetoface\seminar_attachment_item;
use mod_facetoface\seminar_session;

/**
 * Generate asset details.
 */
class asset_content extends content_generator {
    /**
     * Constructor.
     * @param string $idparam a parameter name that represents 'id'
     * @param string|moodle_url $pageurl the URL to this page
     */
    public function __construct(string $idparam, $pageurl) {
        parent::__construct($idparam, 'modfacetofaceassets', 'facetoface_summary_asset', $pageurl);
    }

    protected function load(int $id): seminar_attachment_item {
        return new asset($id);
    }

    protected function get_title(seminar_attachment_item $item): string {
        /** @var asset $item */
        return $item->get_name();
    }

    protected function has_edit_capability(seminar_attachment_item $item, context $context, stdClass $user): bool {
        // The managesitewide capability is always system level.
        return has_capability('mod/facetoface:managesitewideassets', context_system::instance(), $user);
    }

    protected function has_report_capability(seminar_attachment_item $item, context $context, stdClass $user): bool {
        return rb_facetoface_summary_asset_embedded::is_capable_static($user->id);
    }

    protected function get_report_header(seminar_attachment_item $item): string {
        return get_string('upcomingsessionsinasset', 'mod_facetoface');
    }

    protected function render_details(seminar_attachment_item $item, stdClass $user, mod_facetoface_renderer $renderer): string {
        return $renderer->render_asset_details($item);
    }

    protected function render_empty(moodle_url $manageurl): string {
        return get_string('reports:selectasset', 'mod_facetoface', $manageurl->out());
    }

    protected function render_card(?seminar_session $session, seminar_attachment_item $item, stdClass $user, mod_facetoface_renderer $renderer): ?seminarresource_card {
        return null;
    }

    protected function get_manage_button(bool $frommanage): string {
        if ($frommanage) {
            return get_string('backtoassets', 'mod_facetoface');
        } else {
            return get_string('viewallassets', 'mod_facetoface');
        }
    }

    protected function get_manage_url(bool $frommanage): moodle_url {
        return new moodle_url('/mod/facetoface/asset/manage.php');
    }

    protected function get_edit_button(seminar_attachment_item $item): string {
        return get_string('editasset', 'mod_facetoface');
    }

    protected function get_edit_url(seminar_attachment_item $item): ?moodle_url {
        /** @var asset $item */
        if ($item->get_custom()) {
            return null;
        } else {
            return new moodle_url('/mod/facetoface/asset/edit.php', ['id' => $item->get_id()]);
        }
    }
}
