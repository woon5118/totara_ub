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

  @author Kevin Hottinger <kevin.hottinger@totaralearning.com>
  @package totara_samples
-->

<template>
  <Adder
    :open="open"
    title="Select the best capital cities!"
    :existing-items="existingItems"
    @added="$emit('added', $event)"
    @cancel="$emit('cancel')"
  >
    <template v-slot:browse-filters>
      <FilterBar :title="'Filter cities'">
        <SearchFilter
          v-model="filters.search"
          label="Filter items by search"
          :show-label="false"
          :placeholder="'Search'"
        />
      </FilterBar>
    </template>

    <template v-slot:browse-list="{ disabledItems, selectedItems, update }">
      <SelectTable
        :value="selectedItems"
        :data="data"
        :disabled-ids="disabledItems"
        checkbox-v-align="center"
        :color-odd-rows="true"
        :select-all-enabled="true"
        :border-bottom-hidden="true"
        @input="update($event)"
      >
        <template v-slot:header-row>
          <HeaderCell size="10" valign="center">City</HeaderCell>
          <HeaderCell size="6" valign="center">Language</HeaderCell>
        </template>

        <template v-slot:row="{ row }">
          <Cell size="10" column-header="Name" valign="center">
            {{ row.name }}
          </Cell>

          <Cell size="6" column-header="Language" valign="center">
            {{ row.language }}
          </Cell>
        </template>
      </SelectTable>
    </template>

    <template v-slot:basket-list="{ selectedItems, update }">
      <SelectTable
        :value="selectedItems"
        :data="dummyDataSelected"
        checkbox-v-align="center"
        :border-bottom-hidden="true"
        :color-odd-rows="true"
        :select-all-enabled="true"
        @input="update($event)"
      >
        <template v-slot:header-row>
          <HeaderCell size="10" valign="center">City</HeaderCell>
          <HeaderCell size="6" valign="center">Language</HeaderCell>
        </template>

        <template v-slot:row="{ row }">
          <Cell size="10" column-header="Name" valign="center">
            {{ row.name }}
          </Cell>

          <Cell size="6" column-header="Language" valign="center">
            {{ row.language }}
          </Cell>
        </template>
      </SelectTable>
    </template>
  </Adder>
</template>

<script>
import Adder from 'totara_core/components/adder/Adder';
import Cell from 'totara_core/components/datatable/Cell';
import FilterBar from 'totara_core/components/filters/FilterBar';
import HeaderCell from 'totara_core/components/datatable/HeaderCell';
import SearchFilter from 'totara_core/components/filters/SearchFilter';
import SelectTable from 'totara_core/components/datatable/SelectTable';

export default {
  components: {
    Adder,
    Cell,
    FilterBar,
    HeaderCell,
    SearchFilter,
    SelectTable,
  },

  props: {
    existingItems: {
      type: Array,
      default: () => [],
    },
    open: Boolean,
  },

  data() {
    return {
      data: [
        {
          language: 'French',
          name: 'Paris',
        },
        {
          language: 'German',
          name: 'Berlin',
        },
        {
          language: 'English',
          name: 'London',
        },
        {
          language: 'Italian',
          name: 'Rome',
        },
        {
          language: 'English',
          name: 'Wellington',
        },
        {
          language: 'Japanese',
          name: 'Tokyo',
        },
        {
          language: 'Spanish',
          name: 'Buenos Aires',
        },
        {
          language: 'Portuguese',
          name: 'Brasilia',
        },
        {
          language: 'Spanish',
          name: 'Lima',
        },
      ],
      dummyDataSelected: [
        {
          language: 'French',
          name: 'Paris',
        },
      ],
      filters: {
        search: '',
      },
      existingSelection: [],
      selection: [],
      pageSelection: [],
      codeTemplate: `<Lozenge :text="text" :type="type" />`,
      codeScript: `import Lozenge from 'totara_core/components/lozenge/Lozenge';

export default {
  components: {
    Lozenge,
  }
}`,
    };
  },
};
</script>
