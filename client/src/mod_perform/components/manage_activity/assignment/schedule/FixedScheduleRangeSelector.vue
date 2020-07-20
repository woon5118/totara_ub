<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD's customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Jaron Steenson <jaron.steenson@totaralearning.com>
  @module mod_perform
-->

<template>
  <FormScope :validate="fixedDateRangeValidator" :path="path">
    <div
      :class="{
        'tui-performAssignmentScheduleFixedDateSelector': true,
        'tui-performAssignmentScheduleFixedDateSelector--has-range': hasRange,
      }"
    >
      <div class="tui-performAssignmentScheduleFixedDateSelector__input">
        <Label
          class="tui-performAssignmentScheduleFixedDateSelector__label"
          :for="$id('fixed-date-from')"
          :label="this.$str('fixed_date_selector_from', 'mod_perform')"
          :hidden="!hasRange"
        />
        <FormDateSelector
          :id="$id('fixed-date-from')"
          name="from"
          :disabled="disabled"
          :validations="v => [v.required()]"
          type="date"
          has-timezone
        />
      </div>

      <template v-if="hasRange">
        <div class="tui-performAssignmentScheduleFixedDateSelector__input">
          <Label
            class="tui-performAssignmentScheduleFixedDateSelector__label"
            :for="$id('fixed-date-to')"
            :label="this.$str('fixed_date_selector_to', 'mod_perform')"
          />
          <FormDateSelector
            :id="$id('fixed-date-to')"
            name="to"
            :disabled="disabled"
            :validations="v => [v.required()]"
            type="date"
          />
        </div>
      </template>
    </div>
  </FormScope>
</template>

<script>
import Label from 'tui/components/form/Label';
import { isIsoAfter } from 'tui/date';
import { FormScope, FormDateSelector } from 'tui/components/uniform';

export default {
  components: { FormDateSelector, FormScope, Label },
  props: {
    initialStartDate: {
      type: String,
      required: false,
    },
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
  },

  methods: {
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

      if (!isIsoAfter(values.to.iso, values.from.iso)) {
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
      "fixed_date_selector_to",
      "fixed_date_selector_from"
    ]
  }
</lang-strings>
