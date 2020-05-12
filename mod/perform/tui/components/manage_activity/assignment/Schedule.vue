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
    <div
      v-if="initialValues"
      class="tui_performAssignmentSchedule__controls-container"
    >
      <div class="tui_performAssignmentSchedule__controls">
        <ToggleSet v-model="scheduleOpenClosed">
          <ToggleButtonIcon
            value="closed"
            :label="$str('schedule_is_closed', 'mod_perform')"
            class="tui_performAssignmentSchedule__toggle-button"
          >
            <div class="tui_performAssignmentSchedule__toggle-button-content">
              <CalendarIcon />
              <CalendarIcon />
              <div class="tui_performAssignmentSchedule__button-label">
                {{ $str('schedule_is_closed', 'mod_perform') }}
              </div>
            </div>
          </ToggleButtonIcon>
          <ToggleButtonIcon
            value="open"
            :label="$str('schedule_is_open', 'mod_perform')"
            class="tui_performAssignmentSchedule__toggle-button"
          >
            <div class="tui_performAssignmentSchedule__toggle-button-content">
              <CalendarIcon />
              <CalendarIcon />
              <CalendarIcon />
              <div class="tui_performAssignmentSchedule__button-label">
                {{ $str('schedule_is_open', 'mod_perform') }}
              </div>
            </div>
          </ToggleButtonIcon>
        </ToggleSet>
      </div>
      <div class="tui_performAssignmentSchedule__controls">
        <ToggleSet v-model="scheduleFixedDynamic">
          <ToggleButtonIcon
            value="fixed"
            :label="$str('schedule_is_fixed', 'mod_perform')"
            class="tui_performAssignmentSchedule__toggle-button"
          >
            <div class="tui_performAssignmentSchedule__toggle-button-content">
              <CalendarIcon :size="300" />
              <div class="tui_performAssignmentSchedule__button-label">
                {{ $str('schedule_is_fixed', 'mod_perform') }}
              </div>
            </div>
          </ToggleButtonIcon>
          <ToggleButtonIcon
            value="dynamic"
            :label="$str('schedule_is_dynamic', 'mod_perform')"
            class="tui_performAssignmentSchedule__toggle-button"
          >
            <div class="tui_performAssignmentSchedule__toggle-button-content">
              <UserIcon :size="300" />
              <div class="tui_performAssignmentSchedule__button-label">
                {{ $str('schedule_is_dynamic', 'mod_perform') }}
              </div>
            </div>
          </ToggleButtonIcon>
        </ToggleSet>
      </div>
    </div>
    <Uniform
      v-slot="{ reset }"
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
            class="tui_performAssignmentSchedule__action-submit"
            :disabled="isSaving"
          />
          <Button
            :text="$str('cancel', 'moodle')"
            :disabled="isSaving"
            @click="reset"
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
import UserIcon from 'mod_perform/components/manage_activity/assignment/schedule/icon/User';
import ButtonGroup from 'totara_core/components/buttons/ButtonGroup';
import Button from 'totara_core/components/buttons/Button';
import DateRangeClosedFixed from 'mod_perform/components/manage_activity/assignment/schedule/DateRangeClosedFixed';
import DateRangeOpenFixed from 'mod_perform/components/manage_activity/assignment/schedule/DateRangeOpenFixed';
import UpdateTrackScheduleMutation from 'mod_perform/graphql/update_track_schedule';

const SCHEDULE_IS_OPEN = 'open';
const SCHEDULE_IS_CLOSED = 'closed';
const SCHEDULE_IS_FIXED = 'fixed';
const SCHEDULE_IS_DYNAMIC = 'dynamic';

export default {
  components: {
    ToggleSet,
    ToggleButtonIcon,
    CalendarIcon,
    UserIcon,
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
    const scheduleOpenClosed = this.track.schedule_is_open
      ? SCHEDULE_IS_OPEN
      : SCHEDULE_IS_CLOSED;
    const scheduleFixedDynamic = this.track.schedule_is_fixed
      ? SCHEDULE_IS_FIXED
      : SCHEDULE_IS_DYNAMIC;
    let initialValues = {};

    const fromDate = this.getFormattedTime(this.track.schedule_fixed_from);
    const toDate = this.getFormattedTime(this.track.schedule_fixed_to);

    if (scheduleFixedDynamic === SCHEDULE_IS_FIXED) {
      if (scheduleOpenClosed === SCHEDULE_IS_OPEN) {
        initialValues = {
          open: { from: fromDate },
          closed: { from: fromDate },
        };
      } else {
        // Closed.
        initialValues = {
          open: {
            from: fromDate,
          },
          closed: { from: fromDate, to: toDate },
        };
      }
    } else {
      // Dynamic.
      if (scheduleOpenClosed === SCHEDULE_IS_OPEN) {
        // TODO in dynamic form patch
      } else {
        // Closed.
        // TODO in dynamic form patch
      }
    }

    return {
      scheduleOpenClosed: scheduleOpenClosed,
      scheduleFixedDynamic: scheduleFixedDynamic,
      initialValues: initialValues,
      isSaving: false,
    };
  },
  methods: {
    /**
     * Get current form component
     */
    getFormComponent() {
      // TODO in dynamic form patch
      if (this.scheduleOpenClosed == SCHEDULE_IS_CLOSED) {
        return 'DateRangeClosedFixed';
      }

      return 'DateRangeOpenFixed';
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
    async trySave(values) {
      this.isSaving = true;

      try {
        const resultData = await this.save(values);
        let fromDate = null;
        let toDate = null;
        console.log(resultData);
        const track = resultData.mod_perform_update_track_schedule.track;
        if (this.scheduleFixedDynamic == SCHEDULE_IS_FIXED) {
          if (this.scheduleOpenClosed == SCHEDULE_IS_CLOSED) {
            fromDate = this.getFormattedTime(track.schedule_fixed_from);
            toDate = this.getFormattedTime(track.schedule_fixed_to);
          } else {
            // Open.
            fromDate = this.getFormattedTime(track.schedule_fixed_from);
            toDate = this.getFormattedTime(track.schedule_fixed_to);
          }
        }
        this.initialValues = {
          closed: {
            from: fromDate,
            to: toDate,
          },
          open: { from: fromDate },
        };
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
    async save(values) {
      let track_schedule = {
        track_id: this.track.id,
        is_open: this.scheduleOpenClosed == SCHEDULE_IS_OPEN,
        is_fixed: this.scheduleFixedDynamic == SCHEDULE_IS_FIXED,
      };
      if (this.scheduleFixedDynamic == SCHEDULE_IS_FIXED) {
        if (this.scheduleOpenClosed == SCHEDULE_IS_CLOSED) {
          track_schedule.fixed_from = this.getUnixTime(values.closed.from);
          track_schedule.fixed_to = this.getUnixTime(values.closed.to);
        } else {
          // Open.
          track_schedule.fixed_from = this.getUnixTime(values.open.from);
        }
      } else {
        // Dynamic.
        if (this.scheduleOpenClosed == SCHEDULE_IS_CLOSED) {
          // TODO in TL-24472
        } else {
          // Open.
          // TODO in TL-24472
        }
      }

      const { data: resultData } = await this.$apollo.mutate({
        mutation: UpdateTrackScheduleMutation,
        variables: {
          track_schedule: track_schedule,
        },
      });
      return resultData;
    },
    getFormattedTime(timestamp) {
      if (timestamp) {
        return new Date(timestamp * 1000).toLocaleDateString('en-US');
      }
    },
    getUnixTime(dataString) {
      return new Date(dataString).getTime() / 1000;
    },
  },
};
</script>
<lang-strings>
  {
  "mod_perform" : [
    "save_changes",
    "schedule_is_closed",
    "schedule_is_fixed",
    "schedule_is_dynamic",
    "schedule_is_open",
    "schedule_creation_range_and_date_type",
    "toast_success_save_schedule",
    "toast_error_generic_update"
  ],
  "moodle": [
    "cancel"
  ]
  }
</lang-strings>
