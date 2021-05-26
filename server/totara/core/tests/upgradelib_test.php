<?php
/*
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
 * @author  Petr Skoda <petr.skoda@totaralearning.com>
 * @package totara_core
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Test functions in totara/core/db/upgradelib.php
 */
class totara_core_upgradelib_testcase extends advanced_testcase {
    public function test_totara_core_upgrade_fix_role_risks(): void {
        global $CFG, $DB;
        require_once("{$CFG->dirroot}/totara/core/db/upgradelib.php");

        $initialcaps = $DB->get_records_menu('capabilities', [], 'name ASC', 'name, riskbitmask');
        totara_core_upgrade_fix_role_risks();
        $this->assertSame($initialcaps, $DB->get_records_menu('capabilities', [], 'name ASC', 'name, riskbitmask'));

        $DB->set_field('capabilities', 'riskbitmask', RISK_SPAM | RISK_PERSONAL | RISK_XSS | RISK_CONFIG | RISK_DATALOSS, ['name' => 'moodle/site:config']);
        $DB->set_field('capabilities', 'riskbitmask', RISK_CONFIG, ['name' => 'totara/core:appearance']);
        $DB->set_field('capabilities', 'riskbitmask', RISK_PERSONAL | RISK_ALLOWXSS, ['name' => 'moodle/backup:backupcourse']);
        $DB->set_field('capabilities', 'riskbitmask', RISK_CONFIG | RISK_ALLOWXSS | RISK_ALLOWXSS, ['name' => 'totara/core:appearance']);
        totara_core_upgrade_fix_role_risks();
        $this->assertSame($initialcaps, $DB->get_records_menu('capabilities', [], 'name ASC', 'name, riskbitmask'));

        // Make sure missing caps are skipped and extra ignored.
        $oldcap = $DB->get_record('capabilities', ['name' => 'totara/core:appearance']);
        unset($oldcap->id);
        $oldcap->name = 'totara/core:xappearance';
        $DB->insert_record('capabilities', $oldcap);
        $DB->delete_records('capabilities', ['name' => 'totara/core:appearance']);
        $initialcaps = $DB->get_records_menu('capabilities', [], 'name ASC', 'name, riskbitmask');
        totara_core_upgrade_fix_role_risks();
        $this->assertSame($initialcaps, $DB->get_records_menu('capabilities', [], 'name ASC', 'name, riskbitmask'));
    }

    /**
     * Test that the refresh default category only recreates the misc
     * category as a last resort.
     *
     * covers totara_core_refresh_default_category
     */
    public function test_totara_core_refresh_default_category(): void {
        global $DB;

        // 1) First lets check the expected defaults.
        $cats = $DB->get_records('course_categories', [], 'sortorder');

        $misc = $cats['1'];
        $this->assertSame('Miscellaneous', $misc->name);
        $this->assertSame('0', $misc->issystem);
        $this->assertSame('10000', $misc->sortorder);

        $perf = $cats['2'];
        $this->assertSame('performance-activities', $perf->name);
        $this->assertSame('1', $perf->issystem);
        $this->assertSame('20000', $perf->sortorder);

        $work = $cats['3'];
        $this->assertSame('Space category', $work->name);
        $this->assertSame('1', $work->issystem);
        $this->assertSame('30000', $work->sortorder);

        // 2) Then check that calling it with the default categories does nothing.
        totara_core_refresh_default_category();
        $cat2 = $DB->get_records('course_categories', [], 'sortorder');
        $this->assertCount(3, $cat2);
        foreach ($cats as $key => $cat) {
            if ($new = $cat2[$key]) {
                $this->assertSame($cat->id, $new->id);
                $this->assertSame($cat->name, $new->name);
                $this->assertSame($cat->issystem, $new->issystem);
                $this->assertSame($cat->sortorder, $new->sortorder);
            } else {
                $this->fail("Expected category not found: {$cat->name}");
            }
        }
        $this->assertSame($misc->id, get_config('core', 'defaultrequestcategory'));

        // 3) Then check that calling it after replacing the default misc does nothing.
        $misc2 = $this->totara_core_insert_category('Misc2', '40000', null, false);
        $cats[$misc2->id] = $misc2;
        set_config('defaultrequestcategory', $misc2->id);

        totara_core_refresh_default_category();
        $cat3 = $DB->get_records('course_categories', [], 'sortorder');
        $this->assertCount(4, $cat3);
        foreach ($cats as $key => $cat) {
            if ($new = $cat3[$key]) {
                $this->assertSame($cat->id, $new->id);
                $this->assertSame($cat->name, $new->name);
                $this->assertSame($cat->issystem, $new->issystem);
                $this->assertSame($cat->sortorder, $new->sortorder);
            } else {
                $this->fail("Expected category not found: {$cat->name}");
            }
        }
        $this->assertSame($misc2->id, get_config('core', 'defaultrequestcategory'));

        // 4) Then check that calling it with misc deleted after being correctly replaced does nothing.
        unset($cats[$misc->id]);
        $DB->delete_records('course_categories', ['id' => $misc->id]);
        totara_core_refresh_default_category();
        $cat4 = $DB->get_records('course_categories', [], 'sortorder');
        $this->assertCount(3, $cat4);
        foreach ($cats as $key => $cat) {
            if ($new = $cat4[$key]) {
                $this->assertSame($cat->id, $new->id);
                $this->assertSame($cat->name, $new->name);
                $this->assertSame($cat->issystem, $new->issystem);
                $this->assertSame($cat->sortorder, $new->sortorder);
            } else {
                $this->fail("Expected category not found: {$cat->name}");
            }
        }
        $this->assertSame($misc2->id, get_config('core', 'defaultrequestcategory'));

        // 5) - Now check it replaces a bad default with an existing category if possible.
        set_config('defaultrequestcategory', $perf->id);
        totara_core_refresh_default_category();
        $cat4 = $DB->get_records('course_categories', [], 'sortorder');
        $this->assertCount(3, $cat4);
        foreach ($cats as $key => $cat) {
            if ($new = $cat4[$key]) {
                $this->assertSame($cat->id, $new->id);
                $this->assertSame($cat->name, $new->name);
                $this->assertSame($cat->issystem, $new->issystem);
                $this->assertSame($cat->sortorder, $new->sortorder);
            } else {
                $this->fail("Expected category not found: {$cat->name}");
            }
        }
        $this->assertSame($misc2->id, get_config('core', 'defaultrequestcategory'));

        // 6) - Finally check that when all else fails it recreates misc and sets it as default.
        set_config('defaultrequestcategory', $perf->id);
        unset($cats[$misc2->id]);
        $DB->delete_records('course_categories', ['id' => $misc2->id]);
        totara_core_refresh_default_category();
        $cat5 = $DB->get_records('course_categories', [], 'sortorder');
        $this->assertCount(3, $cat5);
        foreach ($cat5 as $key => $cat) {
            if (isset($cats[$key]) && $old = $cats[$key]) {
                $this->assertSame($cat->id, $old->id);
                $this->assertSame($cat->name, $old->name);
                $this->assertSame($cat->issystem, $old->issystem);
                $this->assertSame($cat->sortorder, $old->sortorder);
            } else {
                // This should be our newly recreated misc category.
                $this->assertSame('Miscellaneous', $cat->name);
                $this->assertSame('0', $cat->issystem);
                $this->assertSame($cat->id, get_config('core', 'defaultrequestcategory'));
            }
        }
    }

    /**
     * Note: That programs and certifications sort order does not seem to be affected by
     *       the category sortorder in the same way that courses is.
     *
     * covers totara_core_fix_course_sortorder
     * covers totara_core_fix_category_sortorder
     */
    public function test_totara_core_fix_course_sortorder(): void {
        global $DB;

        // 1) First fetch and check the default categories.
        $cats = $DB->get_records('course_categories');
        $misc = $cats['1'];
        $this->assertSame('Miscellaneous', $misc->name);

        $perf = $cats['2'];
        $this->assertSame('performance-activities', $perf->name);

        $work = $cats['3'];
        $this->assertSame('Space category', $work->name);

        // 2) Check that calling the function when there are no issues changes nothing.
        totara_core_fix_course_sortorder();
        $cat2 = $DB->get_records('course_categories', [], 'sortorder');
        $this->assertCount(3, $cat2);
        foreach ($cats as $key => $cat) {
            if ($new = $cat2[$key]) {
                $this->assertSame($cat->id, $new->id);
                $this->assertSame($cat->name, $new->name);
                $this->assertSame($cat->issystem, $new->issystem);
                $this->assertSame($cat->sortorder, $new->sortorder);
            } else {
                $this->fail("Expected category not found: {$cat->name}");
            }
        }

        // 3 - Set up some complicated dummy data with system categories throughout.
        // Misc (aka Top 1)
        //    | -> Course 100
        //    | -> SubCat T1S1
        //             | -> Course 111
        //    | -> SubCat T1S2 (system)
        //    | -> SubCat T1S3
        //             | -> Course 131
        // (system perf)
        // (system work)
        // Top 2
        //     | -> Course 2000
        //     | -> SubCat T2S1
        //               | -> Course 2100
        //               | -> SubCat T2S1.1
        //                         | -> Course 2111
        //                         | -> Course 2112
        //               | -> SubCat T2S1.2 (system)
        //               | -> SubCat T2S1.3
        //                         | -> course 2131
        //     | -> SubCat T2S2 (system)
        //     | -> SubCat T2S3
        //              | -> Course 2310
        // Top 3
        //     | -> Course 3.1

        $t1s1 = $this->totara_core_insert_category('Category T1S1', '20000', $misc, false);
        $c111 = $this->totara_core_insert_course('c111', $t1s1, '20001');
        $t1s2 = $this->totara_core_insert_category('Category T1S2', '30000', $misc, true);
        $t1s3 = $this->totara_core_insert_category('Category T1S3', '40000', $misc, false);
        $c131 = $this->totara_core_insert_course('c131', $t1s3, '40001');

        $perf->sortorder = '50000'; // Fix perf sortorder.
        $DB->update_record('course_categories', $perf);
        $work->sortorder = '60000'; // Fix work sortorder.
        $DB->update_record('course_categories', $work);

        $t2 = $this->totara_core_insert_category('Category Top2', '70000', null, false);
        $c200 = $this->totara_core_insert_course('c2000', $t2, '70001');

        $t2s1 = $this->totara_core_insert_category('Category T2S1', '80000', $t2, false);
        $c210 = $this->totara_core_insert_course('c2100', $t2s1, '80001');

        $t2s11 = $this->totara_core_insert_category('SubCat T2S1.1', '90000', $t2s1, false);
        $c2111 = $this->totara_core_insert_course('c2111', $t2s11, '90001');
        $c2112 = $this->totara_core_insert_course('c2112', $t2s11, '90002');

        $t2s12 = $this->totara_core_insert_category('SubCat T2S1.2', '100000', $t2s1, true);
        $t2s13 = $this->totara_core_insert_category('SubCat T2S1.3', '110000', $t2s1, false);
        $c2131 = $this->totara_core_insert_course('c2131', $t2s13, '110001');

        $t2s2 = $this->totara_core_insert_category('Category T2S2', '120000', $t2, true);

        $t2s3 = $this->totara_core_insert_category('Category T2S3', '130000', $t2, false);
        $c231 = $this->totara_core_insert_course('c231', $t2s3, '130001');

        $t3 = $this->totara_core_insert_category('Category Top3', '140000', null, false);
        $c300 = $this->totara_core_insert_course('c300', $t3, '140001');

        // Rebuild context paths.
        context_helper::build_all_paths(false);

        // 3) Check that all system categories have been ordered to the back of their teir.
        // Misc (aka Top 1)
        //    | -> Course 100
        //    | -> SubCat T1S1
        //             | -> Course 111
        //    | -> SubCat T1S3
        //             | -> Course 131
        //    | -> SubCat T1S2 (system)
        // Top 2
        //     | -> Course 2000
        //     | -> SubCat T2S1
        //               | -> Course 2100
        //               | -> SubCat T2S1.1
        //                         | -> Course 2111
        //                         | -> Course 2112
        //               | -> SubCat T2S1.3
        //                         | -> course 2131
        //               | -> SubCat T2S1.2 (system)
        //     | -> SubCat T2S3
        //              | -> Course 2310
        //     | -> SubCat T2S2 (system)
        // Top 3
        //     | -> Course 3.1
        // (system perf)
        // (system work)

        // Set up the order we expect to see everything.
        $expectations = [
            '10000' => $misc,
            '20000' => $t1s1,
            '30000' => $t1s3,
            '40000' => $t1s2,
            '50000' => $t2,
            '60000' => $t2s1,
            '70000' => $t2s11,
            '80000' => $t2s13,
            '90000' => $t2s12,
            '100000' => $t2s3,
            '110000' => $t2s2,
            '120000' => $t3,
            '130000' => $perf,
            '140000' => $work
        ];

        totara_core_fix_course_sortorder();
        $cat3 = $DB->get_records('course_categories', [], 'sortorder');
        foreach ($expectations as $sortorder => $cat) {
            $found = array_shift($cat3);

            $this->assertSame($found->name, $cat->name);
            $this->assertEquals($found->sortorder, $sortorder);

            $courses = $DB->get_records('course', ['category' => $found->id], 'sortorder');
            $this->assertCount($cat->coursecount, $courses);
            foreach ($courses as $course) {
                // Check that the courses sort order is within still the expected bounds for the category.
                $this->assertGreaterThan($found->sortorder, $course->sortorder, 'course sortorder before category');
                $this->assertGreaterThan($course->sortorder, $found->sortorder + 10000, 'course sortorder after category'); // Magic number MAX_COURSES_IN_CATEGORY.
            }
        }

        // 4) As a last check, now we have good data, run it again and check nothing changes.
        totara_core_fix_course_sortorder();
        $cat4 = $DB->get_records('course_categories', [], 'sortorder');
        foreach ($expectations as $sortorder => $cat) {
            $found = array_shift($cat4);

            $this->assertSame($found->name, $cat->name);
            $this->assertEquals($found->sortorder, $sortorder);

            $courses = $DB->get_records('course', ['category' => $found->id], 'sortorder');
            $this->assertCount($cat->coursecount, $courses);
            foreach ($courses as $course) {
                // Check that the courses sort order is within still the expected bounds for the category.
                $this->assertGreaterThan($found->sortorder, $course->sortorder, 'course sortorder before category');
                $this->assertGreaterThan($course->sortorder, $found->sortorder + 10000, 'course sortorder after category'); // Magic number MAX_COURSES_IN_CATEGORY.
            }
        }
    }

    /**
     * A quick function to manually insert fairly accurate dummy categories
     * We don't want to use the api since we fixed it to create accurate data
     * and we need to test the upgrade on some moderately broken data
     *
     * @param string  $name      the name of the category
     * @param string  $sortorder the sort order of the category
     * @param object  $parent    the database record for the parent category)
     * @param boolean $system    whether the category is a system one or not)
     */
    private function totara_core_insert_category($name, $sortorder, $parent = null, $system = false) {
        global $DB;

        $category = new \stdClass();
        $category->name = $name;
        $category->idnumber = '';
        $category->sortorder = $sortorder;
        $category->parent = empty($parent) ? 0 : $parent->id;
        $category->depth = empty($parent) ? 1 : $parent->depth + 1;
        $category->visible = 1;
        $category->description = '';
        $category->descriptionformat = 0;
        $category->issystem = $system ? '1' : '0';
        $category->coursecount = 0;

        $category->id = (string) $DB->insert_record('course_categories', $category);
        $category->path = empty($parent) ? "/{$category->id}" : "{$parent->path}/{$category->id}";
        $DB->update_record('course_categories', $category);

        return $category;
    }

    /**
     * A quick function to manually insert reasonably accurate dummy courses
     * We don't want to use the api since we fixed it to create accurate data
     * and we need to test the upgrade on some moderately broken data
     *
     * @param string $name
     * @param object $category  The database object of the category we are inserting the course into
     * @param string $sortorder The sortorder of the course
     */
    private function totara_core_insert_course($name, &$category, $sortorder) {
        global $DB;

        $course = new \stdClass();
        $course->fullname = $name;
        $course->shortname = $name;
        $course->idnumber = '';
        $course->summary = '';
        $course->summaryformat = 0;
        $course->category = $category->id;
        $course->timecreated = time();
        $course->timemodified = time();
        $course->sortorder = $sortorder;

        $course->id = $DB->insert_record('course', $course);

        // Not sure we really need to but lets check this.
        $category->coursecount++;
        $DB->update_record('course_categories', $category);

        return $course;
    }

    public function test_totara_core_init_setting_disableconsistentcleaning() {
        global $CFG;
        require_once("{$CFG->dirroot}/totara/core/db/upgradelib.php");

        $CFG->disableconsistentcleaning = 1;
        totara_core_init_setting_disableconsistentcleaning();
        self::assertEquals(1, $CFG->disableconsistentcleaning);

        $CFG->disableconsistentcleaning = 0;
        totara_core_init_setting_disableconsistentcleaning();
        self::assertEquals(0, $CFG->disableconsistentcleaning);

        unset($CFG->disableconsistentcleaning);
        totara_core_init_setting_disableconsistentcleaning();
        self::assertEquals(1, $CFG->disableconsistentcleaning);
    }
}
