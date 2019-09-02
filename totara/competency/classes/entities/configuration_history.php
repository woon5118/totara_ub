<?php

namespace totara_competency\entities;

use core\orm\entity\entity;

/**
 * @property-read int $id ID
 * @property int $comp_id
 * @property int $assignment_id
 * @property int $active_from
 * @property int $active_to
 * @property string $configuration
 */
class configuration_history extends entity {

    public const TABLE = 'totara_competency_configuration_history';

}
