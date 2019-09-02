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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_criteria
 */

use criteria_childcompetency\childcompetency;
use criteria_childcompetency\items_processor;
use totara_competency\entities\competency;
use totara_competency\pathway;
use totara_criteria\criterion;

class criteria_childcompetency_items_processor_testcase extends advanced_testcase {

    private function setup_data() {
        global $DB;

        $data = new class() {
            /** @var [\stdClass] competencies */
            public $competencies = [];
        };

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        /** @var totara_criteria_generator $criteria_generator */
        $criteria_generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $to_create = [
            'Comp A' => ['with_criteria' => true],
            'Comp A-1' => ['parent' => 'Comp A'],
            'Comp A-1-1' => ['parent' => 'Comp A-1'],
            'Comp B' => ['with_criteria' => false],
            'Comp B-1' => ['parent' => 'Comp B'],
            'Comp B-1-1' => ['parent' => 'Comp B-1'],
            'Comp C' => ['with_criteria' => true],
            'Comp C-1' => ['parent' => 'Comp C'],
            'Comp C-2' => ['parent' => 'Comp C'],
            'Comp D' => ['with_criteria' => false],
            'Comp D-1' => ['parent' => 'Comp D'],
            'Comp D-2' => ['parent' => 'Comp D'],
            'Comp E' => ['with_criteria' => true],
            'Comp F' => ['with_criteria' => false],
        ];

        // Create competencies with 2 levels of children
        foreach ($to_create as $compname => $compdata) {
            $comp_record = isset($compdata['parent']) ? ['parentid' => $data->competencies[$compdata['parent']]->id] : [];
            $data->competencies[$compname] = $competency_generator->create_competency($compname, null, null, $comp_record);

            if (isset($compdata['with_criteria']) && $compdata['with_criteria']) {
                $cc = $criteria_generator->create_childcompetency();

                $cg_record = [
                    'comp_id' => $data->competencies[$compname]->id,
                    'scale_value_id' => 1,
                    'criteria' => [$cc],
                ];

                $cg = $competency_generator->create_criteria_group($cg_record);
            }
        }

        // Verify generated data
        $this->assertSame(count($to_create), $DB->count_records('comp'));

        foreach ($to_create as $compname => $compdata) {
            if (isset($compdata['parent'])) {
                $this->assertNotFalse($DB->get_record('comp',
                    ['id' => $data->competencies[$compname]->id, 'parentid' => $data->competencies[$compdata['parent']]->id]));
            }

            if (isset($compdata['with_criteria']) && $compdata['with_criteria']) {
                $this->verify_criterion($data->competencies[$compname]->id, true);
            }
        }

        return $data;
    }


    /**
     * Test constructor without attributes
     */
    public function test_update_items() {
        global $DB;

        $data = $this->setup_data();

        // Update items for non-existent competency
        $this->verify_criterion(1, false);
        $this->verify_items(1, []);
        items_processor::update_items(1);
        $this->verify_criterion(1, false);
        $this->verify_items(1, []);

        // Update items for competency without any children
        items_processor::update_items($data->competencies['Comp E']->id);
        $this->verify_items($data->competencies['Comp E']->id, []);

        items_processor::update_items($data->competencies['Comp F']->id);
        $this->verify_items($data->competencies['Comp F']->id, []);

        // Update items for competencies with children but without criteria
        foreach (['Comp B', 'Comp D'] as $compname) {
            items_processor::update_items($data->competencies[$compname]->id);
            $this->verify_items($data->competencies[$compname]->id, []);
        }

        // Update items for competency with criteria and multi level children
        items_processor::update_items($data->competencies['Comp A']->id);
        $this->verify_items($data->competencies['Comp A']->id, [$data->competencies['Comp A-1']->id]);

        // Update items for competency with criteria and multiple direct children
        items_processor::update_items($data->competencies['Comp C']->id);
        $this->verify_items($data->competencies['Comp C']->id,
            [$data->competencies['Comp C-1']->id, $data->competencies['Comp C-2']->id]);
    }

    /**
     * Test constructor without attributes
     */
    public function test_update_items_with_changed_children() {
        global $DB;

        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');

        $data = $this->setup_data();

        // Comp E starts with criterion, but no children
        items_processor::update_items($data->competencies['Comp E']->id);
        $this->verify_items($data->competencies['Comp E']->id, []);

        // Create 2 child competencies of Comp E
        $newChild1 = $competency_generator->create_competency('New Child 1', null, null, ['parentid' => $data->competencies['Comp E']->id]);
        $newChild2 = $competency_generator->create_competency('New Child 2', null, null, ['parentid' => $data->competencies['Comp E']->id]);
        $newChild3 = $competency_generator->create_competency('New Child 3', null, null, ['parentid' => $data->competencies['Comp E']->id]);

        // Now we should create 3 items for Comp E
        items_processor::update_items($data->competencies['Comp E']->id);
        $this->verify_items($data->competencies['Comp E']->id,
            [$newChild1->id, $newChild2->id, $newChild3->id]);

        // Retrieve the current item record ids to be used later
        [$items_sql, $items_params] = $this->build_items_sql($data->competencies['Comp E']->id);
        $initial_items_id_map = $DB->get_records_sql_menu($items_sql, $items_params);
        $initial_items_id_map = array_flip($initial_items_id_map);

        // Change NewChild1 to point to another parent
        // Add Another Child to Comp E
        $anotherChild = $competency_generator->create_competency('Anoterh Child', null, null,
            ['parentid' => $data->competencies['Comp E']->id]);

        $entity1 = new competency($newChild1->id);
        $entity1->parentid = 0;
        $entity1->path = '/' . $newChild1->id;
        $entity1->save();

        items_processor::update_items($data->competencies['Comp E']->id);
        $this->verify_items($data->competencies['Comp E']->id,
            [$newChild2->id, $newChild3->id, $anotherChild->id], $initial_items_id_map);
    }


    private function verify_criterion(int $comp_id, bool $expect_to_exist = true) {
        global $DB;

        $sql =
            "SELECT tc.id
               FROM {totara_competency_pathway} pw
               JOIN {pathway_criteria_group} pcg
                 ON pcg.id = pw.path_instance_id
               JOIN {pathway_criteria_group_criterion} pcgc
                 ON pcgc.criteria_group_id = pcg.id
                AND pcgc.criterion_type = :criteriontype
               JOIN {totara_criteria} tc
                 ON tc.id = pcgc.criterion_id
                AND tc.plugin_type = pcgc.criterion_type
              WHERE pw.comp_id = :compid
                AND pw.path_type = :pathtype
                AND status = :activestatus";
        $params = [
            'compid' => $comp_id,
            'pathtype' => 'criteria_group',
            'activestatus' => pathway::PATHWAY_STATUS_ACTIVE,
            'criteriontype' => 'childcompetency',
        ];

        $rows = $DB->get_records_sql($sql, $params);
        $this->assertSame($expect_to_exist ? 1 : 0, count($rows));
    }

    private function build_items_sql(int $comp_id): array {
        $sql =
            "SELECT tci.id,
                    tci.item_id
               FROM {totara_competency_pathway} pw
               JOIN {pathway_criteria_group} pcg
                 ON pcg.id = pw.path_instance_id
               JOIN {pathway_criteria_group_criterion} pcgc
                 ON pcgc.criteria_group_id = pcg.id
                AND pcgc.criterion_type = :criteriontype
               JOIN {totara_criteria} tc
                 ON tc.id = pcgc.criterion_id
                AND tc.plugin_type = pcgc.criterion_type
               JOIN {totara_criteria_item} tci
                 ON tci.criterion_id = tc.id
                AND tci.item_type = :itemtype
              WHERE pw.comp_id = :compid
                AND pw.path_type = :pathtype
                AND status = :activestatus";
        $params = [
            'compid' => $comp_id,
            'pathtype' => 'criteria_group',
            'activestatus' => pathway::PATHWAY_STATUS_ACTIVE,
            'criteriontype' => 'childcompetency',
            'itemtype' => 'competency',
        ];

        return [$sql, $params];
    }

    private function verify_items(int $comp_id, array $expected_item_ids, ?array $previous_item_id_map = null) {
        global $DB;

        [$sql, $params] = $this->build_items_sql($comp_id);
        $rows = $DB->get_records_sql($sql, $params);

        $this->assertSame(count($expected_item_ids), count($rows));
        foreach ($rows as $row) {
            $this->assertTrue(in_array($row->item_id, $expected_item_ids));

            if (!is_null($previous_item_id_map) && isset($previous_item_id_map[$row->item_id])) {
                $this->assertEquals($previous_item_id_map[$row->item_id], $row->id);
            }
        }
    }

}
