<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2015 onwards Totara Learning Solutions LTD
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
 * @author Alastair Munro <alastair.munro@totaralms.com>
 * @package totara_appraisal
 * @category  test
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode;
use Behat\Behat\Exception\PendingException as PendingException;

class behat_totara_appraisal extends behat_base {

    /**
     * Creates the specified element. More info about available elements in http://docs.moodle.org/dev/Acceptance_testing#Fixtures.
     *
     * @Given /^I create "([0-9]*)" appraisal questions on the page "([^"]*)"$/
     *
     * @throws Exception
     * @throws PendingException
     * @param string    $elementname The name of the entity to add
     * @param string    $component The Frankenstyle name of the plugin
     * @param TableNode $data
     */
    public function create_appraisal_questions_on_page($numberofquestions, $page) {
        global $DB;

        $datagenerator = testing_util::get_data_generator()->get_plugin_generator('totara_appraisal');

        $page = $DB->get_record('appraisal_stage_page', array('name' => $page));

        for ($i = 1; $i <= $numberofquestions; $i++) {
            $datagenerator->create_complex_question($page->id);
        }
    }


}
