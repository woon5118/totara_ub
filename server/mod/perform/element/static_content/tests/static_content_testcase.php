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
 * @package performelement_static_element
 */

defined('MOODLE_INTERNAL') || die();

class performelement_static_content_testcase extends advanced_testcase {

    protected function create_element_data(): string {
        global $USER;

        // First get unused draft id.
        $draft_id = file_get_unused_draft_itemid();
        $context = \context_user::instance($USER->id);

        // Create a file in draft area.
        $data['wekaDoc'] = $this->create_document($draft_id, $context);
        $data['docFormat'] = 'FORMAT_JSON_EDITOR';
        $data['format'] = 'HTML';
        $data['draftId'] = $draft_id;
        $data = json_encode($data);

        return $data;
    }

    protected function create_document(int $draft_id, context $context): string {
        // Create a draft image.
        $draft = new \stdClass();
        $draft->filename = "test_file.png";
        $draft->filepath = '/';
        $draft->component = 'user';
        $draft->filearea = 'draft';
        $draft->itemid = $draft_id;
        $draft->contextid = $context->id;

        $fs = get_file_storage();
        $file = $fs->create_file_from_string($draft, 'blah blah');
        $url = \moodle_url::make_draftfile_url(
            $draft_id,
            $draft->filepath,
            $draft->filename
        )->out(false);

        return json_encode(
            [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'attachments',
                        'content' => [
                            [
                                'type' => 'attachment',
                                'attrs' => [
                                    'url' => $url,
                                    'filename' => $draft->filename,
                                    'size' => $file->get_filesize(),
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            JSON_UNESCAPED_SLASHES
        );
    }

}