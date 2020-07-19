<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package pathway_manual
 */

namespace pathway_manual\webapi\resolver\query;

use context_user;
use core\entities\user;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use pathway_manual\data_providers\user_rateable_competencies as user_rateable_competencies_provider;
use pathway_manual\models\roles\role;
use pathway_manual\models\roles\role_factory;
use pathway_manual\models\user_competencies;

class user_rateable_competencies implements query_resolver {

    /**
     * @param array $args
     * @param execution_context $ec
     * @return user_competencies
     */
    public static function resolve(array $args, execution_context $ec) {
        require_login(null, false, null, false, true);

        $user_id = $args['user_id'];
        $role = role_factory::create($args['role']);
        $filters = $args['filters'] ?? [];

        // Capabilities are checked inside the data provider.
        $data_provider = user_rateable_competencies_provider::for_user_and_role($user_id, $role);

        if (empty($filters)) {
            // If no filters are specified then we load what available filter options there are too.
            return $data_provider->get_with_filter_options();
        } else {
            return $data_provider
                ->add_filters($filters)
                ->get();
        }
    }

}
