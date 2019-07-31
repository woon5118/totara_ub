<?php
/**
 * This file is part of Totara Learn
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package totara_topic
 */
namespace totara_topic\output;

use core\output\template;

/**
 * This output componen is used for rendering a box of action icons within a column
 * of a report build. It allows us to use the AMD modules for the interactions at FRONT-END
 */
final class report_actions extends template {
    /**
     * @param \moodle_url|null $deleteurl
     * @param \moodle_url|null $updateurl
     * @param int              $totalusage
     *
     * @return report_actions
     */
    public static function create(
        ?\moodle_url $deleteurl,
        ?\moodle_url $updateurl,
        int $totalusage = 0
    ): report_actions {
        $data = [
            'deleteurl' => null,
            'updateurl' => null,
            'hasusage' => false,
            'message' => null
        ];

        if (null != $deleteurl) {
            // Please do not escape these url
            $data['deleteurl'] = $deleteurl->out(false);
        }

        if (null != $updateurl) {
            $data['updateurl'] = $updateurl->out(false);
        }

        if (0 < $totalusage) {
            $data['hasusage'] = true;
            $data['message'] = get_string('confirmdeletewithusage', 'totara_topic', $totalusage);
        }

        return new static($data);
    }
}