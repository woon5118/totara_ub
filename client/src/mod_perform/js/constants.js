/**
 * This file is part of Totara Enterprise Extensions.
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *
 * Totara Enterprise Extensions is provided only to Totara
 * Learning Solutions LTD's customers and partners, pursuant to
 * the terms and conditions of a separate agreement with Totara
 * Learning Solutions LTD or its affiliate.
 *
 * If you do not have an agreement with Totara Learning Solutions
 * LTD, you may not access, use, modify, or distribute this software.
 * Please contact [licensing@totaralearning.com] for more information.
 *
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @module mod_perform
 */

// We need to define the constants that we want to use across multiple components here
// because the theme overrides don't support having more than one export per vue file.

export const NOTIFICATION_DURATION = 10 * 1000; // 10 seconds (in milliseconds)

// The idnumber for the core subject relationship will ALWAYS be 'subject'.
// This also corresponds to \mod_perform\constants::RELATIONSHIP_SUBJECT in the back end.
export const RELATIONSHIP_SUBJECT = 'subject';

// This should correspond to mod_perform\models\activity\activity::NAME_MAX_LENGTH in the back end.
export const ACTIVITY_NAME_MAX_LENGTH = 1024;

// Corresponds to activity state classes found in \mod_perform\state\activity
export const ACTIVITY_STATUS_ACTIVE = 'ACTIVE';
export const ACTIVITY_STATUS_DRAFT = 'DRAFT';

export const INSTANCE_AVAILABILITY_STATUS_OPEN = 'OPEN';
export const INSTANCE_AVAILABILITY_STATUS_CLOSED = 'CLOSED';

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
