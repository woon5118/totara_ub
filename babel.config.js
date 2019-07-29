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
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @package totara_core
 */

/* Switch eslint config environment to 'node' to prevent 'module' definition error */
/* eslint-env node */

const babelConfigs = require('./totara/core/tui/scripts/configs/babel');

module.exports = api => {
    const isTest = api.env('test');
    const legacy = !!process.env.BABEL_LEGACY;

    if (isTest) {
        if (legacy) {
            return babelConfigs.legacy;
        } else {
            return babelConfigs.test;
        }
    }

    return {};
};
