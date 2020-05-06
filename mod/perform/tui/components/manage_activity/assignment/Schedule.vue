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
  <div class="tui_performAssignmentSchedule">
    <h4>{{ $str('schedule_creation_range_and_date_type', 'mod_perform') }}</h4>
    <div class="tui_performAssignmentSchedule__controls">
      <ToggleSet v-model="scheduleOpenClosed">
        <ToggleButtonIcon
          value="closed"
          label="left label"
          text="Limited"
          class="tui_performAssignmentSchedule__toggle-button"
        >
          <div class="tui_performAssignmentSchedule__toggle-button-content">
            <CalendarIcon />
            <CalendarIcon />
            <div class="tui_performAssignmentSchedule__button-label">
              {{ $str('schedule_type_closed', 'mod_perform') }}
            </div>
          </div>
        </ToggleButtonIcon>
        <ToggleButtonIcon
          value="open"
          label="right label"
          text="Open-ended"
          class="tui_performAssignmentSchedule__toggle-button"
        >
          <div class="tui_performAssignmentSchedule__toggle-button-content">
            <CalendarIcon />
            <CalendarIcon />
            <CalendarIcon />
            <div class="tui_performAssignmentSchedule__button-label">
              {{ $str('schedule_type_open', 'mod_perform') }}
            </div>
          </div>
        </ToggleButtonIcon>
      </ToggleSet>
    </div>
    <Uniform
      :initial-values="initialValues"
      input-width="full"
      @submit="trySave"
    >
      <div class="tui_performAssignmentSchedule__data">
        <component :is="getFormComponent()" :path="scheduleOpenClosed" />
      </div>

      <div class="tui_performAssignmentSchedule__action">
        <ButtonGroup>
          <Button
            :styleclass="{ primary: 'true' }"
            :text="$str('save_changes', 'mod_perform')"
            type="submit"
            :disabled="isSaving"
          />
          <Button
            :text="$str('cancel', 'moodle')"
            :disabled="isSaving"
            @click="cancel"
          />
        </ButtonGroup>
      </div>
    </Uniform>
  </div>
</template>
<script>
import { Uniform } from 'totara_core/components/uniform';
import { notify } from 'totara_core/notifications';
import ToggleSet from 'totara_core/components/buttons/ToggleSet';
import ToggleButtonIcon from 'totara_core/components/buttons/ToggleButtonIcon';
import CalendarIcon from 'mod_perform/components/manage_activity/assignment/schedule/icon/Calendar';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import Button from 'totara_core/components/buttons/Button';
import DateRangeClosedFixed from 'mod_perform/components/manage_activity/assignment/schedule/DateRangeClosedFixed';
import DateRangeOpenFixed from 'mod_perform/components/manage_activity/assignment/schedule/DateRangeOpenFixed';
import UpdateTrackScheduleClosedFixedMutation from 'mod_perform/graphql/update_track_schedule_closed_fixed';
import UpdateTrackScheduleOpenFixedMutation from 'mod_perform/graphql/update_track_schedule_open_fixed';

const SCHEDULE_TYPE_OPEN = 'open';
const SCHEDULE_TYPE_CLOSED = 'closed';
const SCHEDULE_TYPE_OPEN_VALUE = 0;
const SCHEDULE_TYPE_CLOSED_VALUE = 1;

export default {
  components: {
    ToggleSet,
    ToggleButtonIcon,
    CalendarIcon,
    Uniform,
    ButtonGroup,
    Button,
    DateRangeClosedFixed,
    DateRangeOpenFixed,
  },
  props: {
    track: {
      type: Object,
      required: true,
    },
  },
  data() {
    let scheduleOpenClosed = null;
    let initialValues = {};
    switch (this.track.schedule_type) {
      case SCHEDULE_TYPE_OPEN_VALUE:
        scheduleOpenClosed = SCHEDULE_TYPE_OPEN;
        initialValues = {
          closedFrom: '',
          closedTo: '',
        };
        break;
      case SCHEDULE_TYPE_CLOSED_VALUE:
        scheduleOpenClosed = SCHEDULE_TYPE_CLOSED;
        initialValues = {
          openFrom: '',
        };
        break;
    }
    return {
      scheduleOpenClosed: scheduleOpenClosed,
      initialValues: initialValues,
      isSaving: false,
    };
  },
  methods: {
    /**
     * Get current form component
     */
    getFormComponent() {
      if (this.scheduleOpenClosed == SCHEDULE_TYPE_CLOSED) {
        return 'DateRangeClosedFixed';
      }

      return 'DateRangeOpenFixed';
    },

    /**
     * Canceling the form
     */
    cancel() {
      console.log('canceling');
    },

    /**
     * Show a generic saving error toast.
     */
    showErrorNotification() {
      notify({
        duration: 10000,
        message: this.$str('toast_error_generic_update', 'mod_perform'),
        type: 'error',
      });
    },

    /**
     * Show a generic saving error toast.
     */
    showSuccessNotification() {
      notify({
        duration: 10000,
        message: this.$str('toast_success_save_schedule', 'mod_perform'),
        type: 'success',
      });
    },

    /**
     * Try to persist the schedule to the back end
     * Emitting events on success/failure.
     *
     * @returns {Promise<void>}
     */
    async trySave() {
      this.isSaving = true;

      try {
        await this.save();
        this.showSuccessNotification();
      } catch (e) {
        this.showErrorNotification();
        // If something goes wrong during create, allow the user to try again.
        this.isSaving = false;
      } finally {
        this.isSaving = false;
      }
    },

    /**
     * Calling the mutation
     * @returns {Promise<any>}
     */
    async save() {
      const mutation =
        this.scheduleOpenClosed == SCHEDULE_TYPE_CLOSED
          ? UpdateTrackScheduleClosedFixedMutation
          : UpdateTrackScheduleOpenFixedMutation;
      const track_schedule = { track_id: this.track.id };

      const { data: resultData } = await this.$apollo.mutate({
        mutation: mutation,
        variables: {
          track_schedule: track_schedule,
        },
      });
      return resultData;
    },
  },
};
</script>
<lang-strings>
  {
  "mod_perform" : [
    "save_changes",
    "schedule_type_closed",
    "schedule_type_open",
    "schedule_creation_range_and_date_type",
    "toast_success_save_schedule",
    "toast_error_generic_update"
  ],
  "moodle": [
    "cancel"
  ]
  }
</lang-strings>
