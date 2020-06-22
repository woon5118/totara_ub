<!--
  This file is part of Totara Learn

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @package mod_perform
-->
<template>
  <div class="tui-performAssignmentSchedule">
    <h3 class="tui-performAssignmentSchedule__heading">
      {{ $str('activity_instance_creation_heading', 'mod_perform') }}
    </h3>

    <ScheduleToggles
      :schedule-is-open.sync="scheduleIsOpen"
      :schedule-is-fixed.sync="scheduleIsFixed"
      :due-date-is-enabled.sync="dueDateIsEnabled"
      :repeating-is-enabled.sync="repeatingIsEnabled"
    />

    <Uniform
      v-slot="{ getSubmitting, reset }"
      :initial-values="initialValues"
      input-width="full"
      :validate="validator"
      @submit="trySave"
      @change="onChange"
    >
      <ScheduleSettings
        :is-open="scheduleIsOpen"
        :is-fixed="scheduleIsFixed"
        :dynamic-date-sources="dynamicDateSources"
      />

      <FrequencySettings
        :is-open="scheduleIsOpen"
        :is-repeating="repeatingIsEnabled"
        :repeating-type="repeatingType"
      />

      <DueDateSettings
        :is-enabled="dueDateIsEnabled"
        :is-fixed="dueDateIsFixed"
        :schedule-is-limited-fixed="!scheduleIsOpen && scheduleIsFixed"
      />

      <AdditionalScheduleSettings v-if="isGenerationControlEnabled" />

      <div class="tui-performAssignmentSchedule__action">
        <ButtonGroup>
          <Button
            :disabled="isSaving"
            :styleclass="{ primary: 'true' }"
            :text="$str('save_changes', 'mod_perform')"
            type="submit"
          />
          <Button
            :disabled="isSaving"
            :text="$str('cancel', 'moodle')"
            @click="reset"
          />
        </ButtonGroup>
      </div>
    </Uniform>
  </div>
</template>

<script>
// Imports
import AdditionalScheduleSettings from 'mod_perform/components/manage_activity/assignment/schedule/AdditionalScheduleSettings';
import Button from 'totara_core/components/buttons/Button';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import FrequencySettings from 'mod_perform/components/manage_activity/assignment/schedule/FrequencySettings';
import DueDateSettings from 'mod_perform/components/manage_activity/assignment/schedule/DueDateSettings';
import ScheduleSettings from 'mod_perform/components/manage_activity/assignment/schedule/ScheduleSettings';
import ScheduleToggles from 'mod_perform/components/manage_activity/assignment/schedule/ScheduleToggles';
import UpdateTrackScheduleMutation from 'mod_perform/graphql/update_track_schedule';
import { Uniform } from 'totara_core/components/uniform';

// Util
import { notify } from 'totara_core/notifications';
import {
  NOTIFICATION_DURATION,
  RELATIVE_DATE_DIRECTION_BEFORE,
  RELATIVE_DATE_UNIT_DAY,
  RELATIVE_DATE_UNIT_WEEK,
  SCHEDULE_REPEATING_TYPE_AFTER_CREATION,
  SCHEDULE_REPEATING_TYPE_AFTER_CREATION_WHEN_COMPLETE,
  SCHEDULE_REPEATING_TYPE_AFTER_COMPLETION,
} from 'mod_perform/constants';

export default {
  components: {
    AdditionalScheduleSettings,
    Button,
    ButtonGroup,
    FrequencySettings,
    DueDateSettings,
    ScheduleSettings,
    ScheduleToggles,
    Uniform,
  },
  props: {
    track: {
      type: Object,
      required: true,
    },
    dynamicDateSources: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      scheduleIsOpen: this.track.schedule_is_open,
      scheduleIsFixed: this.track.schedule_is_fixed,
      dueDateIsEnabled: this.track.due_date_is_enabled,
      dueDateIsFixed: this.track.due_date_is_fixed || false,
      repeatingIsEnabled: this.track.repeating_is_enabled,
      repeatingType:
        this.track.repeating_type || SCHEDULE_REPEATING_TYPE_AFTER_CREATION,
      isSaving: false,
      initialValues: this.getInitialValues(this.track),
      isGenerationControlEnabled: this.track
        .subject_instance_generation_control_is_enabled,
    };
  },
  methods: {
    /**
     * React to changes in the form.
     */
    onChange(values) {
      this.dueDateIsFixed = values.dueDateIsFixed === 'true';
      this.repeatingType = values.repeatingType;
    },

    /**
     * Validate that the dates inputted are logically correct.
     */
    validator(values) {
      if (!this.dueDateIsEnabled) {
        return {};
      }

      const dueDateErrorString = this.$str(
        'due_date_error_must_be_after_creation_date',
        'mod_perform'
      );

      if (this.dueDateIsFixed) {
        let creationEndDate = new Date(values.scheduleFixed.to);
        let dueDate = new Date(values.dueDateFixed.from);
        if (creationEndDate >= dueDate) {
          return {
            dueDateFixed: {
              from: dueDateErrorString,
            },
          };
        }
      } else {
        if (values.dueDateOffset.from_count <= 0) {
          return {
            dueDateOffset: {
              from_count: dueDateErrorString,
            },
          };
        }
      }

      return {};
    },

    /**
     * Generate the initial form values.
     * Called upon page load, and after saving.
     * @param {Object} track object
     * @return {Object}
     */
    getInitialValues(track) {
      let schedule_dynamic_from_count = '0';
      let schedule_dynamic_from_unit = RELATIVE_DATE_UNIT_DAY;
      let schedule_dynamic_from_direction = RELATIVE_DATE_DIRECTION_BEFORE;
      if (track.schedule_dynamic_from) {
        schedule_dynamic_from_count = track.schedule_dynamic_from.count;
        schedule_dynamic_from_unit = track.schedule_dynamic_from.unit;
        schedule_dynamic_from_direction = track.schedule_dynamic_from.direction;
      }

      let schedule_dynamic_to_count = '0';
      let schedule_dynamic_to_unit = RELATIVE_DATE_UNIT_DAY;
      let schedule_dynamic_to_direction = RELATIVE_DATE_DIRECTION_BEFORE;
      if (track.schedule_dynamic_to) {
        schedule_dynamic_to_count = track.schedule_dynamic_to.count;
        schedule_dynamic_to_unit = track.schedule_dynamic_to.unit;
        schedule_dynamic_to_direction = track.schedule_dynamic_to.direction;
      }

      let due_date_offset_count = '14';
      let due_date_offset_unit = RELATIVE_DATE_UNIT_DAY;
      if (track.due_date_offset) {
        due_date_offset_count = track.due_date_offset.count;
        due_date_offset_unit = track.due_date_offset.unit;
      }

      return {
        // Creation range initial settings
        scheduleFixed: {
          from: this.getInitialDate(track.schedule_fixed_from),
          to: this.getInitialDate(track.schedule_fixed_to),
        },
        scheduleDynamic: {
          from_count: schedule_dynamic_from_count, // Uniform required validation doesn't support int 0 at time of writing.
          from_unit: schedule_dynamic_from_unit,
          from_direction: schedule_dynamic_from_direction,
          to_count: schedule_dynamic_to_count, // Uniform required validation doesn't support int 0 at time of writing.
          to_unit: schedule_dynamic_to_unit,
          to_direction: schedule_dynamic_to_direction,
          dynamic_source: this.getCombinedDynamicSourceKey(track),
          use_anniversary: track.schedule_use_anniversary,
        },

        // Due date initial settings
        dueDateIsFixed: (track.due_date_is_fixed || false).toString(),
        dueDateFixed: {
          from: this.getInitialDate(track.due_date_fixed),
        },
        dueDateOffset: {
          from_count: due_date_offset_count, // Uniform required validation doesn't support int 0 at time of writing.
          from_unit: due_date_offset_unit,
        },
        additionalSettings: {
          multiple_job_assignment: track.subject_instance_generation,
        },

        // Repeating initial settings
        repeatingType:
          track.repeating_type || SCHEDULE_REPEATING_TYPE_AFTER_CREATION,
        repeatingIsLimited: track.repeating_is_limited || false,
        repeatingLimit: track.repeating_limit || '3',
        repeatingOffset: this.initRepeatingOffset(track),
      };
    },

    /**
     * Get a string key representing a resolver and option key pair.
     *
     * @param track {Object}
     * @returns {String}
     */
    getCombinedDynamicSourceKey(track) {
      let dynamic_source = null;
      if (track.schedule_dynamic_source) {
        dynamic_source = track.schedule_dynamic_source;
      } else {
        dynamic_source = this.dynamicDateSources[0];
      }

      return `${dynamic_source.resolver_class_name}--${dynamic_source.option_key}`;
    },

    /**
     * Try to persist the schedule to the back end
     * Emitting events on success/failure.
     *
     * @returns {Promise<void>}
     */
    async trySave(values) {
      this.isSaving = true;
      try {
        const track = await this.save(this.getMutationVariables(values));
        this.initialValues = this.getInitialValues(track);
        this.showSuccessNotification();
        this.isSaving = false;
      } catch (e) {
        this.showErrorNotification();
        this.isSaving = false;
      }
    },

    /**
     * Extract the Uniform values and instance variables
     * into a variable payload for the save mutation.
     * @return {Object}
     */
    getMutationVariables(formValues) {
      const track_schedule = Object.assign(
        {
          track_id: this.track.id,
        },
        this.getScheduleVariables(formValues),
        this.getRepeatingVariables(formValues),
        this.getDueDateVariables(formValues),
        this.getAdditionalSettingsVariables(formValues)
      );
      return { track_schedule };
    },

    /**
     * Add the required schedule variables for the mutation.
     * @return Object
     */
    getScheduleVariables(form) {
      const gql = {};

      gql.schedule_is_open = this.scheduleIsOpen;
      gql.schedule_is_fixed = this.scheduleIsFixed;

      if (this.scheduleIsFixed) {
        // Fixed start date
        gql.schedule_fixed_from = this.getUnixTime(form.scheduleFixed.from);

        if (!this.scheduleIsOpen) {
          // Fixed start date with closing date
          gql.schedule_fixed_to = this.getUnixTime(form.scheduleFixed.to);
        }
      } else {
        // Dynamic start date
        gql.schedule_dynamic_from = {};
        gql.schedule_dynamic_from.count = Number(
          form.scheduleDynamic.from_count
        ); // Gql does not handle "-1" and an int type.
        gql.schedule_dynamic_from.unit = form.scheduleDynamic.from_unit;
        gql.schedule_dynamic_from.direction =
          form.scheduleDynamic.from_direction;
        gql.schedule_use_anniversary = form.scheduleDynamic.use_anniversary;

        const dynamicDateSourceParts = form.scheduleDynamic.dynamic_source.split(
          '--'
        );
        gql.schedule_dynamic_source = {
          resolver_class_name: dynamicDateSourceParts[0],
          option_key: dynamicDateSourceParts[1],
        };

        gql.due_date_offset = null;

        if (!this.scheduleIsOpen) {
          // Dynamic start date with closing date
          gql.schedule_dynamic_to = {};
          gql.schedule_dynamic_to.count = Number(form.scheduleDynamic.to_count); // Gql does not handle "-1" and an int type.
          gql.schedule_dynamic_to.unit = form.scheduleDynamic.to_unit;
          gql.schedule_dynamic_to.direction = form.scheduleDynamic.to_direction;
        }
      }

      return gql;
    },

    /**
     * Initialise repeatingOffset for each possible repeating type
     * @param {Object} track
     * @return {Object}
     */
    initRepeatingOffset(track) {
      // We want a separate relative relative date for each type.
      let repeatingOffset = [];
      let repeatingTypes = [
        SCHEDULE_REPEATING_TYPE_AFTER_CREATION,
        SCHEDULE_REPEATING_TYPE_AFTER_CREATION_WHEN_COMPLETE,
        SCHEDULE_REPEATING_TYPE_AFTER_COMPLETION,
      ];

      // First set all to the default
      repeatingTypes.forEach(currentType => {
        repeatingOffset[currentType] = {
          from_count: '4',
          from_unit: RELATIVE_DATE_UNIT_WEEK,
        };
      });

      // Now set the currently selected type's stored values
      if (track.repeating_type) {
        let repeating_offset_count =
          repeatingOffset[track.repeating_type].from_count;
        let repeating_offset_unit =
          repeatingOffset[track.repeating_type].from_unit;
        if (track.repeating_offset) {
          repeating_offset_count = track.repeating_offset.count;
          repeating_offset_unit = track.repeating_offset.unit;
        }
        repeatingOffset[
          track.repeating_type
        ].from_count = repeating_offset_count;
        repeatingOffset[track.repeating_type].from_unit = repeating_offset_unit;
      }

      return repeatingOffset;
    },

    /**
     * Add the required repeating variables for the mutation.
     * @return Object
     */
    getRepeatingVariables(form) {
      const gql = {};

      gql.repeating_is_enabled = this.repeatingIsEnabled;
      if (gql.repeating_is_enabled) {
        gql.repeating_type = form.repeatingType;

        let key = gql.repeating_type;
        gql.repeating_offset = {};
        gql.repeating_offset.count = Number(
          form.repeatingOffset[key].from_count
        );
        gql.repeating_offset.unit = form.repeatingOffset[key].from_unit;

        gql.repeating_is_limited = form.repeatingIsLimited;
        if (gql.repeating_is_limited) {
          gql.repeating_limit = Number(form.repeatingLimit);
        }
      }

      return gql;
    },

    /**
     * Add the required due date variables for the mutation.
     * @return Object
     */
    getDueDateVariables(form) {
      const gql = {};

      gql.due_date_is_enabled = this.dueDateIsEnabled;

      if (!this.dueDateIsEnabled) {
        return gql;
      }

      if (!this.scheduleIsOpen && this.scheduleIsFixed) {
        gql.due_date_is_fixed = this.dueDateIsFixed;
      }

      if (gql.due_date_is_fixed) {
        gql.due_date_fixed = this.getUnixTime(form.dueDateFixed.from);
      } else {
        gql.due_date_offset = {};
        gql.due_date_offset.count = Number(form.dueDateOffset.from_count); // Gql does not handle "-1" and an int type.
        gql.due_date_offset.unit = form.dueDateOffset.from_unit;
      }

      return gql;
    },

    /**
     * Add the additional settings variables for the mutation.
     * @return Object
     */
    getAdditionalSettingsVariables(form) {
      const gql = {};
      if (this.isGenerationControlEnabled) {
        gql.subject_instance_generation =
          form.additionalSettings.multiple_job_assignment;
      }

      return gql;
    },

    /**
     * Calling the mutation
     * @returns {Promise<any>}
     */
    async save(variables) {
      const { data: resultData } = await this.$apollo.mutate({
        mutation: UpdateTrackScheduleMutation,
        variables,
      });

      return resultData.mod_perform_update_track_schedule.track;
    },

    /**
     * TODO: Remove this and usages after proper date picker implementation.
     *
     * Convert a date string date into a unix time stamp.
     * If a falsey dateString value is supplied null will be returned.
     *
     * @param dataString {string|null|undefined}
     * @return {int|null}
     */
    getUnixTime(dataString) {
      if (!dataString) {
        return null;
      }

      return new Date(dataString).getTime() / 1000;
    },

    /**
     * TODO: Remove this and usages after proper date picker implementation.
     *
     * Get the initial time to display from graphql attribute.
     *
     * @param variable {int}
     * @return String date string
     */
    getInitialDate(variable) {
      let date = new Date();
      if (variable) {
        date = new Date(variable * 1000);
      }
      return date.toISOString().substring(0, 10);
    },

    /**
     * Show a generic saving error toast.
     */
    showErrorNotification() {
      notify({
        duration: NOTIFICATION_DURATION,
        message: this.$str('toast_error_generic_update', 'mod_perform'),
        type: 'error',
      });
    },

    /**
     * Show a generic saving success toast.
     */
    showSuccessNotification() {
      notify({
        duration: NOTIFICATION_DURATION,
        message: this.$str('toast_success_save_schedule', 'mod_perform'),
        type: 'success',
      });
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "activity_instance_creation_heading",
      "due_date_error_must_be_after_creation_date",
      "save_changes",
      "toast_error_generic_update",
      "toast_success_save_schedule"
    ],
    "moodle": [
      "cancel"
    ]
  }
</lang-strings>
