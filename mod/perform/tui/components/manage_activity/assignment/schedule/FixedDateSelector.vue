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
  <FormScope :validate="fixedDateRangeValidator" :path="path">
    <div class="tui-performAssignmentScheduleFixedDateSelector">
      <div class="tui-performAssignmentScheduleFixedDateSelector__input">
        <Label
          :for="$id('fixed-date-from')"
          :label="$str('fixed_date_selector_date', 'mod_perform')"
          hidden
        />
        <FormText
          :id="$id('fixed-date-from')"
          name="from"
          :disabled="disabled"
          :validations="dateValidator"
        />
      </div>

      <template v-if="hasRange">
        <span>{{ toText }}</span>
        <div class="tui-performAssignmentScheduleFixedDateSelector__input">
          <Label
            :for="$id('fixed-date-to')"
            :label="$str('fixed_date_selector_date', 'mod_perform')"
            hidden
          />
          <FormText
            :id="$id('fixed-date-to')"
            name="to"
            :disabled="disabled"
            :validations="dateValidator"
          />
        </div>
      </template>
    </div>
  </FormScope>
</template>

<script>
import Label from 'totara_core/components/form/Label';
import { FormText, FormScope } from 'totara_core/components/uniform';

export default {
  components: { FormText, FormScope, Label },
  props: {
    path: {
      type: String,
      required: true,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    hasRange: {
      type: Boolean,
      default: false,
    },
    toText: {
      type: String,
      default() {
        return this.$str('fixed_date_selector_to', 'mod_perform');
      },
    },
  },

  methods: {
    /**
     * Validator for date strings.
     * Please note: Note this is most likely temporary until the date picker component is built.
     *
     * @return {{}}
     */
    dateValidator() {
      return {
        validate: val => this.disabled || !isNaN(Date.parse(val)),
        message: () =>
          this.$str('fixed_date_selector_error_required', 'mod_perform'),
      };
    },

    /**
     * Validator for the fixed instance creation to/from range.
     * From must not be after to.
     * Please note: Note this is most likely temporary until the date picker component is built.
     *
     * @param values
     * @return {{}}
     */
    fixedDateRangeValidator(values) {
      if (this.disabled || !this.hasRange) {
        return {};
      }

      const errors = {};
      const fromDate = Date.parse(values.from);
      const toDate = Date.parse(values.to);

      if (fromDate > toDate) {
        errors.to = this.$str('fixed_date_selector_error_range', 'mod_perform');
      }

      return errors;
    },
  },
};
</script>

<lang-strings>
  {
    "mod_perform": [
      "fixed_date_selector_date",
      "fixed_date_selector_error_range",
      "fixed_date_selector_error_required",
      "fixed_date_selector_to"
    ]
  }
</lang-strings>
