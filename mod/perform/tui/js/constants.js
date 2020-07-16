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

// Corresponds to activity state classes found in \mod_perform\state\activity
export const ACTIVITY_STATUS_ACTIVE = 'ACTIVE';
export const ACTIVITY_STATUS_DRAFT = 'DRAFT';

export const RELATIVE_DATE_DIRECTION_AFTER = 'AFTER';
export const RELATIVE_DATE_DIRECTION_BEFORE = 'BEFORE';
export const RELATIVE_DATE_UNIT_DAY = 'DAY';
export const RELATIVE_DATE_UNIT_WEEK = 'WEEK';

// These should correspond to constants found in mod/perform/classes/constants.php in the back end.
export const SCHEDULE_REPEATING_TYPE_AFTER_CREATION = 'AFTER_CREATION';
export const SCHEDULE_REPEATING_TYPE_AFTER_CREATION_WHEN_COMPLETE =
  'AFTER_CREATION_WHEN_COMPLETE';
export const SCHEDULE_REPEATING_TYPE_AFTER_COMPLETION = 'AFTER_COMPLETION';

export const SUBJECT_INSTANCE_GENERATION_ONE_PER_SUBJECT = 'ONE_PER_SUBJECT';
export const SUBJECT_INSTANCE_GENERATION_ONE_PER_JOB = 'ONE_PER_JOB';

export const DATE_RESOLVER_EMPTY_BASE = 'EMPTY_BASE';
export const DATE_RESOLVER_USER_BASED = 'USER_BASED';
export const DATE_RESOLVER_JOB_BASED = 'JOB_BASED';
