<?php
define('CLI_SCRIPT', 1);
require __DIR__ . '/../../../config.php';
require_once($CFG->dirroot . '/lib/clilib.php');
require_once($CFG->dirroot . '/lib/phpunit/classes/util.php');

use mod_perform\entities\activity\subject_instance;

raise_memory_limit(MEMORY_HUGE);

core\session\manager::set_user(get_admin());

function eager_query($num) {
    $time_start = microtime(true);
    $test = subject_instance::repository()
        ->where('id', '<=', $num)
        ->with('track')
        ->get();
    $time_stop = microtime(true);
    echo "query time with {$test->count()} results: " . ($time_stop - $time_start) . "\n";
}

eager_query(5000);
eager_query(10000);
eager_query(20000);
eager_query(30000);
eager_query(40000);
eager_query(50000);
eager_query(60000);
eager_query(70000);
eager_query(80000);
eager_query(90000);
eager_query(100000);
