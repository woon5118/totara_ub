<?php
/**
 * This file is part of Totara LMS
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Andrew McGhie <andrew.mcghie@totaralearning.com>
 * @package block_totara_featured_links
 */

use block_totara_featured_links\tile\base;
use block_totara_featured_links\tile\default_tile;

defined('MOODLE_INTERNAL') || die();


/**
 * Class block_totara_featured_links_upgradelib_testcase
 * Tests the upgrade steps for the featured links block
 */
class block_totara_featured_links_upgradelib_testcase extends advanced_testcase {

    private function get_setup_data() {
        global $DB;
        $data = new class() {
            /** @var \block_totara_featured_links\tile\gallery_tile $oldgallerytile */
            public $oldgallerytile;
            /** @var block_totara_featured_links $block */
            public $block;
        };

        /** @var block_totara_featured_links_generator $blockgenerator */
        $blockgenerator = $this->getDataGenerator()->get_plugin_generator('block_totara_featured_links');
        $data->block = $blockgenerator->create_instance();

        $data->oldgallerytile = $blockgenerator->create_gallery_tile($data->block->id);

        $fs = get_file_storage();
        $file_record = [
            'contextid' => context_block::instance($data->block->id)->id,
            'component' => 'block_totara_featured_links',
            'filearea' => 'tile_backgrounds',
            'itemid' => $data->oldgallerytile->id,
            'filepath' => '/',
            'filename' => 'image1.png'
        ];
        $fs->create_file_from_string($file_record, 'test file');

        $file_record = [
            'contextid' => context_block::instance($data->block->id)->id,
            'component' => 'block_totara_featured_links',
            'filearea' => 'tile_backgrounds',
            'itemid' => $data->oldgallerytile->id,
            'filepath' => '/',
            'filename' => 'image2.png'
        ];
        $fs->create_file_from_string($file_record, 'test file');

        $databaserow = $DB->get_record('block_totara_featured_links_tiles', ['id' => $data->oldgallerytile->id]);
        $tiledata = json_decode($databaserow->dataraw);
        $tiledata->url = '/';
        $tiledata->heading = 'heading';
        $tiledata->textbody = 'text';
        $tiledata->background_imgs = ['image1.png', 'image2.png'];
        $databaserow->dataraw = json_encode($tiledata);
        $DB->update_record('block_totara_featured_links_tiles', $databaserow);

        $datastring = $DB->get_field('block_totara_featured_links_tiles', 'dataraw', ['id' => $data->oldgallerytile->id]);
        $this->assertContains('heading', $datastring);
        $this->assertContains('text', $datastring);
        $this->assertContains('/', $datastring);

        return $data;
    }

    /**
     * Tests that the upgrade step to 2018032600 will work correctly.
     */
    public function test_upgrading_a_gallery_tile_makes_correct_static_tiles() {
        global $DB, $CFG;
        $this->resetAfterTest();
        $data = $this->get_setup_data();

        require_once($CFG->dirroot.'/blocks/totara_featured_links/db/upgradelib.php');

        split_gallery_tiles_into_subtiles();
        $fs = get_file_storage();
        $newtiles = $DB->get_records('block_totara_featured_links_tiles', ['parentid' => $data->oldgallerytile->id]);
        $expectedimages = ['image1.png', 'image2.png'];
        foreach ($newtiles as $newtile) {
            /** @var default_tile $newtileinstance */
            $newtileinstance = base::get_tile_instance($newtile);
            $this->assertInstanceOf(default_tile::class, $newtileinstance);
            $this->assertEquals('heading', $newtileinstance->data->heading);
            $this->assertEquals('text', $newtileinstance->data->textbody);

            $key = array_search($newtileinstance->data->background_img, $expectedimages);
            if ($key !== false) {
                unset($expectedimages[$key]);
            } else {
                $this->fail('The image was not an expected value');
            }

            $this->assertNotFalse($fs->get_file(
                context_block::instance($data->block->id)->id,
                'block_totara_featured_links',
                'tile_background',
                $newtileinstance->id,
                '/',
                $newtileinstance->data->background_img
            ));
        }
        $this->assertEmpty($expectedimages);
    }
}