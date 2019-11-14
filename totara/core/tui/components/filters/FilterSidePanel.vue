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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_core
-->

<template>
  <Card class="tui-filterSidePanel">
    <div class="tui-filterSidePanel__heading">
      <h2 class="tui-filterSidePanel__header">
        {{ title }}
        <span v-if="activeCount" aria-hidden="true">( {{ activeCount }} )</span>
      </h2>
      <div
        v-if="Object.entries(value).length"
        class="tui-filterSidePanel__status"
        role="status"
      >
        {{
          $str(
            activeCount == 1
              ? 'a11y_active_filter_type'
              : 'a11y_active_filter_type_plural',
            'totara_core',
            activeCount
          )
        }}
      </div>
      <div class="tui-filterSidePanel__instructions">
        {{ $str('a11y_filter_panel_desc', 'totara_core') }}
      </div>
      <Button
        v-if="Object.entries(value).length"
        class="tui-filterSidePanel__clearBtn"
        :aria-disabled="!activeCount"
        :aria-label="$str('filter_clear_active', 'totara_core')"
        :disabled="!activeCount"
        :styleclass="{
          srOnly: !activeCount,
          transparent: 'true',
          small: true,
        }"
        :text="$str('clearall', 'totara_core')"
        @click="clearAllFilters"
      />
    </div>
    <slot />
  </Card>
</template>

<script>
import Button from 'totara_core/components/buttons/Button';
import Card from 'totara_core/components/card/Card';

export default {
  components: {
    Button,
    Card,
  },

  props: {
    title: String,
    value: {
      default() {
        return {};
      },
      type: Object,
    },
  },

  computed: {
    /**
     * Calculate the number of active filters
     *
     * @return {Int}
     */
    activeCount() {
      let count = 0;
      Object.keys(this.value).forEach(key => {
        let val = this.value[key];

        if (val instanceof Array) {
          if (val.length !== 0) {
            count += val.length;
          }
        } else if (val !== '') {
          count++;
        }
      });
      return count;
    },
  },

  watch: {
    activeCount: {
      handler(value) {
        this.$emit('active-count-change', value);
      },
      immediate: true,
    },
  },

  methods: {
    clearAllFilters() {
      Object.keys(this.value).forEach(nestedKey => {
        let type = this.value[nestedKey] instanceof Array ? [] : '';
        this.value[nestedKey] = type;
      });
    },
  },
};
</script>

<lang-strings>
{
  "totara_core": [
    "a11y_active_filter_type",
    "a11y_active_filter_type_plural",
    "a11y_filter_panel_desc",
    "clearall",
    "filter_clear_active"
  ]
}
</lang-strings>
