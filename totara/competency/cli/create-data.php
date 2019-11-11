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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package totara_competency
 */

use Faker\Generator;

define('CLI_SCRIPT', 'yes');

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/phpunit/classes/util.php');
require_once($CFG->dirroot . '/cohort/lib.php');

global $DB;

$USER = get_admin();

/** @var totara_competency_generator $gen */
$gen = phpunit_util::get_data_generator()->get_plugin_generator('totara_competency');

if (!file_exists(totara::config()->dirroot . '/vendor/fzaninotto/faker/src/autoload.php')) {
    echo "In order to execute this script you need to execute: 'composer require fzaninotto/faker'!\n";
    echo "Please don't commit changes to composer.json and composer.lock files\n";
    exit(1);
}


require_once totara::config()->dirroot . '/vendor/fzaninotto/faker/src/autoload.php';
require_once totara::config()->dirroot . '/totara/hierarchy/lib.php';


// Faker factory
$faker = Faker\Factory::create();

$what = $argv[1] ?? null;

switch (strtolower($what)) {
    case 'all':
        $exec = PHP_BINARY;
        $script = __FILE__;

        $commands = [
            'users',
            'cohorts',
            'positions',
            'organisations',
            'competencies',
            'add_to_cohort',
            'add_to_org',
            'add_to_pos',
        ];

        totara::transaction(function () use ($commands, $exec, $script) {
            foreach ($commands as $command) {
                $return = -1;
                $output = [];

                exec("{$exec} {$script} {$command}", $output, $return);

                echo implode("\n", $output);
                if ($return != 0) {
                    echo "Error creating data";
                    throw new \Exception("Error creating data, code: {$return}");
                }
            }
        });

        echo "Everything has been created successfully";

        exit(0);

    case 'cohorts':
        echo "Creating cohorts...\n";
        $how_many = intval($argv[2] ?? 500);

        totara::transaction(function () use ($gen, $how_many, $faker) {
            for ($i = 1; $i <= $how_many; $i++) {
                $gen->assignment_generator()->create_cohort(['name' => $faker->realText(100)]);
            }
        });

        exit(0);

    case 'users':
        echo "Creating users...\n";
        $how_many = intval($argv[2] ?? 1000);

        totara::transaction(function () use ($gen, $how_many) {
            for ($i = 1; $i <= $how_many; $i++) {
                $gen->assignment_generator()->create_user();
            }
        });

        exit(0);

    case 'positions':
        echo "Creating positions...\n";
        // That may create way too many, stick to 5 frameworks or less
        $frameworks = intval($argv[2] ?? 5);
        $max_depth = intval($argv[3] ?? 4);
        $max_competencies = intval($argv[2] ?? 10);

        totara::transaction(function () use ($frameworks, $max_depth, $max_competencies, $faker, $gen) {
            for ($fw = 1; $fw <= $frameworks; $fw++) {
                $framework = create_pos_framework($faker, $gen);
                $depth = rand(1, $max_depth);

                create_positions($faker, $gen, $framework, 0, $max_competencies, $depth);
            }
        });

        exit(0);

    case 'organisations':
        echo "Creating organisations...\n";
        // That may create way too many, stick to 5 frameworks or less
        $frameworks = intval($argv[2] ?? 5);
        $max_depth = intval($argv[3] ?? 4);
        $max_competencies = intval($argv[2] ?? 10);

        totara::transaction(function () use ($frameworks, $max_depth, $max_competencies, $faker, $gen) {
            for ($fw = 1; $fw <= $frameworks; $fw ++) {
                $framework = create_org_framework($faker, $gen);
                $depth = rand(1, $max_depth);

                create_organisations($faker, $gen, $framework, 0, $max_competencies, $depth);
            }
        });

        exit(0);

    case 'competencies':
        echo "Creating competencies...\n";
        // That may create way too many, stick to 5 frameworks or less
        $frameworks = intval($argv[2] ?? 5);
        $max_depth = intval($argv[3] ?? 4);
        $max_competencies = intval($argv[2] ?? 10);

        totara::transaction(function () use ($frameworks, $max_depth, $max_competencies, $faker) {
            for ($fw = 1; $fw <= $frameworks; $fw ++) {
                $framework = create_framework($faker);
                $depth = rand(1, $max_depth);

                create_competencies($faker, $framework, 0, $max_competencies, $depth);
            }
        });

        exit(0);

    case 'add_to_pos':
        echo "Creating members for positions...\n";
        $positions = \hierarchy_position\entities\position::repository()->get();

        $counter = \hierarchy_position\entities\position::repository()
            ->select_raw('count(*) + 1 as next_num')
            ->where_like_starts_with('idnumber', 'posass_')
            ->one();

        if (!$counter) {
            $counter = 1;
        } else {
            $counter = $counter->get_attribute('next_num');
        }

        totara::transaction(function () use ($positions, $counter) {
            foreach ($positions as $position) {
                $count = rand(1, intval($argv[2] ?? 3));

                $users = \core\entities\user::repository()
                    ->limit($count)
                    ->order_by_raw('random()')
                    ->get();

                foreach ($users as $user) {
                    \totara_job\job_assignment::create([
                        'userid' => $user->id,
                        'positionid' => $position->id,
                        'idnumber' => 'posass_' . $counter,
                    ]);

                    $counter += 1;
                }
            }
        });

        exit(0);

    case 'add_to_org':
        echo "Creating members for organisations...\n";

        $organisations = \hierarchy_organisation\entities\organisation::repository()->get();

        $counter = \hierarchy_organisation\entities\organisation::repository()
            ->select_raw('count(*) + 1 as next_num')
            ->where_like_starts_with('idnumber', 'posass_')
            ->one();

        if (!$counter) {
            $counter = 1;
        } else {
            $counter = $counter->get_attribute('next_num');
        }

        totara::transaction(function () use ($organisations, $counter) {

            foreach ($organisations as $organisation) {
                $count = rand(1, intval($argv[2] ?? 3));

                $users = \core\entities\user::repository()
                    ->limit($count)
                    ->order_by_raw('random()')
                    ->get();

                foreach ($users as $user) {
                    \totara_job\job_assignment::create([
                        'userid' => $user->id,
                        'organisationid' => $organisation->id,
                        'idnumber' => 'orgass_' . $counter,
                    ]);

                    $counter += 1;
                }
            }
        });

        exit(0);

    case 'add_to_cohort':
        echo "Creating members for cohorts...\n";

        $count = rand(1, intval($argv[2] ?? 3));

        $users = \core\entities\user::repository()
            ->limit($count)
            ->order_by_raw('random()')
            ->get();

        totara::transaction(function () use ($users){
            foreach (\core\orm\query\builder::table('cohort')->get()->to_array() as $cohort) {
                foreach ($users as $user) {
                    cohort_add_member($cohort['id'], $user->id);
                }
            }
        });

        exit(0);
}

echo 'Data not generated...' . "\n";

function create_competency(Generator $faker, $parent = 0, $framework = null, $data = []) {
    $hierarchy = hierarchy::load_hierarchy('competency');

    $item = array_merge([
        'description' => '<p>' . $faker->realText(500) . '</p>',
        'idnumber' => null,
        'visible' => 1,
        'aggregationmethod' => $faker->numberBetween(1,3),
        'proficiencyexpected' => 1,
        'evidencecount' => 0,
        'timecreated' => time(),
        'timemodified' => time(),
        'usermodified' => 2,
        'fullname' => $faker->realText(100),
        'typeid' => 1,
    ], $data);

    return $hierarchy->add_hierarchy_item((object) $item, $parent, $framework, true, false, false)->id;
}

function create_competencies(Generator $faker, $framework, $parent, $max_count, $depth) {

    if ($depth < 1) {
        return [];
    }

    $count = $faker->numberBetween(1, $max_count);
    $competencies = [];

    for ($i = 1; $i <= $count; $i ++) {
        $id = create_competency($faker, $parent, $framework);
        $competencies[$id] = $id;
    }

    if ($depth > 1) {
        foreach ($competencies as $id => &$set) {
            $set = create_competencies($faker, $framework, $id, $max_count, $depth - 1);
        }
    }

    return $competencies;
}


function create_pos_framework(Generator $faker, totara_competency_generator $gen = null, $data = []) {

    $data = array_merge([
        'fullname' => $faker->realText(100),
        'description' => $faker->realText(400),
        'timecreated' => time(),
        'timemodified' => time(),
        'usermodified' => 2,
        'visible' => 1,
    ], $data);

    return $gen->hierarchy_generator()->create_pos_frame($data);
}

function create_position(Generator $faker, totara_competency_generator $gen, $parent = 0, $framework = null, $data = []) {

    $framework = $framework->id ?? $framework ?? create_pos_framework($faker, $gen)->id;

    return $gen->assignment_generator()->create_position(array_merge([
        'fullname' => $faker->realText(100),
        'description' => $faker->realText(400),
        'parentid' => $parent,
    ], $data), $framework);
}

function create_positions(Generator $faker, totara_competency_generator $gen, $framework, $parent, $max_count, $depth) {

    if ($depth < 1) {
        return [];
    }

    $count = $faker->numberBetween(1, $max_count);
    $items = [];

    for ($i = 1; $i <= $count; $i ++) {
        $id = create_position($faker, $gen, $parent, $framework)->id;
        $items[$id] = $id;
    }

    if ($depth > 1) {
        foreach ($items as $id => &$set) {
            $set = create_positions($faker, $gen, $framework, $id, $max_count, $depth - 1);
        }
    }

    return $items;
}

function create_org_framework(Generator $faker, totara_competency_generator $gen = null, $data = []) {

    $data = array_merge([
        'fullname' => $faker->realText(100),
        'description' => $faker->realText(400),
        'timecreated' => time(),
        'timemodified' => time(),
        'usermodified' => 2,
        'visible' => 1,
    ], $data);

    return $gen->hierarchy_generator()->create_org_frame($data);
}

function create_organisation(Generator $faker, totara_competency_generator $gen, $parent = 0, $framework = null, $data = []) {

    $framework = $framework->id ?? $framework ?? create_org_framework($faker, $gen)->id;

    return $gen->assignment_generator()->create_organisation(array_merge([
        'fullname' => $faker->realText(100),
        'description' => $faker->realText(400),
        'parentid' => $parent,
    ], $data), $framework);
}

function create_organisations(Generator $faker, totara_competency_generator $gen, $framework, $parent, $max_count, $depth) {

    if ($depth < 1) {
        return [];
    }

    $count = $faker->numberBetween(1, $max_count);
    $items = [];

    for ($i = 1; $i <= $count; $i ++) {
        $id = create_organisation($faker, $gen, $parent, $framework)->id;
        $items[$id] = $id;
    }

    if ($depth > 1) {
        foreach ($items as $id => &$set) {
            $set = create_organisations($faker, $gen, $framework, $id, $max_count, $depth - 1);
        }
    }

    return $items;
}

// Create frameworks
function create_framework(Generator $faker, $scale = null, $data = []) {

    $order = totara::db()->get_field('comp_framework', 'MAX(sortorder) + 1', []) ?: 1;

    $default = [
        //'shortname' => $faker->realText(50), Apparently short name is not really used in the interface :shrug:
        'fullname' => $faker->realText(100),
        'idnumber' => '',
        'description' => $faker->realText(400),
        'sortorder' => $order,
        'visible' => 1,
        'hidecustomfields' => 0,
        'timecreated' => time(),
        'timemodified' => time(),
        'usermodified' => 2,
    ];

    $item = array_merge($default, $data);

    if (is_null($scale)) {
        // Grab the first scale we could find or just hard-code 1
        $scale = 1;
    } else {
        $scale = $scale->id;
    }

    return totara::transaction(function () use ($item, $scale) {
        $id = totara::db()->insert_record('comp_framework', (object) $item);

        totara::db()->insert_record('comp_scale_assignments', (object) [
            'scaleid' => $scale,
            'frameworkid' => $id,
            'usermodified' => 2,
            'timemodified' => time(),
        ]);

        return $id;
    });
}

class totara {
    public static function db(): \moodle_database {
        return $GLOBALS['DB'];
    }

    public static function transaction(Closure $closure) {
        $transaction = self::db()->start_delegated_transaction();
        try {
            $result = $closure();
        } catch (\Exception $exception) {
            $transaction->rollback($exception);
            // ^^ Throws another stupid exception anyway
            return null;
        }
        $transaction->allow_commit();

        return $result;
    }

    public static function config(): \stdClass {
        return $GLOBALS['CFG'];
    }
}