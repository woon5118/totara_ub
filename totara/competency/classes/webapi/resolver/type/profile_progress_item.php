<?php

namespace totara_competency\webapi\resolver\type;

use core\webapi\execution_context;
use core\webapi\type_resolver;
use tassign_competency\models\assignment;

class profile_progress_item implements type_resolver {
    public static function resolve(string $field, $progress, array $args, execution_context $ec) {
        switch ($field) {
            case 'items':
                return $progress->assignments->map(function($assignment) {
                    return (object) [
                        'assignment' => assignment::load_by_entity($assignment),
                        'competency' => $assignment->competency,
                        'proficient' => boolval($assignment->my_value->proficient ?? false),
                        'min_value' => $assignment->min_value,
                        'my_value' => $assignment->my_value,
                    ];
                })->all();

            default:
                return $progress->{$field};
        }

    }
}