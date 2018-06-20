<?php
/*
 * This file is part of Totara Learn
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
 * @author Alastair Munro <alastair.munro@totaralearning.com>
 * @package core_course
 */

namespace core_course\rb\source;

defined('MOODLE_INTERNAL') || die();

trait report_trait {

    /**
     * Adds some common course info to the $columnoptions array
     *
     * @param array &$columnoptions Array of current column options
     *                              Passed by reference and updated by
     *                              this method
     * @param string $join Name of the join that provides the 'course' table
     *
     * @return True
     */
    protected function add_core_course_columns(&$columnoptions, $join='course') {
        global $DB;

        $columnoptions[] = new \rb_column_option(
            'course',
            'fullname',
            get_string('coursename', 'totara_reportbuilder'),
            "$join.fullname",
            array('joins' => $join,
                  'dbdatatype' => 'char',
                  'outputformat' => 'text')
        );
        $columnoptions[] = new \rb_column_option(
            'course',
            'courselink',
            get_string('coursenamelinked', 'totara_reportbuilder'),
            "$join.fullname",
            array(
                'joins' => $join,
                'displayfunc' => 'link_course',
                'defaultheading' => get_string('coursename', 'totara_reportbuilder'),
                'extrafields' => array('course_id' => "$join.id",
                                       'course_visible' => "$join.visible",
                                       'course_audiencevisible' => "$join.audiencevisible")
            )
        );
        $columnoptions[] = new \rb_column_option(
            'course',
            'courseexpandlink',
            get_string('courseexpandlink', 'totara_reportbuilder'),
            "$join.fullname",
            array(
                'joins' => $join,
                'displayfunc' => 'course_expand',
                'defaultheading' => get_string('coursename', 'totara_reportbuilder'),
                'extrafields' => array(
                    'course_id' => "$join.id",
                    'course_visible' => "$join.visible",
                    'course_audiencevisible' => "$join.audiencevisible"
                )
            )
        );
        $columnoptions[] = new \rb_column_option(
            'course',
            'courselinkicon',
            get_string('coursenamelinkedicon', 'totara_reportbuilder'),
            "$join.fullname",
            array(
                'joins' => $join,
                'displayfunc' => 'link_course_icon',
                'defaultheading' => get_string('coursename', 'totara_reportbuilder'),
                'extrafields' => array(
                    'course_id' => "$join.id",
                    'course_icon' => "$join.icon",
                    'course_visible' => "$join.visible",
                    'course_audiencevisible' => "$join.audiencevisible"
                )
            )
        );
        $columnoptions[] = new \rb_column_option(
            'course',
            'visible',
            get_string('coursevisible', 'totara_reportbuilder'),
            "$join.visible",
            array(
                'joins' => $join,
                'displayfunc' => 'yes_no'
            )
        );
        $columnoptions[] = new \rb_column_option(
            'course',
            'audvis',
            get_string('audiencevisibility', 'totara_reportbuilder'),
            "$join.audiencevisible",
            array(
                'joins' => $join,
                'displayfunc' => 'audience_visibility'
            )
        );
        $columnoptions[] = new \rb_column_option(
            'course',
            'icon',
            get_string('courseicon', 'totara_reportbuilder'),
            "$join.icon",
            array(
                'joins' => $join,
                'displayfunc' => 'course_icon',
                'defaultheading' => get_string('courseicon', 'totara_reportbuilder'),
                'extrafields' => array(
                    'course_name' => "$join.fullname",
                    'course_id' => "$join.id",
                )
            )
        );
        $columnoptions[] = new \rb_column_option(
            'course',
            'shortname',
            get_string('courseshortname', 'totara_reportbuilder'),
            "$join.shortname",
            array('joins' => $join,
                  'dbdatatype' => 'char',
                  'outputformat' => 'text')
        );
        $columnoptions[] = new \rb_column_option(
            'course',
            'idnumber',
            get_string('courseidnumber', 'totara_reportbuilder'),
            "$join.idnumber",
            array('joins' => $join,
                  'displayfunc' => 'plaintext',
                  'dbdatatype' => 'char',
                  'outputformat' => 'text')
        );
        $columnoptions[] = new \rb_column_option(
            'course',
            'id',
            get_string('courseid', 'totara_reportbuilder'),
            "$join.id",
            array('joins' => $join)
        );
        $columnoptions[] = new \rb_column_option(
            'course',
            'timecreated',
            get_string('coursedatecreated', 'totara_reportbuilder'),
            "$join.timecreated",
            array(
                'joins' => $join,
                'displayfunc' => 'nice_date',
                'dbdatatype' => 'timestamp'
            )
        );
        $columnoptions[] = new \rb_column_option(
            'course',
            'startdate',
            get_string('coursestartdate', 'totara_reportbuilder'),
            "$join.startdate",
            array(
                'joins' => $join,
                'displayfunc' => 'nice_date',
                'dbdatatype' => 'timestamp'
            )
        );
        $columnoptions[] = new \rb_column_option(
            'course',
            'name_and_summary',
            get_string('coursenameandsummary', 'totara_reportbuilder'),
            // Case used to merge even if one value is null.
            "CASE WHEN $join.fullname IS NULL THEN $join.summary
                WHEN $join.summary IS NULL THEN $join.fullname
                ELSE " . $DB->sql_concat("$join.fullname", "'" . \html_writer::empty_tag('br') . "'",
                    "$join.summary") . ' END',
            array(
                'joins' => $join,
                'displayfunc' => 'editor_textarea',
                'extrafields' => array(
                    'filearea' => '\'summary\'',
                    'component' => '\'course\'',
                    'context' => '\'context_course\'',
                    'recordid' => "$join.id"
                )
            )
        );
        $columnoptions[] = new \rb_column_option(
            'course',
            'summary',
            get_string('coursesummary', 'totara_reportbuilder'),
            "$join.summary",
            array(
                'joins' => $join,
                'displayfunc' => 'editor_textarea',
                'extrafields' => array(
                    'format' => "$join.summaryformat",
                    'filearea' => '\'summary\'',
                    'component' => '\'course\'',
                    'context' => '\'context_course\'',
                    'recordid' => "$join.id"
                ),
                'dbdatatype' => 'text',
                'outputformat' => 'text'
            )
        );
        $columnoptions[] = new \rb_column_option(
            'course',
            'coursetypeicon',
            get_string('coursetypeicon', 'totara_reportbuilder'),
            "$join.coursetype",
            array(
                'joins' => $join,
                'displayfunc' => 'course_type_icon',
                'defaultheading' => get_string('coursetypeicon', 'totara_reportbuilder'),
            )
        );
        $columnoptions[] = new \rb_column_option(
            'course',
            'coursetype',
            get_string('coursetype', 'totara_reportbuilder'),
            "$join.coursetype",
            array(
                'joins' => $join,
                'displayfunc' => 'course_type',
                'defaultheading' => get_string('coursetype', 'totara_reportbuilder'),
            )
        );
        // add language option
        $columnoptions[] = new \rb_column_option(
            'course',
            'language',
            get_string('courselanguage', 'totara_reportbuilder'),
            "$join.lang",
            array(
                'joins' => $join,
                'displayfunc' => 'language_code'
            )
        );

        return true;
    }

    /**
     * Adds some common course filters to the $filteroptions array
     *
     * @param array &$filteroptions Array of current filter options
     *                              Passed by reference and updated by
     *                              this method
     * @return True
     */
    protected function add_core_course_filters(&$filteroptions) {
        $filteroptions[] = new \rb_filter_option(
            'course',
            'fullname',
            get_string('coursename', 'totara_reportbuilder'),
            'text'
        );
        $filteroptions[] = new \rb_filter_option(
            'course',
            'shortname',
            get_string('courseshortname', 'totara_reportbuilder'),
            'text'
        );
        $filteroptions[] = new \rb_filter_option(
            'course',
            'idnumber',
            get_string('courseidnumber', 'totara_reportbuilder'),
            'text'
        );
        $audvisibility = get_config(null, 'audiencevisibility');
        if (empty($audvisibility)) {
            $coursevisiblestring = get_string('coursevisible', 'totara_reportbuilder');
            $audvisiblilitystring = get_string('audiencevisibilitydisabled', 'totara_reportbuilder');
        } else {
            $coursevisiblestring = get_string('coursevisibledisabled', 'totara_reportbuilder');
            $audvisiblilitystring = get_string('audiencevisibility', 'totara_reportbuilder');
        }
        $filteroptions[] = new \rb_filter_option(
            'course',
            'visible',
            $coursevisiblestring,
            'select',
            array(
                'selectchoices' => array(0 => get_string('no'), 1 => get_string('yes')),
                'simplemode' => true
            )
        );
        $filteroptions[] = new \rb_filter_option(
            'course',
            'audvis',
            $audvisiblilitystring,
            'select',
            array(
                'selectchoices' => array(
                    COHORT_VISIBLE_NOUSERS => get_string('visiblenousers', 'totara_cohort'),
                    COHORT_VISIBLE_ENROLLED => get_string('visibleenrolled', 'totara_cohort'),
                    COHORT_VISIBLE_AUDIENCE => get_string('visibleaudience', 'totara_cohort'),
                    COHORT_VISIBLE_ALL => get_string('visibleall', 'totara_cohort')),
                'simplemode' => true
            )
        );
        $filteroptions[] = new \rb_filter_option(
            'course',
            'timecreated',
            get_string('coursedatecreated', 'totara_reportbuilder'),
            'date',
            array('castdate' => true)
        );
        $filteroptions[] = new \rb_filter_option(
            'course',
            'startdate',
            get_string('coursestartdate', 'totara_reportbuilder'),
            'date',
            array('castdate' => true)
        );
        $filteroptions[] = new \rb_filter_option(
            'course',
            'name_and_summary',
            get_string('coursenameandsummary', 'totara_reportbuilder'),
            'textarea'
        );
        $filteroptions[] = new \rb_filter_option(
            'course',
            'coursetype',
            get_string('coursetype', 'totara_reportbuilder'),
            'multicheck',
            array(
                'selectfunc' => 'course_types',
                'simplemode' => true,
                'showcounts' => array(
                        'joins' => array("LEFT JOIN {course} coursetype_filter ON base.id = coursetype_filter.id"),
                        'dataalias' => 'coursetype_filter',
                        'datafield' => 'coursetype')
            )
        );
        $filteroptions[] = new \rb_filter_option(
            'course',
            'language',
            get_string('courselanguage', 'totara_reportbuilder'),
            'select',
            array(
                'selectfunc' => 'course_languages',
                'attributes' => \rb_filter_option::select_width_limiter(),
            )
        );
        $filteroptions[] = new \rb_filter_option(
            'course',
            'id',
            get_string('coursemultiitem', 'totara_reportbuilder'),
            'course_multi'
        );
        return true;
    }

    /**
     * Adds the course table to the $joinlist array
     *
     * @param array &$joinlist Array of current join options
     *                         Passed by reference and updated to
     *                         include new table joins
     * @param string $join Name of the join that provides the
     *                     'course id' field
     * @param string $field Name of course id field to join on
     * @param string $jointype Type of Join (INNER, LEFT, RIGHT)
     */
    protected function add_core_course_tables(&$joinlist, $join, $field, $jointype = 'LEFT') {

        $joinlist[] = new \rb_join(
            'course',
            $jointype,
            '{course}',
            "course.id = $join.$field",
            REPORT_BUILDER_RELATION_ONE_TO_ONE,
            $join
        );
    }

    /**
     * Adds the course_category table to the $joinlist array
     *
     * @param array &$joinlist Array of current join options
     *                         Passed by reference and updated to
     *                         include course_category
     * @param string $join Name of the join that provides the 'course' table
     * @param string $field Name of category id field to join on
     * @return boolean True
     */
    protected function add_core_course_category_tables(&$joinlist,
        $join, $field) {

        $joinlist[] = new \rb_join(
            'course_category',
            'LEFT',
            '{course_categories}',
            "course_category.id = $join.$field",
            REPORT_BUILDER_RELATION_MANY_TO_ONE,
            $join
        );

        return true;
    }


    /**
     * Adds some common course category info to the $columnoptions array
     *
     * @param array &$columnoptions Array of current column options
     *                              Passed by reference and updated by
     *                              this method
     * @param string $catjoin Name of the join that provides the
     *                        'course_categories' table
     * @param string $coursejoin Name of the join that provides the
     *                           'course' table
     * @return True
     */
    protected function add_core_course_category_columns(&$columnoptions,
        $catjoin='course_category', $coursejoin='course', $column='coursecount') {
        $columnoptions[] = new \rb_column_option(
            'course_category',
            'name',
            get_string('coursecategory', 'totara_reportbuilder'),
            "$catjoin.name",
            array('joins' => $catjoin,
                  'dbdatatype' => 'char',
                  'outputformat' => 'text')
        );
        $columnoptions[] = new \rb_column_option(
            'course_category',
            'namelink',
            get_string('coursecategorylinked', 'totara_reportbuilder'),
            "$catjoin.name",
            array(
                'joins' => $catjoin,
                'displayfunc' => 'link_course_category',
                'defaultheading' => get_string('category', 'totara_reportbuilder'),
                'extrafields' => array('cat_id' => "$catjoin.id",
                                        'cat_visible' => "$catjoin.visible",
                                        $column => "{$catjoin}.{$column}")
            )
        );
        $columnoptions[] = new \rb_column_option(
            'course_category',
            'idnumber',
            get_string('coursecategoryidnumber', 'totara_reportbuilder'),
            "$catjoin.idnumber",
            array(
                'joins' => $catjoin,
                'displayfunc' => 'plaintext',
                'dbdatatype' => 'char',
                'outputformat' => 'text'
            )
        );
        $columnoptions[] = new \rb_column_option(
            'course_category',
            'id',
            get_string('coursecategoryid', 'totara_reportbuilder'),
            "$coursejoin.category",
            array('joins' => $coursejoin)
        );
        return true;
    }


    /**
     * Adds some common course category filters to the $filteroptions array
     *
     * @param array &$columnoptions Array of current filter options
     *                              Passed by reference and updated by
     *                              this method
     * @return True
     */
    protected function add_core_course_category_filters(&$filteroptions) {
        $filteroptions[] = new \rb_filter_option(
            'course_category',
            'id',
            get_string('coursecategory', 'totara_reportbuilder'),
            'select',
            array(
                'selectfunc' => 'course_categories_list',
                'attributes' => \rb_filter_option::select_width_limiter(),
            )
        );
        $filteroptions[] = new \rb_filter_option(
            'course_category',
            'path',
            get_string('coursecategorymultichoice', 'totara_reportbuilder'),
            'category',
            array(),
            'course_category.path',
            'course_category'
        );
        return true;
    }
}
