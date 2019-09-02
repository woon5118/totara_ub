<?php

namespace totara_competency\webapi\resolver\type;

use core\webapi\execution_context;
use core\webapi\type_resolver;

// Todo: Remove this comment. Only adding to give visibility in a PR.

// Todo: This should sit in the assignment component.
class assignment implements type_resolver {
    public static function resolve(string $field, $assignment, array $args, execution_context $ec) {
        return $assignment->{$field};
    }
}
