<?php

define('CLI_SCRIPT', true);

require_once('config.php');
require_once($CFG->dirroot . '/lib/clilib.php');

// now get cli options
list($options, $unrecognized) = cli_get_params(
    array(
        'remove'            => false,
        'list'              => false,
        'stats'             => false,
        'tolerance'         => 10,
        'help'              => false
    ),
    array(
        'h' => 'help'
    )
);

$help = <<<EOD
Reports on and removes duplicate course completion history records

This tool is designed to identify and optionally remove duplciate
course completion history records.

To run this tool:

 * php course_completion_history_clearner --list
         Identifies and reports on duplicates.
 * php course_completion_history_clearner --stats
         Produces a summary report.
 * php course_completion_history_clearner --remove
         Identifies and removes duplicates. Leaving just a single record.
         The record with the lowest ID is left.
         Ensure you have a full backup of the course_completion_history
         table before running this argument.
         
Additional options:

  --tolerance=10
       Sets the tolerance for identifying duplicates.
       There have to be MORE THAN x duplicates in order to be reported on
       or removed.
       Defaults to 10.
       There are valid situations in which duplicates may exist, such as 
       when they are imported. We recommend you keep this at 10 or even
       raise it in order to remove only the duplicates where there are 
       large numbers.
EOD;

if ($options['help'] || (!$options['remove'] && !$options['list'] && !$options['stats'])) {
    echo $help;
    die;
}

$tolerance = (int)$options['tolerance'];
if ($tolerance < 2) {
    die('Tolerance is too low.');
}
if ($tolerance > PHP_INT_MAX) {
    die('Tolerance is too high.');
}

$sql = "SELECT courseid, userid, timecompleted, grade, count(id) AS duplicates 
          FROM {course_completion_history}
      GROUP BY courseid, userid, timecompleted, grade
        HAVING count(id) > " . (int)$tolerance . "
      ORDER BY userid, courseid";
$rs = $DB->get_recordset_sql($sql);
$stats = [];
foreach ($rs as $row) {
    if ($options['stats']) {
        if (!isset($stats[$row->duplicates])) {
            $stats[$row->duplicates] = 1;
        } else {
            $stats[$row->duplicates]++;
        }
    }
    if ($options['list']) {
        $userid = str_pad($row->userid, 10, ' ');
        $courseid = str_pad($row->courseid, 10, ' ');
        $timecompleted = str_pad($row->timecompleted, 10, ' ');
        $grade = str_pad($row->grade, 10, ' ');
        $duplicates = str_pad($row->duplicates, 10, ' ');
        echo "User {$userid} / Course {$courseid} / Time {$timecompleted} / Grade {$grade} has {$duplicates} duplicates\n";
    }
    if ($options['remove']) {
        $id_sql = 'SELECT min(id) AS min_id
                  FROM {course_completion_history}
                  WHERE userid = :userid AND
                        courseid = :courseid AND
                        timecompleted = :timecompleted AND';
        $del_sql = 'DELETE FROM {course_completion_history} 
                    WHERE userid = :userid AND
                        courseid = :courseid AND
                        timecompleted = :timecompleted AND
                        id <> :min_id AND ';
        $params = [
            'userid' => $row->userid,
            'courseid' => $row->courseid,
            'timecompleted' => $row->timecompleted,
        ];
        if (is_null($row->grade)) {
            $id_sql .= ' grade IS NULL';
            $del_sql .= ' grade IS NULL';
        } else {
            $id_sql .= ' grade = :grade';
            $del_sql .= ' grade = :grade';
            $params['grade'] = $row->grade;
        }
        $id = $DB->get_field_sql($id_sql, $params);
        $params['min_id'] = $id;
        $DB->execute($del_sql, $params);
    }
}
$rs->close();

if (!empty($options['stats'])) {
    krsort($stats);
    echo "\nStats\n";
    echo "|-------------------------------|\n";
    echo "| Duplicates    | Occurrences   |\n";
    foreach ($stats as $duplicates => $count) {
        echo "| ";
        echo str_pad($duplicates, 14, ' ');
        echo '| ';
        echo str_pad($count, 14, ' ');
        echo '|';
        echo "\n";
    }
    echo "|-------------------------------|\n";
    echo "| Total: " . array_sum($stats) . "\n";
    echo "|-------------------------------|\n";
}

echo "\n";
exit(0);