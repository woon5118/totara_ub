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
 * @package engage_survey
 */

use engage_survey\entity\survey as survey_entity;
use engage_survey\totara_engage\resource\survey;
use totara_engage\resource\resource_item;
use core\orm\query\builder;
use engage_survey\entity\survey_question;
use totara_engage\entity\question;

/**
 * Behat steps to generate survey related data.
 *
 */
class behat_engage_survey extends behat_base {

    /**
     * @param string $name
     * @return resource_item
     */
    public static function get_item_by_name(string $name): resource_item {
        $builder = builder::table(survey_entity::TABLE, 's')
            ->map_to(survey_entity::class)
            ->join([survey_question::TABLE, 'sq'], 'sq.surveyid', '=', 's.id')
            ->join([question::TABLE, 'q'], 'q.id', '=', 'sq.questionid')
            ->where('q.value', $name)
            ->order_by('sq.timecreated');

        /** @var survey_entity $survey */
        $survey = $builder->first();

        return survey::from_instance($survey->id, survey::get_resource_type());
    }

}