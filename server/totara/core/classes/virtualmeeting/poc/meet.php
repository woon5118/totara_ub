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
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package core_virtualmeeting
 */

use core\plugininfo\virtualmeeting;

/**
 * The PoC meeting page.
 */

global $CFG;
if (empty($CFG)) {
    require_once(__DIR__ . '/../../../../config.php');
}

if (!isset($USER) || !isset($PAGE) || !virtualmeeting::is_poc_available()) {
    die;
}

$name = required_param('name', PARAM_RAW);
$timestart = required_param('timestart', PARAM_INT);
$timefinish = required_param('timefinish', PARAM_INT);
$host = required_param('host', PARAM_USERNAME);

if ($host) {
    $title = s('Host meeting');
} else {
    $title = s('Join meeting');
}

?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $title; ?></title>
</head>
<body>
<main role="main">
<h1><?php echo $title; ?></h1>
<h2><?php
?></h2>
<p><?php
$time = time();
if (!$host && ($time < $timestart - 900 || $timefinish < $time)) {
    echo 'Right place, wrong time';
} else {
    echo "Joined '".s($name)."'";
}
?></p>
</main>
<footer>
<!-- Add some buttons for convenience. -->
<button type="button" onclick="history.back(-1)">History back 1</button>
<button type="button" onclick="history.back(-2)">History back 2</button>
<button type="button" onclick="history.back(-3)">History back 3</button>
<button type="button" onclick="window.close()">Window close</button>
</footer>
</body>
</html>
