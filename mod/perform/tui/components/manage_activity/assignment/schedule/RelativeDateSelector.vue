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

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
  @package mod_perform
-->

<template>
  <FormScope :validate="relativeDateValidator" :path="path">
    <div class="tui-performAssignmentScheduleRelativeDateSelector">
      <div class="tui-performAssignmentScheduleRelativeDateSelector__count">
        <Label
          :for="$id('relative-date-count-from')"
          :label="$str('relative_date_selector_count', 'mod_perform')"
          hidden
        />
        <FormNumber
          :id="$id('relative-date-count-from')"
          name="count"
          :disabled="disabled"
          :validations="countValidator"
          :min="0"
        />
      </div>

      <template v-if="hasRange">
        <span>{{ untilText }}</span>
        <div class="tui-performAssignmentScheduleRelativeDateSelector__count">
          <Label
            :for="$id('relative-date-count-to')"
            :label="$str('relative_date_selector_count', 'mod_perform')"
            hidden
          />
          <FormNumber
            :id="$id('relative-date-count-to')"
            name="count_to"
            :disabled="disabled"
            :validations="countValidator"
            :min="0"
          />
        </div>
      </template>

      <div class="tui-performAssignmentScheduleRelativeDateSelector__unit">
        <Label
          :for="$id('relative-date-unit')"
          :label="$str('relative_date_selector_unit', 'mod_perform')"
          hidden
        />
        <FormSelect
          :id="$id('relative-date-unit')"
          name="unit"
          :options="unitOptions"
          :disabled="disabled"
        />
      </div>

      <div
        v-if="hasDirection"
        class="tui-performAssignmentScheduleRelativeDateSelector__direction"
      >
        <Label
          :for="$id('relative-date-direction')"
          :label="$str('relative_date_selector_direction', 'mod_perform')"
          hidden
        />
        <FormSelect
          :id="$id('relative-date-direction')"
          name="direction"
          :options="directionOptions"
          :disabled="disabled"
        />
      </div>
    </div>
  </FormScope>
</template>

<script>
import Label from 'totara_core/components/form/Label';
import {
  FormSelect,
  FormNumber,
  FormScope,
} from 'totara_core/components/uniform';
import {
  RELATIVE_DATE_DIRECTION_BEFORE,
  RELATIVE_DATE_DIRECTION_AFTER,
  RELATIVE_DATE_UNIT_DAY,
  RELATIVE_DATE_UNIT_WEEK,
  RELATIVE_DATE_UNIT_MONTH,
} from 'mod_perform/constants';

export default {
  components: { FormSelect, FormNumber, FormScope, Label },
  props: {
    path: {
      type: String,
      required: true,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    hasDirection: {
      type: Boolean,
      default: false,
    },
    hasRange: {
      type: Boolean,
      default: false,
    },
    untilText: {
      type: String,
      default() {
        return this.$str('relative_date_selector_until', 'mod_perform');
      },
    },
  },
  computed: {
    unitOptions() {
      return [
        {
          label: this.$str('relative_date_selector_days', 'mod_perform'),
          id: RELATIVE_DATE_UNIT_DAY,
        },
        {
          label: this.$str('relative_date_selector_weeks', 'mod_perform'),
          id: RELATIVE_DATE_UNIT_WEEK,
        },
        {
          label: this.$str('relative_date_selector_months', 'mod_perform'),
          id: RELATIVE_DATE_UNIT_MONTH,
        },
      ];
    },
    directionOptions() {
      return [
        {
          label: this.$str('relative_date_selector_before', 'mod_perform'),
          id: RELATIVE_DATE_DIRECTION_BEFORE,
        },
        {
          label: this.$str('relative_date_selector_after', 'mod_perform'),
          id: RELATIVE_DATE_DIRECTION_AFTER,
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
    countValidator(v) {
      if (this.disabled) {
        return [];
      }
      return [v.min(0), v.integer(), v.required()];
    },

    /**
     * Validator for the dynamic instance creation to/until range.
     * From must not be after until.
     *
     * @param values
     * @return {{}}
     */
    relativeDateValidator(values) {
      if (!this.hasRange || !this.hasDirection || this.disabled) {
        return {};
      }

      const direction = values.direction;
      const from = Number(values.count);
      const to = Number(values.count_to);

      if (direction === RELATIVE_DATE_DIRECTION_AFTER && from > to) {
        return {
          count_to: this.$str(
            'relative_date_selector_error_range',
            'mod_perform'
          ),
        };
      } else if (direction === RELATIVE_DATE_DIRECTION_BEFORE && from < to) {
        return {
          count: this.$str('relative_date_selector_error_range', 'mod_perform'),
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
      "relative_date_selector_after",
      "relative_date_selector_before",
      "relative_date_selector_count",
      "relative_date_selector_days",
      "relative_date_selector_direction",
      "relative_date_selector_error_range",
      "relative_date_selector_from",
      "relative_date_selector_months",
      "relative_date_selector_unit",
      "relative_date_selector_until",
      "relative_date_selector_weeks"
    ]
  }
</lang-strings>
