<?php

if (php_sapi_name() !== "cli") {
    die();
}

// Update the environment to ensure the same PHP binary is used to run
// the behat init script. This way we can be sure that all forked processes use
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
        'arguments' => [realpath(__DIR__ . '/../../server/admin/tool/behat/cli/init.php')],
        'description' => 'Initialises Behat for a single thread, run with --help to see options'
    ],
    'util' => [
        'command' => 'php',
        'arguments' => [realpath(__DIR__ . '/../../server/admin/tool/behat/cli/util.php')],
        'description' => 'Utility script for the Totara Behat implementation, run with --help to see options'
    ],
    'behat_run' => [
        'command' => 'php',
        'arguments' => [realpath(__DIR__ . '/../../server/admin/tool/behat/cli/run.php')],
        'description' => 'Initialises Behat for multiple threads, run with --help to see options'
    ],
    'util_single_run' => [
        'command' => 'php',
        'arguments' => [realpath(__DIR__ . '/../../server/admin/tool/behat/cli/util_single_run.php')],
        'description' => 'Utility script for the Totara Behat implementation, run with --help to see options'
    ],
    'run' => [
        'command' => realpath(__DIR__ . '/vendor/bin/behat'),
        'arguments' => [],
        'description' => 'Runs Behat, run with --help to see options'
    ],
];

// If they didn't ask for anything, or they asked for something that does not exist then print help.
if (!isset($scripts[$script])) {
    echo "Totara Behat implementation\n";
    echo "\n";
    echo "This script makes it easy to interact with Totara's Behat implementation by centralising\n";
    echo "where those scripts are called from. The following options exist:\n";
    echo "\n";
    foreach ($scripts as $script => $details) {
        echo "  {$script}\n";
        echo "    {$details['description']}\n\n";
    }
    echo "\n";
    echo "e.g.\n";
    echo "  php test/behat/behat.php init\n";
    echo "  php test/behat/behat.php util --buildconfig\n";
    echo "  php test/behat/behat.php run\n";
    echo "  php test/behat/behat.php run --name 'User account creation'\n";
    exit(0);
}
// We have a valid script request.

// Establish the command we need to run.
$command = $scripts[$script]['command'];
// Add the required arguments to the command to the start.
$passthrough = array_merge($scripts[$script]['arguments'], $passthrough);
// A little hack to set the behat configuration path when running behat.
$set_config_argument = ($script === 'run');
foreach ($passthrough as &$arg) {
    if ($script === 'run') {
        if ($arg === 'config') {
            $set_config_argument = false;
        }
    }
    $arg = escapeshellarg($arg);
}
// The hack continued...
if ($set_config_argument) {
    $passthrough[] = '--config='.escapeshellarg(__DIR__.'/behat.yml');
}

passthru(escapeshellcmd($command) . ' '  . join(' ', $passthrough), $code);
exit($code);
