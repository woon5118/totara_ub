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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\activity\details;

use core\orm\query\builder;

/**
 * The internal implementation that represents a non-customisable performance notification setting.
 */
class notification_secret extends notification_sparse {
    /**
     * {@inheritDoc}
     *
     * NOTE: the function always returns true.
     */
    public function get_active(): bool {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function recipients_builder(builder $builder, bool $active_only = false): void {
        // NOTE: The $active_only parameter is not used because all recipients are active!
        $builder->add_select_raw('1 AS active');
    }

    /**
     * {@inheritDoc}
     *
     * NOTE: the function does nothing.
     */
    public function activate(bool $active = true): notification_interface {
        return $this;
    }
}
