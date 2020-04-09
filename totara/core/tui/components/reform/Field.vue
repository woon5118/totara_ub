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

  @author Simon Chester <simon.chester@totaralearning.com>
  @package totara_core
-->

<script>
import { fieldValidator } from 'totara_core/validation';

export default {
  inject: {
    reformScope: {},
    reformFieldContext: { default: undefined },
  },

  props: {
    name: {
      type: [String, Number, Array],
      required: true,
    },

    validate: Function,
    validations: [Function, Array],
  },

  computed: {
    /**
     * HTML ID for field.
     *
     * Used to suppot accessibility.
     *
     * @returns {string}
     */
    id() {
      return (
        (this.reformFieldContext && this.reformFieldContext.getId()) || this.uid
      );
    },

    /**
     * HTML ID for label.
     *
     * Used to suppot accessibility.
     *
     * @returns {?string}
     */
    labelId() {
      return (
        (this.reformFieldContext && this.reformFieldContext.getLabelId()) ||
        undefined
      );
    },

    /**
     * Field value.
     *
     * @returns {*}
     */
    value() {
      return this.reformScope && this.reformScope.getValue(this.name);
    },

    /**
     * Error string if field has an error, or falsy value if it does not.
     *
     * @returns {?string}
     */
    error() {
      return this.reformScope && this.reformScope.getError(this.name);
    },

    /**
     * Validator function.
     *
     * @returns {?function}
     */
    validator() {
      if (typeof this.validate == 'function') {
        return this.validate;
      }
      if (this.validations) {
        return fieldValidator(this.validations);
      }
      return null;
    },

    /**
     * Helper computed property for updating registration
     */
    validatorInfo() {
      return [this.name, this.validator];
    },

    /**
     * Name to use for the input in HTML.
     *
     * @returns {string}
     */
    inputName() {
      return this.reformScope.getInputName(this.name);
    },
  },

  watch: {
    validatorInfo: {
      immediate: true,
      handler(val, old) {
        // re-register validator if it changes or the path changes
        this.reformScope.updateRegistration(
          'validator',
          val && val[0],
          val && val[1],
          old && old[0],
          old && old[1]
        );
      },
    },

    name: {
      immediate: true,
      handler(val, old) {
        // re-register element at new path
        this.reformScope.updateRegistration(
          'element',
          val,
          this.$_getEl,
          old,
          this.$_getEl
        );
      },
    },
  },

  mounted() {
    if (!this.reformScope) {
      console.warn(
        '[Reform] Field must be contained within a <Reform> component.'
      );
    }
  },

  beforeDestroy() {
    if (this.reformScope) {
      this.reformScope.unregister('validator', this.name, this.validator);
      this.reformScope.unregister('element', this.name, this.$_getEl);
    }
  },

  methods: {
    /**
     * Update form with new value for field.
     *
     * @param {*} value
     */
    update(value) {
      this.reformScope.update(this.name, value);
    },

    /**
     * Mark field as touched.
     */
    blur() {
      this.reformScope.blur(this.name);
    },

    /**
     * Get the element associated with this field.
     *
     * @internal
     */
    $_getEl() {
      return this.$el;
    },
  },

  render() {
    return this.$scopedSlots.default({
      id: this.id,
      labelId: this.labelId,
      value: this.value,
      name: this.name,
      update: this.update,
      blur: this.blur,
      error: this.error,
      inputName: this.inputName,
    });
  },
};
</script>
