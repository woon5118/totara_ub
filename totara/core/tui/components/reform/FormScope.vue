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
function makePassthroughsWithName(vm, keys) {
  const obj = {};
  keys.forEach(key => {
    obj[key] = (name, ...args) =>
      vm.reformScope[key](vm.$_getPath(name), ...args);
  });
  return obj;
}

const pathMethods = [
  'getValue',
  'getError',
  'getTouched',
  'update',
  'blur',
  'getInputName',
  '$_internalUpdateSliceState',
];

export default {
  inject: ['reformScope'],

  provide() {
    return {
      reformScope: Object.assign(makePassthroughsWithName(this, pathMethods), {
        register: (type, path, val) =>
          this.reformScope.register(type, this.$_getPath(path), val),
        unregister: (type, path, val) =>
          this.reformScope.unregister(type, this.$_getPath(path), val),
        updateRegistration: (type, path, val, oldPath, oldVal) =>
          this.reformScope.updateRegistration(
            type,
            this.$_getPath(path),
            val,
            this.$_getPath(oldPath),
            oldVal
          ),
      }),
    };
  },

  props: {
    path: {
      type: [String, Array],
      required: true,
    },

    validate: Function,
    process: Function,
    emitSubmit: Boolean,
  },

  computed: {
    validatorInfo() {
      return [this.path, this.validate];
    },
    processorInfo() {
      return [this.path, this.process];
    },
    submitInfo() {
      return [this.path, this.emitSubmit && this.$_handleSubmit];
    },
  },

  watch: {
    validatorInfo: {
      immediate: true,
      handler(val, old) {
        this.$_updateRegistration('validator', val, old);
      },
    },

    processorInfo: {
      immediate: true,
      handler(val, old) {
        this.$_updateRegistration('processor', val, old);
      },
    },

    submitInfo: {
      immediate: true,
      handler(val, old) {
        this.$_updateRegistration('submitHandler', val, old);
      },
    },
  },

  mounted() {
    if (!this.reformScope) {
      console.warn(
        '[Reform] FormScope must be contained within a <Reform> component.'
      );
    }
  },

  beforeDestroy() {
    if (this.reformScope) {
      this.reformScope.unregister('validator', this.path, this.validate);
      this.reformScope.unregister('processor', this.path, this.process);
      this.reformScope.unregister(
        'submitHandler',
        this.path,
        this.$_handleSubmit
      );
    }
  },

  methods: {
    $_getPath(name) {
      return this.path != null ? [].concat(this.path, name) : name;
    },

    $_updateRegistration(type, val, old) {
      this.reformScope.updateRegistration(
        type,
        val && val[0],
        val && val[1],
        old && old[0],
        old && old[1]
      );
    },

    $_handleSubmit(value) {
      this.$emit('submit', value);
    },
  },

  render() {
    return this.$scopedSlots.default ? this.$scopedSlots.default() : null;
  },
};
</script>
