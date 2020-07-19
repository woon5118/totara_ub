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

  @author Simon Chester <simon.chester@totaralearning.com>
  @package totara_core
-->

<template>
  <div class="tui-errorDisplay">
    <h1 class="tui-errorDisplay__title">
      {{ $str('error', 'moodle') }}
    </h1>
    <p>
      {{
        $tryStr('error:pagerender', 'totara_core') ||
          'An error occurred while rendering the page.'
      }}
    </p>
    <div class="tui-errorDisplay__buttons">
      <button v-if="retryable" type="button" @click.prevent="retry">
        {{ $str('retry', 'totara_core') }}
      </button>
    </div>
    <a
      v-if="error"
      class="tui-errorDisplay__detailsToggle"
      href="javascript:;"
      @click.prevent="toggleDetails"
    >
      {{ (showDetails ? '▾ ' : '▸ ') + $str('details', 'totara_core') }}
    </a>
    <pre v-if="error && showDetails" class="tui-errorDisplay__detailsPre">{{
      error.stack ? error.stack : error
    }}</pre>
  </div>
</template>

<script>
export default {
  props: {
    // error can accept any type as long as it or its .stack property can be converted to a string
    // eslint-disable-next-line vue/require-prop-types
    error: {
      default: undefined,
    },
    retryable: Boolean,
  },

  data() {
    return {
      showDetails: false,
    };
  },

  methods: {
    toggleDetails() {
      this.showDetails = !this.showDetails;
    },

    retry() {
      this.$emit('retry');
    },
  },
};
</script>

<lang-strings>
{
  "moodle": [
    "error"
  ],
  "totara_core": [
    "details",
    "error:pagerender",
    "retry"
  ]
}
</lang-strings>
