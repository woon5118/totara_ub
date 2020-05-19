<!--
  This file is part of Totara Learn

  Copyright (C) 2019 onwards Totara Learning Solutions LTD

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

  @author Jaron Steenson jaron.steenson@totaralearning.com>
  @package mod_perform
-->

<template>
  <FormScope :validate="dynamicFromUntilValidator" :path="uniformScopePath">
    <div class="tui_performAssignmentSchedule__narrative-inputs">
      <Label
        :for="$id('schedule_dynamic_count_from')"
        :label="$str('schedule_date_from', 'mod_perform')"
        class="tui_performAssignmentSchedule__narrative-label"
      />
      <div class="tui_performAssignmentSchedule__narrative-count">
        <FormNumber
          :id="$id('schedule_dynamic_count_from')"
          :name="dynamicCountFromName"
          :validations="dynamicCountValidator"
          :min="0"
        />
      </div>

      <Label
        v-if="scheduleIsLimitedDynamic"
        :for-id="$id('schedule_dynamic_count_to')"
        :label="$str('schedule_date_range_until', 'mod_perform')"
        class="tui_performAssignmentSchedule__narrative-label"
      />

      <div
        v-if="scheduleIsLimitedDynamic"
        class="tui_performAssignmentSchedule__narrative-count"
      >
        <FormNumber
          :id="$id('schedule_dynamic_count_to')"
          :name="dynamicCountToName"
          :validations="dynamicCountValidator"
        />
      </div>

      <div class="tui_performAssignmentSchedule__narrative-select">
        <FormSelect
          :id="$id('schedule_dynamic_unit')"
          :aria-label="$str('schedule_dynamic_unit_label', 'mod_perform')"
          :options="unitOptions"
          :name="dynamicUnitName"
        />
      </div>

      <div class="tui_performAssignmentSchedule__narrative-select">
        <FormSelect
          :id="$id('schedule_dynamic_direction')"
          :aria-label="
            $str('schedule_dynamic_before_after_label', 'mod_perform')
          "
          :options="directionOptions"
          :name="dynamicDirectionName"
        />
      </div>

      <div class="tui_performAssignmentSchedule__narrative-label">
        <strong>
          {{ $str('user_creation_date', 'mod_perform') }}
        </strong>
      </div>
    </div>
  </FormScope>
</template>
<script>
import FormNumber from 'totara_core/components/uniform/FormNumber';
import FormScope from 'totara_core/components/reform/FormScope';
import FormSelect from 'totara_core/components/uniform/FormSelect';
import Label from 'totara_core/components/form/Label';
import {
  SCHEDULE_DYNAMIC_DIRECTION_BEFORE,
  SCHEDULE_DYNAMIC_DIRECTION_AFTER,
  SCHEDULE_DYNAMIC_UNIT_DAY,
  SCHEDULE_DYNAMIC_UNIT_WEEK,
  SCHEDULE_DYNAMIC_UNIT_MONTH,
} from 'mod_perform/constants';

export default {
  components: { FormNumber, FormScope, FormSelect, Label },
  props: {
    uniformScopePath: {
      required: true,
      type: String,
    },
    dynamicCountFromName: {
      required: true,
      type: String,
    },
    dynamicCountToName: {
      required: true,
      type: String,
    },
    dynamicUnitName: {
      required: true,
      type: String,
    },
    dynamicDirectionName: {
      required: true,
      type: String,
    },
    scheduleIsLimitedDynamic: {
      required: true,
      type: Boolean,
    },
  },
  computed: {
    unitOptions() {
      return [
        {
          label: this.$str('schedule_dynamic_unit_days', 'mod_perform'),
          id: SCHEDULE_DYNAMIC_UNIT_DAY,
        },
        {
          label: this.$str('schedule_dynamic_unit_weeks', 'mod_perform'),
          id: SCHEDULE_DYNAMIC_UNIT_WEEK,
        },
        {
          label: this.$str('schedule_dynamic_unit_months', 'mod_perform'),
          id: SCHEDULE_DYNAMIC_UNIT_MONTH,
        },
      ];
    },
    directionOptions() {
      return [
        {
          label: this.$str('schedule_dynamic_direction_before', 'mod_perform'),
          id: SCHEDULE_DYNAMIC_DIRECTION_BEFORE,
        },
        {
          label: this.$str('schedule_dynamic_direction_after', 'mod_perform'),
          id: SCHEDULE_DYNAMIC_DIRECTION_AFTER,
        },
      ];
    },
  },
  methods: {
    /**
     * All the validation functions for dynamic count fields.
     *
     * @return {function[]}
     */
    dynamicCountValidator(v) {
      return [v.min(0), v.integer(), v.required()];
    },

    /**
     * Validator for the dynamic instance creation to/until range.
     * From must not be after until.
     *
     * @param values
     * @return {{}}
     */
    dynamicFromUntilValidator(values) {
      const from = Number(values.dynamic_count_from);
      const until = Number(values.dynamic_count_to);

      if (!this.scheduleIsLimitedDynamic) {
        return {};
      }

      if (
        values.dynamic_direction === SCHEDULE_DYNAMIC_DIRECTION_AFTER &&
        from > until
      ) {
        return {
          dynamic_count_to: this.$str(
            'schedule_error_date_range',
            'mod_perform'
          ),
        };
      } else if (
        values.dynamic_direction === SCHEDULE_DYNAMIC_DIRECTION_BEFORE &&
        from < until
      ) {
        return {
          dynamic_count_from: this.$str(
            'schedule_error_date_range',
            'mod_perform'
          ),
        };
      }

      return {};
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "schedule_date_from",
      "schedule_date_range_until",
      "schedule_dynamic_before_after_label",
      "schedule_dynamic_direction_after",
      "schedule_dynamic_direction_before",
      "schedule_dynamic_unit_days",
      "schedule_dynamic_unit_label",
      "schedule_dynamic_unit_months",
      "schedule_dynamic_unit_weeks",
      "schedule_error_date_range",
      "user_creation_date"
    ]
  }
</lang-strings>
