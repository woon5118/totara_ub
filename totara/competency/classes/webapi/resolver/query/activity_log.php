<?php

namespace totara_competency\webapi\resolver\query;

use core\webapi\execution_context;
use core\webapi\query_resolver;
use totara_competency\data_providers;
use totara_core\advanced_feature;

class activity_log implements query_resolver {
    
    public static function resolve(array $args, execution_context $ec) {
        advanced_feature::require('perform');
        
        return data_providers\activity_log::create($args['user_id'], $args['competency_id'])
            ->set_filters($args['filters'] ?? [])
            ->fetch();
    }
    
}