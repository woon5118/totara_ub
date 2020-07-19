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
  @package totara_core
-->

<template>
  <div>
    <SamplesExample>
      <FilterBar
        v-model="selection"
        :title="'Filter results'"
        :has-top-bar="filterHasTopBar"
        :has-bottom-bar="filterHasBottomBar"
      >
        <template v-slot:filters-left="{ stacked }">
          <SelectFilter
            v-model="selection.category"
            label="Within category"
            :show-label="true"
            :options="categories"
            :stacked="stacked"
          />

          <SelectFilter
            v-model="selection.colours"
            label="Colour"
            :show-label="true"
            :options="colourOptions"
            :stacked="stacked"
          />
        </template>

        <template v-slot:filters-right="{ stacked }">
          <SearchFilter
            v-model="selection.search"
            label="Filter items by search"
            :show-label="false"
            :placeholder="'Search'"
            :stacked="stacked"
          />
        </template>
      </FilterBar>

      <div>
        <h2>Filter values</h2>
        {{ selection }}
      </div>
    </SamplesExample>

    <SamplesPropCtl>
      <FormRow v-slot="{ labelId }" label="hasTopBar">
        <RadioGroup v-model="filterHasTopBar" :horizontal="true">
          <Radio :value="true">true</Radio>
          <Radio :value="false">false</Radio>
        </RadioGroup>
      </FormRow>

      <FormRow v-slot="{ labelId }" label="hasBottomBar">
        <RadioGroup v-model="filterHasBottomBar" :horizontal="true">
          <Radio :value="true">true</Radio>
          <Radio :value="false">false</Radio>
        </RadioGroup>
      </FormRow>
    </SamplesPropCtl>

    <SamplesCode>
      <template v-slot:template>{{ codeTemplate }}</template>
      <template v-slot:script>{{ codeScript }}</template>
    </SamplesCode>
  </div>
</template>

<script>
import SamplesCode from 'totara_samples/components/sample_parts/misc/SamplesCode';
import SamplesExample from 'totara_samples/components/sample_parts/misc/SamplesExample';
import SamplesPropCtl from 'totara_samples/components/sample_parts/misc/SamplesPropCtl';
import FormRow from 'totara_core/components/form/FormRow';
import Radio from 'totara_core/components/form/Radio';
import RadioGroup from 'totara_core/components/form/RadioGroup';

// Components
import FilterBar from 'totara_core/components/filters/FilterBar';
import SearchFilter from 'totara_core/components/filters/SearchFilter';
import SelectFilter from 'totara_core/components/filters/SelectFilter';

export default {
  components: {
    FormRow,
    Radio,
    RadioGroup,
    SamplesCode,
    SamplesPropCtl,
    SamplesExample,

    FilterBar,
    SearchFilter,
    SelectFilter,
  },

  data() {
    return {
      colourOptions: [
        {
          id: '',
          label: 'Please select',
        },
        {
          id: 'orange',
          label: 'Orange',
        },
        {
          id: 'darkRed',
          label: 'Dark red',
        },
      ],
      categories: [
        {
          id: '',
          label: 'No category selected',
        },
        {
          id: 'films',
          label: 'films',
        },
        {
          id: 'comics',
          label: 'Comic books',
        },
      ],
      filterHasTopBar: true,
      filterHasBottomBar: true,
      selection: {
        category: '',
        colours: '',
        search: '',
      },
      codeTemplate: `<FilterBar v-model="selection" :title="'Filter results'">

  <!-- Left aligned content -->
  <template v-slot:filters-left="{ stacked }">
    <SelectFilter
      v-model="selection.category"
      label="Within category"
      :show-label="true"
      :options="categories"
      :stacked="stacked"
    />

    <SelectFilter
      v-model="selection.colours"
      label="Colour"
      :show-label="true"
      :options="colourOptions"
      :stacked="stacked"
    />
  </template>

  <!-- Right aligned content -->
  <template v-slot:filters-right="{ stacked }">
    <SearchFilter
      v-model="selection.search"
      label="Filter items by search"
      :show-label="false"
      :placeholder="'Search'"
      :stacked="stacked"
    />
  </template>

</FilterBar>`,
      codeScript: `import FilterBar from 'totara_core/components/filters/FilterBar';
import SearchFilter from 'totara_core/components/filters/SearchFilter';
import SelectFilter from 'totara_core/components/filters/SelectFilter';

export default {
  components: {
    FilterBar,
    SearchFilter,
    SelectFilter,
  },

  data() {
    return {
      colourOptions: [
        {
          id: '',
          label: 'Please select',
        },
        {
          id: 'orange',
          label: 'Orange',
        },
        {
          id: 'darkRed',
          label: 'Dark red',
        },
      ],
      categories: [
        {
          id: '',
          label: 'No category selected',
        },
        {
          id: 'films',
          label: 'films',
        },
        {
          id: 'comics',
          label: 'Comic books',
        },
      ],
      selection: {
        category: '',
        colours: '',
        search: '',
      },
    }
  }
}`,
    };
  },
};
</script>
