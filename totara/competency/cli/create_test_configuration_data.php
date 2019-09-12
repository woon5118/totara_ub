<?php

use criteria_linkedcourses\metadata_processor;
use totara_competency\achievement_configuration;
use totara_competency\linked_courses;
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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_competency
 */

define('CLI_SCRIPT', 1);

require __DIR__.'/../../../config.php';
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->libdir . '/phpunit/classes/util.php');

global $DB;

$USER = get_admin();

$help =
"Competency Criteria test data generator

Options:
-sv, --scalevalues=NUMBER     Number of scalevalues to create in the test scale. Default = 0. If number == 0, the default competency scale is used.
-fw, --frameworks=NUMBER      Number of test frameworks to create. Default = 2.
-comp, --competencies=NUMBER  Number of competencies to create in each framework. Default = 3.
-c, --courses=NUMBER          Number of test courses to create. Default = 0.
-l, --linked=NUMBER           Number of courses to link to each competency. Default = 0. Ignored if number of test courses == 0.
-h, --help                    Print out this help
";

list($options, $unrecognized) = cli_get_params(
    array(
        'scalevalues'   => 0,
        'frameworks'    => 2,
        'competencies'  => 3,
        'courses'       => 0,
        'linked'        => 0,
        'help'          => false
    ),
    array(
        'sv' => 'scalevalues',
        'fw' => 'frameworks',
        'comp' => 'competencies',
        'c' => 'courses',
        'l' => 'linked',
        'h' => 'help'
    )
);

if ($options['help']) {
    echo $help;
    die;
}

$generator = phpunit_util::get_data_generator();
/** @var totara_hierarchy_generator $hierarchy_generator */
$hierarchy_generator = $generator->get_plugin_generator('totara_hierarchy');
/** @var totara_competency_generator $competency_generator */
$comp_generator = $generator->get_plugin_generator('totara_competency');

$num_scalevalues = $options['scalevalues'];
$num_fw = $options['frameworks'];
$num_comp = $options['competencies'];
$num_courses = $options['courses'];
$num_linked = empty($num_courses) ? 0 : $options['linked'];

printf("Creating test data for competency criteria:\n");
printf("\t%d courses\n", $num_courses);
printf("\t%d scale values\n", $num_scalevalues);
printf("\t%d Competency frameworks with %d competencies in each\n", $num_fw, $num_comp);
printf("\tLinking %d courses to each competency\n", $num_linked);

// Courses
$courses = create_courses($num_courses);
// Scale
$scale = create_scale($num_scalevalues);
// Competencies
$competencies = create_fw_competencies($num_fw, $num_comp, $scale);
//Linked courses
link_courses_to_competencies($num_linked, $competencies, $courses);
//Default preset criteria
link_default_preset_to_competencies($competencies);


function create_scale($num_scalevalues) {
    global $hierarchy_generator;

    if ($num_scalevalues == 0) {
        return null;
    }

    $scalevalues = [];
    for ($i = $num_scalevalues; $i >= 1; $i--) {
        $scalevalues[$i] = [
            'name' => "Level $i",
            'proficient' => ($i >= $num_scalevalues - 1),
            'sortorder' => $num_scalevalues - $i + 1,
            'default' => ($i == 1)];
    }

    $name = 'Test scale';
    printf("\nScale: [%s]\n", $name);
    $scale = $hierarchy_generator->create_scale(
        'comp',
        ['name' => $name, 'description' => 'Test scale'],
        $scalevalues
    );

    return $scale;
}

function create_fw_competencies($num_fw, $num_comp, $scale): array {
    global $hierarchy_generator, $comp_generator;

    printf("\n");
    $competencies = [];
    for ($i = 1; $i <= $num_fw; $i++) {
        $name = "Test framework $i";
        printf("Framework: [%s]\n", $name);

        $fw_params = ['fullname' => $name];
        if (!empty($scale) && !empty($scale->id)) {
            $fw_params['scale'] = $scale->id;
        }

        $fw = $hierarchy_generator->create_comp_frame($fw_params);

        for ($j = 1; $j <= $num_comp; $j++) {
            $name = "Test competency $i-$j";
            printf("\tCompetency: [%s]\n", $name);

            $competencies[] = $comp_generator->create_competency($name, $fw->id);
        }
    }

    return $competencies;
}

function create_courses($num_courses): array {
    global $generator;

    printf("\n");

    $courses = [];
    for ($i = 1; $i <= $num_courses; $i++) {
        $name = "Course $i";
        printf("Course: [%s]\n", $name);

        $record = [
            'shortname' => $name,
            'fullname' => $name,
        ];

        $courses[$i] = $generator->create_course($record);
    }

    return $courses;
}

function link_courses_to_competencies($num_linked, $competencies, $courses) {
    $num_courses = count($courses);
    $course_cnt = 0;

    printf("\nLinking courses:\n");
    foreach ($competencies as $comp) {
        $courses_to_link = [];

        printf("\tCompetency: [%s]\n", $comp->fullname);
        for ($i = 0; $i < $num_linked; $i++) {
            $linktype = ($i % 2) ? linked_courses::LINKTYPE_MANDATORY : linked_courses::LINKTYPE_MANDATORY;

            $idx = ($course_cnt % $num_courses) + 1;
            printf("\t\t[%s] - %s\n",
                $courses[$idx]->shortname,
                $linktype == linked_courses::LINKTYPE_MANDATORY ? 'mandatory' : 'optional'
            );

            $courses_to_link[] = [
                'id' => $courses[$idx]->id,
                'linktype' => $linktype,
            ];
            $course_cnt++;
        }

        linked_courses::set_linked_courses($comp->id, $courses_to_link);
    }
}

function link_default_preset_to_competencies($competencies) {
    printf("\nLinking the default preset to competencies:\n");

    foreach($competencies as $comp) {
        printf("\t%s\n", $comp->fullname);

        $config = new achievement_configuration($comp);
        $config->link_default_preset();
    }

    printf("\nCreating items for linked course criteria");
    metadata_processor::update_item_links(null);
}
