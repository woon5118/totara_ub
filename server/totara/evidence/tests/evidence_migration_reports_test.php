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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package totara_evidence
 */

use core\orm\query\builder;

global $CFG;
require_once(__DIR__ . '/evidence_migration_test.php');
require_once($CFG->dirroot . '/totara/reportbuilder/lib.php');

/**
 * @group totara_evidence
 */
class totara_evidence_migration_reports_testcase extends totara_evidence_migration_testcase {

    /**
     * Make sure that user reports using the Record of Learning: Evidence report source are migrated to the
     * new report source and have their columns, filters and saved searches updated with the new options.
     */
    public function test_migrate_reports(): void {
        // Create a user report with all the available evidence columns
        $user_report_id = $this->create_report('report_test');
        $this->create_report_data($user_report_id, [
            'evidence' => [
                'name',
                'namelink',
                'viewevidencelink',
                'timecreated',
                'timemodified',
                'evidenceinuse',
                'actionlinks',
                'evidencetypename',
                'evidencetypeid',
            ],
            'user' => [
                'fullname',
                'institution',
            ],
            'job_assignment' => [
                'numjobassignments',
            ],
            'dp_plan_evidence' => [
                'custom_field_1',
                'custom_field_4',
            ]
        ]);

        // Create the old record of learning evidence embedded report
        $this->create_report('plan_evidence', ['embedded' => 1]);
        $this->assertTrue(builder::table('report_builder')
            ->where('shortname', 'plan_evidence')
            ->where('source', 'dp_evidence')
            ->where('embedded', 1)
            ->exists()
        );

        totara_evidence_migrate();

        // Embedded report should be deleted
        $this->assertFalse(builder::table('report_builder')
            ->where('shortname', 'plan_evidence')
            ->where('source', 'dp_evidence')
            ->where('embedded', 1)
            ->exists()
        );
        $this->assertTrue(builder::table('report_builder')
            ->where('shortname', 'evidence_record_of_learning')
            ->where('source', 'evidence_item')
            ->where('embedded', 1)
            ->exists()
        );

        $migrated_report_record = builder::table('report_builder')->where('shortname', 'report_test')->one();
        $migrated_report = reportbuilder::create($migrated_report_record->id, null, false);

        $expected_columns = [
            'base-name',
            'base-created_at',
            'base-modified_at',
            'base-in_use',
            'base-actions',
            'type-name',
            'user-fullname',
            'user-institution',
            'job_assignment-numjobassignments',
        ];
        $this->assertEquals($expected_columns, array_keys($migrated_report->columns));

        // Filters doesn't have actions column
        $expected_filters = [
            'base-name',
            'base-created_at',
            'base-modified_at',
            'base-in_use',
            'type-name',
            'user-fullname',
            'user-institution',
            'job_assignment-numjobassignments',
        ];
        $this->assertEquals($expected_filters, array_keys($migrated_report->filters));

        // Saved searches should be same as filters, although the order doesn't matter
        $saved_search_record = builder::table('report_builder_saved')->one();
        $actual_saved_searches = array_keys(unserialize($saved_search_record->search, ['allowed_classes' => true]));
        foreach ($expected_filters as $expected_filter) {
            $this->assertContains($expected_filter, $actual_saved_searches);
        }
    }

    private function create_report(string $shortname, array $attributes = []): int {
        return builder::table('report_builder')->insert(array_merge([
            'fullname' => 'Test Report',
            'shortname' => $shortname,
            'source' => 'dp_evidence',
            'hidden' => 0,
            'cache' => 0,
            'accessmode' => 0,
            'contentmode' => 0,
            'recordsperpage' => 0,
            'defaultsortorder' => 0,
            'embedded' => 0,
            'initialdisplay' => 0,
            'toolbarsearch' => 0,
            'globalrestriction' => 0,
            'timemodified' => 0,
            'showtotalcount' => 0,
            'useclonedb' => 0,
        ], $attributes));
    }

    private function create_report_data(int $report_id, array $mappings): void {
        $sortorder = 0;
        $saved_searches = [];
        foreach ($mappings as $type => $values) {
            foreach ($values as $value) {
                builder::table('report_builder_columns')->insert([
                    'reportid' => $report_id,
                    'type' => $type,
                    'value' => $value,
                    'sortorder' => $sortorder,
                    'hidden' => 0,
                    'customheading' => 0,
                ]);
                builder::table('report_builder_filters')->insert([
                    'reportid' => $report_id,
                    'type' => $type,
                    'value' => $value,
                    'sortorder' => $sortorder,
                    'advanced' => 0,
                    'filtername' => '',
                    'customname' => 0,
                    'region' => 0,
                ]);
                $saved_searches["$type-$value"] = ['operator' => 0, 'value' => $sortorder];
                $sortorder++;
            }
        }
        builder::table('report_builder_saved')->insert([
            'reportid' => $report_id,
            'userid' => 0,
            'name' => '',
            'ispublic' => 0,
            'isdefault' => 0,
            'timemodified' => 0,
            'search' => serialize($saved_searches),
        ]);

        // Make sure invalid saved searches aren't migrated
        builder::table('report_builder_saved')->insert([
            'reportid' => $report_id,
            'userid' => 0,
            'name' => '',
            'ispublic' => 0,
            'isdefault' => 0,
            'timemodified' => 0,
            'search' => null,
        ]);
        builder::table('report_builder_saved')->insert([
            'reportid' => $report_id,
            'userid' => 0,
            'name' => '',
            'ispublic' => 0,
            'isdefault' => 0,
            'timemodified' => 0,
            'search' => serialize('invalid search'),
        ]);
    }

}
