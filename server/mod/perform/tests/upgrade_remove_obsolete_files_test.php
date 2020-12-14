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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package mod_perform
 */

use core\orm\query\builder;
use mod_perform\entity\activity\activity as activity_entity;
use mod_perform\entity\activity\element as element_entity;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\element;
use mod_perform\models\activity\section_element;

require_once(__DIR__ . '/../db/upgradelib.php');

/**
 * @group perform
 */
class upgrade_remove_obsolete_files_testcase extends advanced_testcase {

    private function perform_generator() {
        return self::getDataGenerator()->get_plugin_generator('mod_perform');
    }

    public function test_remove_obsolete_files() {
        self::setAdminUser();

        $generator = $this->perform_generator();
        $activity_to_keep = $generator->create_activity_in_container();
        $activity_to_delete = $generator->create_activity_in_container();

        $file_to_keep = $this->create_static_element_with_file($activity_to_keep);
        $file_to_delete = $this->create_static_element_with_file($activity_to_delete);

        $fs = get_file_storage();
        self::assertTrue($fs->file_exists_by_hash($file_to_delete->get_pathnamehash()));
        self::assertTrue($fs->file_exists_by_hash($file_to_keep->get_pathnamehash()));

        // Upgrade shouldn't delete anything.
        mod_perform_upgrade_remove_obsolete_static_content_element_files();
        self::assertTrue($fs->file_exists_by_hash($file_to_delete->get_pathnamehash()));
        self::assertTrue($fs->file_exists_by_hash($file_to_keep->get_pathnamehash()));

        // Delete one activity.
        activity_entity::repository()->where('id', $activity_to_delete->get_id())->delete();
        // Upgrade still shouldn't delete anything because the file's element still exists.
        mod_perform_upgrade_remove_obsolete_static_content_element_files();
        self::assertTrue($fs->file_exists_by_hash($file_to_delete->get_pathnamehash()));
        self::assertTrue($fs->file_exists_by_hash($file_to_keep->get_pathnamehash()));

        // Delete the activity's elements.
        builder::create()
            ->from(element_entity::TABLE, 'element')
            ->where('context_id', $activity_to_delete->context_id)
            ->delete();
        // Upgrade should delete the file because activity and element don't exist.
        mod_perform_upgrade_remove_obsolete_static_content_element_files();
        self::assertFalse($fs->file_exists_by_hash($file_to_delete->get_pathnamehash()));
        self::assertTrue($fs->file_exists_by_hash($file_to_keep->get_pathnamehash()));
    }

    /**
     * @param activity $activity
     * @return stored_file
     */
    private function create_static_element_with_file(activity $activity): stored_file {
        $section = $this->perform_generator()->create_section($activity);
        $element = element::create(
            $activity->get_context(),
            'static_content',
            'test element 1 title',
            'test identifier',
            '{}',
            true
        );
        section_element::create($section, $element, 123);

        $fs = get_file_storage();
        $file_record = [
            'component' => 'performelement_static_content',
            'filearea' => 'mod_perform',
            'itemid' => $element->id,
            'filepath' => '/',
            'filename' => 'test.txt'
        ];

        return $fs->create_file_from_string(
            array_merge($file_record, ['contextid' => $activity->context_id]),
            'Test text'
        );
    }
}
