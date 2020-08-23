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
 * @author Vernon Denny <vernon.denny@totaralearning.com>
 * @package ml_recommender
 */
defined('MOODLE_INTERNAL') || die();

use ml_recommender\local\environment;
use ml_recommender\local\export\item_data_export;
use ml_recommender\local\export\user_data_export;
use ml_recommender\local\export\user_interactions_export;
use ml_recommender\local\import\bulk_item_predictions;
use ml_recommender\local\import\bulk_user_predictions;

class ml_recommender_export_import_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_ml_export(): void {
        global $DB, $CFG;

        require_once($CFG->libdir . '/csvlib.class.php');
        require_once(__DIR__ . '/../../../enrol/self/externallib.php');

        // Set up as admin.
        $gen = $this->getDataGenerator();
        $this->setAdminUser();

        // Some users to work with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Courses with tags - one will have self enrolment.
        $tag_objects = core_tag_tag::create_if_missing(core_tag_collection::get_default(), ['tag1', 'tag2', 'tag3'], true);
        $course_self_enrol = $this->getDataGenerator()->create_course();
        core_tag_tag::add_item_tag('core', 'course', $course_self_enrol->id, context_course::instance($course_self_enrol->id), 'tag1');
        core_tag_tag::add_item_tag('core', 'course', $course_self_enrol->id, context_course::instance($course_self_enrol->id), 'tag3');

        $course_not_self = $this->getDataGenerator()->create_course();
        core_tag_tag::add_item_tag('core', 'course', $course_not_self->id, context_course::instance($course_not_self->id), 'tag1');
        core_tag_tag::add_item_tag('core', 'course', $course_not_self->id, context_course::instance($course_not_self->id), 'tag2');

        // Self enrol a user.
        $selfplugin = enrol_get_plugin('self');
        $this->assertNotEmpty($selfplugin);

        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $this->assertNotEmpty($studentrole);
        $instance1id = $selfplugin->add_instance($course_self_enrol, ['status' => ENROL_INSTANCE_ENABLED, 'name' => 'Test instance 1', 'customint6' => 1, 'roleid' => $studentrole->id]);
        self::setUser($user1);
        $self_enrol_result = enrol_self_external::enrol_user($course_self_enrol->id);

        // The rest we do as admin.
        $this->setAdminUser();

        // Engage topics.
        $mytopics = ['topic1', 'topic2', 'topics3'];
        foreach ($mytopics as $key => $topic) {
            $topics[$key] = \totara_topic\topic::create($topic);
        }

        // Engage content.
        $article_generator = $gen->get_plugin_generator('engage_article');

        $data = [];
        $data['topics'] = [];
        $data['topics'][] = $topics[1]->get_id();
        $data['topics'][] = $topics[2]->get_id();
        $article_1 = $article_generator->create_article($data);

        $data['topics'] = [];
        $data['topics'][] = $topics[0]->get_id();
        $data['topics'][] = $topics[2]->get_id();
        $article_2 = $article_generator->create_article($data);

        // Interactions.
        $recommendations_generator = $gen->get_plugin_generator('ml_recommender');
        $recommendations_generator->create_recommender_interaction($user1->id, $article_1->get_id(), 'engage_article');
        $recommendations_generator->create_recommender_interaction($user2->id, $article_1->get_id(), 'engage_article');
        $recommendations_generator->create_recommender_interaction($user2->id, $article_2->get_id(), 'engage_article');

        // Set up list of exporters.
        $data_path = self::get_data_path();
        $exporters = [
            'user_interactions' => user_interactions_export::class,
            'user_data' => user_data_export::class,
            'item_data' => item_data_export::class,
        ];

        // Run the data exports.
        foreach ($exporters as $exportname => $exportclass) {
            // Delete old data.
            $csv_path = $data_path . '/' . $exportname . '.csv';
            @unlink($csv_path);

            // Instantiate data exporter.
            $export = new $exportclass();

            // Generate the csv content in temp file.
            $csv_writer = new \csv_export_writer('comma');
            $result = $export->export($csv_writer);

            // Copy exported data to data directory after successful completion.
            if ($result && isset($csv_writer->path) && file_exists($csv_writer->path)) {
                copy($csv_writer->path, $csv_path);
            } else {
                // Things are not ending well, flag it as such.
                $this->assertEquals(true, false);
            }
            unset($csv_writer);
        }

        // Get data counts to compare to CSV.
        $count_users = $DB->count_records_sql("SELECT COUNT(u.id) FROM {user} u");

        $count_interactions = $DB->count_records_sql("SELECT COUNT(mli.id) FROM  {ml_recommender_interactions} mli");
        $count_interactions += $DB->count_records_sql("SELECT count(ue.userid) FROM {user_enrolments} ue JOIN {enrol} e ON (ue.enrolid = e.id) WHERE e.enrol = 'self' AND e.status = " . ENROL_USER_ACTIVE);

        $count_items = 0;
        $items_sql = [
            'articles' => "SELECT COUNT(er.id) FROM {engage_resource} er JOIN {engage_article} ea ON er.instanceid = ea.id WHERE er.resourcetype = 'engage_article'",
            'playlists' => "SELECT COUNT(tp.id) FROM {playlist} tp",
            'workpaces' => "SELECT COUNT(cw.id) FROM {course} cw WHERE cw.containertype = 'container_workspace'",
            'course_self_enrol' => "SELECT COUNT(cc.id) FROM {course} cc JOIN {enrol} te on cc.id = te.courseid WHERE cc.containertype = 'container_course' AND te.enrol = 'self'"
        ];
        foreach ($items_sql as $item_type => $item_sql) {
            $count_items += $DB->count_records_sql($item_sql);
        }

        // Compare DB counts to CSV record counts.
        $csv_files = [
            'user_data' => $count_users,
            'item_data' => $count_items,
            'user_interactions' => $count_interactions
        ];

        $data = [];
        foreach ($csv_files as $csv => $db_count) {
            $data[$csv] = [];
            $path = "{$data_path}/{$csv}.csv";
            $file = fopen($path,'r');
            while (! feof($file)) {
                $data[$csv][] = fgetcsv($file);
            }
            fclose($file);

            // Compare db counts to (csv records - 2) to account for headings and EOF.
            $this->assertEquals($db_count, count($data[$csv]) - 2);
        }
    }

    public function test_ml_upload(): void {
        global $DB;

        require_once(__DIR__ . '/../../../lib/csvlib.class.php');

        // Create fake recsys output to test uploads.
        $data_path = self::get_data_path();
        $csv_files = [
            // Items related to items.
            'i2i' => [
                ['target_iid', 'similar_iid', 'ranking'],
                ['engage_article1', 'totara_playlist7', 0.743599951267],
                ['engage_article1', 'engage_article267', 0.633537650108],
                ['totara_playlist1', 'engage_article96', 0.629082918167],
                ['totara_playlist1', 'totara_playlist70', 0.627931892872],
                ['engage_article2', 'engage_article24', 0.602713346481],
                ['engage_article2', 'engage_article47', 0.599358260632]
            ],
            // Items recommended to users.
            'i2u' => [
                ['uid', 'iid', 'ranking'],
                [1, 'engage_article277', 2.203594684601],
                [1, 'engage_article1', 1.936550498009],
                [2, 'engage_article242', 1.892577528954],
                [2, 'totara_playlist1', 1.879106402397],
                [1, 'engage_article271', 1.845853328705],
                [1, 'engage_article122', 1.669565081596]
            ]
        ];

        foreach ($csv_files as $csv => $records) {
            $path = "{$data_path}/{$csv}.csv";
            $file = fopen($path,'w');
            foreach ($records as $record) {
                fputcsv($file, $record);
            }
            fclose($file);
        }

        // Upload items per user.
        $user_recommendations = new bulk_user_predictions('i2u');
        $user_recommendations->upload();

        // Upload items per item.
        $item_recommendations = new bulk_item_predictions('i2i');
        $item_recommendations->upload();

        // Check count of uploaded records (excluding heading row of array).
        $count_i2i = $DB->count_records_sql("SELECT COUNT(mri.id) FROM  {ml_recommender_items} mri");
        $this->assertEquals($count_i2i, count($csv_files['i2i']) - 1);

        $count_i2u = $DB->count_records_sql("SELECT COUNT(mru.id) FROM  {ml_recommender_users} mru");
        $this->assertEquals($count_i2u, count($csv_files['i2u']) - 1);
    }

    /**
     * Ensure data directory exists and return its path.
     *
     * @return string Path to ML data directory.
     */
    private function get_data_path(): string {
        global $CFG;

        // Ensure we have a data directory to work with for exports and uploads.
        $data_path = environment::get_data_path();
        if (!is_dir($data_path)) {
            if (!mkdir($data_path, $CFG->directorypermissions, true)) {
                // Oh dear :-(
                $this->assertEquals(true, false);
            }
        }

        return $data_path;
    }
}
