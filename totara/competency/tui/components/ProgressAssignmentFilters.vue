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

  @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
  @package totara_competency
-->

<template>
  <div>
    <p class="sr-only">
      {{ $str('filter:viewing_by_assignment', 'totara_competency') }}
    </p>
    <SelectFilter
      v-if="displayFilters"
      :show-label="true"
      :label="$str('viewing_by_assignment', 'totara_competency')"
      :options="
        filterOptions.map(option => ({ id: option.id, label: option.name }))
      "
      :disabled="disable"
      :value="value && value.key"
      @input="filterUpdated"
    />
  </div>
</template>

<script>
import SelectFilter from 'totara_core/components/filters/SelectFilter';

export default {
  components: {
    SelectFilter,
  },

  props: {
    filters: {
      type: Array,
      required: true,
    },
    value: {
      type: Object,
      required: true,
    },
    disable: {
      type: Boolean,
      default: false,
    },
  },

  computed: {
    displayFilters() {
      return !(this.filters.length === 1 && this.filters[0].status === 1);
    },

    filterOptions() {
      const options = [];

      this.filters.forEach(group => {
        options.push({
          name: group.name,
          id: group.id,
          value: group.value,
        });

        group.filters.forEach(x =>
          options.push(
            Object.assign({}, x, {
              name: '\xa0\xa0\xa0' + x.name,
            })
          )
        );
      });

      return options;
    },
  },

  methods: {
    toggle() {
      this.open = !this.open;
    },

    filterUpdated(value) {
      const item = this.filterOptions.find(x => x.id == value);
      this.$emit('input', item && item.value);
    },
  },
};
</script>

<lang-strings>
{
  "totara_competency": [
    "filter:viewing_by_assignment",
    "viewing_by_assignment"
    ]
}
</lang-strings>
