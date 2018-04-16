<?php
/**
 * This file is part of Totara LMS
 *
 * Copyright (C) 2017 onwards Totara Learning Solutions LTD
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

defined('MOODLE_INTERNAL') || die();

use block_totara_featured_links\tile\base;
use block_totara_featured_links\tile\gallery_tile;

/**
 * Copies the file that is required from a gallery tile to a new static tile.
 *
 * @param gallery_tile $oldtile
 * @param base $newtile
 */
function split_gallery_tiles_into_subtiles() {
    global $DB;
    $fs = get_file_storage();

    $sql = 'SELECT *  FROM {block_totara_featured_links_tiles} btflt
                WHERE ' . $DB->sql_compare_text('type', 100) . '=\'block_totara_featured_links-gallery_tile\'';
    $gallerytiles = $DB->get_records_sql($sql);
    foreach ($gallerytiles as $gallerytilerow) {
        $gallerytile = base::get_tile_instance($gallerytilerow);
        $context = \context_block::instance($gallerytile->blockid);

        foreach ($gallerytile->data->background_imgs as $background_img) {
            $newtile = clone($gallerytile);

            $newtile->id = block_totara_featured_links\tile\default_tile::add($newtile->blockid, $gallerytile->id)->id;
            unset($newtile->data->background_imgs);
            unset($newtile->type);
            $newtile->parentid = $gallerytile->id;
            $newtile->data->background_img = $background_img;



            $files = $fs->get_area_files($context->id, 'block_totara_featured_links', 'tile_backgrounds');
            $oldfile = null;
            foreach ($files as $file) {
                if ($file->get_filename() == $newtile->data->background_img) {
                    $oldfile = $file;
                    break;
                }
            }
            if (!isset($oldfile)) {
                continue;
            }
            $newfile = [
                'contextid' => $context->id,
                'component' => 'block_totara_featured_links',
                'filearea' => 'tile_background',
                'itemid' => $newtile->id,
                'filepath' => '/',
                'filename' => $newtile->data->background_img
            ];
            $fs->create_file_from_storedfile($newfile, $oldfile);

            // Encodes the data into the raw fields read to be saved.
            $newtile->save_content($newtile->data);
            $DB->update_record('block_totara_featured_links_tiles', $newtile);

        }
        $fs = get_file_storage();
        $fs->delete_area_files(
            context_block::instance($gallerytile->blockid)->id,
            'block_totara_featured_links',
            'tile_backgrounds'
        );
    }
}