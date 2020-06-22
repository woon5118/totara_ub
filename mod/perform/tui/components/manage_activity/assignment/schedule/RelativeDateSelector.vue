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
          :for="$id('relative-date-from-count')"
          :label="$str('relative_date_selector_count', 'mod_perform')"
          hidden
        />
        <FormNumber
          :id="$id('relative-date-from-count')"
          name="from_count"
          :disabled="disabled"
          :validations="countValidator"
          :min="0"
        />
      </div>

      <div class="tui-performAssignmentScheduleRelativeDateSelector__unit">
        <Label
          :for="$id('relative-date-from-unit')"
          :label="$str('relative_date_selector_unit', 'mod_perform')"
          hidden
        />
        <FormSelect
          :id="$id('relative-date-from-unit')"
          name="from_unit"
          :options="unitOptions"
          :disabled="disabled"
        />
      </div>

      <div
        v-if="hasDirection"
        class="tui-performAssignmentScheduleRelativeDateSelector__direction"
      >
        <Label
          :for="$id('relative-date-from-direction')"
          :label="$str('relative_date_selector_direction', 'mod_perform')"
          hidden
        />
        <FormSelect
          :id="$id('relative-date-from-direction')"
          name="from_direction"
          :options="directionOptions"
          :disabled="disabled"
        />
      </div>

      <template v-if="hasRange">
        <span>{{ untilText }}</span>
        <div class="tui-performAssignmentScheduleRelativeDateSelector__count">
          <Label
            :for="$id('relative-date-to-count')"
            :label="$str('relative_date_selector_count', 'mod_perform')"
            hidden
          />
          <FormNumber
            :id="$id('relative-date-to-count')"
            name="to_count"
            :disabled="disabled"
            :validations="countValidator"
            :min="0"
          />
        </div>

        <div class="tui-performAssignmentScheduleRelativeDateSelector__unit">
          <Label
            :for="$id('relative-date-to-unit')"
            :label="$str('relative_date_selector_unit', 'mod_perform')"
            hidden
          />
          <FormSelect
            :id="$id('relative-date-to-unit')"
            name="to_unit"
            :options="unitOptions"
            :disabled="disabled"
          />
        </div>

        <div
          v-if="hasDirection"
          class="tui-performAssignmentScheduleRelativeDateSelector__direction"
        >
          <Label
            :for="$id('relative-date-to-direction')"
            :label="$str('relative_date_selector_direction', 'mod_perform')"
            hidden
          />
          <FormSelect
            :id="$id('relative-date-to-direction')"
            name="to_direction"
            :options="directionOptions"
            :disabled="disabled"
          />
        </div>
      </template>

      <div
        class="tui-performAssignmentScheduleRelativeDateSelector__reference-date"
      >
        <Label
          :for="$id('relative-date-reference-date')"
          :label="$str('relative_date_selector_reference_date', 'mod_perform')"
          hidden
        />
        <FormSelect
          v-if="dynamicDateSources"
          :id="$id('relative-date-reference-date')"
          name="dynamic_source"
          :options="dynamicSourcesForSelect"
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
} from 'mod_perform/constants';

export default {
  components: { FormSelect, FormNumber, FormScope, Label },
  props: {
    path: {
      type: [String, Array],
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
    dynamicDateSources: {
      type: Array,
      required: false,
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
    dynamicSourcesForSelect() {
      if (!this.dynamicDateSources) {
        return [];
      }

      return this.dynamicDateSources.map(option => {
        return {
          label: option.display_name,
          id: `${option.resolver_class_name}--${option.option_key}`,
        };
      });
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

      let from_count = Number(values.from_count);
      const from_direction = values.from_direction;
      let to_count = Number(values.to_count);
      const to_direction = values.to_direction;

      if (values.from_unit == RELATIVE_DATE_UNIT_WEEK) {
        from_count *= 7;
      }

      if (values.to_unit == RELATIVE_DATE_UNIT_WEEK) {
        to_count *= 7;
      }

      const rangeOrderErrorString = this.$str(
        'relative_date_selector_error_range',
        'mod_perform'
      );

      if (
        from_direction === RELATIVE_DATE_DIRECTION_AFTER &&
        to_direction === RELATIVE_DATE_DIRECTION_BEFORE
      ) {
        return {
          to_direction: rangeOrderErrorString,
        };
      } else if (
        from_direction === RELATIVE_DATE_DIRECTION_AFTER &&
        to_direction === RELATIVE_DATE_DIRECTION_AFTER &&
        from_count > to_count
      ) {
        return {
          to_count: rangeOrderErrorString,
        };
      } else if (
        from_direction === RELATIVE_DATE_DIRECTION_BEFORE &&
        to_direction === RELATIVE_DATE_DIRECTION_BEFORE &&
        from_count < to_count
      ) {
        return {
          to_count: rangeOrderErrorString,
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
      "relative_date_selector_reference_date",
      "relative_date_selector_error_range",
      "relative_date_selector_from",
      "relative_date_selector_unit",
      "relative_date_selector_until",
      "relative_date_selector_weeks"
    ]
  }
</lang-strings>
