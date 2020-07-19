<?php

if (php_sapi_name() !== "cli") {
    die();
}

// Update the environment to ensure the same PHP binary is used to run
// the phpunit init script. This way we can be sure that all forked processes use
// the same binary.
$env = getenv();
$env['PATH'] = PHP_BINARY . ':' . $env['PATH'];
putenv('PATH='.$env['PATH']);

// Collect the arguments provided to this script so that we can forward them to the correct script when ready.
$passthrough = $argv;
array_shift($passthrough); // Drop off this script
$script = array_shift($passthrough); // Collect the script that the user wishes to run.

$scripts = [
    'init' => [
        'command' => 'php',
        'arguments' => [realpath(__DIR__ . '/../../server/admin/tool/phpunit/cli/init.php')],
        'description' => 'Initialises PHPUnit for a single thread, run with --help to see options'
    ],
    'util' => [
        'command' => 'php',
        'arguments' => [realpath(__DIR__ . '/../../server/admin/tool/phpunit/cli/util.php')],
        'description' => 'Utility script for the Totara PHPUnit implementation, run with --help to see options'
    ],
    'parallel_init' => [
        'command' => 'php',
        'arguments' => [realpath(__DIR__ . '/../../server/admin/tool/phpunit/cli/parallel_init.php')],
        'description' => 'Initialises PHPUnit for multiple threads, run with --help to see options'
    ],
    'parallel_run' => [
        'command' => 'php',
        'arguments' => [realpath(__DIR__ . '/../../server/admin/tool/phpunit/cli/parallel_run.php')],
        'description' => 'Run multiple parallel PHPUnit threads'
    ],
    'run' => [
        'command' => realpath(__DIR__ . '/vendor/bin/phpunit'),
        'arguments' => [],
        'description' => 'Runs PHPUnit, run with --help to see options'
    ],
];

// If they didn't ask for anything, or they asked for something that does not exist then print help.
if (!isset($scripts[$script])) {
    echo "Totara PHPUnit implementation\n";
    echo "\n";
    echo "This script makes it easy to interact with Totara's PHPUnit implementation by centralising\n";
    echo "where those scripts are called from. The following options exist:\n";
    echo "\n";
    foreach ($scripts as $script => $details) {
        echo "  {$script}\n";
        echo "    {$details['description']}\n\n";
    }
    echo "\n";
    echo "e.g.\n";
    echo "  php test/phpunit/phpunit.php init\n";
    echo "  php test/phpunit/phpunit.php util --buildconfig\n";
    echo "  php test/phpunit/phpunit.php run\n";
    echo "  php test/phpunit/phpunit.php run --filter test_default_environment server/lib/phpunit/tests/advanced_test.php\n";
    exit(0);
}
// We have a valid script request.

// Establish the command we need to run.
$command = $scripts[$script]['command'];
// Add the required arguments to the command to the start.
$passthrough = array_merge($scripts[$script]['arguments'], $passthrough);
// A little hack to set the phpunit configuration path when running phpunit.
$set_config_argument = ($script === 'run' || $script === 'parallel_run');
foreach ($passthrough as &$arg) {
    if ($script === 'run') {
        if ($arg === 'c' || $arg === '--configuration' || $arg === '--no-configuration') {
            $set_config_argument = false;
        }
    }
    $arg = escapeshellarg($arg);
}
// The hack continued...
if ($set_config_argument) {
    $passthrough[] = '-c';
    $passthrough[] = realpath(__DIR__ . '/phpunit.xml');
}

passthru(escapeshellcmd($command) . ' '  . join(' ', $passthrough), $code);
exit($code);
