/*
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package mod_perform
 */

// We need to define the constants that we want to use across multiple components here
// because the theme overrides don't support having more than one export per vue file.

export const NOTIFICATION_DURATION = 10 * 1000; // 10 seconds (in milliseconds)

export const SCHEDULE_IS_DYNAMIC = 'dynamic';
export const SCHEDULE_IS_FIXED = 'fixed';
export const SCHEDULE_IS_LIMITED = 'limited';
export const SCHEDULE_IS_OPEN = 'open';
export const SCHEDULE_DYNAMIC_DIRECTION_BEFORE = 'BEFORE';
export const SCHEDULE_DYNAMIC_DIRECTION_AFTER = 'AFTER';
export const SCHEDULE_DYNAMIC_UNIT_DAY = 'DAY';
export const SCHEDULE_DYNAMIC_UNIT_WEEK = 'MONTH';
export const SCHEDULE_DYNAMIC_UNIT_MONTH = 'YEAR';
