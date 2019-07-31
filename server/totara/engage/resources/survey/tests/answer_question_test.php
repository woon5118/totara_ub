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
 * @package engage_survey
 */

defined('MOODLE_INTERNAL') || die();

class engage_survey_answer_question_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_answer_a_question(): void {
        global $DB;

        $gen = $this->getDataGenerator();

        $user = $gen->create_user();
        $this->setUser($user);

        /** @var engage_survey_generator $surveygen */
        $surveygen = $gen->get_plugin_generator('engage_survey');
        $survey = $surveygen->create_survey();

        $questions = $survey->get_questions();
        $question = reset($questions);

        $options = $question->get_answer_options();
        $optionids = [];

        foreach ($options as $option) {
            $optionids[] = $option->id;
        }

        $survey->add_answer($question->get_id(), $optionids);
        $sql = '
            SELECT * FROM "ttr_engage_answer_choice" WHERE userid = :userid AND questionid = :questionid
        ';

        $params = [
            'userid' => $user->id,
            'questionid' => $question->get_id()
        ];

        $this->assertTrue(
            $DB->record_exists_sql($sql, $params)
        );
    }
}