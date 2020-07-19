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
  @package totara_samples
-->

<template>
  <div>
    <SamplesExample>
      <FilterSidePanel v-model="selection" title="Filter results">
        <MultiSelect
          v-model="selection.colours"
          :options="colourOptions"
          :title="'Contains colours'"
        />

        <MultiSelect
          v-model="selection.languages"
          :options="languages"
          :title="'In these languages'"
        />

        <SearchFilter
          v-model="selection.search"
          label="Filter items by search"
          :show-label="true"
          :placeholder="'Filtering'"
          :stacked="true"
        />

        <SelectFilter
          v-model="selection.category"
          label="Within category"
          :show-label="true"
          :options="categories"
          :stacked="true"
        />
      </FilterSidePanel>

      <div>
        <h2>Filter values</h2>
        {{ selection }}
      </div>
    </SamplesExample>

    <SamplesCode>
      <template v-slot:template>{{ codeTemplate }}</template>
      <template v-slot:script>{{ codeScript }}</template>
    </SamplesCode>
  </div>
</template>

<script>
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
// Components
import FilterSidePanel from 'totara_core/components/filters/FilterSidePanel';
import MultiSelect from 'totara_core/components/filters/MultiSelectFilter';
import SearchFilter from 'totara_core/components/filters/SearchFilter';
import SelectFilter from 'totara_core/components/filters/SelectFilter';

export default {
  components: {
    SamplesCode,
    SamplesExample,

    FilterSidePanel,
    MultiSelect,
    SearchFilter,
    SelectFilter,
  },

  data() {
    return {
      colourOptions: [
        {
          id: 'orange',
          label: 'Orange',
        },
        {
          id: 'darkRed',
          label: 'Dark red',
        },
        {
          id: 'brightPink',
          label: 'Bright pink',
        },
        {
          id: 'gold',
          label: 'Gold',
        },
      ],
      languages: [
        {
          id: 'english',
          label: 'English',
        },
        {
          id: 'french',
          label: 'French',
        },
        {
          id: 'german',
          label: 'German',
        },
      ],
      categories: [
        {
          id: '',
          label: 'No category selected',
        },
        {
          id: 'films',
          label: 'Films',
        },
        {
          id: 'comics',
          label: 'Comic books',
        },
        {
          id: 'music',
          label: 'Music',
        },
        {
          id: 'travel',
          label: 'Travel',
        },
      ],
      selection: {
        category: '',
        colours: [],
        languages: [],
        search: '',
      },

      codeTemplate: `<FilterSidePanel v-model="selection" :title="'Filter results'">

  <!-- Filters -->
  <MultiSelect
    v-model="selection.colours"
    :options="colourOptions"
    :title="'Contains colours'"
  />

  <MultiSelect
    v-model="selection.languages"
    :options="languages"
    :title="'In these languages'"
  />

  <SearchFilter
    v-model="selection.search"
    label="Filter items by search"
    :show-label="true"
    :placeholder="'Filtering'"
    :stacked="true"
  />

  <SelectFilter
    v-model="selection.category"
    label="Within category"
    :show-label="true"
    :options="categories"
    :stacked="true"
  />

</FilterSidePanel>`,
      codeScript: `import FilterSidePanel from 'totara_core/components/filters/FilterSidePanel';
import MultiSelect from 'totara_core/components/filters/MultiSelectFilter';
import SearchFilter from 'totara_core/components/filters/SearchFilter';
import SelectFilter from 'totara_core/components/filters/SelectFilter';

export default {
  components: {
    FilterSidePanel,
    MultiSelect,
    SearchFilter,
    SelectFilter,
  },

  data() {
    return {
      colourOptions: [
        {
          id: 'orange',
          label: 'Orange',
        },
        {
          id: 'darkRed',
          label: 'Dark red',
        },
        {
          id: 'brightPink',
          label: 'Bright pink',
        },
        {
          id: 'gold',
          label: 'Gold',
        },
      ],
      languages: [
        {
          id: 'english',
          label: 'English',
        },
        {
          id: 'french',
          label: 'French',
        },
        {
          id: 'german',
          label: 'German',
        },
      ],
      categories: [
        {
          id: '',
          label: 'No category selected',
        },
        {
          id: 'films',
          label: 'Films',
        },
        {
          id: 'comics',
          label: 'Comic books',
        },
        {
          id: 'music',
          label: 'Music',
        },
        {
          id: 'travel',
          label: 'Travel',
        },
      ],
      selection: {
        category: '',
        colours: [],
        languages: [],
        search: '',
      },
    }
  }
}`,
    };
  },
};
</script>
