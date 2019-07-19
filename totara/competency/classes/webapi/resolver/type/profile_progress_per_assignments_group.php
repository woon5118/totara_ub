<?php

namespace totara_competency\webapi\resolver\type;

use core\webapi\execution_context;
use core\webapi\type_resolver;
use tassign_competency\entities\assignment;

class profile_progress_per_assignments_group implements type_resolver {
    public static function resolve(string $field, $progress, array $args, execution_context $ec) {
        switch ($field) {
            case 'competencies':
                return $progress->assignments->map(function($assignment) {
                    return (object) [
                        'id' => $assignment->competency->id,
                        'assignment_id' => $assignment->id,
                        'name' => $assignment->competency->fullname,
                        'min_value' => $assignment->min_value,
                        'my_value' => $assignment->my_value,
                    ];
                })->all();

            default:
                return $progress->{$field};
        }

    }
}