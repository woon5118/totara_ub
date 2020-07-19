<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD’s customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @module mod_perform
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
      @submit="showConfirmationModal"
      @change="onChange"
    >
      <ScheduleSettings
        :is-open="scheduleIsOpen"
        :is-fixed="scheduleIsFixed"
        :dynamic-date-sources="dynamicDateSources"
        :dynamic-date-setting-component="dynamicDateSettingComponent"
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

      <div class="tui-performAssignmentSchedule__additional_settings">
        <AdditionalScheduleSettings
          :uses-job-based-dynamic-source="isUsingJobBasedDynamicSource"
          :dynamic-source-name="dynamicDateSourceName"
        />
      </div>

      <div class="tui-performAssignmentSchedule__action">
        <ButtonGroup>
          <Button
            :disabled="isSaving"
            :styleclass="{ primary: 'true' }"
            :text="$str('schedule_save_changes', 'mod_perform')"
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

    <ConfirmationModal
      :title="$str('schedule_confirm_title', 'mod_perform')"
      :confirm-button-text="$str('modal_confirm', 'mod_perform')"
      :open="confirmationModalOpen"
      @confirm="trySave"
      @cancel="hideConfirmationModal"
    >
      <p v-if="isActive">
        {{ $str('schedule_confirm_title_active', 'mod_perform') }}
      </p>
      <p v-else>
        {{ $str('schedule_confirm_title_draft', 'mod_perform') }}
      </p>
    </ConfirmationModal>
  </div>
</template>

<script>
// Imports
import AdditionalScheduleSettings from 'mod_perform/components/manage_activity/assignment/schedule/AdditionalScheduleSettings';
import Button from 'tui/components/buttons/Button';
import ButtonGroup from 'tui/components/buttons/ButtonGroup';
import FrequencySettings from 'mod_perform/components/manage_activity/assignment/schedule/FrequencySettings';
import ConfirmationModal from 'tui/components/modal/ConfirmationModal';
import DueDateSettings from 'mod_perform/components/manage_activity/assignment/schedule/DueDateSettings';
import ScheduleSettings from 'mod_perform/components/manage_activity/assignment/schedule/ScheduleSettings';
import ScheduleToggles from 'mod_perform/components/manage_activity/assignment/schedule/ScheduleToggles';
import UpdateTrackScheduleMutation from 'mod_perform/graphql/update_track_schedule';
import { ACTIVITY_STATUS_ACTIVE } from 'mod_perform/constants';
import { Uniform } from 'tui/components/uniform';

// Util
import { notify } from 'tui/notifications';
import { isIsoAfter } from 'tui/date';
import {
  NOTIFICATION_DURATION,
  RELATIVE_DATE_DIRECTION_BEFORE,
  RELATIVE_DATE_UNIT_DAY,
  RELATIVE_DATE_UNIT_WEEK,
  SCHEDULE_REPEATING_TYPE_AFTER_CREATION,
  SCHEDULE_REPEATING_TYPE_AFTER_CREATION_WHEN_COMPLETE,
  SCHEDULE_REPEATING_TYPE_AFTER_COMPLETION,
  DATE_RESOLVER_JOB_BASED,
} from 'mod_perform/constants';

export default {
  components: {
    AdditionalScheduleSettings,
    Button,
    ButtonGroup,
    ConfirmationModal,
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
    defaultFixedDate: {
      type: Object,
    },
    activityId: {
      type: Number,
      required: true,
    },
    activityState: {
      type: String,
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
      dynamicDateSettingComponent: {
        name: this.getDynamicCustomSettingComponent(),
        data: this.getDynamicCustomSettingData(this.track),
        configData: this.getDynamicCustomSettingConfig(),
      },
      formValuesToSave: null,
      confirmationModalOpen: false,
      isUsingJobBasedDynamicSource: this.dynamicDateSourceIsJobBased(),
      dynamicDateSourceName: this.getDynamicDateSourceName(),
    };
  },
  computed: {
    isActive() {
      return this.activityState == ACTIVITY_STATUS_ACTIVE;
    },
  },
  methods: {
    /**
     * React to changes in the form.
     */
    onChange(values) {
      this.dueDateIsFixed = values.dueDateIsFixed === 'true';
      this.repeatingType = values.repeatingType;

      if (values.scheduleDynamic.dynamic_source) {
        const selectedDynamicSource = this.getSelectedDynamicDateSourceFromValues(
          values
        );

        this.dynamicDateSettingComponent.name =
          selectedDynamicSource.custom_setting_component;

        this.dynamicDateSettingComponent.data = JSON.parse(
          selectedDynamicSource.custom_data
        );

        this.isUsingJobBasedDynamicSource =
          !this.scheduleIsFixed &&
          this.dynamicDateSourceIsJobBased(selectedDynamicSource);
        this.dynamicDateSourceName = this.getDynamicDateSourceName(
          selectedDynamicSource
        );
      }
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
        // Due date must be at least a day after. Note timezones are not factored in here,
        // but with an entire day difference there is a very slim chance of the validation being technically incorrect.
        if (
          values.dueDateFixed.iso === values.scheduleFixed.to.iso ||
          !isIsoAfter(values.dueDateFixed.iso, values.scheduleFixed.to.iso)
        ) {
          return {
            dueDateFixed: dueDateErrorString,
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
          from: track.schedule_fixed_from || this.defaultFixedDate,
          to: track.schedule_fixed_to || this.defaultFixedDate,
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
          dynamicCustomSettings: this.getDynamicCustomSettingData(track),
        },

        // Due date initial settings
        dueDateIsFixed: (track.due_date_is_fixed || false).toString(),
        dueDateFixed: track.due_date_fixed || this.defaultFixedDate,
        dueDateOffset: {
          from_count: due_date_offset_count, // Uniform required validation doesn't support int 0 at time of writing.
          from_unit: due_date_offset_unit,
        },
        additionalSettings: {
          subject_instance_generation: track.subject_instance_generation,
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
    async trySave() {
      const values = this.formValuesToSave;
      this.isSaving = true;
      try {
        const track = await this.save(this.getMutationVariables(values));
        this.initialValues = this.getInitialValues(track);
        this.hideConfirmationModal();
        this.showSuccessNotification();
        this.isSaving = false;
      } catch (e) {
        this.hideConfirmationModal();
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
        let timezone = form.scheduleFixed.from.timezone;

        // Fixed start date
        gql.schedule_fixed_from = {
          iso: form.scheduleFixed.from.iso,
          timezone,
        };

        if (!this.scheduleIsOpen) {
          // Fixed start date with closing date
          gql.schedule_fixed_to = {
            iso: form.scheduleFixed.to.iso,
            timezone,
          };
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

        const customData = Object.assign(
          {},
          this.getDynamicCustomSettingConfig(),
          form.scheduleDynamic.dynamicCustomSettings
        );

        gql.schedule_dynamic_source = {
          resolver_class_name: dynamicDateSourceParts[0],
          option_key: dynamicDateSourceParts[1],
          custom_data: JSON.stringify(customData),
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
        gql.due_date_fixed = form.dueDateFixed;
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
      gql.subject_instance_generation =
        form.additionalSettings.subject_instance_generation;

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

    /**
     * Get dynamic custom setting data
     * @param track
     * @returns {object}
     */
    getDynamicCustomSettingData(track) {
      let data = {};
      if (track.schedule_dynamic_source) {
        data = JSON.parse(track.schedule_dynamic_source.custom_data);
      } else {
        data = JSON.parse(this.dynamicDateSources[0].custom_data);
      }
      return data;
    },

    /**
     * Get dynamic custom setting component
     * @returns {string}
     */
    getDynamicCustomSettingComponent() {
      let componentName = null;
      if (this.track && this.track.schedule_dynamic_source) {
        componentName = this.track.schedule_dynamic_source
          .custom_setting_component;
      } else {
        componentName = this.dynamicDateSources[0].custom_setting_component;
      }
      return componentName;
    },

    /**
     * Get the selected dynamic date source from the uniform values.
     * @return {object}
     */
    getSelectedDynamicDateSourceFromValues(values) {
      return this.dynamicDateSources
        .filter(
          item =>
            `${item.resolver_class_name}--${item.option_key}` ==
            values.scheduleDynamic.dynamic_source
        )
        .shift();
    },

    /**
     * Get dynamic custom setting general configs.
     * @returns {object}
     */
    getDynamicCustomSettingConfig() {
      return { this_activity_id: this.activityId };
    },

    /**
     * Check selected source contains custom settings
     * @param optionKey
     * @returns {boolean}
     */
    isDynamicCustomSettingSource(optionKey) {
      let source = this.dynamicDateSources
        .filter(
          item => `${item.resolver_class_name}--${item.option_key}` == optionKey
        )
        .shift();

      return source.custom_setting_component !== null;
    },

    /**
     * Shows the schedule confirmation dialog.
     */
    showConfirmationModal(values) {
      this.formValuesToSave = values;
      this.confirmationModalOpen = true;
    },

    /**
     * Hides the save confirmation dialog.
     */
    hideConfirmationModal() {
      this.formValuesToSave = null;
      this.confirmationModalOpen = false;
    },

    /**
     * @returns {*|boolean}
     */
    dynamicDateSourceIsJobBased(dynamicSource) {
      if (dynamicSource) {
        return dynamicSource.resolver_base == DATE_RESOLVER_JOB_BASED;
      }
      if (this.track && this.track.schedule_dynamic_source) {
        return (
          this.track.schedule_dynamic_source.resolver_base ==
          DATE_RESOLVER_JOB_BASED
        );
      }
      return false;
    },

    /**
     * @returns {*|string}
     */
    getDynamicDateSourceName(dynamicSource) {
      if (dynamicSource) {
        return dynamicSource.display_name;
      }
      if (this.track && this.track.schedule_dynamic_source) {
        return this.track.schedule_dynamic_source.display_name;
      }
      return null;
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "activity_instance_creation_heading",
      "due_date_error_must_be_after_creation_date",
      "modal_confirm",
      "schedule_confirm_title",
      "schedule_confirm_title_active",
      "schedule_confirm_title_draft",
      "schedule_save_changes",
      "toast_error_generic_update",
      "toast_success_save_schedule"
    ],
    "moodle": [
      "cancel"
    ]
  }
</lang-strings>
