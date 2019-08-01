<?php

namespace totara_competency\webapi\resolver\type;

use core\webapi\execution_context;
use core\webapi\type_resolver;
use tassign_competency\models\assignment;
use totara_competency\models\profile_progress;

class profile_progress_item implements type_resolver {

    /**
     * @param string $field
     * @param \stdClass $progress
     * @param array $args
     * @param execution_context $ec
     * @return mixed
     */
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